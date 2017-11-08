<?php

namespace AppBundle\Application\Project;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Project as ProjectEntity;
use AppBundle\Exception\ValidatorException;

interface ProjectInterface
{
    /**
     * @param $user
     * @param ProjectEntity $project
     * @param Admin|null $addedBy
     * @param string|null $paperVoteBlankNumber
     *
     * @return string
     * @throws ValidatorException
     */
    public function crateUserLike(
        $user,
        ProjectEntity $project,
        ?Admin $addedBy = null,
        ?string $paperVoteBlankNumber = null
    ): string;
}
