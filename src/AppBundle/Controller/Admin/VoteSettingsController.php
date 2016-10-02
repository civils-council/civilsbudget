<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\VoteSettings;
use AppBundle\Form\VoteSettingsType;

/**
 * VoteSettings controller.
 *
 * @Route("/admin/vote_settings")
 */
class VoteSettingsController extends Controller
{
    /**
     * Lists all VoteSettings entities.
     *
     * @Route("/", name="vote_settings")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:VoteSettings')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new VoteSettings entity.
     *
     * @Route("/", name="vote_settings_create")
     * @Method("POST")
     * @Template("AppBundle:VoteSettings:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new VoteSettings();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('vote_settings_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a VoteSettings entity.
     *
     * @param VoteSettings $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(VoteSettings $entity)
    {
        $form = $this->createForm(new VoteSettingsType(), $entity, array(
            'action' => $this->generateUrl('vote_settings_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new VoteSettings entity.
     *
     * @Route("/new", name="vote_settings_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new VoteSettings();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a VoteSettings entity.
     *
     * @Route("/{id}", name="vote_settings_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:VoteSettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VoteSettings entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing VoteSettings entity.
     *
     * @Route("/{id}/edit", name="vote_settings_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:VoteSettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VoteSettings entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a VoteSettings entity.
    *
    * @param VoteSettings $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(VoteSettings $entity)
    {
        $form = $this->createForm(new VoteSettingsType(), $entity, array(
            'action' => $this->generateUrl('vote_settings_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing VoteSettings entity.
     *
     * @Route("/{id}", name="vote_settings_update")
     * @Method("PUT")
     * @Template("AppBundle:VoteSettings:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:VoteSettings')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VoteSettings entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('vote_settings_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a VoteSettings entity.
     *
     * @Route("/{id}", name="vote_settings_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:VoteSettings')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find VoteSettings entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('vote_settings'));
    }

    /**
     * Creates a form to delete a VoteSettings entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vote_settings_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
