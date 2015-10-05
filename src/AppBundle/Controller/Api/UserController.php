<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
//        $code = $request->query->get('code');
        $code = 'N4iFZw50015';
        $data = $this->get('app.security.bank_id')->getAccessToken($code);

        $state = $data['state'];
        if ($data['state'] == 'ok') {
            $clid = $data['customer']['clId'];
            $user = $this->get('app.user.manager')->isUniqueUser($clid);
        }
        return new JsonResponse(["data" => $data]);

    }
}