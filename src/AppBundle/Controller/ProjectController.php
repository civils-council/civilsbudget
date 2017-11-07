<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
use AppBundle\Exception\ValidatorException;
use AppBundle\Form\ProjectType;
use AppBundle\Form\LikeProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    const SERVER_ERROR                    = 'Server Error';
    const QUERY_CITY                      = 'city';
    const QUERY_PROJECT_ID                = 'project_id';

    /**
     * @Route("/votings/{id}/projects", name="votings_projects_list")
     * @Template()
     * @Method({"GET"})
     */
    public function listAction(VoteSettings $voteSetting, Request $request)
    {
        $parameterBag = $request->query;
        $parameterBag->add(['voteSetting' => $voteSetting]);
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository(Project::class)->getProjectShow($parameterBag);
        $countAdminVoted = $em->getRepository(User::class)->findCountAdminVotedUsers($parameterBag);
        $countVoted = $em->getRepository(User::class)->findCountVotedUsers($parameterBag);

        return [
            'debug' => true,
            'projects' => $projects,
            'countVoted' => $countVoted,
            'countAdminVoted' => $countAdminVoted,
            'voteSetting' => $voteSetting
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
            'voteSetting' => $em->getRepository('AppBundle:VoteSettings')->getProjectVoteSettingShow($request)
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
        $project = $em->getRepository(Project::class)->getOneProjectShow($id);
        if (!$project) {
            throw new NotFoundHttpException('This project not found in our source');
        }
        if (empty($this->getUser())) {
            $sessionSet = $this->get('app.session')->setSession($project[0]->getId());
        }
        $parameterBag = $request->query;
        $parameterBag->set(ProjectController::QUERY_PROJECT_ID, $id);
        $countAdminVoted = $em->getRepository(User::class)->findCountAdminVotedUsers($parameterBag);
        $countVoted = $em->getRepository(User::class)->findCountVotedUsers($parameterBag);
        $request->attributes->set(ProjectController::QUERY_CITY, $project[0]->getCity());
        $voteSetting = $em->getRepository(VoteSettings::class)->getProjectVoteSettingShow($request);
        return [
            'project' => $project,
            'voteSetting' => $voteSetting,
            'countVoted' => $countVoted,
            'countAdminVoted' => $countAdminVoted,
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
            ->createForm(LikeProjectType::class, [], [
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

            return $this->redirectToRoute('votings_projects_list', ['id' => $project->getVoteSetting()->getId()] );
        }

        return [
            'form' => $form->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository(VoteSettings::class)
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

                return $this->redirect($this->generateUrl('votings_projects_list', ['id' => $project->getVoteSetting()->getId()]));
            }
        }
        return [
            'entity' => $project,
            'form' => $form->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository(VoteSettings::class)
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
        $form = $this->createForm(ProjectType::class, $entity, array(
            'validation_groups' => ['user_post'],
            'action' => $this->generateUrl('projects_add'),
            'method' => 'POST',
            'attr' => array('class' => 'formCreateClass')
        ));
        $form->add('submit', SubmitType::class, array('label' => 'Add Project'));

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
