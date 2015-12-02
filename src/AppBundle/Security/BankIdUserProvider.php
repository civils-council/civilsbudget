<?php

namespace AppBundle\Security;

use AppBundle\Security\Response\BankIdUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class BankIdUserProvider extends EntityUserProvider
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }
var_dump($response->getResponse());exit;
        $username = $response->getUsername();
        if (null === $user = $this->repository->findOneBy(array($this->properties[$resourceOwnerName] => $username))) {
            $user = $this->createUser($response);
        }

        return $user;
    }

    protected function createUser(BankIdUserResponse $response)
    {
        var_dump(
            $response->getClid(),
            $response->getUsername(),
            $response->getInn(),
            $response->getFirstName(),
            $response->getLastName(),
            $response->getMiddleName(),
            $response->getSex(),
            $response->getBirthday()

        );
        ;
        throw new \Exception('Create new user');
    }
}
