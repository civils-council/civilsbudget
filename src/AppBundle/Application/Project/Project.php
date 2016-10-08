<?php

namespace AppBundle\Application\Project;


use AppBundle\Domain\User\UserInterface;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Request;
use \AppBundle\Entity\Project as ProjectEntity; 

class Project implements ProjectInterface
{
    /**
     * @var ProjectInterface
     */
    private $projectInterface;

    /**
     * @var UserInterface
     */
    private $userInterface;


    /**
     * Project constructor.
     * @param ProjectInterface $projectInterface
     * @param UserInterface $userInterface
     */
    public function __construct(
        ProjectInterface $projectInterface,
        UserInterface $userInterface
    )
    {
        $this->projectInterface = $projectInterface;
        $this->userInterface = $userInterface;
    }

    public function crateUserLike(
        $user,
        ProjectEntity $project
    ) {
        if (!$user instanceof User) {
            throw new ValidatorException('Ви не маєте доступу до голосуваня за проект.');
        }
        
        if (!$project->getVoteSetting()) {
            throw new ValidatorException('Проект без налаштувань голосування');
        }   
        
        $date = new \DateTime();
        
        if (!$project->getVoteSetting()->getDateTo()->getTimestamp() < $date->getTimestamp()) {
            throw new ValidatorException(
                'Вибачте. Кінцева дата голосування до '
                .$project->getVoteSetting()->getDateTo()->format('d.m.Y'));
        }
        
        if ($this->getUserInterface()->getAccessVote($project, $user)
            > $project->getVoteSetting()
        ) {
            throw new ValidatorException('Ви вже вичерпали свій ліміт голосів.');
        }
        if ($user->getLikedProjects()->contains($project)) {
            throw new ValidatorException('Ви вже підтримали цей проект.');
        }

        if (mb_strtolower($user->getLocation()->getCity()) 
            != mb_strtolower($project->getVoteSetting()->getLocation()->getCity())
        ) {
            throw new ValidatorException('Цей проект не стосується міста в якому ви зареєстровані.');
        }
        
        return $this->getUserInterface()->postVote($project, $user);
    }
    
    /**
     * @return ProjectInterface
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
