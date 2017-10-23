<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use AppBundle\Form\ConfirmDataType;
use AppBundle\Form\LoginType;
use AppBundle\Form\LoginUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->redirectToRoute('projects_list', ['city' => $request->get('city')]);
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Template()
     * @Method({"GET"})
     */
    public function profileAction()
    {
        return [];
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['clid' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm( LoginType::class, $data, ['action' => $this->generateUrl('login_check')]);

        if ($code = $request->query->get('code')) {
            $accessToken = $this->get('app.security.bank_id')->getAccessToken($code);
            $data = $this->get('app.security.bank_id')->getBankIdUser($accessToken['access_token']);
            if ($data['state'] == 'ok') {
                $usersData = $this->get('app.user.manager')->isUniqueUser($data);
                /** @var User $userResponse */
                $userResponse = $usersData['user'];
                
                return $this->redirectToRoute(
                    'additional_registration',
                    [
                        'id' => $userResponse->getId(),
                        'status' => $usersData['status'],
                        'city' => $request->get('city')
                    ]
                );
            }
        }

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());

            return $this->redirectToRoute('homepage');
        }

        return [
            'debug' => true,
            'form' => $form->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)            
        ];
    }

    /**
     * @Route("/confirm_data/{id}", name="additional_registration")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function additionalRegistrationAction(User $user, Request $request)
    {
        if ($request->get('status') && $request->get('status') == 'new') {
            $em = $this->getDoctrine()->getManager();
            $form = $this->createForm(ConfirmDataType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                $this->setAuthenticateToken($user);
                if ($user->isIsDataPublic()) {
                    
                    /** @var VoteSettings[] $voteSettings */
                    $voteSettings = $this->getDoctrine()->getRepository('AppBundle:VoteSettings')->getVoteSettingByUserCity($user);

                    $balanceVotes = [];
                    foreach ($voteSettings as $voteSetting) {
                        $limitVoteSetting = $voteSetting->getVoteLimits();

                        $balanceVotes[$voteSetting->getTitle()]=
                            $limitVoteSetting
                            - $this->getDoctrine()->getRepository('AppBundle:User')->getUserVotesBySettingVote($voteSetting, $user);

                    }

                    $response = [];
                    foreach ($balanceVotes as $key=>$balanceVote) {
                        $messageLeft = $messageRight = '';
                        if ($balanceVote >= 2) {
                            $messageLeft .= 'У Вас ';
                            $messageRight .= ' голоси';
                        } elseif ($balanceVote == 1) {
                            $messageLeft .= 'У Вас ';
                            $messageRight .= ' голос';
                        } elseif ($balanceVote == 0) {
                            $messageLeft .= 'У Вас ';
                            $messageRight .= ' голосів';
                        }

                        $response[$key] = $messageLeft . ' ' . $balanceVote . ' ' . $messageRight;
                    }
                    
                    $this->get('app.mail.sender')->sendEmail(
                        [$user->getEmail()],
                        'Вітаємо Вас',
                        'AppBundle:Email:new_user.html.twig',
                        [
                            'user' => $user,
                            'response' => $response,
                            'homePage' => $this->get('router')->generate(
                                'projects_list',
                                ['city' => $user->getLocation()->getCity()],
                                UrlGeneratorInterface::ABSOLUTE_URL),
                        ]
                    );                    
                }

                // if you put a check before send email, during registration of the project will not be sending mail
                if ($this->get('app.session')->check()) {

                    $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($this->get('app.session')->getProjectId());
                    $flashMessage = $this->get('app.like.service')->execute($user, $project);
                    //TODO check return value
                    $flashMessage['text']='Ви успішно зареєструвались. ' . $flashMessage['text'];
                    $this->addFlash($flashMessage['status'], $flashMessage['text']);

                    return $this->redirect($this->generateUrl('projects_show', ['id' => $this->get('app.session')->getProjectId()]));
                }

                if ($error = $this->get('security.authentication_utils')->getLastAuthenticationError()) {
                    $this->addFlash('danger', $error->getMessage());

                    return $this->redirectToRoute('homepage');
                }
                $this->addFlash('info', 'Дякуємо, Ви успішно зареєструвались');
                return $this->redirectToRoute('homepage');
            }
            return [
                'form' => $form->createView(),
                'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                    ->getProjectVoteSettingShow($request)
            ];
        } else {
            if($this->get('app.session')->check()) {
                $this->setAuthenticateToken($user);
                $project = $this->getDoctrine()->getRepository('AppBundle:Project')->find($this->get('app.session')->getProjectId());
                $flashMessage = $this->get('app.like.service')->execute($user, $project);
                //TODO check return value
                $this->addFlash($flashMessage['status'], $flashMessage['text']);

                return $this->redirect($this->generateUrl('projects_show', ['id' => $this->get('app.session')->getProjectId()]));
            } else {
                $this->setAuthenticateToken($user);
                return $this->redirectToRoute('homepage');
            }
        }
    }

    /**
     * @Route("/loginIncluded", name="login_included")
     * @Template()
     */
    public function loginIncludedAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $data = ['secret' => $authenticationUtils->getLastUsername()];
        $form = $this->createForm(LoginType::class, $data, ['action' => $this->generateUrl('login_check')]);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());

            return $this->redirectToRoute('additional_registration');
        }

        return [
            'form' => $form->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)            
        ];
    }


    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="update_user")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodeDirectorySpecialities entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)
        );
    }


    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/edit/submit", name="update_date_user")
     * @Method("PUT")
     * @Template("AppBundle:Default:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CodeDirectorySpecialities entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();

//            return $this->redirect($this->generateUrl('login').'#'.$entity->getClid().'');
            return $this->redirectToRoute('homepage');
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'voteSetting' => $this->getDoctrine()->getRepository('AppBundle:VoteSettings')
                ->getProjectVoteSettingShow($request)            
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(LoginUserType::class, $entity, array(
            'action' => $this->generateUrl('update_date_user', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/verify", name="verify")
     */
    public function verifyAction()
    {
        $result = $this->get('app.mail.sender')->verifyEmail();

        dump($result);

        return new \Symfony\Component\HttpFoundation\Response('ok');

    }

    /**
     * @param User $user
     * @return void
     */
    public function setAuthenticateToken(User $user)
    {
        $token = new PreAuthenticatedToken($user, $user->getClid(), 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
    }

}
