<?php

namespace AppBundle\Controller;

use AppBundle\Form\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['secret' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(new LoginType(), $data, ['action' => $this->generateUrl('login_check')]);

        if ($code = $request->query->get('code')) {
            $bankUser = $this->get('app.security.bank_id')->getUser($code);
            $user = $this->get('app.user_manager')->getUser($bankUser);


            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_home', serialize($token));

            return $this->redirect($this->generateUrl('homepage'));
        }

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());

            return $this->redirectToRoute('homepage');
        }

        return ['form' => $form->createView()];
    }
}
