<?php

namespace AppBundle\Controller;

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
        return [];
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
    public function showProjectAction($id)
    {
        return ['id' => $id];
    }
}
