<?php

namespace mespinosaz\SerializableObjects\Node\Factory;

use mespinosaz\SerializableObjects\Node\Node;
use mespinosaz\SerializableObjects\Node\Tag;

class TagFactory
{
    /**
     * @param string $name
     * @param Node $content
     * @return Tag
     */
    public static function build($name, Node $content)
    {
        $node = new Tag();
        $node->setName($name);
        $node->setContent($content);
        return $node;
    }
}
