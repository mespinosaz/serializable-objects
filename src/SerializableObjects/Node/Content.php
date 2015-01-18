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

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \Exception('Content should be an string!');
        }
        $this->content = $content;
    }

    /**
     * @param NormalizerInterface $normalizer
     * @param string $format
     * @param array $context
     * @return string
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        return $this->content;
    }

    /**
     * @param NormalizerInterface $normalizer
     * @param string $format
     * @param string $context
     * @return array
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        $this->content = $data;
    }
}
