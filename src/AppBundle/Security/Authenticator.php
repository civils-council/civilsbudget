<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class Authenticator
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * Authenticator constructor.
     * @param TokenStorage $tokenStorage
     * @param EntityManager $entityManager
     * @param Session $session
     */
    public function __construct(
        TokenStorage $tokenStorage,
        EntityManager $entityManager,
        Session $session
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }
    
    /**
     * @param User $user
     */
    public function addAuth(User $user)
    {
        $user->setLastLoginAt(new \DateTime('now'));
        $this->entityManager->flush();
        
        $token = new PreAuthenticatedToken($user, $user->getClid(), 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->session->set('_security_main', serialize($token));
    }

    /**
     * @return User|null
     */
    public function getCurrentUser(): ?User
    {
        if (!$this->tokenStorage->getToken()) {
            return null;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
