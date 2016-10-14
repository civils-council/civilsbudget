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
    public function getProjectVoteSettingByCity(
        Request $request
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('vs')
            ->from('AppBundle:VoteSettings', 'vs')
            ->leftJoin('vs.location', 'l');

        if ($city = $request->get(ProjectController::QUERY_CITY)) {
            $qb
                ->andWhere('l.city LIKE :city')
                ->setParameter('city', $city);
        }
        $qb->orderBy('vs.createAt', Criteria::DESC);

        $query = $qb->getQuery();
        $results = $query->getResult();

        return isset($results[0]) ? $results[0] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getVoteSettingByUserCity(
        User $user
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('vs')
            ->from('AppBundle:VoteSettings', 'vs')
            ->leftJoin('vs.location', 'l');

        if ($user->getLocation() && $user->getLocation()->getCity()) {
            $qb
                ->andWhere('l.city LIKE :city')
                ->setParameter('city', $user->getLocation()->getCity());
        }
        $qb->orderBy('vs.createAt', Criteria::DESC);

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectVoteSettingShow(
        Request $request
    ) {
        if ($request->get(ProjectController::QUERY_CITY)) {
            return $this->getProjectVoteSettingByCity($request);
        }

        return [];
    }
}
