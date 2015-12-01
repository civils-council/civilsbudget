<?php
namespace AppBundle\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionSet
{
    const SESSION_NAME = 'project_id';

    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setSession($id)
    {
        $this->session->set(self::SESSION_NAME, $id);
        return true;
    }

    public function check()
    {
        $result = false;
        if (!empty($this->session->get(self::SESSION_NAME))) {
            $result = true;
        }
        return $result;
    }

    public function getProjectId()
    {
        return $this->session->get(self::SESSION_NAME);
    }
}
