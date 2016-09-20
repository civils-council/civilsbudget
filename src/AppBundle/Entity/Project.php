<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProjectRepository")
 */
class Project implements \JsonSerializable
{
    use GedmoTrait;

    /**
     * @var integer
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
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="likedProjects")
     */
    private $likedUsers;

    /**
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
     * Constructor
     */
    public function __construct()
    {
        $this->likedUsers = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set source
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
     * Get source
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
     * Set confirmedAt
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
     * Get confirmedAt
     *
     * @return \DateTime
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /*-------------------------------relation methods------------------------------------------------------------------*/
    /**
     * Set confirmedBy
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
     * Get confirmedBy
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
     * Set owner
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
     * Get owner
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
    function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "description" => $this->getDescription(),
            "charge" => $this->getCharge(),
            "source" => $this->getSource(),
            "picture" => $this->getPicture(),
            "createdAt" => $this->getCreateAt()->format('c'),
            "likes_count" => $this->getLikedUsers()->count(),
            "likes_user" => $this->getLikedUsers()->getValues(),
            "owner" => $this->getOwner()->getFullName(),
            "avatar_owner" => $this->getOwner()->getAvatar(),
            "external_id" => $this->getExternalId(),
        ];
    }

   /**
    * Set approved
    *
    * @param boolean $approved
    *
    * @return Project
    */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * Set charge
     *
     * @param integer $charge
     *
     * @return Project
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge
     *
     * @return integer
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Add likedUser
     *
     * @param \AppBundle\Entity\User $likedUser
     *
     * @return Project
     */
    public function addLikedUser(\AppBundle\Entity\User $likedUser)
    {
        if (!$this->getLikedUsers()->contains($likedUser)) {
            $this->getLikedUsers()->add($likedUser);
        }
        $this->likedUsers[] = $likedUser;

        return $this;
    }

    /**
     * Remove likedUser
     *
     * @param \AppBundle\Entity\User $likedUser
     */
    public function removeLikedUser(\AppBundle\Entity\User $likedUser)
    {
        if ($this->getLikedUsers()->contains($likedUser)) {
            $this->getLikedUsers()->removeElement($likedUser);
        }
        $this->likedUsers->removeElement($likedUser);
    }

    /**
     * Get likedUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikedUsers()
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
}
