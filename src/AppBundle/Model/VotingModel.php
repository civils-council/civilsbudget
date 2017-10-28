<?php

namespace AppBundle\Model;

use AppBundle\DTO\ProjectDTO;
use AppBundle\DTO\VotingDTO;
use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\VoteSettingsRepository;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
use AppBundle\Helper\UrlGeneratorHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    /**
     * @var UrlGeneratorHelper
     */
    protected $urlGeneratorHelper;

    public function __construct(
        Serializer $serializer,
        UrlGeneratorHelper $urlGeneratorHelper,
        VoteSettingsRepository $voteSettingsRepository,
        UserRepository $userRepository
    ) {
        $this->serializer = $serializer;
        $this->voteSettingsRepository = $voteSettingsRepository;
        $this->userRepository = $userRepository;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
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
            $votingList[] = (new VotingDTO($voteSetting))
                ->setVoted($listVotedUserCount[$key]['voted'])
                ->setBackgroundImage($this->urlGeneratorHelper->prepareAbsoluteUrl($voteSetting->getBackgroundImg()))
                ->setLogo($this->urlGeneratorHelper->prepareAbsoluteUrl($voteSetting->getLogo()))
            ;
        }

        return $votingList;
    }

    /**
     * @param VoteSettings $voteSettings
     * @param Request $request
     *
     * @return ProjectDTO[]
     */
    public function getVotingProjectList(VoteSettings $voteSettings, Request $request): array
    {
        $projects = $voteSettings->getProject();
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['clid' => $request->get('clid')]);

        $projectList = [];
        /** @var Project $project */
        foreach ($projects as $project) {
            if (!$project->getApproved()) {
                continue;
            }
            $projectList[] = (new ProjectDTO($project))
                ->setIsVoted($this->isUserVotedForProject($user, $project))
                ->setPicture($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getPicture()))
                ->setOwnerAvatar($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getOwner()->getAvatar()))
            ;
        }

        return $projectList;
    }

    /**
     * @param VoteSettings $voteSettings
     * @param Project $project
     * @param Request $request
     *
     * @throws HttpException
     *
     * @return ProjectDTO
     */
    public function getVotingProject(VoteSettings $voteSettings, Project $project, Request $request): ProjectDTO
    {
        if ($voteSettings->getId() !== $project->getVoteSetting()->getId()) {
            throw new HttpException(404, 'Project not fount for the voting');
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['clid' => $request->get('clid')]);

        return (new ProjectDTO($project))
            ->setIsVoted($this->isUserVotedForProject($user, $project))
            ->setPicture($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getPicture()))
            ->setOwnerAvatar($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getOwner()->getAvatar()))
        ;
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