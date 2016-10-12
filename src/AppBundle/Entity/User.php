<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements UserInterface, \JsonSerializable
{
    use GedmoTrait;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $birthday;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Length(min=3, max=255)
     */
    protected $email;

    /**
     * @var Location
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Location", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $location;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project", mappedBy="owner")
     */
    private $projects;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $clid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inn;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Project", inversedBy="likedUsers")
     */
    private $likedProjects;

    /**
     * @var string $image
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_votes", type="integer", options={"default": 0}, nullable = true)
     */
    private $countVotes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_subscribe", type="boolean", options={"default": true}, nullable = true)
     */
    private $isSubscribe;

    /**
     * @var boolean
     */
    private $isDataPublic;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->likedProjects = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     *
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     *
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set lastLoginAt
     *
     * @param \DateTime $lastLoginAt
     *
     * @return User
     */
    public function setLastLoginAt($lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * Get lastLoginAt
     *
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getClid()
    {
        return $this->clid;
    }

    /**
     * @param $clid
     * @return $this
     */
    public function setClid($clid)
    {
        $this->clid = $clid;

        return $this;
    }

    /*-------------------------------relations methods----------------------------------------------------------------*/

    /**
     * Set location
     *
     * @param Location $location
     *
     * @return User
     */
    public function setLocation(Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Add project
     *
     * @param Project $project
     *
     * @return User
     */
    public function addProject(Project $project)
    {
        if (!$this->getProjects()->contains($project)) {
            $this->getProjects()->add($project);
        }
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param Project $project
     */
    public function removeProject(Project $project)
    {
        if ($this->getProjects()->contains($project)) {
            $this->getProjects()->removeElement($project);
        }
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set sex
     *
     * @param string $sex
     *
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /*----------------------------------------other methods-----------------------------------------------------------*/

    public function getFullName()
    {
        return $this->getLastName() . ' ' . $this->getFirstName() . ' ' . $this->getMiddleName();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getFullName();
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "full_name" => $this->getFullName(),
            "clid" => $this->getClid(),
        ];
    }

    /*----------------------------------------end other methods-----------------------------------------------------------*/

    /**
     * Set inn
     *
     * @param string $inn
     *
     * @return User
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * Get inn
     *
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @param Project $project
     * @return $this
     */
    public function addLikedProjects(Project $project)
    {
        if (!$this->getLikedProjects()->contains($project)) {
            $this->getLikedProjects()->add($project);
        }

        return $this;
    }

    /**
     * Remove project
     *
     * @param Project $project
     * @return User
     */
    public function removeLikedProject(Project $project)
    {
        if ($this->getLikedProjects()->contains($project)) {
            $this->getLikedProjects()->removeElement($project);
        }

        return $this;
    }

    /**
     * Get likedProjects
     *
     * @return Collection
     */
    public function getLikedProjects()
    {
        return $this->likedProjects;
    }

    /**
     * @return int
     */
    public function getCountVotes()
    {
        return $this->countVotes;
    }

    /**
     * @param int $countVotes
     */
    public function setCountVotes($countVotes)
    {
        $this->countVotes = $countVotes;
    }

    /**
     * @return boolean
     */
    public function isIsSubscribe()
    {
        return $this->isSubscribe;
    }

    /**
     * @param boolean $isSubscribe
     */
    public function setIsSubscribe($isSubscribe)
    {
        $this->isSubscribe = $isSubscribe;
    }

    /**
     * @return boolean
     */
    public function isIsDataPublic()
    {
        return $this->isDataPublic;
    }

    /**
     * @param boolean $isDataPublic
     */
    public function setIsDataPublic($isDataPublic)
    {
        $this->isDataPublic = $isDataPublic;
    }

}
