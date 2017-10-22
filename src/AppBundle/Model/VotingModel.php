<?php

namespace AppBundle\Model;

use AppBundle\DTO\ProjectDTO;
use AppBundle\DTO\VotingDTO;
use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\VoteSettingsRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRepository;
use AppBundle\Entity\VoteSettings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class VotingModel
{
    /**
     * @var VoteSettingsRepository
     */
    protected $voteSettingsRepository;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(
        Serializer $serializer,
        VoteSettingsRepository $voteSettingsRepository,
        UserRepository $userRepository
    ) {
        $this->serializer = $serializer;
        $this->voteSettingsRepository = $voteSettingsRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return VotingDTO[]
     */
    public function getVotingList(): array
    {
        $voteSettings = $this->voteSettingsRepository->findAll();
        $listVotedUserCount =  $this->voteSettingsRepository->getVotedUsersCountPerVoting();

        $votingList = [];
        /** @var VoteSettings $voteSetting */
        foreach ($voteSettings as $voteSetting) {
            $key = array_search($voteSetting->getId(), array_column($listVotedUserCount, 'id'));
            $votingList[] = new VotingDTO($voteSetting, $listVotedUserCount[$key]['voted']);
        }

        return $votingList;
    }

    /**
     * @param VoteSettings $voteSettings
     * @param Request $request
     *
     * @return VotingDTO[]
     */
    public function getVotingProjects(VoteSettings $voteSettings, Request $request): array
    {
        $projects = $voteSettings->getProject();
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['clid' => $request->get('clid')]);

        $projectList = [];
        foreach ($projects as $project) {
            $projectList[] = new ProjectDTO($project, $this->isUserVotedForProject($user, $project));
        }

        return $projectList;
    }

    /**
     * @param User|null $user
     * @param Project $project
     *
     * @return bool
     */
    private function isUserVotedForProject(?User $user, Project $project): bool
    {
        return $user ? $project->getLikedUsers()->contains($user) : false;
   }
}