<?php

namespace AppBundle\DTO;

use AppBundle\Entity\Project;
use Symfony\Component\Serializer\Annotation\Groups;

class ProjectDTO
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var bool
     */
    private $voted;



    public function __construct(Project $project, bool $voted = false)
    {
        $this->project = $project;
        $this->voted = $voted;
    }

    /**
     * @Groups({"project_list"})
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->project->getId();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->project->getTitle();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->project->getDescription();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return int
     */
    public function getCharge(): int
    {
        return (int)$this->project->getCharge();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->project->getSource();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->project->getPicture();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->project->getCreateAt();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return int
     */
    public function getLikesCount(): int
    {
        return $this->project->getLikedUsers()->count();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return string
     */
    public function getOwner(): string
    {
        return trim($this->project->getOwner()->getFullName());
    }

    /**
     * @Groups({"project_list"})
     *
     * @return string|null
     */
    public function getOwnerAvatar(): ?string
    {
        return $this->project->getOwner()->getAvatar();
    }

    /**
     * @Groups({"project_list"})
     *
     * @return bool
     */
    public function getVoted(): bool
    {
        return $this->voted;
    }

    public function getVoteSetting()
    {
        return $this->voteSetting;
    }
}