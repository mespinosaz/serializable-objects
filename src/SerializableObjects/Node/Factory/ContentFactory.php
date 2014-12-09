<?php

namespace mespinosaz\SerializableObjects\Node\Factory;

use mespinosaz\SerializableObjects\Node\Content;

class ContentFactory {
    public static function build($content)
    {
        $node = new Content();
        $node->setContent($content);
        return $node;
    }
}