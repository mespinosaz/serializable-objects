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
        if (is_object($content) || is_array($content)) {
            throw new \InvalidArgumentException('Content should be an string!');
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
     * @param DenormalizerInterface $denormalizer
     * @param string $data
     * @param string $format
     * @param array $context
     * @return array
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
        $this->content = $data;
    }
}
