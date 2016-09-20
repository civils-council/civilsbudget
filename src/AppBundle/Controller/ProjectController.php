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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    /**
     * @Route("/projects", name="projects_list")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction()
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->getProjectShow();
        return [
            'debug' => true,
            'projects' => $projects,
        ];
    }

    /**
     * @Route("/statistics", name="projects_statistics")
     * @Template()
     * @Method({"GET"})
     */
    public function statisticsAction()
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->getProjectStat();
        return [
            'debug' => true,
            'projects' => $projects,
        ];
    }

    /**
     * @Route("/projects/{id}", name="projects_show", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET"})
     */
    public function showProjectAction($id)
    {
        $project = $this->getDoctrine()->getRepository('AppBundle:Project')->getOneProjectShow($id);

        if (!$project) {
            throw new NotFoundHttpException('This project not found in our source');
        }

        if (empty($this->getUser())) {
            $sessionSet = $this->get('app.session')->setSession($project[0][0]->getId());
        }
        return ['projects' => $project];
    }

    /**
     * @Route("/projects/{id}/like", name="projects_like", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function likeAction(Project $project, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $limitVotes = $this->getParameter('limit_votes');
        $form = $this
            ->createForm(new LikeProjectType(), [], [
                'user' => $user,
                'action' => $this->generateUrl('projects_like', ['id' => $project->getId()]),
            ]);

        if ($request->getMethod() == Request::METHOD_POST) {

            if ($user instanceof User) {
                if ($user->getCountVotes() < $limitVotes) {
                    if (!$user->getLikedProjects()->contains($project)) {
                        if (mb_strtolower($user->getLocation()->getCity()) == mb_strtolower($project->getCity())) {
                            $user->setCountVotes($user->getCountVotes() + 1);
                            $user->addLikedProjects($project);
                            $project->addLikedUser($user);
                            $em->flush();
                            $balanceVotes = $limitVotes - $user->getCountVotes();
                            $this->addFlash('success', "Дякуємо за Ваш голос. Ваш голос зараховано на підтримку проекту. У вас залишилось $balanceVotes голосів");
                        }
                        else {
                            $uCity = mb_strtolower($user->getLocation()->getCity());
                            $pCity = mb_strtolower($project->getCity());
                            $this->addFlash('danger', "Цей проект не стосується міста в якому ви зареєстровані. Your city: $uCity. 
                            Project city: $pCity.");
                        }
                    } else {
                        $this->addFlash('danger', 'Ви вже підтримали цей проект.');
                    }
                } else {
                    $this->addFlash('danger', 'Ви вже вичерпали свій ліміт голосів.');
                }
            } else {
                $this->addFlash('danger', 'Ви не маєте доступу до створення проекту.');
            }

            return $this->redirectToRoute('projects_show', ['id' => $project->getId()]);

//
//            $vote = $project->getLikedUsers()->contains($this->getUser());
//            $user_vote = $this->getUser()->getLikedProjects();
//            if ($user_vote != null) {
//                if ($vote != false) {
//                    $this->addFlash('', 'Ви вже підтримали цей проект.');
//                } elseif ($project->getLikedUsers()->contains($this->getUser()) == false && $this->getUser()->getLikedProjects()->getId() == $project->getId()) {
//                    $this->getUser()->setLikedProjects($project);
//                    $this->getDoctrine()->getManager()->flush();
//                    $this->addFlash('', 'Ваший голос зараховано на підтримку проект.');
//                } else {
//                    $this->addFlash('', 'Ви використали свiй голос.');
//                }
//            } else {
//                $this->getUser()->setLikedProjects($project);
//                $this->getDoctrine()->getManager()->flush();
//                $this->addFlash('', 'Ваший голос зараховано на підтримку проект.');
//            }
//
//
//            return $this->redirectToRoute('projects_show', ['id' => $project->getId()]);
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
                $project->setApproved(true);

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
