<?php

namespace AppBundle\Entity;
use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Interfaces\UserRepositoryInterface;
use AppBundle\Exception\ValidatorException;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @return mixed
     */
    public function findCountVotedUsers(
        ParameterBag $parameterBag
    ) {
        $firstDay = new \DateTime('first day of this year');
        $lastDay = new \DateTime('last day of this year');
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(u.id)')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.likedProjects', 'l');

        if ($city = $parameterBag->get(ProjectController::QUERY_CITY)) {
            $qb
                ->leftJoin('l.voteSetting', 'vs')
                ->leftJoin('vs.location', 'c')
                ->andWhere('c.city = :city')
                ->setParameter('city', $city);

        }
        $qb
            ->andWhere($qb->expr()->between('l.createAt', ':dateFrom', ':dateTo'))
            ->setParameter(':dateFrom', $firstDay)
            ->setParameter(':dateTo', $lastDay);

        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();
        return $result;
    }

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
            ->select('COUNT(lp.id)')
            ->distinct('lp.id')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.likedProjects', 'lp')
            ->leftJoin('lp.voteSetting', 'v')
            ->where('v.id = :voteId')
            ->andWhere('u.id = :userId')
            ->setParameters([
                'voteId' => $project->getVoteSetting()->getId(),
                'userId' => $user->getId()
            ]);

        $query = $qb->getQuery();
        $results = $query->getSingleScalarResult();

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserVotesBySettingVote(
        VoteSettings $voteSettings,
        User $user
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(lp.id)')
            ->distinct('lp.id')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.likedProjects', 'lp')
            ->leftJoin('lp.voteSetting', 'v')
            ->where('v.id = :voteId')
            ->andWhere('u.id = :userId')
            ->setParameters([
                'voteId' => $voteSettings->getId(),
                'userId' => $user->getId()
            ]);

        $query = $qb->getQuery();
        $results = $query->getSingleScalarResult();

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserByInnOrClid(
        $clid,
        $inn
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('u')
            ->from('AppBundle:User', 'u')
            ->where('u.inn = :inn')
            ->orWhere('u.clid = :clid')
            ->setParameters([
                'inn' => $inn,
                'clid' => $clid
            ])
            ->orderBy('u.createAt', 'DESC')
            ->setFirstResult(1);

        $query = $qb->getQuery();
        $results = $query->getOneOrNullResult();

        return $results;
    }
}
