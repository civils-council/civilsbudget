<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Admin
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdminRepository")
 */
class Admin extends AbstractUser implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

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
     * @ORM\Column(length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(length=255, nullable=true)
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Project", mappedBy="confirmedBy", cascade={"persist"})
     */
    private $confirmedProjects;

    /**
     * @var User[] ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="addedByAdmin")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="City", inversedBy="admin")
     * @ORM\JoinColumn(name="city_id", nullable = false, referencedColumnName="id")
     */
    private $city;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $roles = [];

    public function __construct()
    {
        $this->confirmedProjects = new ArrayCollection();
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
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    public function __toString()
    {
       return $this->username;
    }

    /*------------------------------------------ relations methods----------------------------------------------------*/

    /**
     * Add confirmedProject
     *
     * @param Project $confirmedProject
     *
     * @return Admin
     */
    public function addConfirmedProject(Project $confirmedProject)
    {
        $this->confirmedProjects[] = $confirmedProject;

        return $this;
    }

    /**
     * Remove confirmedProject
     *
     * @param Project $confirmedProject
     */
    public function removeConfirmedProject(Project $confirmedProject)
    {
        $this->confirmedProjects->removeElement($confirmedProject);
    }

    /**
     * Get confirmedProjects
     *
     * @return Collection
     */
    public function getConfirmedProjects()
    {
        return $this->confirmedProjects;
    }

    /*--------------------------------implements methods--------------------------------------------------------------*/
    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return array_unique(array_merge($this->roles, [self::ROLE_ADMIN]));
    }

    /**
     * @param array $roles
     *
     * @return Admin
     */
    public function setRoles(array $roles): Admin
    {
        $this->roles = $roles;

        return $this;
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
    public function eraseCredentials()
    {}

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Admin
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set city
     *
     * @param \AppBundle\Entity\City $city
     *
     * @return Admin
     */
    public function setCity(\AppBundle\Entity\City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \AppBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }
}
