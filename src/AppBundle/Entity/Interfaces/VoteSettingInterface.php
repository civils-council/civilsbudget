<?php

namespace AppBundle\Entity\Interfaces;

use AppBundle\Entity\VoteSettings;
use Symfony\Component\HttpFoundation\Request;

interface VoteSettingInterface
{
    /**
     * @param Request $request
     * @return VoteSettings|null
     */
    public function getProjectVoteSettingShow(
        Request $request
    );

    /**
     * @param Request $request
     * @return array
     */
    public function getProjectVoteSettingByCity(
        Request $request
    );    
}