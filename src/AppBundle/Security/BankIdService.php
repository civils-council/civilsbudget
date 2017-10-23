<?php

namespace AppBundle\Security;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankIdService
{
    const BANK_ID_URL_LOGIN = "/DataAccessService/das/authorize?response_type=code&client_id=%s&redirect_uri=%s";
    const BANK_ID_URL_ACCESS_TOKEN = "/DataAccessService/oauth/token?grant_type=authorization_code&client_id=%s&client_secret=%s&code=%s&redirect_uri=%s";

    private $clientId;
    private $secret;
    private $bi_get_data_url;
    private $bi_oauth_url;
    /**
     * @var Router
     */
    private $router;

    public function __construct($clientId, $secret, $bi_get_data_url, $bi_oauth_url, Router $router)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->bi_get_data_url = $bi_get_data_url;
        $this->bi_oauth_url = $bi_oauth_url;
        $this->router = $router;
    }

    public function getLink()
    {
        $callBack = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return sprintf($this->bi_oauth_url.self::BANK_ID_URL_LOGIN, $this->clientId, $callBack);
    }

    public function getAccessToken($code)
    {
//        dump($code);exit;
        $client = new Client();
        $sha1 = sha1($this->clientId . $this->secret. $code, false);
        $url =  sprintf(
            $this->bi_oauth_url.self::BANK_ID_URL_ACCESS_TOKEN,
            $this->clientId,
            $sha1,
            $code,
            $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $rawAccessToken = $client->request('GET', $url)->getBody();

        $accessToken = json_decode($rawAccessToken, true);
        return $accessToken;
    }

    public function getApiAccessToken($code)
    {
//        dump($code);exit;
        $client = new Client();
        $sha1 = sha1($this->clientId . $this->secret. $code, false);
        $url =  sprintf(
            $this->bi_oauth_url.self::BANK_ID_URL_ACCESS_TOKEN,
            $this->clientId,
            $sha1,
            $code,
            $this->router->generate('api_login', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $rawAccessToken = $client->request('GET', $url)->getBody();
        $accessToken = json_decode($rawAccessToken, true);
        return $accessToken;
    }

    public function getBankIdUser($accessToken)
    {
        $client = new Client();
//        $accessToken = json_decode($rawAccessToken, true);

        $bankUser = $client
            ->request(
                'POST',
                $this->bi_get_data_url."/ResourceService/checked/data",
                [
                    'headers' => [
                        "Content-Type" => "application/json",
                        "Accept" => "application/json",
                        "Authorization" => "Bearer {$accessToken}, Id {$this->clientId}",
                    ],
                    'body' => json_encode([
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
                ]
            )
            ->getBody()
        ;

        return json_decode($bankUser, true);
    }
}
