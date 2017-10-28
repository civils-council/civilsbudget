<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use AppBundle\Model\VotingModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

        return new JsonResponse(['votings' => $normalisedVotingList]);
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
            $this->getVotingModel()->getVotingProjectList($voteSetting, $request),
            null,
            ['groups' => ['project_list']]
        );

        return new JsonResponse(['projects' => $normalizedProjects]);
    }

    /**
     * @Route(
     *     "/api/votings/{id}/projects/{project_id}",
     *     name="api_voting_project",
     *     requirements={
     *          "id" = "\d+",
     *          "project_id" = "\d+"
     *     }
     * )
     * @ParamConverter("project", class="AppBundle:Project", options={"id" = "project_id"})
     * @Method({"GET"})
     *
     * @param VoteSettings $voteSetting
     * @param Project $project
     *
     * @return Response
     */
    public function showVotingProjectAction(VoteSettings $voteSetting, Project $project): Response
    {
        $normalizedProject = $this->getSerializer()->normalize(
            $this->getVotingModel()->getVotingProject($voteSetting, $project, $this->getUser()),
            null,
            ['groups' => ['project_list']]
        );

        return new JsonResponse(['project' => $normalizedProject]);
    }

    /**
     * @Route(
     *     "/api/votings/{id}/projects/{project_id}/like",
     *     name="api_voting_project_like",
     *     requirements={
     *          "id" = "\d+",
     *          "project_id" = "\d+"
     *     }
     * )
     * @ParamConverter("project", class="AppBundle:Project", options={"id" = "project_id"})
     * @Method({"POST"})
     *
     * @param VoteSettings $voteSetting
     * @param Project $project
     *
     * @return Response
     */
    public function likeVotingProjectAction(VoteSettings $voteSetting, Project $project): Response
    {
        try {
            return new JsonResponse([
                'success' => $this->getVotingModel()->likeVotingProjectByUser($voteSetting, $project, $this->getUser())
            ]);

        } catch (\Exception $e) {
            return new JsonResponse(['warning' => $e->getMessage()], $e->getCode());
        }
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