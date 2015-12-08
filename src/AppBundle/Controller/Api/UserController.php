<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/api/authorization", name="api_authorization")
     * @Method({"GET", "POST"})
     */
    public function authorizationAction(Request $request)
    {
        $code = $request->query->get('code');
        if(!empty($code)) {
            $data = $this->get('app.security.bank_id')->getBankIdUser($code);
            if ($data['state'] == 'ok') {
                $user = $this->get('app.user.manager')->isUniqueUser($data);
                $vote = $user[0]->getLikedProjects();
                if(!is_null($vote)){
                    $voted_project = $user[0]->getLikedProjects()->getId();
                }else{
                    $voted_project = false;
                }
                return new JsonResponse(
                    [
                        "id" => $user[0]->getId(),
                        "full_name" => $user[0]->getFullName(),
                        "clid" => $user[0]->getClid(),
                        "voted_project" => $voted_project
                    ]
                );
            }
        }
        return new JsonResponse(["code:" => 401, "message" => "Wrong authorization."]);
    }

    /**
     * @Route("/api/login", name="api_login")
     * @Method({"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        $code = $request->query->get('code');

        if ($code = $request->query->get('code')) {
            $accessToken = $this->get('app.security.bank_id')->getApiAccessToken($code);
            return new JsonResponse($accessToken);
        }else{
            throw new NotFoundHttpException('No find code in request');
        }

    }


}