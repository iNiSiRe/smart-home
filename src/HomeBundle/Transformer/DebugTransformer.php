<?php

namespace HomeBundle\Transformer;

use PrivateDev\Utils\Fractal\TransformerAbstract;

class DebugTransformer extends TransformerAbstract
{

    /**
     * @param object $object
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function transform($object): array
    {
        $reflection = new \ReflectionClass($object);

        $result = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $result[$property->getName()] = $property->getValue($object);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getResourceKey(): string
    {
        return '';
    }
}