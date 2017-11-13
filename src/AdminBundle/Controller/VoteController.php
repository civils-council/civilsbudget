<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\UserProject;
use AppBundle\Entity\VoteSettings;
use AppBundle\Exception\ValidatorException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
class VoteController extends Controller
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
     * @return array | bool | RedirectResponse
     */
    public function newAction(User $user, VoteSettings $voteSettings, Request $request)
    {
        //TODO: this action needs refactoring
        $balanceVotes = $voteSettings->getVoteLimits() -
            $this->getDoctrine()->getRepository(User::class)
                ->getUserVotesBySettingVote($voteSettings, $user);

        if ($balanceVotes == 0) {
            return false;
        }

        $projects = $this->getDoctrine()->getManager()->getRepository(Project::class)->createQueryBuilder('p')
            ->andWhere('p.voteSetting = :vs')
            ->setParameter('vs', $voteSettings)
            ->getQuery()
            ->getResult();

        $addForm = $this->createFormBuilder()
            ->add('blankNumber', null, ['label' => 'Номер бланку'])
            ->add('projects', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Project::class,
                    'choices' => $projects
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Проекти'
            ])
            ->add('Додати', SubmitType::class)
            ->getForm()
        ;

        $addForm->handleRequest($request);

        if ($addForm->isSubmitted()) {
            $data = $addForm->getData();

            if (null === $blankNumber = $data['blankNumber']) {
                $this->addFlash('danger', 'Номер паперового бланку пустий');
                return $this->redirectToRoute('admin_user_new_paper_vote', [
                    'user_id' => $user->getId(),
                    'voting_id' => $voteSettings->getId()
                ]);
            }

            $projects = $data['projects'];

            foreach ($projects as $project) {
                try {
                    $this->addFlash('success', $this->getProjectApplication()->crateUserLike(
                        $user,
                        $project,
                        $this->getUser(),
                        $blankNumber
                    ));

                } catch (ValidatorException $e) {
                    $this->addFlash('danger', $e->getMessage());
                } catch (\Exception $e) {
                    $this->addFlash('danger',
                        $e->getMessage()
                    );
                }
            }

            return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()]);
        }

        return [
            'balanceVotes' => $balanceVotes,
            'voteSettings' => $voteSettings,
            'form' => $addForm->createView()
        ];
    }

    /**
     * @Route("/list/{id}", name="admin_voted_list")
     * @Method({"GET"})
     * @Template()
     *
     * @param VoteSettings $voteSettings
     * @param Request $request
     *
     * @return array
     */
    public function votedAction(VoteSettings $voteSettings, Request $request)
    {
        $userProject = $this->getDoctrine()
            ->getRepository(UserProject::class)
            ->getVotesListByVoteSetting($voteSettings);

        $pagination = $this->get('knp_paginator')->paginate(
            $userProject,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 100),
            ['distinct' => false]
        );

        return ['pagination' => $pagination];
    }

    /**
     * @Route("/list", name="admin_voting_list")
     * @Method({"GET"})
     * @Template()
     *
     * @return array | bool | RedirectResponse
     */
    public function listAction()
    {
        $votingList = $this->get('app.model.voting')->getVotingList();

        return [
            'debug' => true,
            "votingList" => $votingList
        ];
    }

    /**
     * @return \AppBundle\Application\Project\Project
     */
    private function getProjectApplication(): \AppBundle\Application\Project\Project
    {
        return $this->get('app.application.project');
    }
}
