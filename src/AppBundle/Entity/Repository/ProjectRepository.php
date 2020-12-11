<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * ProjectRepository.
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
            ->leftJoin('project.userProjects', 'up')
            ->addSelect('COUNT(up.user) as countVoted')
            ->groupBy('project.id')
            ->orderBy('countVoted', 'DESC');

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectShow(
        ParameterBag $parameterBag
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('project')
            ->from('AppBundle:Project', 'project')
            ->leftJoin('project.userProjects', 'up')
            ->leftJoin('project.userProjects', 'upr', Join::WITH, 'upr.user = :user')
            ->leftJoin('project.voteSetting', 'vs')
            ->leftJoin('vs.location', 'l')
            ->addSelect('COUNT(up.user) as countVoted')
            ->addSelect('COUNT(DISTINCT upr.user) as voted')
            ->where('project.approved = :approved')
            ->setParameter('user', $parameterBag->get('user'))
            ->setParameter('approved', true);

        if ($city = $parameterBag->get(ProjectController::QUERY_CITY)) {
            $qb
                ->andWhere('l.city = :city')
                ->setParameter('city', $city);
        }

        if ($parameterBag->get('voteSetting')) {
            $qb->andWhere('vs.id = :voteSetting')
                ->setParameter('voteSetting', $parameterBag->get('voteSetting'));
        }
        $qb
            ->groupBy('project.id')
            ->orderBy('project.viewOrder', Criteria::ASC);

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }

    /**
     * @param int $id
     *
     * @return array|mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneProjectShow($id)
    {
        return $this->createQueryBuilder('project')
            ->select('project', 'COUNT(up.user) as countVoted')
            ->leftJoin('project.userProjects', 'up')
            ->andWhere('project.approved= :approved')
            ->andWhere('project.id = :id')
            ->setParameter('approved', true)
            ->setParameter('id', $id)
            ->groupBy('project.id')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param Project $project
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countVotesPerProject(Project $project): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(up.user) as voted')
            ->leftJoin('p.userProjects', 'up')
            ->where('p = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Project $project
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAdminVotesPerProject(Project $project): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(up.user) as voted')
            ->leftJoin('p.userProjects', 'up')
            ->where('p = :project')
            ->setParameter('project', $project)
            ->andWhere('up.addedBy IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function projectVoteStatisticByVoteSettings(VoteSettings $voteSettings): QueryBuilder {
        $qb = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.title',
                'p.projectType',
                'p.charge',
                'o.firstName',
                'o.lastName',
                'COUNT(DISTINCT up.user) AS totalVotes'
            )
            ->leftJoin('p.userProjects', 'up')
            ->innerJoin('p.owner', 'o')
            ->where('p.voteSetting = :voteSetting')
            ->groupBy('p')
            ->setParameter('voteSetting', $voteSettings)
            ->addOrderBy('totalVotes', 'DESC');
        if ($voteSettings->isOfflineVotingEnabled()) {
            $qb
                ->addSelect('SUM(CASE WHEN up.addedBy IS NOT NULL THEN 1 ELSE 0 END) AS paperVotes')
                ->addSelect('COUNT(up.user) - SUM(CASE WHEN up.addedBy IS NOT NULL THEN 1 ELSE 0 END) AS selfVotes');
        }

        return $qb;
    }

    /**
     * @param array $ids
     *
     * @return array|Project[]
     */
    public function findProjectsByIds(array $ids)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
