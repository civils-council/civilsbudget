<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\AddProjectType;
use AppBundle\Form\LikeProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/projects", name="projects_list")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction()
    {
        $projects =$this->getDoctrine()->getRepository('AppBundle:Project')->findByConfirm('approved');
        return [
            'projects' => $projects,
        ];
    }

    /**
     * @Route("/projects/{id}", name="projects_show", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET"})
     */
    public function showProjectAction(Project $project)
    {
        return ['project' => $project];
    }

    /**
     * @Route("/projects/{id}/like", name="projects_like", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function likeAction(Project $project, Request $request)
    {
        $form = $this
            ->createForm(new LikeProjectType(), [], [
                'user' => $this->getUser(),
                'action' => $this->generateUrl('projects_like', ['id' => $project->getId()]),
            ]);


        if ($request->getMethod() == Request::METHOD_POST) {
            if ($project->getLikedUsers()->contains($this->getUser())) {
                $this->addFlash('warning', 'Ви вже підтримали цей проект.');
            } else {
                $project->addLikedUser($this->getUser());
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Ваший голос зараховано на підтримку проект.');
            }

            return $this->redirectToRoute('projects_show', ['id' => $project->getId()]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/projects/add", name="projects_add")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function addProjectAction(Request $request)
    {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_USER')) {
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
            'action' => $this->generateUrl('projects_add'),
            'method' => 'POST',
            'attr' => array('class' => 'formCreateClass'),
        ));
        $form->add('submit', 'submit', array('label' => 'Add Project'));

        return $form;
    }
}
