<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
}