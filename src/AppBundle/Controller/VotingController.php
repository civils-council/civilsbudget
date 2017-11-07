<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}
