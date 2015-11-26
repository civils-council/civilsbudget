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
        $code = null;

        if ($request->getMethod() == Request::METHOD_POST) {
            $content = $this->get("request")->getContent();
            if (!empty($content))
            {
                $params = json_decode($content, true);
                $code = $params['code'];
            }else{
                return new JsonResponse(["code:" => 404, "message" => "Not find data"]);
            }
        }

        if(!empty($request->query->get('code'))){
            $code = $request->query->get('code');
        }
        if(!empty($code)) {
            $data = $this->get('app.security.bank_id')->getAccessToken($code);
            if ($data['state'] == 'ok') {
                $user = $this->get('app.user.manager')->isUniqueUser($data);
                return new JsonResponse(["user" => $user[0]]);
            }
        }
        return new JsonResponse(["code:" => 401, "message" => "Wrong authorization."]);
    }
}