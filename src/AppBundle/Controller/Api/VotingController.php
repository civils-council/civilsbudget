<?php

namespace AppBundle\Controller\Api;

use AppBundle\Model\VotingModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VotingController extends Controller
{
    /**
     * @Route("/api/votings", name="api_votings_list")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function listAction(): Response
    {
        $votingList = $this->getVotingModel()->getVotingList();

        return new JsonResponse(["votings" => $votingList]);
    }

    /**
     * @return VotingModel
     */
    private function getVotingModel(): VotingModel
    {
        return $this->get('app.model.voting');
    }
}