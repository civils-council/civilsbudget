<?php

namespace AppBundle\Helper;

use AppBundle\Entity\City;
use AppBundle\Entity\Country;
use AppBundle\Entity\Location;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserManager
{
    /**
     * @var
     */
    protected $_templateEngine;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     */
    private $rootDir;

    /**
     * @param $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * UserManager constructor.
     * @param $_templateEngine
     * @param EntityManager $em
     */
    public function __construct($_templateEngine, EntityManager $em)
    {
        $this->templateEngine = $_templateEngine;
        $this->em = $em;
    }

    /**
     * @param array $data
     * @return array
     */
    public function isUniqueUser($data)
    {
        $fp = fopen($this->rootDir . '/rsa_key.pem', "r");
        $pub_key = fread($fp, 8192);
        fclose($fp);
        $res = openssl_get_privatekey($pub_key);

        $factualAddress = null;
        $clId = null;
        $inn = null;
        $street = null;
        $city = null;
        $country = null;
        $houseNo = null;
        $flatNo = null;
        $middleName = null;
        $lastName = null;
        $firstName = null;
        $sex = null;

        if (array_key_exists('addresses', $data['customer']) == true) {
            $factualKey = array_search('factual', array_column($data['customer']['addresses'], 'type'));
            $factualAddress = $data['customer']['addresses'][$factualKey];
        }

        if ($factualAddress) {
            if (array_key_exists('country', $factualAddress) == true) {
                $encoded_country = base64_decode($factualAddress['country']);
                openssl_private_decrypt($encoded_country, $country, $res);
            }

            if (array_key_exists('state', $factualAddress) == true) {
                $encoded_state = base64_decode($city = $factualAddress['state']);
                openssl_private_decrypt($encoded_state, $state, $res);
            }

            if (array_key_exists('street', $factualAddress) == true) {
                $encoded_street = base64_decode($factualAddress['street']);
                openssl_private_decrypt($encoded_street, $street, $res);
            }

            if (array_key_exists('houseNo', $factualAddress) == true) {
                $encoded_houseNo = base64_decode($factualAddress['houseNo']);
                openssl_private_decrypt($encoded_houseNo, $houseNo, $res);
            }

            if (array_key_exists('flatNo', $factualAddress) == true) {
                $encoded_flatNo = base64_decode($factualAddress['flatNo']);
                openssl_private_decrypt($encoded_flatNo, $flatNo, $res);
            }

            if (array_key_exists('city', $factualAddress) == true) {
                $encoded_city = base64_decode($factualAddress['city']);
                openssl_private_decrypt($encoded_city, $city, $res);
            }
        }

        if (array_key_exists('clId', $data['customer']) == true) {
            $encoded_clId = base64_decode($data['customer']['clId']);
            openssl_private_decrypt($encoded_clId, $clId, $res);
        }

        if (array_key_exists('inn', $data['customer']) == true) {
            $encoded_inn = base64_decode($data['customer']['inn']);
            openssl_private_decrypt($encoded_inn, $inn, $res);
        }

        if (array_key_exists('firstName', $data['customer']) == true) {
            $encoded_firstName = base64_decode($data['customer']['firstName']);
            openssl_private_decrypt($encoded_firstName, $firstName, $res);
        }

        if (array_key_exists('lastName', $data['customer']) == true) {
            $encoded_lastName = base64_decode($data['customer']['lastName']);
            openssl_private_decrypt($encoded_lastName, $lastName, $res);
        }

        if (array_key_exists('middleName', $data['customer']) == true) {
            $encoded_middleName = base64_decode($data['customer']['middleName']);
            openssl_private_decrypt($encoded_middleName, $middleName, $res);
        }

        if (array_key_exists('sex', $data['customer']) == true) {
            $encoded_sex = base64_decode($data['customer']['sex']);
            openssl_private_decrypt($encoded_sex, $sex, $res);
        }

        /** @var User $user */
        $user = $this->em->getRepository('AppBundle:User')->getUserByInnOrClid($clId, $inn);
        if (array_key_exists('city', $factualAddress) == true) {
            $existCity = $this->em->getRepository('AppBundle:City')->findOneBy(['city' => $city]);
            if (!$existCity) {
                $existCity = new City();
                $existCity->setCity($city);
                $this->em->persist($existCity);
            }
        }
        if (array_key_exists('country', $factualAddress) == true) {
            $existCountry = $this->em->getRepository('AppBundle:Country')->findOneBy(['country' => $country]);
            if (!$existCountry) {
                $existCountry = new Country();
                $existCountry->setCountry($country);
                $this->em->persist($existCountry);
            }
        }
        if (empty($user)) {
            $user = new User();

            $location = new Location();

            $location
                ->setCityObject(isset($existCity) ? $existCity : null)
                ->setCountry(isset($existCountry) ? $existCountry : null)
                ->setDistrict($city);
            //TODO check street->home number->flat and lineAddress = lineAddres . street
            if (
                array_key_exists('flatNo', $factualAddress) == true &&
                array_key_exists('street', $factualAddress) == true &&
                array_key_exists('houseNo', $factualAddress) == true
            ) {
                $location->setAddress($street . ',' . $houseNo . 'appartment' . $flatNo);
            };

            $this->em->persist($location);
            $user->setLocation($location);

            if ($clId) {
                $user->setClid($clId);
            };

            if ($sex) {
                $user->setSex($sex);
            };

            if ($middleName) {
                $user->setMiddleName($middleName);
            };

            if ($lastName) {
                $user->setLastName($lastName);
            };

            if ($firstName) {
                $user->setFirstName($firstName);
            };

            if ($inn) {
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
