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
            $value = $value->normalize($normalizer, $format, $context);
            $keys = array_keys($value);
            foreach ($keys as $key) {
                if (!isset($content[$key])) {
                    $content[$key] = $value[$key]['#'];
                } elseif (!is_array($content[$key])) {
                    $content[$key] = array($content[$key],$value[$key]['#']);
                } else {
                    $content[$key][] = $value[$key]['#'];
                }
            }
        }
        return $content;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        foreach ($data as $key => $values) {
            if (!is_array($values) || $this->isAssociativeArray($values)) {
                $values = array($values);
            }
            foreach($values as $value) {
                $tag = new Tag();
                $tag->denormalize($denormalizer, array($key => $value), $format, $context);
                $this->add($tag);
            }
        }
    }

    private function isAssociativeArray(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
