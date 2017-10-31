<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VotingController extends Controller
{
    const SERVER_ERROR                    = 'Server Error';
    const QUERY_CITY                      = 'city';
    const QUERY_PROJECT_ID                = 'project_id';

    /**
     * @Route("/votings", name="votings_list")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $votingList = $this->get('app.model.voting')->getVotingList();

        return [
            'debug' => true,
            "votings" => $votingList
        ];
    }
}
