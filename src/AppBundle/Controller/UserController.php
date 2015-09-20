<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @Route("/users/{id}", name="users_profile", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template()
     */
    public function showUserAction(User $user)
    {
        return ['user' => $user];
    }
}
