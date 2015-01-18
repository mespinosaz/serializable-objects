<?php

namespace mespinosaz\SerializableObjects\Tests;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use mespinosaz\SerializableObjects\Node\Composite;
use mespinosaz\SerializableObjects\Node\Factory\ContentFactory;
use mespinosaz\SerializableObjects\Node\Factory\TagFactory;

class XMLDecodeTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    protected function setUp()
    {
        $this->serializer = new Serializer(
            array(
                new CustomNormalizer()
            ),
            array(
                'xml' => new XmlEncoder()
            )
        );
    }

    public function testContent()
    {
        $expected = ContentFactory::build('foo');
        $xml = '<?xml version="1.0"?>'."\n".
            '<response>foo</response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Content', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testTag()
    {
        $content = ContentFactory::build('value1');
        $expected = TagFactory::build('key1', $content);
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1>value1</key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Tag', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testComposite()
    {
        $content1 = ContentFactory::build('value1');
        $content2 = ContentFactory::build('value2');
        $tag1 = TagFactory::build('key1', $content1);
        $tag2 = TagFactory::build('key2', $content2);
        $expected = new Composite();
        $expected->add($tag1);
        $expected->add($tag2);
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1>value1</key1><key2>value2</key2></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Composite', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testComplex()
    {
        $content1 = ContentFactory::build('value1');
        $content2 = ContentFactory::build('value2');
        $content3 = ContentFactory::build('value3');
        $content4 = ContentFactory::build('value4');
        $tag1 = TagFactory::build('key1', $content1);
        $tag2 = TagFactory::build('key2', $content2);
        $tag3 = TagFactory::build('key3', $content3);
        $tag4 = TagFactory::build('key4', $content4);
        $composite = new Composite();
        $composite->add($tag3);
        $composite->add($tag4);
        $tag5 = TagFactory::build('key5', $composite);
        $expected = new Composite();
        $expected->add($tag1);
        $expected->add($tag2);
        $expected->add($tag5);
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1>value1</key1><key2>value2</key2>'
            .'<key5><key3>value3</key3><key4>value4</key4></key5></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Composite', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testAttributeTag()
    {
        $content = ContentFactory::build('value1');
        $expected = TagFactory::build('key1', $content);
        $expected->setAttribute('foo', 'bar');
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1 foo="bar">value1</key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Tag', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testTagInsideTag()
    {
        $content = ContentFactory::build('value1');
        $tag = TagFactory::build('key2', $content);
        $tag->setAttribute('dance', 'ok');
        $expected = TagFactory::build('key1', $tag);
        $expected->setAttribute('foo', 'bar');
        $expected->setAttribute('foo2', 'bar2');
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1 foo="bar" foo2="bar2"><key2 dance="ok">value1</key2></key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Tag', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testTwoNodesSameName()
    {
        $content1 = ContentFactory::build('value1');
        $content2 = ContentFactory::build('value2');
        $tag1 = TagFactory::build('key1', $content1);
        $tag2 = TagFactory::build('key1', $content2);
        $expected = new Composite();
        $expected->add($tag1);
        $expected->add($tag2);
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1>value1</key1><key1>value2</key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Composite', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testTwoNodesSameNameAndTagsInside()
    {
        $content1 = ContentFactory::build('value1');
        $content2 = ContentFactory::build('value2');
        $tag2 = TagFactory::build('key2', $content1);
        $tag3 = TagFactory::build('key3', $content2);
        $composite = new Composite();
        $composite->add($tag2);
        $composite->add($tag3);
        $tag1 = TagFactory::build('key1', $composite);
        $expected = new Composite();
        $expected->add($tag1);
        $expected->add($tag1);
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1><key2>value1</key2><key3>value2</key3></key1>'
            .'<key1><key2>value1</key2><key3>value2</key3></key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Composite', 'xml');
        $this->assertEquals($expected, $result);
    }
}
