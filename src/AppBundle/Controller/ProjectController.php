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
     *
     * @param VoteSettings $voteSetting
     * @param Request $request
     *
     * @return array
     */
    public function listAction(VoteSettings $voteSetting, Request $request)
    {
        $parameterBag = $request->query;
        $parameterBag->add(['voteSetting' => $voteSetting]);
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository(Project::class)->getProjectShow($parameterBag);
        $countAdminVotes = $em->getRepository(VoteSettings::class)->countAdminVotesPerVoting($voteSetting);
        $countTotalVotes = $em->getRepository(VoteSettings::class)->countVotesPerVoting($voteSetting);
        $countVoted = $em->getRepository(VoteSettings::class)->countVotedUsersPerVoting($voteSetting);

        return [
            'debug' => true,
            'projects' => $projects,
            'countTotalVotes' => $countTotalVotes,
            'countAdminVotes' => $countAdminVotes,
            'countVoted' => $countVoted,
            'voteSetting' => $voteSetting
        ];
    }

    /**
     * @deprecated
     *
     * Working incorrectly
     *
     * @Route("/statistics", name="projects_statistics")
     * @Template()
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return array
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
     *
     * @param Project $project
     * @param Request $request
     *
     * @return array
     */
    public function showProjectAction(Project $project, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $countAdminVoted = $em->getRepository(Project::class)->countAdminVotesPerProject($project);
        $countVoted = $em->getRepository(Project::class)->countVotesPerProject($project);
        $request->attributes->set(ProjectController::QUERY_CITY, $project->getCity());
        return [
            'project' => $project,
            'voteSetting' => $project->getVoteSetting(),
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
