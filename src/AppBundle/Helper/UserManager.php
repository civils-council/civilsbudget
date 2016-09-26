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
        $fp = fopen ($this->rootDir.'/rsa_key.pem', "r");
        $pub_key=fread ($fp,8192);
        fclose($fp);
        $res = openssl_get_privatekey($pub_key);

        if(array_key_exists('addresses', $data['customer']) == true) {
            if (array_key_exists('country', $data['customer']['addresses'][0]) == true) {
                $deceode_country = base64_decode($data['customer']['addresses'][0]['country']);
                $result_field = openssl_private_decrypt($deceode_country, $country, $res);
            }

            if (array_key_exists('state', $data['customer']['addresses'][0]) == true) {
                $deceode_state = base64_decode($city = $data['customer']['addresses'][0]['state']);
                $result_field = openssl_private_decrypt($deceode_state, $state, $res);
            }

            if (array_key_exists('street', $data['customer']['addresses'][0]) == true) {
                $deceode_street = base64_decode($data['customer']['addresses'][0]['street']);
                $result_field = openssl_private_decrypt($deceode_street, $street, $res);
            }

            if (array_key_exists('houseNo', $data['customer']['addresses'][0]) == true) {
                $deceode_houseNo = base64_decode($data['customer']['addresses'][0]['houseNo']);
                $result_field = openssl_private_decrypt($deceode_houseNo, $houseNo, $res);
            }

            if (array_key_exists('flatNo', $data['customer']['addresses'][0]) == true) {
                $deceode_flatNo = base64_decode($data['customer']['addresses'][0]['flatNo']);
                $result_field = openssl_private_decrypt($deceode_flatNo, $flatNo, $res);
            }

            if (array_key_exists('city', $data['customer']['addresses'][0]) == true) {
                $deceode_city = base64_decode($data['customer']['addresses'][0]['city']);
                $result_field = openssl_private_decrypt($deceode_city, $city, $res);
            }
        }

        if(array_key_exists('clId', $data['customer']) == true) {
            $deceode_clId = base64_decode($data['customer']['clId']);
            $result_field = openssl_private_decrypt($deceode_clId, $clId, $res);
        }

        if(array_key_exists('inn', $data['customer']) == true) {
            $deceode_inn = base64_decode($data['customer']['inn']);
            $result_field = openssl_private_decrypt($deceode_inn, $inn, $res);
        }

        if(array_key_exists('firstName', $data['customer']) == true) {
            $deceode_firstName = base64_decode($data['customer']['firstName']);
            $result_field = openssl_private_decrypt($deceode_firstName, $firstName, $res);
        }

        if(array_key_exists('lastName', $data['customer']) == true) {
            $deceode_lastName = base64_decode($data['customer']['lastName']);
            $result_field = openssl_private_decrypt($deceode_lastName, $lastName, $res);
        }

        if(array_key_exists('middleName', $data['customer']) == true) {
            $deceode_middleName = base64_decode($data['customer']['middleName']);
            $result_field = openssl_private_decrypt($deceode_middleName, $middleName, $res);
        }

        if(array_key_exists('sex', $data['customer']) == true) {
            $deceode_sex = base64_decode($data['customer']['sex']);
            $result_field = openssl_private_decrypt($deceode_sex, $sex, $res);
        }

        /** @var User $user */
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(['clid' => $clId]);
        if (empty($user)) {
            $user = new User();
            if(array_key_exists('city', $data['customer']['addresses'][0]) == true) {

                $location = new Location();

                $location
                    ->setCity($city)
                    ->setDistrict($city);

                if (
                    array_key_exists('flatNo', $data['customer']['addresses'][0]) == true &&
                    array_key_exists('street', $data['customer']['addresses'][0]) == true &&
                    array_key_exists('houseNo', $data['customer']['addresses'][0]) == true
                ) {
                    $location->setAddress($street . ',' . $houseNo . 'appartment' . $flatNo);
                };

                if (array_key_exists('country', $data['customer']['addresses'][0]) == true) {
                    $location->setCountry($country);
                };

                $this->em->persist($location);
                $user->setLocation($location);
            }
            if (array_key_exists('clId', $data['customer']) == true) {
                $user->setClid($clId);
            };

            if (array_key_exists('sex', $data['customer']) == true) {
                $user->setSex($sex);
            };

            if (array_key_exists('middleName', $data['customer']) == true) {
                $user->setMiddleName($middleName);
            };

            if (array_key_exists('lastName', $data['customer']) == true) {
                $user->setLastName($lastName);
            };

            if (array_key_exists('firstName', $data['customer']) == true) {
                $user->setFirstName($firstName);
            };

            if (array_key_exists('inn', $data['customer']) == true) {
                $user->setInn($inn);
            };

            $this->em->persist($user);
            $this->em->flush();

            return ['user' => $user, 'status' => 'new'];
        } else {

            return ['user' => $user, 'status' => 'old'];
        }
    }
}
