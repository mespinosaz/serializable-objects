<?php

namespace mespinosaz\SerializableObjects\Tests;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use mespinosaz\SerializableObjects\Node\Content;
use mespinosaz\SerializableObjects\Node\Tag;
use mespinosaz\SerializableObjects\Node\Composite;

class XMLDecodeTest extends \PHPUnit_Framework_TestCase
{
    private $encoder;

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
        $expected = new Content();
        $expected->setContent('foo');
        $xml = '<?xml version="1.0"?>'."\n".
            '<response>foo</response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Content', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testTag()
    {
        $content = new Content();
        $content->setContent('value1');
        $expected = new Tag();
        $expected->setName('key1');
        $expected->setContent($content);
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1>value1</key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Tag', 'xml');
        $this->assertEquals($expected, $result);
    }

    public function testComposite()
    {
        $content1 = new Content();
        $content1->setContent('value1');
        $content2 = new Content();
        $content2->setContent('value2');
        $tag1 = new Tag();
        $tag1->setName('key1');
        $tag1->setContent($content1);
        $tag2 = new Tag();
        $tag2->setName('key2');
        $tag2->setContent($content2);
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
        $content1 = new Content();
        $content1->setContent('value1');
        $content2 = new Content();
        $content2->setContent('value2');
        $content3 = new Content();
        $content3->setContent('value3');
        $content4 = new Content();
        $content4->setContent('value4');
        $tag1 = new Tag();
        $tag1->setName('key1');
        $tag1->setContent($content1);
        $tag2 = new Tag();
        $tag2->setName('key2');
        $tag2->setContent($content2);
        $tag3 = new Tag();
        $tag3->setName('key3');
        $tag3->setContent($content3);
        $tag4 = new Tag();
        $tag4->setName('key4');
        $tag4->setContent($content4);
        $composite = new Composite();
        $composite->add($tag3);
        $composite->add($tag4);
        $tag5 = new Tag();
        $tag5->setName('key5');
        $tag5->setContent($composite);
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
        $content = new Content();
        $content->setContent('value1');
        $expected = new Tag();
        $expected->setContent($content);
        $expected->setName('key1');
        $expected->setAttribute('foo','bar');
        $xml = '<?xml version="1.0"?>'."\n".
            '<response><key1 foo="bar">value1</key1></response>'."\n";
        $result = $this->serializer->deserialize($xml, 'mespinosaz\SerializableObjects\Node\Tag', 'xml');
        $this->assertEquals($expected, $result);
    }
}
