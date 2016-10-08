<?php

namespace AppBundle\Entity\Interfaces;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * UserRepository
 */
interface UserRepositoryInterface 
{
    /**
     * @param ParameterBag $parameterBag
     * @return integer
     */
    public function findCountVotedUsers(
        ParameterBag $parameterBag
    );    
    
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
