<?php

namespace AppBundle\Security;

use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankIdService
{
    const BANK_ID_URL_LOGIN = "https://bankid.privatbank.ua/DataAccessService/das/authorize?response_type=code&client_id=%s&redirect_uri=%s";
    const BANK_ID_URL_ACCESS_TOKEN = "https://bankid.privatbank.ua/DataAccessService/oauth/token?grant_type=authorization_code&client_id=%s&client_secret=%s&code=%s&redirect_uri=%s";

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

    public function getLink($projectId = null)
    {
        $callBack = $this->router->generate('login', ['id' => $projectId], UrlGeneratorInterface::ABSOLUTE_URL);

        return sprintf(self::BANK_ID_URL_LOGIN, $this->clientId, $callBack);
    }

    public function getAccessToken($code, $projectId = null)
    {
//        dump($code);exit;
        $client = new Client();
        $sha1 = sha1($this->clientId . $this->secret. $code, false);
        $url =  sprintf(
            self::BANK_ID_URL_ACCESS_TOKEN,
            $this->clientId,
            $sha1,
            $code,
            $this->router->generate('login', ['id' => $projectId], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $accessToken = $client->get($url)->send()->getBody(true);

        return $this->getBankIdUser($accessToken);
    }

    public function getBankIdUser($rawAccessToken)
    {
        $client = new Client();
        $accessToken = json_decode($rawAccessToken, true);

        $bankUser = $client
            ->post(
                "https://bankid.privatbank.ua/ResourceService/checked/data",
                [
                    "Content-Type" => "application/json",
                    "Accept" => "application/json",
                    "Authorization" => "Bearer {$accessToken['access_token']}, Id {$this->clientId}",
                ],
                json_encode([
                    "type" => "physical",
                    "fields" => ["firstName","middleName","lastName","phone","inn","clId","clIdText","birthDay","email","sex","resident","dateModification"],
                    "addresses" =>  [[
                        "type" => "factual",
                        "fields" => ["country","state","area","city","street","houseNo","flatNo","dateModification"]
                    ],[
                        "type" => "birth",
                        "fields" => ["country","state","area","city","street","houseNo","flatNo","dateModification"]
                    ]],
                    "documents" => [[
                        "type" => "passport",
                        "fields" => ["series","number","issue","dateIssue","dateExpiration","issueCountryIso2","dateModification"]
                    ]]
                ])
            )
            ->send()
            ->getBody(true)
        ;

        return json_decode($bankUser, true);
    }
}
