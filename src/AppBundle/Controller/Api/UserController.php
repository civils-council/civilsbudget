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
     * @Route("/api/authorization", name="api_projects_list")
     * @Method({"GET", "POST"})
     */
    public function authAction(Request $request)
    {
//        $code = $request->query->get('code');
        $code = 'tRMFjd54438';
        $data = $this->get('app.security.bank_id')->getAccessToken($code);
        $json = serialize($data);
        return new JsonResponse(["data" => $json]);
    }
}