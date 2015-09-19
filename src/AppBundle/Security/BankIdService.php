<?php

namespace AppBundle\Security;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankIdService
{
    const BANK_ID_URL = "https://bankid.privatbank.ua/DataAccessService/das/authorize?response_type=code&client_id=%s&redirect_uri=%s";

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

        return sprintf(self::BANK_ID_URL, $this->clientId, $callBack);
    }
}
