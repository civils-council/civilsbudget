<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * UserProject - manyToMany relation with extra fields.
 * @UniqueEntity(
 *     fields={"user_id", "project_id"},
 *     message="Виборець вже віддав голос за проект"
 * )
 *
 * @ORM\Table(
 *     name="user_project",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="user_projects_uidx", columns={"user_id", "project_id"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserProjectRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class UserProject
{
    use GedmoTrait;

    /**
     * @var User
     *
     * @ORM\Id()
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="userProjects",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Project
     *
     * @ORM\Id()
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Project",
     *     inversedBy="userProjects",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $blankNumber;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin")
     * @ORM\JoinColumn(nullable = true, referencedColumnName="id")
     */
    private $addedBy;

    public function __construct(User $user, Project $project)
    {
        $this->user = $user;
        $this->project = $project;
    }

    /**
     * @param Admin|null $addedBy
     *
     * @return UserProject
     */
    public function setAddedBy(?Admin $addedBy): UserProject
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * @return Admin|null
     */
    public function getAddedBy(): ?Admin
    {
        return $this->addedBy;
    }

    /**
     * @param string|null $blankNumber
     *
     * @return UserProject
     */
    public function setBlankNumber(?string $blankNumber): UserProject
    {
        $this->blankNumber = $blankNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBlankNumber(): ?string
    {
        return $this->blankNumber;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
