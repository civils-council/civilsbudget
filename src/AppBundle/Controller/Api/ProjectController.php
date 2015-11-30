<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProjectController extends Controller
{
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
        $em = $this->getDoctrine()->getManager();
        $clid = null;
        $content = $this->get("request")->getContent();
        if (!empty($content))
        {
            $params = json_decode($content, true);
            $clid = $params['clid'];
        }
        if(empty($clid)) {
            $clid = $this->get('request')->request->get('clid');
        }

        if ($request->getMethod() == Request::METHOD_POST) {
            $user = $em->getRepository('AppBundle:User')->findOneByClid($clid);
            if(!empty($user)) {
                $user_vote = $user->getLikedProjects();
                if ($user_vote != null) {
                    if ($project->getLikedUsers()->contains($user)) {
                        return new JsonResponse(['warning' => 'Ви вже підтримали цей проект.']);
                    } elseif ($project->getLikedUsers()->contains($user) == false && $user->getLikedProjects()->getId() == $project->getId()) {
                        $user->setLikedProjects($project);
                        $this->getDoctrine()->getManager()->flush();

                        return new JsonResponse(['success' => 'Ваший голос зараховано на підтримку проект.']);
                    } else {
                        return new JsonResponse(['warning' => 'Ви використали свiй голос.']);
                    }
                } else {
                    $user->setLikedProjects($project);
                    $this->getDoctrine()->getManager()->flush();
                    return new JsonResponse(['success' => 'Ваший голос зараховано на підтримку проект.']);
                }
            }else{
                return new JsonResponse(['warning' => 'Такого користувача не iснуэ.']);
            }
        }

        return new JsonResponse(['project' => $project]);
    }
}