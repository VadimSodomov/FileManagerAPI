<?php

declare(strict_types=1);

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionDTO implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException'
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if($exception instanceof ValidationFailedException || $exception->getPrevious() instanceof ValidationFailedException) {
            $validationFailedException = ($exception instanceof ValidationFailedException)
                ? $exception
                : $exception->getPrevious()
            ;

            $errors = [];
            foreach($validationFailedException->getViolations() as $violation) {
                $errors[] = [
                    'path' => $violation->getPropertyPath(),
                    'error' => $violation->getMessage()
                ];
            }

            $event->setResponse(new JsonResponse($errors, 400));
        }
    }
}
