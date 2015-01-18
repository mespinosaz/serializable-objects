<?php

namespace mespinosaz\SerializableObjects\Node\Factory;

use mespinosaz\SerializableObjects\Node\Content;

class ContentFactory
{
    /**
     * @param string $content
     * @return Content
     */
    public static function build($content)
    {
        $node = new Content();
        $node->setContent($content);
        return $node;
    }
}
