<?php

namespace AppBundle\Model;

use AppBundle\Application\Project\ProjectInterface as ProjectApplicationInterface;
use AppBundle\DTO\ProjectDTO;
use AppBundle\DTO\VotingDTO;
use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\VoteSettingsRepository;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
use AppBundle\Exception\NotFoundException;
use AppBundle\Helper\UrlGeneratorHelper;
use AppBundle\Security\Authenticator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Serializer;

class VotingModel
{
    /**
     * @var VoteSettingsRepository
     */
    private $voteSettingsRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var UrlGeneratorHelper
     */
    private $urlGeneratorHelper;

    /**
     * @var ProjectApplicationInterface
     */
    private $projectApplication;

    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(
        Serializer $serializer,
        UrlGeneratorHelper $urlGeneratorHelper,
        ProjectApplicationInterface $projectApplication,
        Authenticator $authenticator,
        VoteSettingsRepository $voteSettingsRepository,
        UserRepository $userRepository
    ) {
        $this->serializer = $serializer;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->projectApplication = $projectApplication;
        $this->authenticator = $authenticator;
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
                ->setUserVoted($this->getCurrentUserVotedTimes($voteSetting))
            ;
        }

        return $votingList;
    }

    /**
     * @param VoteSettings $voteSettings
     *
     * @return ProjectDTO[]
     */
    public function getVotingProjectList(VoteSettings $voteSettings): array
    {
        $projects = $voteSettings->getProject();

        $projectList = [];
        /** @var Project $project */
        foreach ($projects as $project) {
            if (!$project->getApproved()) {
                continue;
            }
            $projectList[] = (new ProjectDTO($project))
                ->setIsVoted($this->isUserVotedForProject($this->authenticator->getCurrentUser(), $project))
                ->setPicture($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getPicture()))
                ->setOwnerAvatar($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getOwner()->getAvatar()))
            ;
        }

        return $projectList;
    }

    /**
     * @param VoteSettings $voteSettings
     * @param Project $project
     *
     * @throws HttpException
     *
     * @return ProjectDTO
     */
    public function getVotingProject(VoteSettings $voteSettings, Project $project): ProjectDTO
    {
        $this->validateVotingProject($voteSettings, $project);

        return (new ProjectDTO($project))
            ->setIsVoted($this->isUserVotedForProject($this->authenticator->getCurrentUser(), $project))
            ->setPicture($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getPicture()))
            ->setOwnerAvatar($this->urlGeneratorHelper->prepareAbsoluteUrl($project->getOwner()->getAvatar()))
        ;
    }

    /**
     * @param VoteSettings $voteSettings
     * @param Project $project
     *
     * @return string
     */
    public function likeVotingProjectByUser(VoteSettings $voteSettings, Project $project): string
    {
        $this->validateVotingProject($voteSettings, $project);

        return $this->projectApplication->crateUserLike($this->authenticator->getCurrentUser(), $project);
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

    /**
     * @param VoteSettings $voteSettings
     *
     * @return int
     */
   private function getCurrentUserVotedTimes(VoteSettings $voteSettings): int
   {
       if ((null === $user = $this->authenticator->getCurrentUser()) &&
           $voteSettings->getStatus() !== VoteSettings::STATUS_ACTIVE
       ){
           return 0;
       }

       return $this->userRepository->getUserVotesBySettingVote($voteSettings, $user);
   }
}