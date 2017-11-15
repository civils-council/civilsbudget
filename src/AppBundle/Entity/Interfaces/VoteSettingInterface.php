<?php

namespace AppBundle\Entity\Interfaces;

use AppBundle\Entity\User;
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

    /**
     * @param User $user
     * @param bool|null $paperVote
     *
     * @return VoteSettings[]|[]
     */
    public function getVoteSettingByUserCity(User $user, ?bool $paperVote = false): array;

    /**
     * @return array
     */
    public function getVoteSettingCities();    
}