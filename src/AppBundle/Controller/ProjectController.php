<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use AppBundle\Form\ProjectType;
use AppBundle\Form\LikeProjectType;
use Doctrine\Common\Collections\Expr\Expression;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    const SERVER_ERROR                    = 'Server Error';
    const QUERY_CITY                      = 'city';
    
    /**
     * @Route("/projects", name="projects_list")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $parameterBag = $request->query;

        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('AppBundle:Project')->getProjectShow($parameterBag);
        $countVoted = $em->getRepository('AppBundle:User')->findCountVotedUsers($parameterBag);
        $vote =  $em->getRepository('AppBundle:VoteSettings')
            ->getProjectVoteSettingShow($request);
        return [
            'debug' => true,
            'projects' => $projects,
            'countVoted' => $countVoted,
            'voteSetting' => $vote
        ];
    }

    /**
     * @Route("/statistics", name="projects_statistics")
     * @Template()
     * @Method({"GET"})
     */
    public function statisticsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->getProjectStat();
        return [
            'debug' => true,
            'projects' => $projects,
            'voteSetting' => $em->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)            
        ];
    }

    /**
     * @Route("/projects/{id}", name="projects_show", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET"})
     */
    public function showProjectAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('AppBundle:Project')->getOneProjectShow($id);

        if (!$project) {
            throw new NotFoundHttpException('This project not found in our source');
        }

        if (empty($this->getUser())) {
            $sessionSet = $this->get('app.session')->setSession($project[0][0]->getId());
        }
        return [
            'projects' => $project,
            'voteSetting' => $em->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)
        ];
    }

    /**
     * @Route("/projects/{id}/like", name="projects_like", requirements={"id" = "\d+"})
     * @Template()
     * @Method({"GET", "POST"})
     */
    public function likeAction(Project $project, Request $request)
    {
        $user = $this->getUser();
        $form = $this
            ->createForm(new LikeProjectType(), [], [
                'user' => $user,
                'action' => $this->generateUrl('projects_like', ['id' => $project->getId()]),
            ]);
        
        if ($request->getMethod() == Request::METHOD_POST) {

            try {
                $this->addFlash('success', $this->getProjectApplication()->crateUserLike(
                    $user,
                    $project
                ));
                
            } catch (ValidatorException $e) {
                $this->addFlash('danger', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('danger',
                    self::SERVER_ERROR
                );
            }

            return $this->redirectToRoute('projects_list');
        }

        return [
            'form' => $form->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)
        ];
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
                $project->setApproved(false);
                if ($form->getData()->getPicture() instanceof UploadedFile) {
                    $project->setPicture($this->get('app.file_uploader')->uploadImage($form->getData()->getPicture()));
                }

                $em->persist($project);
                $em->flush();

                $this->addFlash('success', 'Проект був успішно створений. Після перегляду адміністратором, його буде опрелюднено.');

                return $this->redirect($this->generateUrl('projects_list'));
            }
        }
        return [
            'entity' => $project,
            'form' => $form->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)            
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
            'validation_groups' => ['user_post'],
            'action' => $this->generateUrl('projects_add'),
            'method' => 'POST',
            'attr' => array('class' => 'formCreateClass')
        ));
        $form->add('submit', 'submit', array('label' => 'Add Project'));

        return $form;
    }

    /**
     * @return \AppBundle\Application\Project\Project
     */
    private function getProjectApplication()
    {
        return $this->get('app.application.project');
    }
}
