<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param User $user
     * @param VoteSettings $voteSettings
     * @param Request $request
     *
     * @return array | bool
     */
    public function newAction(User $user, VoteSettings $voteSettings, Request $request)
    {
        $balanceVotes = $voteSettings->getVoteLimits() - $this->getDoctrine()->getRepository(User::class)->getUserVotesBySettingVote($voteSettings, $user);

        if ($balanceVotes == 0) {
            return false;
        }

        $projects = $this->getDoctrine()->getManager()->getRepository(Project::class)->createQueryBuilder('p')
            ->andWhere('p.voteSetting = :vs')
            ->setParameter('vs', $voteSettings)
            ->getQuery()
            ->getResult();

        $addForm = $this->createFormBuilder()
            ->add('blank', null)
            ->add('projects', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Project::class,
                    'choices' => $projects
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('Додати', SubmitType::class)
            ->getForm()
        ;

        $addForm->handleRequest($request);

        if ($addForm->isSubmitted()) {
            $data = $addForm->getData();
            dump($data);die();
//            return [
//                'balanceVotes' => $balanceVotes,
//                'voteSettings' => $voteSettings,
//                'form' => $addForm->createView()
//            ];
        }

        return [
            'balanceVotes' => $balanceVotes,
            'voteSettings' => $voteSettings,
            'form' => $addForm->createView()
        ];
    }

}
