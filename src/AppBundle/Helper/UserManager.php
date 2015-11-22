<?php

namespace AppBundle\Helper;

use AppBundle\Entity\Location;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserManager
{
    protected $_templateEngine;
    protected $em;

    private $rootDir;

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function __construct($_templateEngine, EntityManager $em)
    {
        $this->templateEngine = $_templateEngine;
        $this->em = $em;
    }

    public function isUniqueUser($data)
    {
        $user = $this->em->getRepository('AppBundle:User')->findByClid($data['customer']['clId']);
        if (empty($user)) {
            $location = new Location();
            $location
                ->setCity($data['customer']['addresses'][0]['state'])
                ->setAddress($data['customer']['addresses'][0]['street'] . ',' . $data['customer']['addresses'][0]['houseNo'] . 'appartment' . $data['customer']['addresses'][0]['flatNo'])
                ->setCountry($data['customer']['addresses'][0]['country'])
                ->setDistrict($data['customer']['addresses'][0]['area'])
                ;
            $this->em->persist($location);
            $user = new User();
            $user
                ->setFirstName($data['customer']['firstName'])
                ->setLastName($data['customer']['lastName'])
                ->setMiddleName($data['customer']['middleName'])
                ->setClid($data['customer']['clId']);
            $user->setLocation($location);

            $this->em->persist($user);
            $this->em->flush();
        }
        else
        {
            //ToDo add user in security context
        }
        return $user;
    }
}
