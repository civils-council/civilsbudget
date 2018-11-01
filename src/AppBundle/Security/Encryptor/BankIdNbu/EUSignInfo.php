<?php

namespace AppBundle\Security\Encryptor\BankIdNbu;


class EUSignInfo
{
    public $signTime			= null;
    public $useTSP				= false;
    public $signerInfo			= null;
    public $data				= null;

    function __construct()
    {
        $this->signerInfo = new EUCertInfo();
    }
}