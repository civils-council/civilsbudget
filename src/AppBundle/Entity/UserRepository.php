<?php

namespace AppBundle\Entity;
use AppBundle\Entity\Interfaces\UserRepositoryInterface;
use AppBundle\Exception\ValidatorException;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function saveEntity(User $entity)
    {
        $this->persistEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistEntity(User $entity)
    {
        $this->_em->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function flushEntity()
    {
        $this->_em->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUserVotesByProjectSettingVote(
        Project $project,
        User $user
    ) {
        if (!$project->getVoteSetting()) {
            throw new ValidatorException('Проекту повинен бути назначений тип голосування');
        }
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT lp.id')
            ->distinct('lp.id')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.likedProjects', 'lp')
            ->leftJoin('lp.voteSetting', 'v')
            ->where('v.id = voteId')
            ->andWhere('u.id = userId')
            ->setParameters([
                'voteId', $project->getVoteSetting()->getId(),
                'userId', $user->getId()
            ]);
        
        $query = $qb->getQuery();
        $results = $query->getSingleScalarResult();
        
        return $results;
    }
}
