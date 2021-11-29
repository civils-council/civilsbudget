<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Controller\ProjectController;
use AppBundle\Helper\SessionSet;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProjectRequestSubscriber implements EventSubscriberInterface
{
    /** @var SessionSet */
    private $session;

    public function __construct(SessionSet $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof ProjectController) {
            return;
        }


        if ($event->getRequest()->get('_route') !== 'projects_show') {
            return;
        }

        $this->session->setProjectId($event->getRequest()->get('id'));
    }
}