<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Form\ProjectType;
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
            $vote = $project->getLikedUsers()->contains($this->getUser());
            $user_vote = $this->getUser()->getLikedProjects();
            if($user_vote != null) {
                if ($vote != false) {
                    $this->addFlash('warning', 'Ви вже підтримали цей проект.');
                } elseif ($project->getLikedUsers()->contains($this->getUser()) == false && $this->getUser()->getLikedProjects()->getId() == $project->getId()) {
                    $this->getUser()->setLikedProjects($project);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'Ваший голос зараховано на підтримку проект.');
                } else {
                    $this->addFlash('warning', 'Ви використали свiй голос.');
                }
            }else{
                $this->getUser()->setLikedProjects($project);
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
        $project = new Project();
        $form = $this->createCreateForm($project);
            $form->submit($request);
            if ($request->isMethod('POST')) {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $project->setOwner($this->getUser());

                    $em->persist($project);
                    $em->flush();

                    return $this->redirect($this->generateUrl('projects_show', array('id' => $project->getId())));
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
            'action' => $this->generateUrl('projects_add'),
            'method' => 'POST',
            'attr' => array('class' => 'formCreateClass'),
        ));
        $form->add('submit', 'submit', array('label' => 'Add Project'));

        return $form;
    }
}
