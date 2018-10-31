<?php

declare(strict_types=1);

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AlphaSmsSender.
 */
class AlphaSmsSender implements SmsSenderInterface
{
    private const STATUS_OK = 'ok';
    private const STATUS_ERROR = 'error';
    private const XML_EXAMPLE = '<?xml version="1.0" encoding="utf-8" ?><package key="%s"><message><msg recipient= "%s" sender="%s" type="0">%s</msg></message></package>';
    private const ALPHA_URL = 'http://alphasms.ua/api/xml.php';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string|null
     */
    private $from;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * TurboSmsSender constructor.
     *
     * @param string      $apiKey
     * @param string|null $from
     */
    public function __construct(string $apiKey, string $from = null)
    {
        $this->apiKey = $apiKey;
        $this->from = $from;
    }

    /**
     * @param string $phone
     * @param string $text
     *
     * @return string
     */
    public function send(string $phone, string $text)
    {
        $client = new Client();
        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => sprintf(self::XML_EXAMPLE, $this->apiKey, $phone, $this->from, $text),
        ];

        try {
            $client->request(Request::METHOD_POST, self::ALPHA_URL, $options);
            $this->logger->info('[SMS]: Send message on '.$phone);

            return self::STATUS_OK;
        } catch (\Exception $e) {
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
        }

        return self::STATUS_ERROR;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }
}
