<?php

namespace AppBundle\Security\Response;

use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class BankIdUserResponse extends AbstractUserResponse implements UserResponseInterface
{
    /**
     * Get the unique user identifier.
     *
     * Note that this is not always common known "username" because of implementation
     * in Symfony2 framework. For more details follow link below.
     * @link https://github.com/symfony/symfony/blob/2.1/src/Symfony/Component/Security/Core/User/UserProviderInterface.php#L20-L28
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getClid();
    }

    public function getClid()
    {
        return isset($this->getResponse()['customer']['clId']) ? $this->getResponse()['customer']['clId'] : null;
    }

    public function getInn()
    {
        return isset($this->getResponse()['customer']['inn']) ? $this->getResponse()['customer']['inn'] : null;
    }

    public function getFirstName()
    {
        return isset($this->getResponse()['customer']['firstName']) ? $this->getResponse()['customer']['firstName'] : null;
    }

    public function getLastName()
    {
        return isset($this->getResponse()['customer']['lastName']) ? $this->getResponse()['customer']['lastName'] : null;
    }

    public function getMiddleName()
    {
        return isset($this->getResponse()['customer']['middleName']) ? $this->getResponse()['customer']['middleName'] : null;
    }

    public function getSex()
    {
        return isset($this->getResponse()['customer']['sex']) ? $this->getResponse()['customer']['sex'] : null;
    }

    public function getBirthday()
    {
        return isset($this->getResponse()['customer']['birthDay']) ? $this->getResponse()['customer']['birthDay'] : null;
    }

    /**
     * Get the username to display.
     *
     * @return string
     */
    public function getNickname()
    {
        // TODO: Implement getNickname() method.
    }

    /**
     * Get the real name of user.
     *
     * @return null|string
     */
    public function getRealName()
    {
        // TODO: Implement getRealName() method.
    }
}
