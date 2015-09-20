<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
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
        return [
            'projects' => $this->getDoctrine()->getRepository('AppBundle:Project')->findAll(),
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
}
