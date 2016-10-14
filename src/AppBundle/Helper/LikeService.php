<?php

namespace AppBundle\Helper;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\AWS\ServiceSES;

class LikeService
{
    private $limitVotes;

    public function setLimitVotes($limitVotes)
    {
        //TODO get parameter from base
        $this->limitVotes = $limitVotes;
    }

    protected $em;
    /**
     * @var \AppBundle\AWS\ServiceSES
     */
    protected $mail;

    public function __construct(EntityManager $em, ServiceSES $mail)
    {
        $this->em = $em;
        $this->mail = $mail;
    }

    public function execute(User $user, Project $project)
    {
        if ($user instanceof User) {
            if ($project->getLastDateOfVotes() > new \DateTime()) {
                if ($user->getCountVotes() < $this->limitVotes) {
                    if (!$user->getLikedProjects()->contains($project)) {
                        if (mb_strtolower($user->getLocation()->getCity()) == mb_strtolower($project->getCity())) {
                            $user->setCountVotes(($user->getCountVotes()) ? ($user->getCountVotes() + 1) : 1);
                            $user->addLikedProjects($project);
                            $project->addLikedUser($user);
                            $this->em->flush();
                            $balanceVotes = $this->limitVotes - $user->getCountVotes();

                            // send email when voting ended
                            if ($balanceVotes == 0) {
                                $this->mail->sendEmail(
                                    [$user->getEmail()],
                                    'Golos.ck.ua: Онлайн голосування',
                                    'AppBundle:Email:votes_end.html.twig',
                                    [
                                        'user' => $user,
                                    ]
                                );
                            }
                            $arrayMessage =[];
                            $message = 'У Вас';
                            // TODO If the vote is more than 5 - will test endings (залишилось 5 голосів)
                            if ($balanceVotes >= 2) {
                                $message .= " залишилось $balanceVotes голоси";
                            } elseif ($balanceVotes == 1) {
                                $message .= ' залишився 1 голос';
                            } elseif ($balanceVotes == 0) {
                                $message .= ' залишилось 0 голосів';
                            }

                            $arrayMessage['status']='success';
                            $arrayMessage['text']='Дякуємо за Ваш голос. Ваш голос зараховано на підтримку проекту. ' . $message;
                        } else {
                            $arrayMessage['status']='danger';
                            $arrayMessage['text']='Цей проект не стосується міста в якому ви зареєстровані.';

                        }
                    } else {
                        $arrayMessage['status']='danger';
                        $arrayMessage['text']='Ви вже підтримали цей проект.';
                    }
                } else {
                    $arrayMessage['status']='danger';
                    $arrayMessage['text']='Ви вже вичерпали свій ліміт голосів.';
                }
            } else {
                $lastDate = $project->getLastDateOfVotes()->format('d.m.Y');
                $arrayMessage['status']='danger';
                $arrayMessage['text']='Вибачте. Кінцева дата голосування до ' . $lastDate;
            }
        } else {
            $arrayMessage['status']='danger';
            $arrayMessage['text']='Ви не маєте доступу до голосуваня за проект.';
        }

        return $arrayMessage;
    }
}
