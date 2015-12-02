<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CustomController extends Controller
{
    /**
     * @Route("/api/check", name="api_check")
     * @Method({"GET"})
     */
    public function checkAction(Request $request)
    {
        $check = $this->container->getParameter('bi_oauth_url');
        return new JsonResponse(["bid_auth_url" => $check]);
    }
}