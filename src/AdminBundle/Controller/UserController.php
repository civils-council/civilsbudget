<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\CreateUser;
use AdminBundle\Model\CreateUserModel;
use AppBundle\Entity\Location;
use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use AppBundle\Exception\ValidatorException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $dql = 'SELECT a FROM AppBundle:User a INNER JOIN a.addedByAdmin aba WHERE aba.id = :abaId';
        $query = $em->createQuery($dql);
        $query->setParameter('abaId', $this->getUser()->getId());

        $paginator = $this->get('knp_paginator');
        $entitiesPagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            20
        );

        return array(
            'pagination' => $entitiesPagination,
        );
    }

    /**
     * Search User entity by INN.
     *
     * @param string  $inn
     * @param Request $request
     *
     * @Route("/{inn}/search", name="admin_users_search_by_inn")
     * @Method("GET")
     *
     * @return array | JsonResponse
     *
     * @Template("@Admin/User/ajax-search.html.twig")
     */
    public function searchByInnAction(string $inn, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => 'You are not able to access this resource!'], 400);
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $voter = $em->getRepository('AppBundle:User')->findOneBy(['inn' => $inn]);

        return ['voter' => $voter];
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="admin_users_create")
     * @Method("POST")
     * @Template("AdminBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CreateUserModel();
        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);
        $validationGroups = ['admin_user_post'];

        if (!$this->isGranted('ROLE_REGIONAL_ADMIN')) {
            $validationGroups[] = 'user_inn_numeric';
        }

        $errors = $this->get('validator')->validate($entity->getUser(), null, $validationGroups);
        $errors->addAll($this->get('validator')->validate($entity->getLocation(), null, ['admin_user_post']));

        if ($form->isValid()
            && $form->get('user')->isValid()
            && $form->get('location')->isValid()
            && 0 === count($errors)
        ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity->getLocation());
            $entity->getUser()->addLocation($entity->getLocation());
            $entity->getUser()->setAddedByAdmin($this->getUser());
            if (null === $entity->getUser()->getBirthday() && $this->getBirthDayFromInn($entity->getUser()->getInn())) {
                $entity->getUser()->setBirthday(
                    $this->getBirthDayFromInn($entity->getUser()->getInn())->format('Y-m-d')
                );
            }
            $em->persist($entity->getUser());
            $entity->getLocation()->setUser($entity->getUser())->setAddedByAdmin($this->getUser());
            $em->flush();
            $user = $entity->getUser();

            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'errors' => $errors,
        );
    }

    /**
     * @Route("/find", name="admin_user_find")
     * @Template()
     */
    public function findAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('inn', null)
            ->add('Пошук', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['inn' => $data['inn']]);

            if ($user) {
                return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()]);
            } else {
                return $this->redirectToRoute('admin_users_new');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="admin_users_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CreateUserModel();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'errors' => null,
        );
    }

    /**
     * @Route("/{id}", name="admin_users_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(User $user, Request $request)
    {
        if (!$user->getCurrentLocation()) {
            $this->addFlash('danger', 'User must have location.');

            return $this->redirectToRoute('admin_users');
        }

        if ($user->getCurrentLocation() && !$user->getCurrentLocation()->getCity()) {
            $this->addFlash('danger', 'User must have city in location.');

            return $this->redirectToRoute('admin_users');
        }

        $deleteForm = $this->createDeleteForm($user->getId());

        /** @var VoteSettings[] $voteSettings */
        $voteSettings = $this->getDoctrine()->getRepository('AppBundle:VoteSettings')->getVoteSettingByUserCity($user, true);

        $balanceVotes = [];
        foreach ($voteSettings as $voteSetting) {
            $limitVoteSetting = $voteSetting->getVoteLimits();

            $balanceVotes[] = [$voteSetting,
                'balance' => $limitVoteSetting - $this->getDoctrine()->getRepository(User::class)->getUserVotesBySettingVote($voteSetting, $user), ];
        }

        $query = $this->getDoctrine()->getRepository(Project::class)
            ->createQueryBuilder('p')
            ->select('p.id',
                'p.title as project_title',
                'vs.title as vote_title',
                'up.blankNumber',
                'up.createAt',
                'a.firstName',
                'a.lastName'
            )
            ->join('p.userProjects', 'up')
            ->join('p.voteSetting', 'vs')
            ->leftJoin('up.addedBy', 'a')
            ->andWhere('up.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
        ;

        $entitiesPagination = $this->get('knp_paginator')->paginate(
            $query,
            $request->query->get('page', 1),
            10
        );

        return [
            'entity' => $user,
            'delete_form' => $deleteForm->createView(),
            'pagination' => $entitiesPagination,
            'balanceVotes' => $balanceVotes,
        ];
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_users_edit")
     * @Method("GET")
     * @Template()
     *
     * @param User    $user
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function editAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entityUserModel = new CreateUserModel();
        $entityUserModel->setUser($user);
        $entityUserModel->setLocation($user->getCurrentLocation());

        $editForm = $this->createEditForm($entityUserModel);
        $deleteForm = $this->createDeleteForm($user->getId());

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
        }

        return array(
            'entity' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errors' => [],
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_users_update")
     * @Method("PUT")
     * @Template("AdminBundle:User:edit.html.twig")
     *
     * @param User    $user
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function updateAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted(
            ['ROLE_REGIONAL_ADMIN', 'ROLE_SUPER_ADMIN'],
            $user,
            'You cannot edit this item.'
        );

        $em = $this->getDoctrine()->getManager();

        $entityUserModel = new CreateUserModel();
        $entityUserModel->setUser($user);
        $entityUserModel->setLocation(new Location());

        $deleteForm = $this->createDeleteForm($user->getId());
        $editForm = $this->createEditForm($entityUserModel);

        $editForm->handleRequest($request);

        $errors = $this->get('validator')->validate($entityUserModel->getUser(), null, ['admin_user_put']);

        if ($editForm->isValid() && 0 === count($errors)) {
            $newLocation = $entityUserModel->getLocation();
            if ($this->isNewLocation($newLocation, $user->getCurrentLocation())) {
                $em->persist($entityUserModel->getLocation());
                $user->addLocation($entityUserModel->getLocation()->setUser($user)->setAddedByAdmin($this->getUser()));
            }
            $em->flush();

            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
        }

        return array(
            'entity' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'errors' => $errors,
        );
    }

    /**
     * @Route("/projects/{id_project}/like/user/{id_user}", name="admin_projects_like", requirements={"id_project" = "\d+", "id_user" = "\d+"})
     * @Template()
     *
     * @deprecated
     *
     * @Method({"GET"})
     * @ParamConverter("project", class="AppBundle:Project", options={"mapping": {"id_project": "id"}})
     * @ParamConverter("user", class="AppBundle:User", options={"mapping": {"id_user": "id"}})
     */
    public function likeAction(Project $project, User $user, Request $request)
    {
        try {
            $this->addFlash('success', $this->getProjectApplication()->crateUserLike(
                $user,
                $project
            ));
        } catch (ValidatorException $e) {
            $this->addFlash('danger', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('danger',
                $e->getMessage()
            );
        }

        return $this->redirectToRoute('admin_users_show', ['id' => $user->getId()]);
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="admin_users_delete")
     * @Method("DELETE")

     *
     * @param User    $user
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', $user, 'You cannot delete this item.');

        $form = $this->createDeleteForm($user->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_users'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_users_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param CreateUserModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CreateUserModel $entity)
    {
        $form = $this->createForm(CreateUser::class, $entity, array(
            'validation_groups' => ['admin_user_put'],
            'action' => $this->generateUrl('admin_users_update', array('id' => $entity->getUser()->getId())),
            'method' => 'PUT',
            'cities' => $this->getAvailableCities(),
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param CreateUserModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CreateUserModel $entity)
    {
        $form = $this->createForm(CreateUser::class, $entity, array(
            'action' => $this->generateUrl('admin_users_create'),
            'method' => 'POST',
            'cities' => $this->getAvailableCities(),
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * @return array|null
     */
    public function getAvailableCities(): ?array
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return null;
        }

        return [$this->getUser()->getCity()];
    }

    /**
     * @return \AppBundle\Application\Project\Project
     */
    private function getProjectApplication()
    {
        return $this->get('app.application.project');
    }

    /**
     * @param string $inn
     *
     * @return \DateTime | null
     */
    private function getBirthDayFromInn(string $inn): ?\DateTime
    {
        if (!is_numeric($inn)) {
            return null;
        }
        $day = substr($inn, 0, 5);

        return (new \DateTime('1899-12-31'))->modify("+$day day");
    }

    /**
     * @param Location $locationNew
     * @param Location $locationOld
     *
     * @return bool
     */
    private function isNewLocation(Location $locationNew, Location $locationOld): bool
    {
        if ($locationOld->getAddress() === $locationNew->getAddress() &&
            $locationOld->getCity() === $locationNew->getCity() &&
            $locationOld->getCityObject() === $locationNew->getCityObject()
        ) {
            return false;
        }

        return true;
    }
}
