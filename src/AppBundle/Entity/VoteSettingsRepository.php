<?php

namespace AppBundle\Entity;
use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\VoteSettingInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * VoteSettingsRepository
 */
class VoteSettingsRepository extends EntityRepository implements VoteSettingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProjectShow(
        ParameterBag $parameterBag
    )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('vs')
            ->from('AppBundle:VoteSettings', 'vs')
            ->leftJoin('vs.location', 'l');

        if ($city = $parameterBag->get(ProjectController::QUERY_CITY)) {
            $qb
                ->andWhere('l.city = :city')
                ->setParameter('city', $city);
        }
        $qb->orderBy('vs.createAt', Criteria::DESC);
        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();

        return $results;
    }
}
