<?php

namespace HomeBundle\Transformer;

use HomeBundle\Entity\Firmware;
use PrivateDev\Utils\Fractal\TransformerAbstract;

class FirmwareTransformer extends TransformerAbstract
{
    /**
     * @param Firmware $object
     *
     * @return array
     */
    public function transform($object): array
    {
        return [
            'file' => $object->getFile() === null ? null : $object->getFile()->getBasename(),
            'version' => $object->getVersion()
        ];
    }

    /**
     * @return string
     */
    public function getResourceKey(): string
    {
        return 'firmware';
    }
}