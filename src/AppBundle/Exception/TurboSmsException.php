<?php

declare(strict_types=1);

namespace AppBundle\Exception;

use Throwable;

/**
 * Class TurboSmsException.
 */
class TurboSmsException extends \Exception
{
    /**
     * @var string
     */
    private $smsText;

    /**
     * TurboSmsException constructor.
     *
     * @param string         $message
     * @param string         $smsText
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $smsText = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->smsText = $smsText;
    }

    /**
     * @return null|string
     */
    public function getSmsText(): ?string
    {
        return $this->smsText;
    }
}
