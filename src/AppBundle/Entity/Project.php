<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ProjectRepository")
 */
class Project implements \JsonSerializable
{
    use GedmoTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $charge;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmedAt", type="datetime", nullable=true)
     */
    private $confirmedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="approved", type="boolean", options = {"default": true})
     */
    private $approved;

    /**
     * @var Admin
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin", inversedBy="confirmedProjects")
     * @ORM\JoinColumn(name="confirmBy_id", nullable = true, referencedColumnName="id")
     */
    private $confirmedBy;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $externalId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $owner;

    /**
     * @deprecated
     *
     * @var User[] ArrayCollection
     *
     * ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="likedProjects", fetch="EXTRA_LAZY")
     */
    private $likedUsers;

    /**
     * @deprecated
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $lastDateOfVotes;

    /**
     * @var VoteSettings
     *
     * @Assert\NotBlank(groups={"approve_admin"})
     * @ORM\ManyToOne(targetEntity="VoteSettings", inversedBy="project")
     * @ORM\JoinColumn(name="vote_setting_id", nullable = true, referencedColumnName="id")
     */
    private $voteSetting;

    /**
     * @var UserProject[] ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\UserProject",
     *     mappedBy="project",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $userProjects;

    /**
     * @var Gallery[] ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Gallery",
     *     mappedBy="project",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $gallery;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewOrder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->likedUsers = new ArrayCollection();
        $this->userProjects = new ArrayCollection();
        $this->gallery = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Project
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return Project
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * Set confirmedAt.
     *
     * @param \DateTime $confirmedAt
     *
     * @return Project
     */
    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Get confirmedAt.
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /*-------------------------------relation methods------------------------------------------------------------------*/

    /**
     * Set confirmedBy.
     *
     * @param Admin $confirmedBy
     *
     * @return Project
     */
    public function setConfirmedBy(Admin $confirmedBy = null)
    {
        $this->confirmedBy = $confirmedBy;

        return $this;
    }

    /**
     * Get confirmedBy.
     *
     * @return Admin
     */
    public function getConfirmedBy()
    {
        return $this->confirmedBy;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * Set owner.
     *
     * @param User $owner
     *
     * @return Project
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'charge' => $this->getCharge(),
            'source' => $this->getSource(),
            'picture' => $this->getPicture(),
            'createdAt' => $this->getCreateAt()->format('c'),
            'likes_count' => $this->getLikedUsers()->count(),
            'likes_user' => $this->getLikedUsers()->getValues(),
            'owner' => $this->getOwner()->getFullName(),
            'avatar_owner' => $this->getOwner()->getAvatar(),
            'external_id' => $this->getExternalId(),
        ];
    }

    /**
     * Set approved.
     *
     * @param bool $approved
     *
     * @return Project
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * Set charge.
     *
     * @param int $charge
     *
     * @return Project
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge.
     *
     * @return int
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @deprecated
     *
     * Add likedUser
     *
     * @param \AppBundle\Entity\User $likedUser
     *
     * @return Project
     */
    public function addLikedUser(User $likedUser): Project
    {
        if (!$this->getLikedUsers()->contains($likedUser)) {
            return $this->addUserProject(new UserProject($likedUser, $this));
        }

        return $this;
    }

    /**
     * @deprecated
     *
     * Remove likedUser
     *
     * @param User $likedUser
     */
    public function removeLikedUser(User $likedUser): void
    {
        if ($this->getLikedUsers()->contains($likedUser)) {
            $this->getLikedUsers()->removeElement($likedUser);
        }
        $this->likedUsers->removeElement($likedUser);
    }

    /**
     * @deprecated
     *
     * Get likedUsers
     *
     * @return User[]|Collection
     */
    public function getLikedUsers(): Collection
    {
        return $this->likedUsers;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return \DateTime
     */
    public function getLastDateOfVotes()
    {
        return $this->lastDateOfVotes;
    }

    /**
     * @param \DateTime $lastDateOfVotes
     */
    public function setLastDateOfVotes($lastDateOfVotes)
    {
        $this->lastDateOfVotes = $lastDateOfVotes;
    }

    /**
     * Get approved.
     *
     * @return bool
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set voteSetting.
     *
     * @param VoteSettings $voteSetting
     *
     * @return Project
     */
    public function setVoteSetting(VoteSettings $voteSetting = null): Project
    {
        if ($voteSetting instanceof VoteSettings) {
            $this->setCity($voteSetting->getLocation()->getCity());
        }
        $this->voteSetting = $voteSetting;

        return $this;
    }

    /**
     * Get voteSetting.
     *
     * @return \AppBundle\Entity\VoteSettings
     */
    public function getVoteSetting()
    {
        return $this->voteSetting;
    }

    /**
     * @return UserProject[] | Collection
     */
    public function getUserProjects(): Collection
    {
        return $this->userProjects;
    }

    /**
     * @param UserProject $userProject
     *
     * @return Project
     */
    public function addUserProject(UserProject $userProject): Project
    {
        if (!$this->getUserProjects()->contains($userProject)) {
            $this->getUserProjects()->add($userProject);
        }

        return $this;
    }

    /**
     * @return Gallery[] | Collection
     */
    public function getGallery(): Collection
    {
        return $this->gallery;
    }

    /**
     * @param User $user
     *
     * @return Collection
     */
    public function getUserProjectByUser(User $user)
    {
        return $this->getUserProjects()->filter(function (UserProject $userProjects) use ($user) {
            return $user === $userProjects->getUser();
        });
    }

    /**
     * @return int
     */
    public function getViewOrder(): int
    {
        return $this->viewOrder;
    }

    /**
     * @param int $viewOrder
     *
     * @return self
     */
    public function setViewOrder(int $viewOrder): self
    {
        $this->viewOrder = $viewOrder;

        return $this;
    }
}
