<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends Controller
{

    public function showExceptionAction()
    {
//        return new Response('Error');
//        return new RedirectResponse($this->container->getParameter('error_redirect'));
//        $this->addFlash('inforormation', 'sorry, try again later');
//        return $this->redirect($this->generateUrl('homepage'));
        return $this->render('AppBundle::exception.html.twig');
    }
}
