<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProjectController extends Controller
{
    /**
     * @Route("/api/projects", name="api_projects_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findAll();

        return new JsonResponse(["projects" => $projects]);
    }

    /**
     * @Route("/api/projects/{id}", name="api_projects_show", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function showProjectAction(Project $project)
    {
        return new JsonResponse(['project' => $project]);
    }

    /**
     * @Route("/api/projects/{id}/like", name="api_projects_like", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * @ParamConverter("project", class="AppBundle:Project")
     */
    public function likeAction(Project $project, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $clid = null;
        $content = $this->get("request")->getContent();
        if (!empty($content))
        {
            $params = json_decode($content, true);
            $clid = $params['clid'];
        }
        if(empty($clid)) {
            $clid = $this->get('request')->request->get('clid');
        }

        if ($request->getMethod() == Request::METHOD_POST) {
            $user = $em->getRepository('AppBundle:User')->findOneByClid($clid);
            if ($project->getLikedUsers()->contains($user)) {
                $this->addFlash('warning', 'Ви вже підтримали цей проект.');
                return new JsonResponse(['warning' => 'Ви вже підтримали цей проект.']);
            } else {
                $project->addLikedUser($user);
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(['success' => 'Ваший голос зараховано на підтримку проект.']);
            }
        }

        return new JsonResponse(['project' => $project]);
    }
}