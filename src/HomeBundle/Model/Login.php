<?php

namespace HomeBundle\Model;

class Login
{
    /**
     * @var string
     */
    private $MAC;

    /**
     * @return string
     */
    public function getMAC()
    {
        return $this->MAC;
    }

    /**
     * @param string $MAC
     *
     * @return Login
     */
    public function setMAC($MAC)
    {
        $this->MAC = $MAC;

        return $this;
    }
}