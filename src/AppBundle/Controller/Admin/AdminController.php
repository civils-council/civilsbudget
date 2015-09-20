<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\AdminLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/admin/login", name="admin_login")
     * @Template()
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['username' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(new AdminLoginType(), $data, ['action' => $this->generateUrl('admin_login_check')]);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());
        }

        return ['form' => $form->createView()];
    }
}
