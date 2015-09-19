<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return [
            'projects' => $this->getDoctrine()->getRepository('AppBundle:Project')->findAll(),
        ];
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Template()
     */
    public function profileAction()
    {
        return [];
    }

    /**
     * @Route("/project/{id}", name="show_project")
     * @Template()
     */
    public function showProjectAction(Project $project)
    {
        return ['project' => $project];
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        return [];
    }
}
