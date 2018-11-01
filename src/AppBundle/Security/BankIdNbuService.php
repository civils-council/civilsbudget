<?php

namespace AppBundle\Security;

use AppBundle\Exception\NotFoundException;
use AppBundle\Security\Encryptor\BankIdNbu\BandIdNbuEncryptor;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankIdNbuService
{
    //TODO: BankIdNbuService and BankIdService are similar services. Combine them.
    //TODO: realize state to avoid CSRF attacks => Store state and check it in Bank response
    const BANK_ID_URL_LOGIN = "/v1/bank/oauth2/authorize?response_type=code&client_id=%s&redirect_uri=%s&state=%s";
    const BANK_ID_URL_ACCESS_TOKEN = '/v1/bank/oauth2/token';
    const BANK_ID_URL_CLIENT_DATA = '/v1/bank/resource/client';

    private $clientId;
    private $secret;
    private $oauthUrl;
    private $certPath;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var BandIdNbuEncryptor
     */
    private $bandIdNbuEncryptor;


    public function __construct(
        $clientId,
        $secret,
        $biOauthUrl,
        Router $router,
        $certPath,
        BandIdNbuEncryptor $bandIdNbuEncryptor
    ) {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->oauthUrl = $biOauthUrl;
        $this->router = $router;
        $this->certPath = $certPath;
        $this->bandIdNbuEncryptor = $bandIdNbuEncryptor;
    }

    public function getLink(): string
    {
        $callBack = $this->router->generate('login_nbu', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return sprintf(
            $this->oauthUrl . self::BANK_ID_URL_LOGIN,
            $this->clientId,
            $callBack,
            Uuid::uuid4()->toString()
        );
    }

    public function getAccessToken(string $code): array
    {
        //TODO: client injection
        $client = new Client();
        $callBackUrl = $this->router->generate('api_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $response = $client->request(
            'POST',
            $this->oauthUrl . self::BANK_ID_URL_ACCESS_TOKEN,
            [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->secret,
                    'code' => $code,
                    'redirect_uri' => $callBackUrl,
                ]
            ]
        )->getBody();

        return json_decode($response, true);
    }

    public function getBankIdUserData(string $accessToken): array
    {
        $client = new Client();

        $bankUser = $client
            ->request(
                'POST',
                $this->oauthUrl . self::BANK_ID_URL_CLIENT_DATA,
                [
                    'headers' => [
                        "Content-Type" => "application/json",
                        "Accept" => "application/json",
                        "Authorization" => "Bearer {$accessToken}",
                    ],
                    'body' => json_encode([
                        "type" => "physical",
                        'cert' => base64_encode($this->getCert()),
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

    /**
     * @param array $data
     *
     * @return array
     */
    public function decodeResponse(array $data): array
    {
        $decodedData = $this->bandIdNbuEncryptor->decode($data);

        return $decodedData;
    }

    /**
     * Get certificate content
     *
     * @return string
     */
    private function getCert(): string
    {
        if (!$this->certPath) {
            throw new NotFoundException('Certificate path not defined');
        }

        if (false === $cert = file_get_contents($this->certPath)) {
            throw new NotFoundException('Certificate not found in path: ' . $this->certPath);
        }

        return $cert;
    }
}
