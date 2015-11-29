<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

class BankIdTokenResponse
{
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     * @Serializer\Type("string")
     */
    protected $accessToken;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Serializer\Type("string")
     */
    protected $refreshToken;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Serializer\Type("string")
     */
    protected $tokenType;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @Serializer\Type("integer")
     */
    protected $expiresIn;

    /**
     * @var array
     * @Assert\Type(type="array")
     * @Serializer\Type("string")
     */
    protected $scope;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Serializer\Type("string")
     */
    protected $error;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Serializer\Type("string")
     */
    protected $errorDescription;

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return SuccessTokenResponse
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return SuccessTokenResponse
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param string $tokenType
     * @return SuccessTokenResponse
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param int $expiresIn
     * @return SuccessTokenResponse
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return SuccessTokenResponse
     */
    public function setScope($scope)
    {
        $this->scope = explode("", $scope);
        return $this;
    }

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
