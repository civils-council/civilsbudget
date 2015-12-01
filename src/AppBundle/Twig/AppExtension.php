<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Project;
use AppBundle\Security\BankIdResourceOwner;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppExtension extends \Twig_Extension
{
    /**
     * @var BankIdResourceOwner
     */
    private $bankId;

    /**
     * @var Router
     */
    private $router;

    public function __construct(BankIdResourceOwner $bankId, Router $router)
    {
        $this->bankId = $bankId;
        $this->router = $router;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('regexUrl', [$this, 'regexUrl']),
            new \Twig_SimpleFilter('projectCallback', [$this, 'projectCallback']),
        ];
    }

    public function projectCallback(Project $project = null)
    {
        return $this->bankId->getLink(!$project ?: $project->getId());
    }

    public function getGlobals()
    {
        return [
            'login_url' => $this->bankId->getAuthorizationUrl($this->router->generate('bank_id_login', [], UrlGeneratorInterface::ABSOLUTE_URL)),
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
