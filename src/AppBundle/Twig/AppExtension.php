<?php

namespace AppBundle\Twig;

use AppBundle\Security\BankIdService;

class AppExtension extends \Twig_Extension
{
    /**
     * @var BankIdService
     */
    private $bankId;

    public function __construct(BankIdService $bankId)
    {
        $this->bankId = $bankId;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('regexUrl', [$this, 'regexUrl']),
        ];
    }

    public function getGlobals()
    {
        return [
            'login_url' => $this->bankId->getLink(),
        ];
    }

    public function regexUrl($url)
    {
        preg_match('/https?:\/\/([^\/]+)\//i', $url, $matches);

        return $matches[1];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_extension';
    }
}
