<?php
namespace AppBundle\AWS;

use Aws\Ses\SesClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

class ServiceSES
{
    /**
     * @var SesClient
     */
    private $client;

    /** @var TwigEngine  */
    private $twig;

    /** @var  string */
    private $email_sender;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct($email_sender, SesClient $client, TwigEngine $twig, LoggerInterface $logger)
    {
        $this->email_sender = $email_sender;
        $this->client = $client;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * @param string $subject
     * @param string $template
     * @param array $options
     * @param array $emails
     */
    public function sendEmail($emails, $subject, $template, $options)
    {
        try {
            $this->client->sendEmail([
//                    'Source' => "Golos.ck.ua <" . $this->email_sender . ">",
                    'Source' => "Громадський бюджет <" . $this->email_sender . ">",
                    'Destination' => [
                        'ToAddresses' => $emails
                    ],
                    'Message' => [
                        'Subject' => [
                            'Data' => $subject,
                            'Charset' => 'UTF-8',
                        ],
                        'Body' => [
                            'Html' => [
                                'Data' => $this->twig->render(
                                    $template, $options),
                                'Charset' => 'UTF-8',
                            ],
                        ],
                    ]]
            );
            $this->logger->info('Sending email was successful', ['to' => $emails, 'subject' => $subject]);
        } catch (\Exception $e) {
            $this->logger->critical('sending email was crashed', ['to' => $emails, 'subject' => $subject, 'e' => $e->getMessage()]);
        }
    }

    public function verifyEmail()
    {
        $result = $this->client->verifyEmailIdentity([
            'EmailAddress' => 'your_email@gmail.com', // REQUIRED
        ]);

        return $result;
    }
}
