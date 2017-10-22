<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\VoteSettings;
use AppBundle\Model\VotingModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

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
        $normalisedVotingList = $this->getSerializer()->normalize(
            $this->getVotingModel()->getVotingList(),
            null,
            ['groups' => ['voting_list']]
        );

        return new JsonResponse(["votings" => $normalisedVotingList]);
    }

    /**
     * @Route("/api/votings/{id}/projects", name="api_voting_projects_list", requirements={"id" = "\d+"})
     * @Method({"GET"})
     *
     * @param VoteSettings $voteSetting
     * @param Request $request
     *
     * @return Response
     */
    public function projectsListAction(VoteSettings $voteSetting, Request $request): Response
    {
        $normalizedProjects = $this->getSerializer()->normalize(
            $this->getVotingModel()->getVotingProjects($voteSetting, $request),
            null,
            ['groups' => ['project_list']]
        );

        return new JsonResponse(["projects" => $normalizedProjects]);
    }

    /**
     * @return VotingModel
     */
    private function getVotingModel(): VotingModel
    {
        return $this->get('app.model.voting');
    }

    /**
     * @return Serializer
     */
    private function getSerializer(): Serializer
    {
        return $this->get('serializer');
    }
}