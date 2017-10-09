<?php

namespace HomeBundle\Transformer;

use HomeBundle\Entity\Unit;
use PrivateDev\Utils\Fractal\TransformerAbstract;

class UnitTransformer extends TransformerAbstract
{
    /**
     * @param Unit $object
     *
     * @return array
     */
    public function transform($object): array
    {
        return [
            'id' => $object->getId(),
            'value' => $object->getValue()
        ];
    }

    /**
     * @return string
     */
    public function getResourceKey(): string
    {
        return 'unit';
    }
}