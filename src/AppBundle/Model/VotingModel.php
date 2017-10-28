<?php

namespace AppBundle\Model;

use AppBundle\Application\Project\ProjectInterface as ProjectApplicationInterface;
use AppBundle\DTO\ProjectDTO;
use AppBundle\DTO\VotingDTO;
use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\VoteSettingsRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRepository;
use AppBundle\Entity\VoteSettings;
use AppBundle\Exception\NotFoundException;
use AppBundle\Helper\UrlGeneratorHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;

class VotingModel
{
    /**
     * @var VoteSettingsRepository
     */
    private $voteSettingsRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UrlGeneratorHelper
     */
    private $urlGeneratorHelper;

    /**
     * @var ProjectApplicationInterface
     */
    private $projectApplication;

    public function __construct(
        Serializer $serializer,
        UrlGeneratorHelper $urlGeneratorHelper,
        ProjectApplicationInterface $projectApplication,
        VoteSettingsRepository $voteSettingsRepository,
        UserRepository $userRepository
    ) {
        $this->serializer = $serializer;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->projectApplication = $projectApplication;
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
     * @param User|null $user
     *
     * @throws HttpException
     *
     * @return ProjectDTO
     */
    public function getVotingProject(VoteSettings $voteSettings, Project $project, ?User $user): ProjectDTO
    {
        $this->validateVotingProject($voteSettings, $project);

        return (new ProjectDTO($project))
            ->setIsVoted($this->isUserVotedForProject($user, $project))
            ->setPicture($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getPicture()))
            ->setOwnerAvatar($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getOwner()->getAvatar()))
        ;
    }

    /**
     * @param VoteSettings $voteSettings
     * @param Project $project
     * @param User|null $user
     *
     * @return string
     */
    public function likeVotingProjectByUser(VoteSettings $voteSettings, Project $project, ?User $user): string
    {
        $this->validateVotingProject($voteSettings, $project);

        return $this->projectApplication->crateUserLike($user, $project);
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

    /**
     * @param VoteSettings $voteSettings
     * @param Project $project
     *
     * @throws HttpException
     */
   private function validateVotingProject(VoteSettings $voteSettings, Project $project): void
   {
       if ($voteSettings->getId() !== $project->getVoteSetting()->getId()) {
           throw new NotFoundException('Проект не знайдено для даного голосування', 404);
       }

   }
}