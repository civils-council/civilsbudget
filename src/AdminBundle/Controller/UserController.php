<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\CreateUser;
use AdminBundle\Model\CreateUserModel;
use AppBundle\Entity\Project;
use AppBundle\Entity\VoteSettings;
use AppBundle\Exception\ValidatorException;
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
 * @Route("/admin/users")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     * @param Request $request
     * @return array
     *
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:User a LEFT JOIN a.location l INNER JOIN a.addedByAdmin aba WHERE aba.id = :abaId";
        $query = $em->createQuery($dql);
        $query->setParameter('abaId', $this->getUser()->getId());

        $paginator  = $this->get('knp_paginator');
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

        $errors = $this->get('validator')->validate($entity->getUser());

        if ($form->isValid()
            && $form->get('user')->isValid()
            && $form->get('location')->isValid()
            && count($errors) === 0
        ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity->getLocation());
            $entity->getUser()->setLocation($entity->getLocation());
            $entity->getUser()->setAddedByAdmin($this->getUser());
            $em->persist($entity->getUser());
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
            ->add("Пошук", SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
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
            'form' => $form->createView()
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
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="admin_users_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$user->getLocation()) {
            $this->addFlash('danger', 'User must have location.');
            return $this->redirectToRoute('admin_users');
        }

        if ($user->getLocation() && !$user->getLocation()->getCity()) {
            $this->addFlash('danger', 'User must have city in location.');
            return $this->redirectToRoute('admin_users');
        }

        $deleteForm = $this->createDeleteForm($user->getId());

        /** @var VoteSettings[] $voteSettings */
        $voteSettings = $this->getDoctrine()->getRepository('AppBundle:VoteSettings')->getVoteSettingByUserCity($user);

        $balanceVotes = [];
        foreach ($voteSettings as $voteSetting) {
            $limitVoteSetting = $voteSetting->getVoteLimits();

            $balanceVotes[]= [$voteSetting,
                'balance' => $limitVoteSetting - $this->getDoctrine()->getRepository(User::class)->getUserVotesBySettingVote($voteSetting, $user)];
        }

        $query = $em->getRepository(Project::class)
            ->createQueryBuilder('p')
            ->join('p.likedUsers', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
        ;

        $paginator  = $this->get('knp_paginator');
        $entitiesPagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            70
        );

        return [
            'entity'      => $user,
            'delete_form' => $deleteForm->createView(),
            'pagination' => $entitiesPagination,
            'balanceVotes' => $balanceVotes
        ];
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_users_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $entity */
        $entity = $em->getRepository('AppBundle:User')->findOneBy(['id' => $id]);

        if (!$entity) {
            $this->addFlash('danger', 'No user was found for this id.');
            return $this->redirectToRoute('admin_users');
        }

        $entityUserModel = new CreateUserModel();
        $entityUserModel->setUser($entity);
        $entityUserModel->setLocation($entity->getLocation());
        
        $editForm = $this->createEditForm($entityUserModel);
        $deleteForm = $this->createDeleteForm($id);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_users_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_users_update")
     * @Method("PUT")
     * @Template("AdminBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $entity */
        $entity = $em->getRepository('AppBundle:User')->findOneBy(['id' => $id]);

        if (!$entity) {
            $this->addFlash('danger', 'No user was found for this id.');
            return $this->redirectToRoute('admin_users');
        }

        $entityUserModel = new CreateUserModel();
        $entityUserModel->setUser($entity);
        $entityUserModel->setLocation($entity->getLocation());

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entityUserModel);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_users_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/projects/{id_project}/like/user/{id_user}", name="admin_projects_like", requirements={"id_project" = "\d+", "id_user" = "\d+"})
     * @Template()
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
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
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
//            'cascade_validation' => true,
            'action' => $this->generateUrl('admin_users_update', array('id' => $entity->getUser()->getId())),
            'method' => 'PUT',
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
            'validation_groups' => ['admin_user_post'],
            'constraints' => new \Symfony\Component\Validator\Constraints\Valid(),
//            'cascade_validation' => true,
            'action' => $this->generateUrl('admin_users_create'),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * @return \AppBundle\Application\Project\Project
     */
    private function getProjectApplication()
    {
        return $this->get('app.application.project');
    }
}
