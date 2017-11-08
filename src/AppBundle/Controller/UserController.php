<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\VoteSettings;
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
        /** @var VoteSettings[] $voteSettings */
        $voteSettings = $this->getDoctrine()->getRepository('AppBundle:VoteSettings')->getVoteSettingByUserCity($user);

        $balanceVotes = [];
        foreach ($voteSettings as $voteSetting) {
            if  ($voteSetting->getStatus() !== VoteSettings::STATUS_ACTIVE ||
                $voteSetting->getLocation()->getCity() !== $user->getCurrentLocation()->getCity()
            ) {
                continue;
            }

            $limitVoteSetting = $voteSetting->getVoteLimits();

            $balanceVotes[$voteSetting->getId().', '.$voteSetting->getTitle()]=
                $limitVoteSetting
                - $this->getDoctrine()->getRepository('AppBundle:User')->getUserVotesBySettingVote($voteSetting, $user);

        }

        $response = [];
        foreach ($balanceVotes as $key=>$balanceVote) {
            $messageLeft = $messageRight = '';
            if ($balanceVote >= 2) {
                $messageLeft .= 'У Вас залишилось ';
                $messageRight .= ' голоси';
            } elseif ($balanceVote == 1) {
                $messageLeft .= 'У Вас залишився ';
                $messageRight .= ' голос';
            } elseif ($balanceVote == 0) {
                $messageLeft .= 'У Вас залишилось ';
                $messageRight .= ' голосів';
            }
            
            $response[$key] = $messageLeft . ' ' . $balanceVote . ' ' . $messageRight;  
        }

        return [
            'response' => $response,
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)
        ];
    }
}
