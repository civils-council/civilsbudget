<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Location
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\LocationRepository")
 */
class Location
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
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cityRegion;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"admin_user_post", "admin_user_put"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var City
     *
     * @Assert\NotBlank(groups={"admin_user_post", "admin_user_put"})
     * @ORM\ManyToOne(targetEntity="City", inversedBy="location")
     * @ORM\JoinColumn(name="location_id", nullable = true, referencedColumnName="id")
     */
    private $cityObject;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="location")
     * @ORM\JoinColumn(name="country_id", nullable = true, referencedColumnName="id")
     */
    private $country;    

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
     * Set district
     *
     * @param string $district
     *
     * @return Location
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return Location
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set cityRegion
     *
     * @param string $cityRegion
     *
     * @return Location
     */
    public function setCityRegion($cityRegion)
    {
        $this->cityRegion = $cityRegion;

        return $this;
    }

    /**
     * Get cityRegion
     *
     * @return string
     */
    public function getCityRegion()
    {
        return $this->cityRegion;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Location
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Location
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
     * Set cityObject
     *
     * @param \AppBundle\Entity\City $cityObject
     *
     * @return Location
     */
    public function setCityObject(\AppBundle\Entity\City $cityObject = null)
    {
        if ($cityObject instanceof City) {
            $this->setCity($cityObject->getCity());
        }
        $this->cityObject = $cityObject;

        return $this;
    }

    /**
     * Get cityObject
     *
     * @return \AppBundle\Entity\City
     */
    public function getCityObject()
    {
        return $this->cityObject;
    }

    /**
     * Set country
     *
     * @param \AppBundle\Entity\Country $country
     *
     * @return Location
     */
    public function setCountry(\AppBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \AppBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->city;
    }
}
