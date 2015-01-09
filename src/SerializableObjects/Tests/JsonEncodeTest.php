<?php

namespace mespinosaz\SerializableObjects\Tests;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use mespinosaz\SerializableObjects\Node\Composite;
use mespinosaz\SerializableObjects\Node\Factory\ContentFactory;
use mespinosaz\SerializableObjects\Node\Factory\TagFactory;


class JsonEncodeTest extends \PHPUnit_Framework_TestCase
{
    private $encoder;

    protected function setUp()
    {
        $this->serializer = new Serializer(
            array(
                new CustomNormalizer()
            ),
            array(
                'json' => new JsonEncoder()
            )
        );
    }

    public function testContent()
    {
        $node = ContentFactory::build('foo');
        $expected = '"foo"';
        $this->assertEquals($expected, $this->serializer->serialize($node, 'json'));
    }

    public function testTag()
    {
        $content = ContentFactory::build('value1');
        $node = TagFactory::build('key1', $content);
        $expected = '{"key1":{"#":"value1"}}';
        $this->assertEquals($expected, $this->serializer->serialize($node, 'json'));
    }

    public function testComposite()
    {
        $content1 = ContentFactory::build('value1');
        $content2 = ContentFactory::build('value2');
        $tag1 = TagFactory::build('key1', $content1);
        $tag2 = TagFactory::build('key2', $content2);
        $node = new Composite();
        $node->add($tag1);
        $node->add($tag2);
        $expected = '{"key1":"value1","key2":"value2"}';

        $this->assertEquals($expected, $this->serializer->serialize($node, 'json'));
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
        $node = new Composite();
        $node->add($tag1);
        $node->add($tag2);
        $node->add($tag5);
        $expected = '{"key1":"value1","key2":"value2","key5":{"key3":"value3","key4":"value4"}}';
        $this->assertEquals($expected, $this->serializer->serialize($node, 'json'));
    }

    public function testAttributeTag()
    {
        $content = ContentFactory::build('value1');
        $node = TagFactory::build('key1', $content);
        $node->setAttribute('foo', 'bar');
        $expected = '{"key1":{"#":"value1","@foo":"bar"}}';
        $this->assertEquals($expected, $this->serializer->serialize($node, 'json'));
    }

    public function testTagInsideTag()
    {
        $content = ContentFactory::build('value1');
        $tag = TagFactory::build('key2', $content);
        $tag->setAttribute('dance', 'ok');
        $node = TagFactory::build('key1', $tag);
        $node->setAttribute('foo', 'bar');
        $node->setAttribute('foo2', 'bar2');
        $expected = '{"key1":{"#":{"key2":{"#":"value1","@dance":"ok"}},"@foo":"bar","@foo2":"bar2"}}';
        $result = $this->serializer->serialize($node, 'json');
        $this->assertEquals($expected, $result);
    }

    public function testTwoNodesSameName()
    {
        $content1 = ContentFactory::build('value1');
        $content2 = ContentFactory::build('value2');
        $tag1 = TagFactory::build('key1', $content1);
        $tag2 = TagFactory::build('key1', $content2);
        $node = new Composite();
        $node->add($tag1);
        $node->add($tag2);
        $expected = '{"key1":["value1","value2"]}';
        $result = $this->serializer->serialize($node,  'json');
        $this->assertEquals($expected, $result);
    }
}
