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

    /**
     * @Route("/users/{id}/count_votes", name="user_count_votes", requirements={"id" = "\d+"})
     * @Template()
     */
    public function countVotesAction(User $user)
    {
        $userCountVotes = ($user->getCountVotes())?:0;

        $balanceVotes = $this->getParameter('limit_votes') - $userCountVotes;
        $message = 'У Вас';
        // TODO If the vote is more than 5 - will test endings (залишилось 5 голосів)
        if ($balanceVotes >= 2) {
            $message .= " залишилось $balanceVotes голоси";
        } elseif ($balanceVotes == 1) {
            $message .= ' залишився 1 голос';
        } elseif ($balanceVotes == 0) {
            $message .= ' залишилось 0 голосів';
        }


        return ['message' => $message];
    }
}
