<?php

namespace AppBundle\Entity;
use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\ProjectRepositoryInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

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
    public function getProjectShow(
        ParameterBag $parameterBag
    )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('project')
            ->from('AppBundle:Project', 'project')
            ->addSelect('COUNT(user.id) as countLikes')
            ->leftJoin('project.likedUsers', 'user')
            ->leftJoin('project.voteSetting', 'vs')
            ->leftJoin('vs.location', 'l')
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
            ->orderBy('vs.title', Criteria::ASC);

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
