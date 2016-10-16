<?php

namespace AppBundle\Domain\User;

use AppBundle\Entity\Project;
use AppBundle\Entity\User as UserEntity;
use AppBundle\Exception\ValidatorException;

interface UserInterface
{
    /**
     * @param Project $project
     * @param UserEntity $user
     * @return mixed
     */
    public function getAccessVote(
        Project $project,
        UserEntity $user
    );

    /**
     * @param Project $project
     * @param UserEntity $user
     * @return string
     * @throws ValidatorException
     */
    public function postVote(
        Project $project,
        UserEntity $user
    );    
}
