<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Security\Response\BankIdUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;

class BankIdUserProvider extends EntityUserProvider
{
    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @param BankIdUserResponse $response
     * @return User
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $username = $response->getUsername();

        if (null === $user = $this->repository->findOneBy(array($this->properties[$resourceOwnerName] => $username))) {
            $user = $this->getUserFromResponse($response);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    /**
     * @param BankIdUserResponse $response
     * @return User
     * @throws \Exception`
     */
    protected function getUserFromResponse(BankIdUserResponse $response)
    {
        $user = new User();

        $user
            ->setClid($this->encryptor->decrypt($response->getClid()))
            ->setFirstName($this->encryptor->decrypt($response->getFirstName()))
            ->setLastName($this->encryptor->decrypt($response->getLastName()))
            ->setMiddleName($this->encryptor->decrypt($response->getMiddleName()))
            ->setInn($this->encryptor->decrypt($response->getInn()))
            ->setSex($this->encryptor->decrypt($response->getSex()))
            ->setBirthday($this->encryptor->decrypt($response->getBirthday()))
        ;

        return $user;
    }

    public function setEncryptor(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }
}
