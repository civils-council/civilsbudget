<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Class OtpToken.
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="otp_token_idx", columns={"token"})}
 * )
 * @ORM\Entity()
 */
class OtpToken
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="otpTokens")
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $used = false;

    /**
     * @var bool
     */
    private $permission = false;

    /**
     * OtpToken constructor.
     *
     * @param int $id
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->generateToken();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return self
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return (string) $this->token;
    }

    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @param bool $used
     *
     * @return self
     */
    public function setUsed(bool $used): self
    {
        $this->used = $used;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function generateToken()
    {
        $this->token = random_int(100, 999);

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermission(): bool
    {
        return $this->permission;
    }

    /**
     * @param bool $permission
     *
     * @return self
     */
    public function setPermission(bool $permission): self
    {
        $this->permission = $permission;

        return $this;
    }
}
