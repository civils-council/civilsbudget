<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\VoteSettings;
use Doctrine\ORM\EntityRepository;

/**
 * UserProjectRepository
 */
class UserProjectRepository extends EntityRepository
{

    public function getVotesListByVoteSetting(VoteSettings $voteSettings)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p.title',
                'u.lastName',
                'u.firstName',
                'u.inn',
                'up.createAt',
                'up.blankNumber',
                'a.lastName as adminLastName',
                'a.firstName as adminFirstName'
            )
            ->from('AppBundle:Project','p')
            ->innerJoin('p.userProjects','up')
            ->innerJoin('up.user','u')
            ->leftJoin('up.addedBy','a')
            ->where('p.voteSetting = :voteSetting')
            ->setParameter('voteSetting', $voteSettings)
            ->getQuery();
    }
}
