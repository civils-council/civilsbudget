<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use AppBundle\Form\ConfirmDataType;
use AppBundle\Form\LoginType;
use AppBundle\Form\LoginUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

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
        //http://i.prntscr.com/64388f0de71e4ea496ba43c9e1b2c704.png

        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['clid' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(new LoginType(), $data, ['action' => $this->generateUrl('login_check')]);

        if ($code = $request->query->get('code')) {
            $accessToken = $this->get('app.security.bank_id')->getAccessToken($code);
            $data = $this->get('app.security.bank_id')->getBankIdUser($accessToken['access_token']);
            if ($data['state'] == 'ok') {
                $usersData = $this->get('app.user.manager')->isUniqueUser($data);

                return $this->redirectToRoute('additional_registration', ['id' => $usersData['user']->getid(), 'status' => $usersData['status']]);
            }
        }

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());

            return $this->redirectToRoute('homepage');
        }

        return [
            'debug' => true,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/confirm_data/{id}", name="additional_registration")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function additionalRegistrationAction(User $user, Request $request)
    {
        if ($request->get('status') && $request->get('status') == 'new') {
            $em = $this->getDoctrine()->getManager();
            $form = $this->createForm(new ConfirmDataType(), $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                $this->setAuthenticateToken($user);

                $this->addFlash('info', 'Дякуємо, Ви успішно зареєструвались');
                $this->get('app.mail.sender')->sendEmail(
                    [$user->getEmail()],
                    'Вітаємо Вас',
                    'AppBundle:Email:new_user.html.twig',
                    ['user' => $user]
                );

                // if you put a check before send email, during registration of the project will not be sending mail
                if ($this->get('app.session')->check()) {

                    $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($this->get('app.session')->getProjectId());
                    $flashMessage = $this->get('app.like.service')->execute($user, $project);
                    //TODO check return value
                    $this->addFlash($flashMessage['status'], $flashMessage['text']);

                    return $this->redirect($this->generateUrl('projects_show', ['id' => $this->get('app.session')->getProjectId()]));
                }

                if ($error = $this->get('security.authentication_utils')->getLastAuthenticationError()) {
                    $this->addFlash('danger', $error->getMessage());

                    return $this->redirectToRoute('homepage');
                }
                return $this->redirectToRoute('homepage');
            }
            return [
                'form' => $form->createView()
            ];
        } else {
            if($this->get('app.session')->check()) {
                $this->setAuthenticateToken($user);
                $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($this->get('app.session')->getProjectId());
                $flashMessage = $this->get('app.like.service')->execute($user, $project);
                //TODO check return value
                $this->addFlash($flashMessage['status'], $flashMessage['text']);

                return $this->redirect($this->generateUrl('projects_show', ['id' => $this->get('app.session')->getProjectId()]));
            } else {
                $this->setAuthenticateToken($user);
                return $this->redirectToRoute('homepage');
            }
        }
    }

    /**
     * @Route("/loginIncluded", name="login_included")
     * @Template()
     */
    public function loginIncludedAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['secret' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(new LoginType(), $data, ['action' => $this->generateUrl('login_check')]);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());

            return $this->redirectToRoute('additional_registration');
        }

        return ['form' => $form->createView()];
    }


    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="update_user")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodeDirectorySpecialities entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/edit/submit", name="update_date_user")
     * @Method("PUT")
     * @Template("AppBundle:Default:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodeDirectorySpecialities entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();

//            return $this->redirect($this->generateUrl('login').'#'.$entity->getClid().'');
            return $this->redirectToRoute('homepage');
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new LoginUserType(), $entity, array(
            'action' => $this->generateUrl('update_date_user', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/verify", name="verify")
     */
    public function verifyAction()
    {
        $result = $this->get('app.mail.sender')->verifyEmail();

        dump($result);

        return new \Symfony\Component\HttpFoundation\Response('ok');

    }

    public function setAuthenticateToken(User $user)
    {
        $token = new PreAuthenticatedToken($user, $user->getClid(), 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
    }

}
