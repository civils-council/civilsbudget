<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VotingController extends Controller
{
    /**
     * @Route("/votings", name="votings_list")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $votingId = $request->get('votingId') ?  explode(',', $request->get('votingId')) : null;
        $votingList = $this->get('app.model.voting')->getVotingList($votingId);

        return [
            'debug' => true,
            "votings" => $votingList
        ];
    }

    /**
     * @Route("/votings/{id}/statistic", name="voting_statistic")
     * @Template()
     * @Method({"GET"})
     *
     * @param VoteSettings $voteSetting
     * @param Request $request
     *
     * @return array
     */
    public function statisticAction(VoteSettings $voteSetting, Request $request)
    {
        //TODO: ADD cache according to query params
//        $cache = $this->get('cache.app');
//        $ttl = $this->getParameter('statistic_ttl');
//
//        try {
//            $statistic = $cache->getItem('statistic-'.$voteSetting->getId());
//
//            if ($statistic->isHit()) {
//                $votingStatistic = $statistic->get();
//            } else {
//                $votingStatistic = $this->getDoctrine()
//                    ->getRepository(Project::class)
//                    ->projectVoteStatisticByVoteSettings($voteSetting)
//                ->getQuery()->getResult();
//                $statistic->set($votingStatistic);
//                $statistic->expiresAfter($ttl);
//                $cache->save($statistic);
//            }
//        } catch (InvalidArgumentException $e) {
            $votingStatistic = $this->getDoctrine()
                ->getRepository(Project::class)
                ->projectVoteStatisticByVoteSettings($voteSetting);
//        }

        $pagination = $this->get('knp_paginator')->paginate(
            $votingStatistic,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 150)
        );

        $voteSettingRepository = $this->getDoctrine()->getRepository(VoteSettings::class);
        $countAdminVotes = $voteSetting->isOfflineVotingEnabled() ? $voteSettingRepository->countAdminVotesPerVoting($voteSetting) : 0;
        $countTotalVotes = $voteSettingRepository->countVotesPerVoting($voteSetting);
        $countVoted = $voteSettingRepository->countVotedUsersPerVoting($voteSetting);

        return [
            'pagination' => $pagination,
            'voteSetting' => $voteSetting,
            'countTotalVotes' => $countTotalVotes,
            'countAdminVotes' => $countAdminVotes,
            'countVoted' => $countVoted
        ];
    }
}
