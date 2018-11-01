<?php

namespace AppBundle\Security\Encryptor\BankIdNbu;


class EUError
{
    const EU_ERROR_UNKNOWN = 0xFFFF;

    public $code = self::EU_ERROR_UNKNOWN;
    public $description = '';
}