<?php

namespace mespinosaz\SerializableObjects\Tests;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use mespinosaz\SerializableObjects\Node\Composite;
use mespinosaz\SerializableObjects\Node\Factory\ContentFactory;
use mespinosaz\SerializableObjects\Node\Factory\TagFactory;

class JsonDecodeTest extends \PHPUnit_Framework_TestCase
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
        $expected = ContentFactory::build('foo');
        $json = '"foo"';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Content', 'json');
        $this->assertEquals($expected, $result);
    }

    public function testTag()
    {
        $content = ContentFactory::build('value1');
        $expected = TagFactory::build('key1', $content);
        $json = '{"key1":{"#":"value1"}}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Tag', 'json');
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
        $json = '{"key1":{"#":"value1"},"key2":{"#":"value2"}}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Composite', 'json');
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
        $json = '{"key1":{"#":"value1"},"key2":{"#":"value2"},"key5":{"#":{"key3":{"#":"value3"},"key4":{"#":"value4"}}}}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Composite', 'json');
        $this->assertEquals($expected, $result);
    }

    public function testAttributeTag()
    {
        $content = ContentFactory::build('value1');
        $expected = TagFactory::build('key1', $content);
        $expected->setAttribute('foo', 'bar');
        $json = '{"key1":{"#":"value1","@foo":"bar"}}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Tag', 'json');
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
        $json = '{"key1":{"#":{"key2":{"#":"value1","@dance":"ok"}},"@foo":"bar","@foo2":"bar2"}}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Tag', 'json');
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
        $json = '{"key1":["value1","value2"]}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Composite', 'json');
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
        $tag1 = TagFactory::build('key1',$composite);
        $expected = new Composite();
        $expected->add($tag1);
        $expected->add($tag1);
        $json = '{"key1":[{"key2":"value1","key3":"value2"},{"key2":"value1","key3":"value2"}]}';
        $result = $this->serializer->deserialize($json, 'mespinosaz\SerializableObjects\Node\Composite', 'json');
        $this->assertEquals($expected, $result);
    }
}
