<?php

namespace AppBundle\Entity;
use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 */
class ProjectRepository extends EntityRepository implements ProjectRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProjectStat()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('project')
            ->from('AppBundle:Project', 'project')
            ->addSelect('COUNT(user.id) as countLikes')
            ->leftJoin('project.likedUsers', 'user')
            ->groupBy('project.id')
            ->orderBy("countLikes", 'DESC');
        
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectShow()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('project')
            ->from('AppBundle:Project', 'project')
            ->addSelect('COUNT(user.id) as countLikes')
            ->leftJoin('project.likedUsers', 'user')
            ->andWhere('project.approved = :approved')
            ->setParameter('approved', true)
            ->groupBy('project.id')
            ->orderBy('project.lastDateOfVotes', Criteria::DESC);
        
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getOneProjectShow($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('project')
            ->from('AppBundle:Project', 'project')
            ->addSelect('COUNT(user.id) as countLikes')
            ->leftJoin('project.likedUsers', 'user')
            ->andWhere('project.approved= :approved')
            ->andWhere('project.id = :id')
            ->setParameter('approved', true)
            ->setParameter('id', $id)
            ->groupBy('project.id');
        
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        return $results;
    }
}
