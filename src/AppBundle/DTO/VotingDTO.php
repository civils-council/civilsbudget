<?php

namespace AppBundle\DTO;

use AppBundle\Entity\VoteSettings;
use Symfony\Component\Serializer\Annotation\Groups;

class VotingDTO
{
    const
        STATUS_ARCHIVED = 'archived',
        STATUS_ACTIVE = 'active',
        STATUS_FUTURE = 'future';

    /**
     * @var VoteSettings
     */
    private $voteSettings;

    /**
     * @var int
     */
    private $voted;

    public function __construct(VoteSettings $voteSettings, ?int $voted = 0)
    {
        $this->voteSettings = $voteSettings;
        $this->voted = $voted;
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->voteSettings->getId();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string
     */
    public function getStatus(): string
    {
        $now = new \DateTime;

        if ($this->voteSettings->getDateTo() < $now) {
            return self::STATUS_ARCHIVED;
        }

        if ($this->voteSettings->getDateFrom() > $now) {
            return self::STATUS_FUTURE;
        }

        return self::STATUS_ACTIVE;
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->voteSettings->getTitleH1();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->voteSettings->getDescription();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return int
     */
    public function getMaxVotesCount(): int
    {
        return $this->voteSettings->getVoteLimits();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return int
     */
    public function getVoted(): int
    {
        return $this->voted;
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return \DateTime
     */
    public function getDateFrom(): \DateTime
    {
        return $this->voteSettings->getDateFrom();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return \DateTime
     */
    public function getDateTo(): \DateTime
    {
        return $this->voteSettings->getDateTo();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->voteSettings->getLogo();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string|null
     */
    public function getBackgroundImage(): ?string
    {
        return $this->voteSettings->getBackgroundImg();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string
     */
    public function getLocation(): string
    {
        return $this->voteSettings->getLocation()->getCity();
    }
}