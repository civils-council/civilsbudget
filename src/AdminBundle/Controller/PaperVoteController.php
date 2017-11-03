<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\CreateUser;
use AdminBundle\Model\CreateUserModel;
use AppBundle\Entity\Project;
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
class PaperVoteController extends Controller
{

    /**
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em    = $this->getDoctrine()->getManager();
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


}
