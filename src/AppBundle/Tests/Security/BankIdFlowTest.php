<?php

namespace AppBundle\Tests\Security;

use AppBundle\Security\Encryptor;
use AppBundle\Security\Response\BankIdUserResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BankIdFlowTest extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        if (null !== static::$kernel) {
            self::bootKernel([
                'environment' => 'test',
                'debug' => true,
            ]);
            $this->container = static::$kernel->getContainer();
        }
    }

    public function testLoadUserByOAuthUserResponse()
    {
        $userProvider = $this->container->get('app.bank_id_user_provider');
        $userInfo = $this->getUserInformation();

        $numberUsers1 = count($this->container->get('doctrine')->getRepository('AppBundle:User')->findAll());
        $userProvider->loadUserByOAuthUserResponse($userInfo);
        $numberUsers2 = count($this->container->get('doctrine')->getRepository('AppBundle:User')->findAll());
        $this->assertEquals($numberUsers1+1, $numberUsers2);

        $user = $userProvider->loadUserByOAuthUserResponse($userInfo);
        $numberUsers3 = count($this->container->get('doctrine')->getRepository('AppBundle:User')->findAll());
        $this->assertEquals($numberUsers2, $numberUsers3);

        // Clean db
        $em = $this->container->get('doctrine')->getManager();
        $em->remove($user);
        $em->flush();
    }

    public function getUserInformation()
    {
        $client = $this->getMockBuilder('Buzz\Client\Curl')->getMock();
        $utils = $this->getMockBuilder('Symfony\Component\Security\Http\HttpUtils')->disableOriginalConstructor()->getMock();
        $options = [
            'infos_url' => 'http://localhost',
            'client_id' => '555',
            'client_secret' => '',
            'access_token_url' => '',
            'authorization_url' => '',
            'user_response_class' => 'AppBundle\Security\Response\BankIdUserResponse'
        ];
        $sessionStorage = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\RequestDataStorage\SessionStorage')->disableOriginalConstructor()->getMock();
        $response = new \Buzz\Message\Response();
        $response->setContent($this->getResponseExample());

        $bankIdROMock = $this->getMockBuilder('AppBundle\Security\BankIdResourceOwner')
            ->setConstructorArgs([$client, $utils, $options, 'bank_id', $sessionStorage])
            ->setMethods(['httpRequest'])
            ->getMock();
        $bankIdROMock->method('httpRequest')->willReturn($response);

        /** @var BankIdUserResponse $userInfo */
        $userInfo = $bankIdROMock->getUserInformation(['access_token' => '555']);

        $this->assertInstanceOf('AppBundle\Security\Response\BankIdUserResponse', $userInfo);
        $this->assertNotNull($userInfo->getFirstName());
        $this->assertNotNull($userInfo->getLastName());
        $this->assertNotNull($userInfo->getMiddleName());
        $this->assertNotNull($userInfo->getClid());
        $this->assertNotNull($userInfo->getInn());
        $this->assertNotNull($userInfo->getSex());
        $this->assertNotNull($userInfo->getBirthday());

        return $userInfo;
    }

    /**
     * Clean up Kernel usage in this test.
     */
    public static function tearDownAfterClass()
    {
        static::ensureKernelShutdown();
    }

    protected function getResponseExample()
    {
        $encryptor = new Encryptor();
        $encryptor->setPublicKey(__DIR__.'/../../../../app/Resources/ssl-keys/test/public.key');

        return [
            'state' => 'ok',
            'customer' => [
                'type' => 'physical',
                'clId' => $encryptor->encrypt('1111'),
                'clIdText' => $encryptor->encrypt('client-id text'),
                'lastName' => $encryptor->encrypt('Pupkin'),
                'firstName' => $encryptor->encrypt('Vasiliy'),
                'middleName' => $encryptor->encrypt('Vasilyevich'),
                'birthDay' => $encryptor->encrypt('1988-12-25'),
                'inn' => $encryptor->encrypt('5942645864'),
                'sex' => $encryptor->encrypt('male'),
                'resident' => $encryptor->encrypt('true'),
                'dateModification' => $encryptor->encrypt('2014-05-26 15:32:11'),
                'signature' => $encryptor->encrypt('f7c3478d6ef4a06461d99b404ba6d9841b594a1e'),
                'addresses' =>
                    [
                        0 => [
                            'type' => 'factual',
                            'country' => $encryptor->encrypt('Ukraine'),
                            'state' => $encryptor->encrypt('Cherkasy'),
                            'city' => $encryptor->encrypt('Cherkasy'),
                            'street' => $encryptor->encrypt('Shevchenko'),
                            'houseNo' => $encryptor->encrypt('455'),
                            'flatNo' => $encryptor->encrypt('29'),
                            'dateModification' => $encryptor->encrypt('2014-05-26 15:32:11'),
                        ],
                    ],
            ],
        ];
    }
}
