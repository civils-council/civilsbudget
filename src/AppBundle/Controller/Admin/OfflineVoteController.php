<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\OtpToken;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\UserProject;
use AppBundle\Entity\VoteSettings;
use AppBundle\Form\OtpTokenType;
use AppBundle\Form\VoterType;
use AppBundle\Helper\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OfflineVoteController.
 *
 * @Route("/admin/offline")
 */
class OfflineVoteController extends Controller
{
    /**
     * @Route("", name="offline_dashboard", options={"expose": true})
     * @Template
     *
     * @param Request $request
     *
     * @return Response|array
     */
    public function indexAction(Request $request)
    {
        if ($inn = $request->query->get('inn')) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['inn' => $inn]);

            if (null === $user) {
                $user = new User();
                $user->setInn($inn);
                $this->getDoctrine()->getManager()->persist($user);
            }
        } else {
            $user = new User();
        }

        $form = $this->createForm(VoterType::class, $user, [
            'action' => $this->generateUrl('offline_dashboard', ['inn' => $user->getInn()]),
            'validation_groups' => ['offline_user'],
        ]);
        $form->handleRequest($request);

        $settings = $this->getCurrentVoteSettings();
        if ($user->getInn() && ($settings instanceof VoteSettings && $user->getCurrentLocation()->getCity() !== $settings->getLocation()->getCity())) {
            $this->addFlash('warning-origin', 'Ви можете продовжувати голосовання, але Ваші голоси будуть враховані після пред\'влення посвідчення особи оператору');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('app.user.manager')->sendSmsUserPhone($user);

            return $this->redirectToRoute('offline_sms_checker', ['inn' => $user->getInn()]);
        }

        return ['user' => $user, 'form' => $form->createView()];
    }

    /**
     * @Route("/voter/phone-checker", name="offline_phone_checker", options={"expose": true})
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkPhone(Request $request)
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->get('validator');

        $errors = $validator->validate($request->get('phone'), [new Regex(['pattern' => UserManager::PHONE_PATTERN])]);

        return new Response($errors->count());
    }

    /**
     * @Route("/{inn}/sms", name="offline_sms_checker", requirements={"inn": "\d{10}"})
     * @Template
     *
     * @throws \Exception
     */
    public function smsAction($inn, Request $request)
    {
        $form = $this->createForm(OtpTokenType::class, []);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['inn' => $inn]);
            $existToken = $this->getDoctrine()->getRepository(OtpToken::class)
                ->findOneBy(['token' => $form->get('token')->getData(), 'used' => false]);

            if (null === $existToken || $user !== $existToken->getUser()) {
                $form->get('token')->addError(new FormError('Невірний код - спробуйте ще раз.'));
            } else {
                $existToken->setPermission($form->get('permission')->getName());
                $existToken->setUsed(true);

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('offline_votes', ['inn' => $user->getInn()]);
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/{inn}/votes", name="offline_votes", requirements={"inn": "\d{10}"})
     * @Template()
     *
     * @param User $user
     *
     * @return array
     */
    public function voteAction(User $user)
    {
        return [
            'user' => $user,
            'vote' => $this->getCurrentVoteSettings(),
        ];
    }

    /**
     * @Route("/{inn}/vote-complete", name="offline_votes_complete", requirements={"inn": "\d{10}"}, options={"expose": true})
     * @Method({"POST"})
     *
     * @param User    $user
     * @param Request $request
     *
     * @return Response
     */
    public function completeVoteAction(User $user, Request $request)
    {
        $ids = $request->get('projects', []);

        if (!$ids) {
            throw new BadRequestHttpException('wrong ids');
        }

        /** @var Project[] $checkedProjects */
        $checkedProjects = $this->getDoctrine()->getRepository(Project::class)->findProjectsByIds($ids);

        if (count($ids) !== count($checkedProjects)) {
            throw new BadRequestHttpException('not find all projects');
        }

        /** @var VoteSettings $settings */
        $settings = $checkedProjects[0]->getVoteSetting();


        $forRemoves = $this->getDoctrine()->getRepository(UserProject::class)->findVotesByUserAndNotInProject($user, $checkedProjects, $settings->getProject()->getValues());

        foreach ($forRemoves as $forRemove) {
            $this->getDoctrine()->getManager()->remove($forRemove);
        }

        foreach ($checkedProjects as $checkedProject) {
            $existProject = $this->getDoctrine()->getRepository(UserProject::class)->findOneBy(['user' => $user, 'project' => $checkedProject]);

            if (null === $existProject) {
                $userProject = new UserProject($user, $checkedProject);
                $this->getDoctrine()->getManager()->persist($userProject);
            }
        }

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Дякуємо. Ваш голос по голосуванню '.$settings->getTitle().' прийнятий.');

        return new Response('ok');
    }

    /**
     * @return VoteSettings|null|object
     */
    protected function getCurrentVoteSettings()
    {
        return $this->getDoctrine()->getRepository(VoteSettings::class)->find(8);
    }
}
