<?php

namespace mespinosaz\SerializableObjects\Node;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Tag extends Node
{
    private $name;
    private $content;
    private $attributes;

    public function __construct()
    {
        $this->content = new NullNode();
        $this->attributes = array();
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setContent(Node $content)
    {
        $this->content = $content;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes['@'.$key] = $value;
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        return array(
            $this->name => array_merge(
                array(
                    '#' => $this->content->normalize($normalizer, $format, $context)
                ),
                $this->attributes
            )
        );
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        $this->name = reset(array_keys($data));
        $data = reset($data);
        $contents = $data;
        if (is_array($data)) {
            $contents = array();
            foreach ($data as $key => $value) {
                if ($key[0] == '@') {
                    $this->setAttribute(ltrim($key, '@'), $value);
                } else {
                    $contents[$key] = $value;
                }
            }
            if (reset(array_keys($contents)) == '#') {
                $contents = reset($contents);
            }
        }
        if (is_string($contents)) {
            $node = new Content();
        } else {
            if (count($contents) > 1) {
                $node = new Composite();
            } else {
                $node = new Tag();
            }
        }
        $node->denormalize($denormalizer, $contents, $format, $context);
        $this->content = $node;
    }
}
