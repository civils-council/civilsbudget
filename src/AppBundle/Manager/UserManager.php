<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Location;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class UserManager
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getUser(array $userData)
    {
        if (!array_key_exists('customer', $userData) && !array_key_exists('clId', $userData['customer'])) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        $data = $userData['customer'];
        if ($existUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['secret' => $data['clId']])) {
            return $existUser;
        }

        $locationData = $data['addresses'][0];
        $location = new Location();
        $location->setCity($locationData['city']);
        $location->setCityRegion($locationData['state']);
        $location->setCountry($locationData['country']);
        $location->setAddress('вул. ' . $locationData['street'] . ', б. ' . $locationData['houseNo'] . '/' . $locationData['flatNo']);

        $user = new User();
        $user->setSecret($data['clId']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setMiddleName($data['middleName']);
        $user->setLocation($location);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $user;
    }
}

