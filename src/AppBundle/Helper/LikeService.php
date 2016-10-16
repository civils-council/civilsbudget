<?php

namespace AppBundle\Helper;

use AppBundle\Application\Project\ProjectInterface;
use AppBundle\Controller\ProjectController;
use AppBundle\Exception\ValidatorException;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\AWS\ServiceSES;

class LikeService
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * @var \AppBundle\AWS\ServiceSES
     */
    protected $mail;

    /**
     * @var ProjectInterface
     */
    protected $projectInterface;

    /**
     * LikeService constructor.
     * @param EntityManager $em
     * @param ServiceSES $mail
     * @param ProjectInterface $projectInterface
     */
    public function __construct(
        EntityManager $em, 
        ServiceSES $mail,
        ProjectInterface $projectInterface
    )
    {
        $this->em = $em;
        $this->mail = $mail;
        $this->projectInterface = $projectInterface;
    }

    public function execute(User $user, Project $project)
    {
        $arrayMessage = [];
        try {
            $arrayMessage['success'] = $this->getProjectInterface()->crateUserLike(
                $user,
                $project
            );

        } catch (ValidatorException $e) {
            $arrayMessage['danger'] = $e->getMessage();
        } catch (\Exception $e) {
            $arrayMessage['danger'] = ProjectController::SERVER_ERROR;
        }

        return $arrayMessage;
    }

    /**
     * @return ProjectInterface
     */
    public function getProjectInterface()
    {
        return $this->projectInterface;
    }
}
