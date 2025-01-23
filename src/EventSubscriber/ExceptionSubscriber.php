<?php 

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    // récupere la liste des evenements qu'on écoute
    public static function getSubscribedEvents(): Array
    {
        // déclenche la methode ExceptionEvent quand une exception survienne
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }

    // Gestion d'evenement Kernel.exception
    public function onKernelException(ExceptionEvent $event): void
    {

        // Récupere l'exception survenu
        $exception = $event->getThrowable();

        // détermine le type d'exception
        // si c'est un code d'erreur , on retourne son code 
        // sinon on retourne code 500
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        // récupere le message de l'exception
        $message = match($statusCode) {
            400 => 'Requete invalide',
            403 => 'acces interdit',
            404 => 'Ressource introuvable',
            default => 'Une erreur interne est survenue.'
        };

        // structure de la réponse en json
        $responseData = [
            "status" => $statusCode,
            "erreur" => $message
        ];

        // crée la reponse http au format JSON
        $response = new JsonResponse($responseData, $statusCode);

        // On envoie la réponse à l'evenement
        $event->setResponse($response);
    }
}

?>