<?php

namespace AppBundle\Entity;
use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\VoteSettingInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * VoteSettingsRepository
 */
class VoteSettingsRepository extends EntityRepository implements VoteSettingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProjectVoteSettingShow(
        Request $request
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('vs')
            ->from('AppBundle:VoteSettings', 'vs')
            ->leftJoin('vs.location', 'l');

        if ($city = $request->get(ProjectController::QUERY_CITY)) {
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
