<?php

namespace AppBundle\Domain\User;

use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;
use AppBundle\Entity\Interfaces\UserRepositoryInterface;
use AppBundle\Entity\Interfaces\VoteSettingInterface;
use AppBundle\Entity\Project;
use \AppBundle\Entity\User as UserEntity;
use AppBundle\Entity\VoteSettings;

class User implements UserInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepositoryInterface;

    /**
     * @var VoteSettingInterface
     */
    private $voteSettingInterface;

    /**
     * User constructor.
     * @param UserRepositoryInterface $userRepositoryInterface
     * @param VoteSettingInterface $voteSettingInterface
     */
    public function __construct(
        UserRepositoryInterface $userRepositoryInterface,
        VoteSettingInterface $voteSettingInterface
    ) {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->voteSettingInterface = $voteSettingInterface;
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


        /** @var VoteSettings[] $voteSettings */
        $voteSettings = $this->getVoteSettingInterface()->getVoteSettingByUserCity($user);

        $balanceVotes = [];
        foreach ($voteSettings as $voteSetting) {
            $limitVoteSetting = $voteSetting->getVoteLimits();

            $balanceVotes[$voteSetting->getTitle()]=
                $limitVoteSetting
                - $this->getUserRepositoryInterface()->getUserVotesBySettingVote($voteSetting, $user);

        }

        $response = [];
        foreach ($balanceVotes as $key=>$balanceVote) {
            $messageLeft = $messageRight = '';
            if ($balanceVote >= 2) {
                $messageRight .= ' голоси';
            } elseif ($balanceVote == 1) {
                $messageRight .= ' голос';
            } elseif ($balanceVote == 0) {
                $messageRight .= ' голосів';
            }

            $response[] = $key. ' ' . $messageLeft . ' ' . $balanceVote . ' ' . $messageRight;
        }

        return
            "Дякуємо за Ваш голос. Ваш голос зараховано на підтримку проекту
            У вас залишилось: ".implode(', ', $response);
    }

    /**
     * @return UserRepositoryInterface
     */
    private function getUserRepositoryInterface()
    {
        return $this->userRepositoryInterface;
    }

    /**
     * @return VoteSettingInterface
     */
    private function getVoteSettingInterface()
    {
        return $this->voteSettingInterface;
    }
}
