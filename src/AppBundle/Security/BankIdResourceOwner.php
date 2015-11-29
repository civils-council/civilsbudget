<?php

namespace AppBundle\Security;

use Guzzle\Http\Client;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\HttpFoundation\Request;

class BankIdResourceOwner extends GenericOAuth2ResourceOwner
{
    const USER_AGENT_HEADER = "User-Agent: Gromadskiy Byudjet mista Cherkasy (https://www.golos.ck.ua/)";

    /**
     * @param string $rawAccessToken
     * @return mixed
     */
    protected function getBankIdUser($rawAccessToken)
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

    /**
     * Performs an HTTP request
     *
     * @param string $url     The url to fetch
     * @param string $content The content of the request
     * @param array  $headers The headers of the request
     * @param string $method  The HTTP method to use
     *
     * @return \HttpResponse The response content
     */
    protected function httpRequest($url, $content = null, $headers = array(), $method = null)
    {
        return parent::httpRequest($url, $content, array_merge([self::USER_AGENT_HEADER], $headers), $method);
    }

    public function getAccessToken(Request $request, $redirectUri, array $extraParameters = array())
    {
        return parent::getAccessToken($request, $redirectUri, [
            'client_secret' => sha1($this->options['client_id'].$this->options['client_secret'].$request->query->get('code'), false)
        ]);
    }

    public function getUserInformation(array $accessToken, array $extraParameters = array())
    {
        $url = $this->normalizeUrl($this->options['infos_url']);
        $content = $this->httpRequest($url, null, [sprintf('Bearer %s, Id %s', $accessToken['access_token'], $this->options['client_id'])]);

        $response = $this->getUserResponse();
        $response->setResponse($content->getContent());
        $response->setResourceOwner($this);
        $response->setOAuthToken(new OAuthToken($accessToken));

        return $response;
    }
}
