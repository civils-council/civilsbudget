<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\ProjectType;
use AppBundle\Entity\VoteSettings;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * User controller.
 *
 * @Route("/admin/vote")
 */
class PaperVoteController extends Controller
{
    /**
     * @Route("/user/{user_id}/voting/{voting_id}/new", name="admin_user_new_paper_vote")
     * @ParamConverter("user", class="AppBundle:User", options={"id" = "user_id"})
     * @ParamConverter("voteSettings", class="AppBundle:VoteSettings", options={"id" = "voting_id"})
     * @Method("GET")
     * @Template()
     */
    public function newAction(User $user, VoteSettings $voteSettings, Request $request)
    {
        $balanceVotes = $voteSettings->getVoteLimits() - $this->getDoctrine()->getRepository(User::class)->getUserVotesBySettingVote($voteSettings, $user);

        if ($balanceVotes == 0) {
            return false;
        }
        $addForm = $this->createFormBuilder()
            ->add('blank', null)
            ->add('projects', CollectionType::class, [
                'entry_type' => ProjectType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('Додати', SubmitType::class)
            ->getForm()
        ;

        return [
            'balanceVotes' => $balanceVotes,
            'voteSettings' => $voteSettings,
            'form' => $addForm->createView()
        ];
    }

//    /**
//     * @Route("/", name="admin_users")
//     * @Method("GET")
//     * @Template()
//     */
//    public function indexAction(Request $request)
//    {
//        $em    = $this->getDoctrine()->getManager();
//        $dql   = "SELECT a FROM AppBundle:User a LEFT JOIN a.location l INNER JOIN a.addedByAdmin aba WHERE aba.id = :abaId";
//        $query = $em->createQuery($dql);
//        $query->setParameter('abaId', $this->getUser()->getId());
//
//        $paginator  = $this->get('knp_paginator');
//        $entitiesPagination = $paginator->paginate(
//            $query,
//            $request->query->get('page', 1),
//            20
//        );
//
//        return array(
//            'pagination' => $entitiesPagination,
//        );
//    }


}
