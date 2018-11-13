<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
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
            ->leftJoin('project.userProjects', 'user_projects')
            ->leftJoin('user_projects.user', 'user')
            ->addSelect('COUNT(user.id) as countLikes')
            ->groupBy('project.id')
            ->orderBy('countLikes', 'DESC');

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
            ->leftJoin('project.likedUsers', 'user')
            ->leftJoin('project.voteSetting', 'vs')
            ->leftJoin('vs.location', 'l')
            ->addSelect('COUNT(user.id) as countLikes')
            ->where('project.approved = :approved')
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
            ->select('project', 'COUNT(user.id) as countLikes')
            ->leftJoin('project.likedUsers', 'user')
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
            ->select('COUNT(u.id) as voted')
            ->leftJoin('p.userProjects', 'up')
            ->leftJoin('up.user', 'u')
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
            ->select('COUNT(u.id) as voted')
            ->leftJoin('p.userProjects', 'up')
            ->leftJoin('up.user', 'u')
            ->where('p = :project')
            ->setParameter('project', $project)
            ->andWhere('up.addedBy IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param VoteSettings $voteSettings
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function projectVoteStatisticByVoteSettings(VoteSettings $voteSettings)
    {
        return $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.title',
                'p.charge',
                'o.firstName',
                'o.lastName',
                'COUNT(up.user) AS totalVotes',
                'SUM(CASE WHEN up.addedBy IS NOT NULL THEN 1 ELSE 0 END) AS paperVotes',
                'COUNT(up.user) - SUM(CASE WHEN up.addedBy IS NOT NULL THEN 1 ELSE 0 END) AS selfVotes'
            )
            ->leftJoin('p.userProjects', 'up')
            ->innerJoin('p.owner', 'o')
            ->where('p.voteSetting = :voteSetting')
            ->groupBy('p')
            ->setParameter('voteSetting', $voteSettings)
            ->addOrderBy('totalVotes', 'DESC');
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
