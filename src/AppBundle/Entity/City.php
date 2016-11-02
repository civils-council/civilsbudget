<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;

/**
 * City
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CityRepository")
 * @AssertBridge\UniqueEntity(
 *     fields="city",
 *     errorPath="not valid",
 *     message="The city is already in use."
 * )
 */
class City
{
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
    private $city;

    /**
     * @var Location[]ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Location", mappedBy="cityObject", cascade={"persist"})
     */
    private $location;

    /**
     * @var Admin[]ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Admin", mappedBy="city", cascade={"persist"})
     */
    private $admin;

    /**
     * @var VoteSettings[]ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="VoteSettings", mappedBy="location", cascade={"persist"})
     */
    private $voteSetting;
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->city;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->voteSetting = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set city
     *
     * @param string $city
     *
     * @return City
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Add location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return City
     */
    public function addLocation(\AppBundle\Entity\Location $location)
    {
        $this->location[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param \AppBundle\Entity\Location $location
     */
    public function removeLocation(\AppBundle\Entity\Location $location)
    {
        $this->location->removeElement($location);
    }

    /**
     * Get location
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Add voteSetting
     *
     * @param \AppBundle\Entity\VoteSettings $voteSetting
     *
     * @return City
     */
    public function addVoteSetting(\AppBundle\Entity\VoteSettings $voteSetting)
    {
        $this->voteSetting[] = $voteSetting;

        return $this;
    }

    /**
     * Remove voteSetting
     *
     * @param \AppBundle\Entity\VoteSettings $voteSetting
     */
    public function removeVoteSetting(\AppBundle\Entity\VoteSettings $voteSetting)
    {
        $this->voteSetting->removeElement($voteSetting);
    }

    /**
     * Get voteSetting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoteSetting()
    {
        return $this->voteSetting;
    }

    /**
     * Add admin
     *
     * @param \AppBundle\Entity\Admin $admin
     *
     * @return City
     */
    public function addAdmin(\AppBundle\Entity\Admin $admin)
    {
        $this->admin[] = $admin;

        return $this;
    }

    /**
     * Remove admin
     *
     * @param \AppBundle\Entity\Admin $admin
     */
    public function removeAdmin(\AppBundle\Entity\Admin $admin)
    {
        $this->admin->removeElement($admin);
    }

    /**
     * Get admin
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdmin()
    {
        return $this->admin;
    }
}
