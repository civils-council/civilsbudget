<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidatorException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProjectController extends Controller
{
    const SERVER_ERROR                    = 'Server Error';
    
    /**
     * @Route("/api/projects", name="api_projects_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findAll();

        $project_array = [];
        for ($i = 0; $i < count($projects); $i++) {
            $em = $this->getDoctrine()->getManager();
            if(!empty($request->get('clid'))){
                $voute = $projects[$i]->getLikedUsers()->contains($em->getRepository('AppBundle:User')->findOneByClid($request->get('clid')));
            }else{
                $voute = false;
            }

            $project_array[$i]["vote"] = $voute;
            $project_array[$i]["id"] = $projects[$i]->getId();
            $project_array[$i]["title"] = $projects[$i]->getTitle();
            $project_array[$i]["description"] = $projects[$i]->getDescription();
            $project_array[$i]["charge"] = $projects[$i]->getCharge();
            $project_array[$i]["source"] = $projects[$i]->getSource();
            $project_array[$i]["picture"] = $projects[$i]->getPicture();
            $project_array[$i]["createdAt"] = $projects[$i]->getCreateAt()->format('c');
            $project_array[$i]["likes_count"] = $projects[$i]->getLikedUsers()->count();
            $project_array[$i]["owner"] = $projects[$i]->getOwner()->getFullName();
            $project_array[$i]["avatar_owner"] = $projects[$i]->getOwner()->getAvatar();

        }

        return new JsonResponse(["projects" => $project_array]);
    }

    /**
     * @Route("/api/projects/{id}", name="api_projects_show", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function showProjectAction(Request $request, Project $project)
    {
        $em = $this->getDoctrine()->getManager();
        if(!empty($request->get('clid'))){
            $voute = $project->getLikedUsers()->contains($em->getRepository('AppBundle:User')->findOneByClid($request->get('clid')));
        }else{
            $voute = false;
        }

        return new JsonResponse(
            [
                 "vote" => $voute,
                 "id" => $project->getId(),
                 "title" => $project->getTitle(),
                 "description" => $project->getDescription(),
                 "charge" => $project->getCharge(),
                 "source" => $project->getSource(),
                 "picture" => $project->getPicture(),
                 "createdAt" => $project->getCreateAt()->format('c'),
                 "likes_count" => $project->getLikedUsers()->count(),
                 "owner" => $project->getOwner()->getFullName(),
                 "avatar_owner" => $project->getOwner()->getAvatar(),
            ]
        );
    }

    /**
     * @Route("/api/projects/{id}/like", name="api_projects_like", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * @ParamConverter("project", class="AppBundle:Project")
     */
    public function likeAction(Project $project, Request $request)
    {
        $user = $this->getUser();
        if ($request->getMethod() == Request::METHOD_POST) {

            try {
                return new JsonResponse('success', $this->getProjectApplication()->crateUserLike(
                    $user,
                    $project
                ));
                
            } catch (ValidatorException $e) {
                return new JsonResponse('danger', $e->getMessage());
            } catch (\Exception $e) {
                return new JsonResponse('danger', self::SERVER_ERROR);
            }
        }

        return new JsonResponse(['project' => $project]);
    }

    /**
     * @return \AppBundle\Application\Project\Project
     */
    private function getProjectApplication()
    {
        return $this->get('app.application.project');
    }    
}