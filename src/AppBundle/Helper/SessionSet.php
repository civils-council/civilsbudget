<?php
namespace AppBundle\Helper;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionSet
{
    const SESSION_PROJECT_ID = 'project_id';
    const SESSION_USER_CLID = 'user_clid';

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param string|null $id
     */
    public function setProjectId(?string $id)
    {
        $this->session->set(self::SESSION_PROJECT_ID, $id);
    }

    /**
     * @return bool
     */
    public function existsProjectId(): bool
    {
        return $this->session->has(self::SESSION_PROJECT_ID) && !empty($this->getProjectId());
    }

    /**
     * @return string
     */
    public function getProjectId(): ?string
    {
        return $this->session->get(self::SESSION_PROJECT_ID);
    }

    /**
     * @param string|null $id
     */
    public function setUserClid(?string $id)
    {
        $this->session->set(self::SESSION_USER_CLID, $id);
    }

    /**
     * @return bool
     */
    public function existsUserClid(): bool
    {
        return $this->session->has(self::SESSION_USER_CLID) && !empty($this->getUserClid());
    }

    /**
     * @return string
     */
    public function getUserClid(): ?string
    {
        return $this->session->get(self::SESSION_USER_CLID);
    }

    public function removeUserClid()
    {
        $this->session->remove(self::SESSION_USER_CLID);
    }
}
