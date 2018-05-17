<?php

namespace HomeBundle\Service;

use HomeBundle\Entity\Firmware;
use HomeBundle\Entity\Module;

class FirmwareUpdater
{
    /**
     * @param Module   $module
     * @param Firmware $firmware
     *
     * @return bool
     */
    public function update(Module $module, Firmware $firmware)
    {
        // curl -v -F file=@build/fw.zip -F commit_timeout=60 http://192.168.31.142/update

        $data = [
            'file' => curl_file_create($firmware->getFile()->getFilename()),
            'commit_timeout' => 600
        ];

        $curl = curl_init(sprintf('http://%s/update', $module->getIp()));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return true;
    }
}