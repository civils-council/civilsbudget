<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class BadTokenResponse
{
    /**
     * @var string
     * @Assert\Type(type="string")
     */
    protected $error;

    /**
     * @var string
     * @Assert\Type(type="string")
     */
    protected $errorDescription;

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return BadTokenResponse
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * @param string $errorDescription
     * @return BadTokenResponse
     */
    public function setErrorDescription($errorDescription)
    {
        $this->errorDescription = $errorDescription;
        return $this;
    }
}
