<?php

namespace AppBundle\DTO;

use AppBundle\Entity\Project;
use Symfony\Component\Serializer\Annotation\Groups;

class ProjectDTO
{
    /**
     * @var string
     */
    private $picture;

    /**
     * @var string
     */
    private $ownerAvatar;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var bool
     */
    private $isVoted;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->ownerAvatar = $this->project->getOwner()->getAvatar();
        $this->picture = $this->project->getPicture();
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
        return $this->picture;
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
    public function getVoted(): int
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
        return $this->ownerAvatar;
    }

    /**
     * @Groups({"project_list"})
     *
     * @return bool
     */
    public function getIsVoted(): bool
    {
        return (bool)$this->isVoted;
    }

    /**
     * @param bool|null $isVoted
     *
     * @return ProjectDTO
     */
    public function setIsVoted(bool $isVoted = false): ProjectDTO
    {
        $this->isVoted = $isVoted;

        return $this;
    }

    /**
     * @param string|null $picture
     * @return ProjectDTO
     */
    public function setPicture(?string $picture): ProjectDTO
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @param string|null $ownerAvatar
     * @return ProjectDTO
     */
    public function setOwnerAvatar(?string $ownerAvatar): ProjectDTO
    {
        $this->ownerAvatar = $ownerAvatar;

        return $this;
    }
}