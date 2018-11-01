<?php

namespace AppBundle\Security\Encryptor\BankIdNbu;


class EUEnvelopInfo
{
    public $signTime			= null;
    public $useTSP				= false;
    public $senderInfo			= null;
    public $data				= null;

    function __construct()
    {
        $this->senderInfo = new EUCertInfo();
    }
}