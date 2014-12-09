<?php

namespace mespinosaz\SerializableObjects\Node;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NullNode extends Node
{
    public function __construct()
    {
    }

    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = null)
    {
        return '';
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = null)
    {
    }
}
