<?php

namespace AppBundle\DTO;

use AppBundle\Entity\VoteSettings;
use Symfony\Component\Serializer\Annotation\Groups;

class VotingDTO
{
    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $backgroundImage;

    /**
     * @var VoteSettings
     */
    private $voteSettings;

    /**
     * @var int
     */
    private $voted;

    public function __construct(VoteSettings $voteSettings)
    {
        $this->voteSettings = $voteSettings;
        $this->logo = $voteSettings->getLogo();
        $this->backgroundImage = $voteSettings->getBackgroundImg();
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
        return $this->voteSettings->getStatus();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string
     */
    public function getTitleH1(): string
    {
        return $this->voteSettings->getTitleH1();
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->voteSettings->getTitle();
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
        return (int)$this->voted;
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
        return $this->logo;
    }

    /**
     * @Groups({"voting_list"})
     *
     * @return string|null
     */
    public function getBackgroundImage(): ?string
    {
        return $this->backgroundImage;
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

    /**
     * @param int|null $voted
     *
     * @return VotingDTO
     */
    public function setVoted(?int $voted = 0): VotingDTO
    {
        $this->voted = $voted;

        return $this;
    }

    /**
     * @param string $logo
     *
     * @return VotingDTO
     */
    public function setLogo(string $logo): VotingDTO
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @param string $backgroundImage
     *
     * @return VotingDTO
     */
    public function setBackgroundImage(string $backgroundImage): VotingDTO
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

}