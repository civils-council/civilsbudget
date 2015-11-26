<?php

use Symfony\Component\Validator\Constraints as Assert;

class SuccessTokenResponse
{
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    protected $accessToken;

    /**
     * @var string
     * @Assert\Type(type="string")
     */
    protected $refreshToken;

    /**
     * @var string
     * @Assert\Type(type="string")
     */
    protected $tokenType;

    /**
     * @var int
     * @Assert\Type(type="string")
     */
    protected $expiresIn;

    /**
     * @var array
     * @Assert\Type(type="array")
     */
    protected $scope;

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
}
