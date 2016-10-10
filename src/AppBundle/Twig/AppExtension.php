<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Project;
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
    public function getFunctions()
    {
        return [
            'isSafary' => new \Twig_Function_Method($this, 'isSafary'),
        ];
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
     * @return bool
     */
    public function isSafary() {
        preg_match("/(MSIE|Opera|Firefox|Chrome|Version)(?:\/| )([0-9.]+)/", $_SERVER['HTTP_USER_AGENT'], $browser_info);
        if ($browser_info[1] =='Version') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_extension';
    }
}
