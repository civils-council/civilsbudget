<?php

namespace AppBundle\Domain\User;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;

interface UserInterface
{
    /**
     * @param Project $project
     * @param User $user
     * @return mixed
     */
    public function getAccessVote(
        Project $project,
        User $user
    );

    /**
     * @param Project $project
     * @param User $user
     * @return string
     * @throws ValidatorException
     */
    public function postVote(
        Project $project,
        User $user
    );    
}
