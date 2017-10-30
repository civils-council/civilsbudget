<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\ProjectController;
use AppBundle\Entity\Admin;
use AppBundle\Form\AdminLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Admin $admin */
        $admin = $this->getUser();
        $request->query->set(ProjectController::QUERY_CITY, $admin->getCity()->getCity());
        $countAdminVoted = $em->getRepository('AppBundle:User')->findCountAdminVotedUsers($request->query);
        $countVoted = $em->getRepository('AppBundle:User')->findCountVotedUsers($request->query);
        $countAdminUsers = $em->getRepository('AppBundle:User')->findCountAdminUsers($request->query);
        $countAuthUsers = $em->getRepository('AppBundle:User')->findCountAuthUsers($request->query);
        
        return [
            'countVoted' => $countVoted,
            'countAdminVoted' => $countAdminVoted,
            'countAdminUsers' => $countAdminUsers,
            'countAuthUsers' => $countAuthUsers
        ];
    }

    /**
     * @Route("/admin/login", name="admin_login")
     * @Template()
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['username' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(AdminLoginType::class, $data, ['action' => $this->generateUrl('admin_login_check')]);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());
        }

        return ['form' => $form->createView()];
    }
}
