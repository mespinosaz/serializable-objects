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
        foreach ($data as $key => $value) {
            $tag = new Tag();
            $tag->denormalize($denormalizer, array($key => $value), $format, $context);
            $this->add($tag);
        }
    }
}
