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
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var
     */
    private $resource;

    /**
     * @param string $kernelRootDir
     */
    public function setRootDir($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * UserManager constructor.
     * @param EntityManager $em
     * @param string $kernelRootDir
     */
    public function __construct(EntityManager $em, string $kernelRootDir)
    {
        $this->em = $em;
        $this->kernelRootDir = $kernelRootDir;
        $this->setResource();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function isUniqueUser($data)
    {
        $location = $this->getLocationFromCustomerData($data['customer']);
        $clId = $this->getDecodedValueFromCustomerData('clId', $data['customer']);
        $inn = $this->getDecodedValueFromCustomerData('inn', $data['customer']);

        if (null === $user = $this->em->getRepository('AppBundle:User')->getUserByInnOrClid($clId, $inn)) {
            $firstName = $this->getDecodedValueFromCustomerData('firstName', $data['customer']);
            $lastName = $this->getDecodedValueFromCustomerData('lastName', $data['customer']);
            $middleName = $this->getDecodedValueFromCustomerData('middleName', $data['customer']);
            $sex = $this->getDecodedValueFromCustomerData('sex', $data['customer']);

            $this->em->persist($location);

            $user = (new User)
                ->addLocation($location)
                ->setClid($clId)
                ->setSex($sex)
                ->setMiddleName($middleName)
                ->setLastName($lastName)
                ->setFirstName($firstName)
                ->setInn($inn)
            ;
            $this->em->persist($user);
            $location->setUser($user);
            $this->em->flush();

            return ['user' => $user, 'status' => 'new'];
        }
        if ($location &&
            (
                $location->getCity() !== $user->getLocation()->getCity() ||
                $location->getAddress() !== $user->getLocation()->getAddress()
            )
        ) {
            $this->em->persist($location);
            $user->setLocation($location);
            $location->setUser($user);
            $this->em->flush();
        }

        return ['user' => $user, 'status' => 'old'];
    }

    /**
     * @return null|resource
     */
    private function getResource()
    {
        return $this->resource;
    }

    /**
     * @return void
     */
    private function setResource(): void
    {
        $fp = fopen($this->kernelRootDir . '/rsa_key.pem', "r");
        $pub_key = fread($fp, 8192);
        fclose($fp);
        $this->resource = openssl_get_privatekey($pub_key) ?: null;
    }

    /**
     * @param string
     *
     * @return string
     */
    private function decodeValue(string $encodedValue): string
    {
        openssl_private_decrypt(base64_decode($encodedValue), $result, $this->getResource());

        return $result;
    }

    /**
     * @param string $key
     * @param array $customerData
     *
     * @return null|string
     */
    private function getDecodedValueFromCustomerData(string $key, array $customerData): ?string
    {
        if (!array_key_exists($key, $customerData)) {
            return null;
        }

        return $this->decodeValue($customerData[$key]);
    }

    /**
     * @param array $customerData
     *
     * @return Location|null
     */
    private function getLocationFromCustomerData(array $customerData): ?Location
    {
        if (!array_key_exists('addresses', $customerData)) {
            return null;
        }

        $factualKey = array_search('factual', array_column($customerData['addresses'], 'type'));
        $factualAddress = $customerData['addresses'][$factualKey];

        if (!$factualAddress) {
            return null;
        }

        $state = $this->getDecodedValueFromCustomerData('state', $factualAddress);
        $street = $this->getDecodedValueFromCustomerData('street', $factualAddress);
        $houseNo = $this->getDecodedValueFromCustomerData('houseNo', $factualAddress);
        $flatNo = $this->getDecodedValueFromCustomerData('flatNo', $factualAddress);
        if (null !== $city = $this->getDecodedValueFromCustomerData('city', $factualAddress)) {
            if (null ===
                $existCity = $this->em->getRepository('AppBundle:City')->findOneBy(['city' => $city])
            ) {
                $existCity = (new City)->setCity($city);
                $this->em->persist($existCity);
            }
            $city = $existCity;
        }

        if (null !== $country = $this->getDecodedValueFromCustomerData('country', $factualAddress)) {
            if (null ===
                $existCountry = $this->em->getRepository('AppBundle:Country')->findOneBy(['country' => $country])
            ) {
                $existCountry = (new Country)->setCountry($country);
                $this->em->persist($existCountry);
            }
            $country = $existCountry;
        }

        $address = implode(', ', array_filter([$street, $houseNo, $flatNo ? 'apt: '. $flatNo : null]));

        return (new Location)
            ->setCityObject($city)
            ->setCountry($country)
            ->setDistrict($state)
            ->setAddress($address != '' ? $address : null);
    }
}
