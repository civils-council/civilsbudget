<?php

namespace AppBundle\Domain\User;

use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;
use AppBundle\Entity\Interfaces\UserRepositoryInterface;
use AppBundle\Entity\Project;
use \AppBundle\Entity\User as UserEntity;

class User implements UserInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepositoryInterface;

    /**
     * User constructor.
     * @param UserRepositoryInterface $userRepositoryInterface
     */
    public function __construct(
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessVote(
        Project $project,
        UserEntity $user
    ) {
        return $this->getUserRepositoryInterface()->getUserVotesByProjectSettingVote(
            $project,
            $user
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postVote(
        Project $project,
        UserEntity $user
    ) {
        $user->setCountVotes(($user->getCountVotes()) ? ($user->getCountVotes() + 1) : 1);
        $user->addLikedProjects($project);
        $project->addLikedUser($user);
        
        $this->getUserRepositoryInterface()->flushEntity();
        
        $balanceVotes = 
            $project->getVoteSetting()->getVoteLimits() 
            - $this->getUserRepositoryInterface()->getUserVotesByProjectSettingVote($project, $user);
        $votes = 'голоси';
        if ($balanceVotes == 1) {
            $votes = 'голос';
        } elseif ($balanceVotes == 0) {
            $votes = 'голосів';
        }
        return
            "Дякуємо за Ваш голос. Ваш голос зараховано на підтримку проекту.
            У вас залишилось $balanceVotes $votes";
    }

    /**
     * @return UserRepositoryInterface
     */
    private function getUserRepositoryInterface()
    {
        return $this->userRepositoryInterface;
    }
}
