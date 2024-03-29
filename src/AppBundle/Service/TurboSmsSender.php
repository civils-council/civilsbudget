<?php

declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Exception\TurboSmsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TurboSmsSender.
 */
class TurboSmsSender implements SmsSenderInterface
{
    private const KERNEL_NOT_IGNORE_MODE = 'prod';
    private const STATUS_OK = 'ok';
    private const STATUS_ERROR = 'error';
    private const AUTH_RESULT_OK = 'Вы успешно авторизировались';

    /**
     * @var string
     */
    private $turbosmsLogin;

    /**
     * @var string
     */
    private $turbosmsPass;

    /**
     * @var string|null
     */
    private $turbosmsFrom;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \SoapClient
     */
    private $client;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * TurboSmsSender constructor.
     *
     * @param string $turbosmsLogin
     * @param string $turbosmsPass
     * @param string $turbosmsFrom
     */
    public function __construct(string $turbosmsLogin, string $turbosmsPass, ?string $turbosmsFrom = null)
    {
        $this->turbosmsLogin = $turbosmsLogin;
        $this->turbosmsPass = $turbosmsPass;
        $this->turbosmsFrom = $turbosmsFrom;
    }

    /**
     * @param string $phone
     * @param string $text
     *
     * @return array|bool
     */
    public function send(string $phone, string $text)
    {
        $body = [
            'sender' => $this->turbosmsFrom,
            'text' => $text,
            'destination' => $phone,
        ];

        $result['results']['message'] = (string) $text;
        $result['results']['locator'] = (string) $phone;
        try {
            $status = ['result' => false, 'message' => ''];
            $auth = [
                'login' => $this->turbosmsLogin,
                'password' => $this->turbosmsPass,
            ];

            $authResult = $this->client->Auth($auth);
            if (isset($authResult->AuthResult) && self::AUTH_RESULT_OK !== $authResult->AuthResult.'') {
                throw new TurboSmsException($authResult->AuthResult, $text);
            }

            if (self::KERNEL_NOT_IGNORE_MODE === $this->kernel->getEnvironment()) {
                $smsResult = $this->client->SendSMS($body);
            } else {
                $smsResult = [];
            }
            if (empty($result->SendSMSResult->ResultArray[0])) {
                return $status;
            }

            if ('Сообщения успешно отправлены' === ''.$smsResult->SendSMSResult->ResultArray[0]) {
                $status['result'] = true;
            } else {
                $status['message'] = implode(',', $smsResult->SendSMSResult->ResultArray);
            }

            $result['results']['status'] = true === $status['result'] ? self::STATUS_OK : self::STATUS_ERROR;
            if (self::STATUS_OK !== $result['results']['status']) {
                $result['results']['error_message'] = $status['message'];
            }
        } catch (TurboSmsException $e) {
            $result['results']['error_message'] = (string) $e->getMessage();
            $result['results']['status'] = self::STATUS_ERROR;
        }

        return self::STATUS_OK === $result['results']['status'];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param \SoapClient $client
     */
    public function setClient(\SoapClient $client): void
    {
        $this->client = $client;
    }

    /**
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }
}
