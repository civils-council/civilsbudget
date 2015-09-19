<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Admin;
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
        $adminDTO = new Admin();
        $form = $this->createForm(new AdminLoginType(), $adminDTO);

        return ['form' => $form->createView()];
    }
}
