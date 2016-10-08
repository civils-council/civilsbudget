<?php

namespace AppBundle\Application\Project;

use AppBundle\Exception\ValidatorException;

interface ProjectInterface
{
    /**
     * @param $user
     * @param Project $project
     * @return mixed
     * @throws ValidatorException
     */
    public function crateUserLike(
        $user,
        Project $project
    );    
}
