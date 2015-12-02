<?php

namespace AppBundle\Security\Response;

use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;

class BankIdUserResponse extends AbstractUserResponse
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
        // TODO: Implement getUsername() method.
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
