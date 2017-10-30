<?php

namespace AppBundle\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UrlGeneratorHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param string|null $url
     *
     * @return string|null
     */
    public function prepareAbsoluteUrl(?string $url): ?string
    {
        if (strpos($url, 'http') === 0 || $url === null) {
            return $url;
        }

        return $this->cleanUpUrl($this->getRequest()->getUriForPath($url));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function cleanUpUrl(string $url): string
    {
        return preg_replace('/\/app_dev.php/', '', $url);
    }

    /**
     * @return Request
     */
    private function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}