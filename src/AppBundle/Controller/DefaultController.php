<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\LoginType;
use AppBundle\Form\LoginUserType;
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
     * @Route("/login/{id}", name="login", defaults={"id" = null}, requirements={"id" = "null|\d+"})
     * @Template()
     */
    public function loginAction($id = null, Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['clid' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(new LoginType(), $data, ['action' => $this->generateUrl('login_check')]);

        if ($code = $request->query->get('code')) {
            $accessToken = $this->get('app.security.bank_id')->getAccessToken($code);
            $data = $this->get('app.security.bank_id')->getBankIdUser($accessToken['access_token']);
            if ($data['state'] == 'ok') {
                $user = $this->get('app.user.manager')->isUniqueUser($data);
//                $form = $this->createForm(new LoginUserType(), $user, ['action' => $this->generateUrl('update_user', ['id' => $user->getId()])]);
//                $form = $this->createEditForm($user[0]);
                if($user[1] == 'new') {
                    $this->addFlash('inforormation', 'add information');
                    $form = $this->createEditForm($user[0]);
                } else {
                    if ($id) {
                        return $this->redirectToRoute('projects_show', ['id' => $id]);
                    }

                    return $this->redirectToRoute('homepage');
                }
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

            return $this->redirectToRoute('homepage');
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
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
}
