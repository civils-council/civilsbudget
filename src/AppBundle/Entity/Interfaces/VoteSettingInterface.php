<?php

namespace AppBundle\Entity\Interfaces;


use AppBundle\Entity\VoteSettings;
use Symfony\Component\HttpFoundation\ParameterBag;

interface VoteSettingInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @return VoteSettings|null
     */
    public function getProjectShow(
        ParameterBag $parameterBag
    );
}