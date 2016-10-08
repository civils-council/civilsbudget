<?php

namespace AppBundle\Domain\Project;

use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;

class Project implements ProjectInterface
{
    /**
     * @var ProjectRepositoryInterface
     */
    private $projectRepositoryInterface;

    /**
     * Project constructor.
     * @param ProjectRepositoryInterface $projectRepositoryInterface
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepositoryInterface
    ) {
        $this->projectRepositoryInterface = $projectRepositoryInterface;
    }

    /**
     * @return ProjectRepositoryInterface
     */
    private function getProjectRepositoryInterface()
    {
        return $this->projectRepositoryInterface;
    }
}
