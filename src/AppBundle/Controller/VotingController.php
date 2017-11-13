<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
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
    public function listAction()
    {
        $votingList = $this->get('app.model.voting')->getVotingList();

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
        $votingStatistic = $this->getDoctrine()
            ->getRepository(Project::class)
            ->projectVoteStatisticByVoteSettings($voteSetting);

        $pagination = $this->get('knp_paginator')->paginate(
            $votingStatistic,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 200)
        );

        $countAdminVotes = $this->getDoctrine()->getRepository(VoteSettings::class)->countAdminVotesPerVoting($voteSetting);
        $countTotalVotes = $this->getDoctrine()->getRepository(VoteSettings::class)->countVotesPerVoting($voteSetting);
        $countVoted = $this->getDoctrine()->getRepository(VoteSettings::class)->countVotedUsersPerVoting($voteSetting);

        return [
            'pagination' => $pagination,
            'voteSetting' => $voteSetting,
            'countTotalVotes' => $countTotalVotes,
            'countAdminVotes' => $countAdminVotes,
            'countVoted' => $countVoted
        ];
    }
}
