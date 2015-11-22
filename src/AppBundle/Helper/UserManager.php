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
        $fp=fopen ("/home/ivan/host/civilsbudget/rsa_key.pem", "r");
        $pub_key=fread ($fp,8192);
        fclose($fp);
        $res = openssl_get_privatekey($pub_key);


        $deceode_clId = base64_decode($data['customer']['clId']);
        $result_field = openssl_private_decrypt($deceode_clId,$clId,$res);

        $deceode_country = base64_decode($data['customer']['addresses'][0]['country']);
        $result_field = openssl_private_decrypt($deceode_country,$country,$res);

        $deceode_state = base64_decode($city = $data['customer']['addresses'][0]['state']);
        $result_field = openssl_private_decrypt($deceode_state,$state,$res);

        $deceode_street = base64_decode($data['customer']['addresses'][0]['street']);
        $result_field = openssl_private_decrypt($deceode_street,$street,$res);

        $deceode_houseNo = base64_decode($data['customer']['addresses'][0]['houseNo']);
        $result_field = openssl_private_decrypt($deceode_houseNo,$houseNo,$res);

        $deceode_flatNo = base64_decode($data['customer']['addresses'][0]['flatNo']);
        $result_field = openssl_private_decrypt($deceode_flatNo,$flatNo,$res);

        $deceode_city = base64_decode($data['customer']['addresses'][0]['city']);
        $result_field = openssl_private_decrypt($deceode_city,$city,$res);


        $deceode_firstName = base64_decode($data['customer']['firstName']);
        $result_field = openssl_private_decrypt($deceode_firstName,$firstName,$res);

        $deceode_lastName = base64_decode($data['customer']['lastName']);
        $result_field = openssl_private_decrypt($deceode_lastName,$lastName,$res);

        $deceode_middleName = base64_decode($data['customer']['middleName']);
        $result_field = openssl_private_decrypt($deceode_middleName,$middleName,$res);

        $deceode_sex = base64_decode($data['customer']['sex']);
        $result_field = openssl_private_decrypt($deceode_sex,$sex,$res);

        $user = $this->em->getRepository('AppBundle:User')->findByClid($clId);
        if (empty($user)) {

            $location = $this->em->getRepository('AppBundle:Location')->findOneByCity($city);
            if(empty($location)) {
                $location = new Location();
            }
            $location
                ->setCity($city)
                ->setAddress($street . ',' . $houseNo . 'appartment' . $flatNo)
                ->setCountry($country)
                ->setDistrict($city)
                ;
            $this->em->persist($location);
            $user = new User();
            $user
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setMiddleName($middleName)
                ->setSex($sex)
                ->setClid($clId);
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
