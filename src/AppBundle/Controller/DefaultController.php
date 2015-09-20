<?php

namespace AppBundle\Controller;

use AppBundle\Form\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->redirectToRoute('projects_list');
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Template()
     * @Method({"GET"})
     */
    public function profileAction()
    {
        return [];
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['secret' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(new LoginType(), $data, ['action' => $this->generateUrl('login_check')]);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());

            return $this->redirectToRoute('homepage');
        }

        return ['form' => $form->createView()];
    }
}
