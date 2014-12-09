<?php

namespace mespinosaz\SerializableObjects\Node;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Composite extends Node
{
    private $nodes;

    public function __construct()
    {
        $this->nodes = array();
    }

    public function add(Tag $node)
    {
        $this->nodes[] = $node;
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        $content = array();
        foreach ($this->nodes as $value) {
            $content = array_merge($content, $value->normalize($normalizer, $format, $context));
        }

        return $content;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        foreach($data as $key => $value)
        {
            $tag = new Tag();
            $tag->setName($key);
            if (is_string($value)) {
                $content = new Content();
            } elseif (is_array($value) && count($value) == 1) {
                $content = new Tag();
            } else {
                $content = new Composite();
            }
            $content->denormalize($denormalizer, $value, $format, $context);
            $tag->setContent($content);
            $this->add($tag);
        }
    }
}
