<?php

namespace HomeBundle\Transformer;

use PrivateDev\Utils\Fractal\TransformerAbstract;

class DummyTransformer extends TransformerAbstract
{

    /**
     * @param object $object
     *
     * @return array
     */
    public function transform($object): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getResourceKey(): string
    {
        return '';
    }
}