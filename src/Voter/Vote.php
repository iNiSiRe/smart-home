<?php

namespace Voter;

class Vote
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var null
     */
    private $reason;

    /**
     * @param      $value
     * @param null $reason
     */
    public function __construct($value, $reason = null)
    {
        $this->value = $value;
        $this->reason = $reason;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param null $reason
     *
     * @return $this
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }
}