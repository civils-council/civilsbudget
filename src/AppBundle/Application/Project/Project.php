<?php

namespace AppBundle\Application\Project;

use AppBundle\Domain\Project\ProjectInterface as DomainProjectInterface;
use AppBundle\Domain\User\UserInterface;
use AppBundle\Entity\Admin;
use AppBundle\Entity\User;
use AppBundle\Exception\AuthException;
use AppBundle\Exception\ValidatorException;
use \AppBundle\Entity\Project as ProjectEntity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Project implements ProjectInterface
{
    /**
     * @var DomainProjectInterface
     */
    private $projectInterface;

    /**
     * @var UserInterface
     */
    private $userInterface;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;


    /**
     * Project constructor.
     * @param DomainProjectInterface $projectInterface
     * @param UserInterface $userInterface
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        DomainProjectInterface $projectInterface,
        UserInterface $userInterface,
        TokenStorage $tokenStorage
    )
    {
        $this->projectInterface = $projectInterface;
        $this->userInterface = $userInterface;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function crateUserLike(
        $user,
        ProjectEntity $project
    ) {
        if (!$user instanceof User) {
            throw new AuthException('Ви не маєте доступу до голосуваня за проект.', 401);
        }
        
        if (!$project->getVoteSetting()) {
            throw new ValidatorException('Проект без налаштувань голосування', 403);
        }   
        
        $date = new \DateTime();
        
        if (($authUser = $this->tokenStorage->getToken()->getUser()) && !$authUser->hasRole(Admin::ROLE_ADMIN)) {
            if ($project->getVoteSetting()->getDateTo()->getTimestamp() < $date->getTimestamp()) {
                throw new ValidatorException(
                    'Вибачте. Кінцева дата голосування до '
                    .$project->getVoteSetting()->getDateTo()->format('d.m.Y'),
                    403
                );
            }

            if ($project->getVoteSetting()->getDateFrom()->getTimestamp() > $date->getTimestamp()) {
                throw new ValidatorException(
                    'Вибачте. Голосування розпочнеться '
                    .$project->getVoteSetting()->getDateFrom()->format('d.m.Y'),
                    403
                );
            }
        }
        
        if ($this->getUserInterface()->getAccessVote($project, $user)
            >= $project->getVoteSetting()->getVoteLimits()
        ) {
            throw new ValidatorException('Ви вже вичерпали свій ліміт голосів.', 403);
        }
        if ($user->getLikedProjects()->contains($project)) {
            throw new ValidatorException('Ви вже підтримали цей проект.', 403);
        }

        if (mb_strtolower($user->getLocation()->getCity()) 
            != mb_strtolower($project->getVoteSetting()->getLocation()->getCity())
        ) {
            throw new ValidatorException(
                'Цей проект не стосується міста в якому ви зареєстровані. Ваша реєстрація: м.'.
                $user->getLocation()->getCity(),
                403
            );
        }
        
        return $this->getUserInterface()->postVote($project, $user);
    }
    
    /**
     * @return DomainProjectInterface
     */
    private function getProjectInterface()
    {
        return $this->projectInterface;
    }

    /**
     * @return UserInterface
     */
    private function getUserInterface()
    {
        return $this->userInterface;
    }
}
