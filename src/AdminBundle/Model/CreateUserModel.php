<?php

namespace AdminBundle\Model;

use AppBundle\Entity\Location;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserModel
{
    /**
     * @var User
     * @Assert\NotBlank(groups={"admin_user_post"})
     */
    public $user;

    /**
     * @var Location
     * @Assert\NotBlank(groups={"admin_user_post"})
     */
    public $location;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
}
