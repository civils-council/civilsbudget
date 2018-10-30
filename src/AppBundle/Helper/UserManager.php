<?php

namespace AppBundle\Helper;

use AppBundle\Entity\City;
use AppBundle\Entity\Country;
use AppBundle\Entity\Location;
use AppBundle\Entity\OtpToken;
use AppBundle\Entity\User;
use AppBundle\Service\TurboSmsSender;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

class UserManager
{
    const PHONE_PATTERN = '/^\+?380[0-9]{9}$/';

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
     * @var TurboSmsSender
     */
    private $smsSender;

    /**
     * @var Router
     */
    private $router;

    /**
     * UserManager constructor.
     *
     * @param EntityManager $em
     * @param string        $kernelRootDir
     */
    public function __construct(EntityManager $em, string $kernelRootDir)
    {
        $this->em = $em;
        $this->kernelRootDir = $kernelRootDir;
        $this->setResource();
    }

    /**
     * @param string $kernelRootDir
     */
    public function setRootDir($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * @param TurboSmsSender $smsSender
     */
    public function setSmsSender(TurboSmsSender $smsSender): void
    {
        $this->smsSender = $smsSender;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router): void
    {
        $this->router = $router;
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

            $user = (new User())
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
                $location->getCity() !== $user->getCurrentLocation()->getCity() ||
                $location->getAddress() !== $user->getCurrentLocation()->getAddress()
            )
        ) {
            $this->em->persist($location);
            $user->addLocation($location);
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

    private function setResource(): void
    {
        $fp = fopen($this->kernelRootDir.'/rsa_key.pem', 'r');
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
     * @param array  $customerData
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
                $existCity = (new City())->setCity($city);
                $this->em->persist($existCity);
            }
            $city = $existCity;
        }

        if (null !== $country = $this->getDecodedValueFromCustomerData('country', $factualAddress)) {
            if (null ===
                $existCountry = $this->em->getRepository('AppBundle:Country')->findOneBy(['country' => $country])
            ) {
                $existCountry = (new Country())->setCountry($country);
                $this->em->persist($existCountry);
            }
            $country = $existCountry;
        }

        $address = implode(', ', array_filter([$street, $houseNo, $flatNo ? 'apt: '.$flatNo : null]));

        return (new Location())
            ->setCityObject($city)
            ->setCountry($country)
            ->setDistrict($state)
            ->setAddress('' != $address ? $address : null);
    }

    /**
     * @param User $user
     *
     * @return User|null
     */
    public function sendSmsUserPhone(User $user): ?User
    {
        if (null === $user->getPhone()) {
            return null;
        }

        try {
            $user->expireOtpTokens();
            $otpToken = new OtpToken();
            $otpToken
                ->setUser($user)
                ->setPhone($user->getPhone());

            $this->em->persist($otpToken);
            $this->em->flush();

            $message = "Vitaemo v NarodnaRada! Vash kod:".$otpToken->getToken();

            $this->smsSender->sendTurboSms($user->getPhone(), $message);
        } catch (\Exception $e) {
        }

        return $user;
    }

    /**
     * @param OtpToken $otpToken
     *
     * @return OtpToken
     *
     * @throws \Exception
     */
    private function checkOtpToken(OtpToken &$otpToken)
    {
        if ($existToken = $this->em->getRepository(OtpToken::class)->findOneBy(['token' => $otpToken->getToken(), 'used' => false])) {
            $otpToken->generateToken();

            $this->checkOtpToken($otpToken);
        }

        return $otpToken;
    }
}
