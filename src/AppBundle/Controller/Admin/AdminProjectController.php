<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Project;
use AppBundle\Form\AddProjectType;
use AppBundle\Form\AdminLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @Route("/admin/projects")
 */
class AdminProjectController extends Controller
{
    /**
     * @Route("/show", name="admin_projects_show")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction()
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN')) {
            return ['projects' => $this->getDoctrine()->getRepository('AppBundle:Project')->findAll()];
        }
        else{
            return $this->redirect($this->generateUrl('login'));
        }
    }

    /**
     * @Route("/{id}", name="admin_project_show", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function showProjectAction(Project $project, Request $request)
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN')) {

            $form = $this->createCreateForm($project);

            if ($request->isMethod('POST')) {
                $form->submit($request);
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $project
                        ->setConfirmedBy($this->getUser())
                        ->setConfirm($form->getData()->getConfirm())
                    ;

                    $em->persist($project);
                    $em->flush();

                    return $this->redirect($this->generateUrl('admin_projects_show', array('id' => $project->getId())));
                }
            }

            return [
                'debug' => true,
                'project' => $project,
                'form' => $form->createView(),
            ];
        }
        else{
            return $this->redirect($this->generateUrl('login'));
        }

    }

    /**
     * @Route("/add", name="admin_projects_add")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function addProjectAction(Request $request)
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN')) {
            $entity = new Project();
            $form = $this->createCreateForm($entity);
            $form->submit($request);
            if ($request->isMethod('POST')) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $entity->setOwner($this->getUser());

                    $em->persist($entity);
                    $em->flush();

                    return $this->redirect($this->generateUrl('projects_show', array('id' => $entity->getId())));
                }
            }
            return [
                'entity' => $entity,
                'form' => $form->createView(),
            ];

        }
        else{
            return $this->redirect($this->generateUrl('login'));
        }
    }

        // --------------------------------- Create Forms ---------------------------------

        /**
         * Creates a form to create a Project entity.
         *
         * @param Project $entity The entity
         *
         * @return \Symfony\Component\Form\Form The form
         */
        private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new AddProjectType(), $entity, array(
            'action' => $this->generateUrl('admin_projects_add', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array('class' => 'formCreateClass'),
        ));
        $this->addFormFields($form);
        $form->add('submit', 'submit', array('label' => 'Add Project'));

        return $form;
    }

    private function addFormFields($form)
    {
        $form->add('confirm', 'choice', array('choices' => array('approved' => 'approved', 'not_approved' => 'not_approved')));
    }
}
