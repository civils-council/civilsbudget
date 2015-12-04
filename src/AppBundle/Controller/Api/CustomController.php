<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CustomController extends Controller
{
    /**
     * @Route("/api/settings", name="api_check")
     * @Method({"GET"})
     */
    public function checkAction(Request $request)
    {
        $check = $this->container->getParameter('bi_oauth_url');
        $clientId = $this->container->getParameter('client_id');
        $base_url = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        return new JsonResponse(
            [
                "bi_auth_url" => $check,
                "bi_client_id" => $clientId,
                "bi_redirect_uri" => $base_url."/api/login"
            ]
        );
    }
}