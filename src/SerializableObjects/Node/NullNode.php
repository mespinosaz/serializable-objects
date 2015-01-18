<?php

namespace mespinosaz\SerializableObjects\Node;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NullNode extends Node
{
    public function __construct()
    {
    }

    /**
     * @param NormalizerInterface $normalizer
     * @param string $format
     * @param array $context
     * @return string
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        return '';
    }

    /**
     * @param DenormalizerInterface $denormalizer
     * @param mixed $data
     * @param string $format
     * @param array $context
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
    }
}
