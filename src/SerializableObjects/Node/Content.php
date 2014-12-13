<?php

namespace mespinosaz\SerializableObjects\Node;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Content extends Node
{
    protected $content;

    /*
     *  @param string $content
     */
    public function __construct()
    {
        $this->content = '';
    }

    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \Exception('Content should be an string!');
        }
        $this->content = $content;
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        return $this->content;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        $this->content = $data;
    }
}
