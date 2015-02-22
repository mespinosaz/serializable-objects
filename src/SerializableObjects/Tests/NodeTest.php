<?php

namespace mespinosaz\SerializableObjects\Tests;

use mespinosaz\SerializableObjects\Node\Content;

class NodeTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \InvalidArgumentException
     */

    public function testArrayContent()
    {
        $content = new Content();
        $content->setContent(array(1,2,3));
    }

    /**
     * @expectedException \InvalidArgumentException
     */

    public function testObjectContent()
    {
        $content = new Content();
        $content->setContent(new \stdClass());
    }
} 