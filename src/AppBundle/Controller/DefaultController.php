<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
//            'link' => $this->get('app.security.bank_id')->getLink(),
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
    public function loginAction(Request $request)
    {
//        dump($request);

        return [
//            'link' => $this->get('app.security.bank_id')->getLink(),
        ];
    }
}
