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
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Admin $admin */
        $admin = $this->getUser();
        $request->query->set(ProjectController::QUERY_CITY, $admin->getCity()->getCity());

        //        $countAdminVoted = $em->getRepository(User::class)->findCountAdminVotedUsers($request->query);
//        $countVoted = $em->getRepository(User::class)->findCountVotedUsers($request->query);
//        $countAdminUsers = $em->getRepository(User::class)->findCountAdminUsers($request->query);
//        $countAuthUsers = $em->getRepository(User::class)->findCountAuthUsers($request->query);

        // TODO: fix it if it is necessary
        $countAdminVoted = 0;
        $countVoted = 0;
        $countAdminUsers = 0;
        $countAuthUsers = 0;

        return [
            'countVoted' => $countVoted,
            'countAdminVoted' => $countAdminVoted,
            'countAdminUsers' => $countAdminUsers,
            'countAuthUsers' => $countAuthUsers,
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
