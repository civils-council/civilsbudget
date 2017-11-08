<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\VoteSettingInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
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

        $qb
            ->andWhere('vs.dateTo > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('vs.createAt', Criteria::DESC);
        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getVoteSettingCities() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('l.city')
            ->from('AppBundle:VoteSettings', 'vs')
            ->leftJoin('vs.location', 'l')
            ->groupBy('l.city, vs.createAt')
            ->orderBy('vs.createAt', Criteria::DESC);

        $query = $qb->getQuery();
        $results = $query->getArrayResult();

        return $results;
    }    

    /**
     * {@inheritdoc}
     */
    public function getProjectVoteSettingShow(Request $request) {
        if ($request->get(ProjectController::QUERY_CITY)) {
            return $this->getProjectVoteSettingByCity($request);
        }

        return [];
    }

    /**
     * @return array
     */
    public function listCountVotedUsersPerVoting(): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('vs.id, COUNT(DISTINCT u.id) as voted')
            ->from(Project::class, 'p')
            ->leftJoin('p.userProjects', 'up')
            ->leftJoin('p.voteSetting', 'vs')
            ->leftJoin('up.user', 'u')
            ->groupBy('p.voteSetting')
            ->orderBy('vs.id', Criteria::DESC)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param VoteSettings $voteSettings
     *
     * @return int
     */
    public function countVotedUsersPerVoting(VoteSettings $voteSettings): int
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(DISTINCT u.id) as voted')
            ->from(Project::class, 'p')
            ->leftJoin('p.userProjects', 'up')
            ->leftJoin('up.user', 'u')
            ->where('p.voteSetting = :voteSetting')
            ->setParameter('voteSetting', $voteSettings)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param VoteSettings $voteSettings
     *
     * @return int
     */
    public function countVotesPerVoting(VoteSettings $voteSettings): int
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u.id) as voted')
            ->from(Project::class, 'p')
            ->leftJoin('p.userProjects', 'up')
            ->leftJoin('up.user', 'u')
            ->where('p.voteSetting = :voteSetting')
            ->setParameter('voteSetting', $voteSettings)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param VoteSettings $voteSettings
     *
     * @return int
     */
    public function countAdminVotesPerVoting(VoteSettings $voteSettings): int
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u.id) as voted')
            ->from(Project::class, 'p')
            ->leftJoin('p.userProjects', 'up')
            ->leftJoin('up.user', 'u')
            ->where('p.voteSetting = :voteSetting')
            ->andWhere('up.addedBy IS NOT NULL')
            ->setParameter('voteSetting', $voteSettings)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
