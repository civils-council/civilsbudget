<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 *
 * @AssertBridge\UniqueEntity(
 *     groups={"admin_user_post", "admin_user_put"},
 *     fields="inn",
 *     errorPath="not valid",
 *     message="Цей iдентифiкацiйний код вже iснує."
 * )
 *
 * @AssertBridge\UniqueEntity(
 *     groups={"admin_user_post", "admin_user_put"},
 *     fields="numberBlank",
 *     errorPath="not valid",
 *     message="Цей номер бланку вже iснує."
 * )
 */
class User extends AbstractUser implements UserInterface, \JsonSerializable
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
     * @Assert\NotBlank(
     *     groups={"admin_user_post", "admin_user_put"},
     *     message="Им'я не може бути пустим"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     groups={"admin_user_post", "admin_user_put"},
     *     message="Прізвище не може бути пустим"
     * )
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
     * @Assert\NotBlank(groups={"admin_user_post", "admin_user_put"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sex;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"admin_user_put"})
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
     * @var Project[] ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project", mappedBy="owner")
     */
    private $projects;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin", inversedBy="user")
     */
    private $addedByAdmin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $clid;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true, unique=true)
     * @Assert\Type(
     *     groups={"admin_user_post", "admin_user_put"},
     *     type="numeric",
     *     message="Не коректне значення Ідентифікаційного коду."
     * )
     * @Assert\NotBlank(
     *     groups={"admin_user_post", "admin_user_put"},
     *     message="Ідентифікаційний код не може бути пустим"
     * )
     * @Assert\Length(
     *     groups={"admin_user_post", "admin_user_put"},
     *      min = "10",
     *      max = "10",
     *      exactMessage = "Ідентифікаційний код має містити {{ limit }} символів",
     * )
     */
    private $inn;

    /**
     * @var Project[]ArrayCollection
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
     * @deprecated
     *
     * @var string
     *
     * @Assert\NotBlank(
     *     groups={"admin_user_post"},
     *     message="Номер бланку не може бути пустим"
     * )
     * @ORM\Column(name="number_blank", type="string", nullable=true)
     */
    private $numberBlank;

    /**
     * @var UserProject[] ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\UserProject",
     *     mappedBy="user",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    private $userProjects;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection;
        $this->likedProjects = new ArrayCollection;
        $this->userProjects = new ArrayCollection;
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
    public function setFirstName($firstName): User
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
    public function setLastName($lastName): User
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
    public function setMiddleName($middleName): User
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
    public function setBirthday($birthday): User
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
    public function setLastLoginAt($lastLoginAt): User
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
     *
     * @return User
     */
    public function setPhone($phone): User
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
     *
     * @return User
     */
    public function setClid($clid): User
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
    public function setLocation(Location $location = null): User
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
     * @return Project[]|Collection
     */
    public function getProjects(): Collection
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
    public function setEmail($email): User
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
    public function setSex($sex): User
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
    public function setAvatar($avatar): User
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
    public function setInn($inn): User
    {
        if (!$this->clid) {
            $this->setClid($inn);            
        }
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
     *
     * @return User
     */
    public function addLikedProjects(Project $project): User
    {
        if (!$this->getLikedProjects()->contains($project)) {
            return $this->addUserProjects(new UserProject($this, $project));
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
     * @return Project[]|Collection
     */
    public function getLikedProjects(): Collection
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


    /**
     * Get isSubscribe
     *
     * @return boolean
     */
    public function getIsSubscribe()
    {
        return $this->isSubscribe;
    }

    /**
     * Add likedProject
     *
     * @param Project $likedProject
     *
     * @return User
     */
    public function addLikedProject(Project $likedProject): User
    {
        if (!$this->getLikedProjects()->contains($likedProject)) {
            $this->getLikedProjects()->add($likedProject);
        }

        return $this;
    }

    /**
     * Set addedByAdmin
     *
     * @param Admin $addedByAdmin
     *
     * @return User
     */
    public function setAddedByAdmin(Admin $addedByAdmin = null): User
    {
        $this->addedByAdmin = $addedByAdmin;

        return $this;
    }

    /**
     * Get addedByAdmin
     *
     * @return \AppBundle\Entity\Admin
     */
    public function getAddedByAdmin()
    {
        return $this->addedByAdmin;
    }

    /**
     * Set numberBlank
     * @deprecated

     * @param string $numberBlank
     *
     * @return User
     */
    public function setNumberBlank($numberBlank)
    {
        $this->numberBlank = $numberBlank;

        return $this;
    }

    /**
     * Get numberBlank
     * @deprecated
     *
     * @return string
     */
    public function getNumberBlank()
    {
        return $this->numberBlank;
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
     * @return User
     */
    public function addUserProjects(UserProject $userProject): User
    {
        if (!$this->getUserProjects()->contains($userProject)) {
            $this->getUserProjects()->add($userProject);
        }

        return $this;
    }
}
