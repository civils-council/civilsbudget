<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * VoteSettings
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VoteSettingsRepository")
 */
class VoteSettings
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
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="vote_limits", type="integer", nullable=false)
     */
    private $voteLimits;

    /**
     * @var Project[]ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Project", mappedBy="voteSetting", cascade={"persist"})
     */
    private $project;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="voteSetting")
     * @ORM\JoinColumn(name="location_id", nullable = true, referencedColumnName="id")
     */
    private $location;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->project = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return VoteSettings
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
     * Set voteLimits
     *
     * @param integer $voteLimits
     *
     * @return VoteSettings
     */
    public function setVoteLimits($voteLimits)
    {
        $this->voteLimits = $voteLimits;

        return $this;
    }

    /**
     * Get voteLimits
     *
     * @return integer
     */
    public function getVoteLimits()
    {
        return $this->voteLimits;
    }

    /**
     * Add project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return VoteSettings
     */
    public function addProject(\AppBundle\Entity\Project $project)
    {
        $this->project[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \AppBundle\Entity\Project $project
     */
    public function removeProject(\AppBundle\Entity\Project $project)
    {
        $this->project->removeElement($project);
    }

    /**
     * Get project
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return VoteSettings
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
