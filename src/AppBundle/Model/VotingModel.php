<?php

namespace AppBundle\Model;

use AppBundle\DTO\VotingDTO;
use AppBundle\Entity\Repository\VoteSettingsRepository;
use AppBundle\Entity\VoteSettings;
use Symfony\Component\Serializer\Serializer;

class VotingModel
{
    /**
     * @var VoteSettingsRepository
     */
    protected $voteSettingsRepository;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(
        Serializer $serializer,
        VoteSettingsRepository $voteSettingsRepository
    ) {
        $this->serializer = $serializer;
        $this->voteSettingsRepository = $voteSettingsRepository;
    }

    /**
     * @return array
     */
    public function getVotingList(): array
    {
        $voteSettings = $this->voteSettingsRepository->findAll();
        $listVotedUserCount =  $this->voteSettingsRepository->getVotedUsersCountPerVoting();

        $votingList = [];
        /** @var VoteSettings $voteSetting */
        foreach ($voteSettings as $voteSetting) {
            $key = array_search($voteSetting->getId(), array_column($listVotedUserCount, 'id'));
            $votingList[] = new VotingDTO($voteSetting, $listVotedUserCount[$key]['voted']);
        }

        return $this->serializer->normalize($votingList, null, ['groups' => ['voting_list']]);
    }
}