<?php

namespace AppBundle\Security;

use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankIdService
{
    const BANK_ID_URL_LOGIN = "https://bankid.privatbank.ua/DataAccessService/das/authorize?response_type=code&client_id=%s&redirect_uri=%s";
    const BANK_ID_URL_ACCESS_TOKEN = "https://bankid.privatbank.ua/DataAccessService/oauth/token";

    private $clientId;
    private $secret;
    /**
     * @var Router
     */
    private $router;

    public function __construct($clientId, $secret, Router $router)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->router = $router;
    }

    public function getLink()
    {
        $callBack = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return sprintf(self::BANK_ID_URL_LOGIN, $this->clientId, $callBack);
    }

    public function getAccessToken($code)
    {
//        $client = new Client();
        $sha1 = sha1($this->clientId . $this->secret . $code);
        dump($sha1, $this->clientId, $this->secret, $code, "https://bankid.privatbank.ua/DataAccessService/oauth/token?grant_type=authorization_code&client_id={$this->clientId}&client_secret={$sha1}&code={$code}&redirect_uri=http://civilsbudget.local/app_dev.php/login" );

//
//        $client->get(self::BANK_ID_URL_ACCESS_TOKEN, [
//            'query' => [
//                'grant_type' => 'authorization_code',
//                'client_id' => $this->clientId,
//                'client_secret' => $this->secret,
//                'code' => $code,
//                'redirect_uri' => $callBack = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL),
//            ]
//        ]);
    }
}
