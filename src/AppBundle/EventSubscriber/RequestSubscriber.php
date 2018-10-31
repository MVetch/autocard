<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 11.02.18
 * Time: 18:16
 */

namespace AppBundle\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['redirectToFront', 10]
            ]
        ];
    }

    public function redirectToFront(GetResponseEvent $event)
    {
        $request = $event->getRequest();

//        if (!preg_match('#^/api/.*$#', $request->getPathInfo()) || !preg_match('#^/export/.*$#', $request->getPathInfo())) {
//            return new RedirectResponse('/');
//        }
    }
}