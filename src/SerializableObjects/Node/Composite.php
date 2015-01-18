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

    /**
     * @param Tag $node
     */
    public function add(Tag $node)
    {
        $this->nodes[] = $node;
    }

    /**
     * @param NormalizerInterface $normalizer
     * @param string $format
     * @param array $context
     * @return array
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        $content = array();
        foreach ($this->nodes as $value) {
            $value = $value->normalize($normalizer, $format, $context);
            $keys = array_keys($value);
            foreach ($keys as $key) {
                if (!isset($content[$key])) {
                    $content[$key] = array();
                }
                $content[$key][] = $value[$key]['#'];
            }
        }
        foreach ($content as $key => $value) {
            if (count($value) == 1) {
                $content[$key] = reset($value);
            }
        }
        return $content;
    }

    /**
     * @param DenormalizerInterface $denormalizer
     * @param array $data
     * @param string $format
     * @param array $context
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        foreach ($data as $key => $values) {
            if (!is_array($values) || $this->isAssociativeArray($values)) {
                $values = array($values);
            }
            foreach ($values as $value) {
                $tag = new Tag();
                $tag->denormalize($denormalizer, array($key => $value), $format, $context);
                $this->add($tag);
            }
        }
    }

    /**
     * @param array $array
     * @return boolean
     */
    private function isAssociativeArray(array $array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
