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
        $this->key = '';
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
        if (is_string($data)) {
            $content = new Content();
            $contents = $data;
        } else {
            $contents = array();
            foreach($data as $key => $value) {
                if ($key[0] == '@') {
                    $this->setAttribute(ltrim($key, '@'), $value);
                } else {
                    $contents[$key] = $value;
                }
            }
            if (count($contents) > 1) {
                $content = new Composite();
            } elseif (count($contents) == 1) {
                if (reset(array_keys($contents)) == '#') {
                    $content = new Content();
                    $contents = reset($contents);
                } else {
                    $content = new Tag();
                }
            }
        }
        $content->denormalize($denormalizer, $contents, $format, $context);
        $this->content = $content;
    }
}
