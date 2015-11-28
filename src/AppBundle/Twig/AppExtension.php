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
