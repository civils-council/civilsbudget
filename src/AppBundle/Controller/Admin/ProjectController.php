<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Project;
use AppBundle\Form\ProjectType;
use AppBundle\Form\AdminLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 *
 * @Route("/admin/projects")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/show", name="admin_projects_show")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction()
    {

        return ['projects' => $this->getDoctrine()->getRepository('AppBundle:Project')->findAll()];

    }

    /**
     * @Route("/{id}/show", name="admin_project_show", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET", "POST"})
     * @ParamConverter("project", class="AppBundle:Project")
     */
    public function showProjectAction(Project $project)
    {
        return ['project' => $project];
    }

    /**
     * @Route("/add", name="admin_projects_add")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function addProjectAction(Request $request)
    {
        $project = new Project();
        $form = $this->createCreateForm($project);
        $form->submit($request);
            if ($request->isMethod('POST')) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $project->setConfirm($this->getUser());
                    $project->setConfirmedBy($this->getUser());

                    $em->persist($project);
                    $em->flush();

                    return $this->redirect($this->generateUrl('admin_project_show', array('id' => $project->getId())));
                }
            }
        return [
                'entity' => $project,
                'form' => $form->createView(),
        ];
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
        $form = $this->createForm(new ProjectType(), $entity, array(
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
