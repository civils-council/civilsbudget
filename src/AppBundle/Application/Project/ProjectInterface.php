<?php

namespace AppBundle\Application\Project;

use AppBundle\Entity\Project as ProjectEntity;
use AppBundle\Exception\ValidatorException;

interface ProjectInterface
{
    /**
     * @param $user
     * @param ProjectEntity $project
     * @return string
     * @throws ValidatorException
     */
    public function crateUserLike(
        $user,
        ProjectEntity $project
    );    
}
