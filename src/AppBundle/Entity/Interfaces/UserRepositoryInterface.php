<?php

namespace AppBundle\Entity\Interfaces;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;

/**
 * UserRepository
 */
interface UserRepositoryInterface 
{
    /**
     * @param User $entity
     * @return void
     */
    public function saveEntity(User $entity);

    /**
     * @param User $entity
     * @return void
     */
    public function persistEntity(User $entity);

    /**
     * @return void
     */
    public function flushEntity();
    
    /**
     * @param Project $project
     * @param User $user
     * @return integer
     * @throws ValidatorException
     */
    public function getUserVotesByProjectSettingVote(
        Project $project,
        User $user    
    );
}
