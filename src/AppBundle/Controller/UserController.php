<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users/{id}", name="users_profile", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template()
     */
    public function showUserAction(User $user, Request $request)
    {
        return [
            'user' => $user,
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)            
        ];
    }

    /**
     * @Route("/users/{id}/count_votes", name="user_count_votes", requirements={"id" = "\d+"})
     * @Template()
     */
    public function countVotesAction(User $user, Request $request)
    {
        $userCountVotes = ($user->getCountVotes())?:0;

        $balanceVotes = $this->getParameter('limit_votes') - $userCountVotes;
        $messageLeft = $messageRight = '';

        // TODO If the vote is more than 5 - will test endings (залишилось 5 голосів)
        if ($balanceVotes >= 2) {
            $messageLeft .= 'У Вас залишилось ';
            $messageRight .= ' голоси';
        } elseif ($balanceVotes == 1) {
            $messageLeft .= 'У Вас залишився ';
            $messageRight .= ' голос';
        } elseif ($balanceVotes == 0) {
            $messageLeft .= 'У Вас залишилось ';
            $messageRight .= ' голосів';
        }

        return [
            'message_left' => $messageLeft,
            'balanceVotes' => $balanceVotes,
            'message_right' => $messageRight,
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)
        ];
    }
}
