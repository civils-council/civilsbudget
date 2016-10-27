<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
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
                $response = $this->get('app.user.manager')->isUniqueUser($data);
                /** @var User $user */
                $user = $response['user'];
                $this->getAuthenticator()->addAuth($user);
                /** @var VoteSettings[] $voteSettings */
                $voteSettings = $this->getDoctrine()->getRepository('AppBundle:VoteSettings')->getVoteSettingByUserCity($user);

                $balanceVotes = [];
                foreach ($voteSettings as $voteSetting) {
                    $limitVoteSetting = $voteSetting->getVoteLimits();

                    $balanceVotes[$voteSetting->getTitle()]=
                        $limitVoteSetting
                        - $this->getDoctrine()->getRepository('AppBundle:User')->getUserVotesBySettingVote($voteSetting, $user);
                }
                
                return new JsonResponse(
                    [
                        "id" => $user->getId(),
                        "full_name" => $user->getFullName(),
                        "clid" => $user->getClid(),
                        "voted_project" => $balanceVotes
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
        if ($code = $request->query->get('code')) {
            $accessToken = $this->get('app.security.bank_id')->getApiAccessToken($code);
            return new JsonResponse($accessToken);
        }else{
            throw new NotFoundHttpException('No find code in request');
        }

    }

    /**
     * @return \AppBundle\Security\Authenticator
     */
    private function getAuthenticator()
    {
        return $this->get('app.authenticator');
    }
}