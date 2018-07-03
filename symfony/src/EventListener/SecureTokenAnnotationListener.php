<?php

namespace App\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use App\Annotation\SecureToken;
use App\Model\Exception\TokenException;
use App\Service\TokenService;
use ReflectionClass;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SecureTokenAnnotationListener
{
    /** @var AnnotationReader $reader */
    protected $reader;

    /** @var RequestStack $requestStack */
    protected $requestStack;

    /** @var TokenService $tokenService */
    protected $tokenService;

    /**
     * SecureTokenAnnotationListener constructor.
     * @param AnnotationReader $reader
     * @param RequestStack $stack
     * @param TokenService $tokenService
     */
    public function __construct(AnnotationReader $reader, RequestStack $stack, TokenService $tokenService)
    {
        $this->reader = $reader;
        $this->requestStack = $stack;
        $this->tokenService = $tokenService;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         *
         */
        if (!is_array($controller)) {
            return;
        }

        /** @var Controller $controllerObject */
        list($controllerObject, $methodName) = $controller;

        $request = $this->requestStack->getCurrentRequest();
        $headers = $request->headers;
        $apiKey = $headers->get('x-api-key');

        // Override the response only if the annotation is used for method or class
        if ($this->hasSecureTokenAnnotation($controllerObject, $methodName)) {
            try {
                if (!$apiKey || !$this->tokenService->isTokenExist($apiKey)) {
                    throw new \Exception('apiKey not found');
                }
            } catch (\Exception $e) {
                throw new AccessDeniedHttpException($e->getMessage(), $e);
            }
        }
    }

    /**
     * @param Controller $controllerObject
     * @param string $methodName
     * @return bool
     */
    private function hasSecureTokenAnnotation($controllerObject, string $methodName) : bool
    {
        $tokenAnnotation = SecureToken::class;

        $hasAnnotation = false;

        // Get class annotation
        // Using ClassUtils::getClass in case the controller is an proxy
        $classAnnotation = $this->reader->getClassAnnotation(
            new ReflectionClass(ClassUtils::getClass($controllerObject)), $tokenAnnotation
        );

        if ($classAnnotation) {
            $hasAnnotation = true;
        }

        // Get method annotation
        $controllerReflectionObject = new ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $tokenAnnotation);

        if ($methodAnnotation) {
            $hasAnnotation = true;
        }

        return $hasAnnotation;
    }
}