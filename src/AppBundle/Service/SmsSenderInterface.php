<?php

declare(strict_types=1);

namespace AppBundle\Service;

/**
 * Interface SmsSenderInterface.
 */
interface SmsSenderInterface
{
    /**
     * @param string $phone
     * @param string $text
     *
     * @return mixed
     */
    public function send(string $phone, string $text);
}
