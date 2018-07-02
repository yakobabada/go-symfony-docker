<?php

namespace App\Controller\Api;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;

    /**
     * @param array $data
     * @param string $format
     *
     * @return mixed
     */
    protected function serialize($data, $format = 'json')
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->container->get('jms_serializer')
            ->serialize($data, $format, $context);
    }

    /**
     * @param array|Request $data
     * @param mixed $target
     *
     * @return mixed
     */
    protected function deserialize($data, $target)
    {
        $json = $data instanceof Request ? $data->getContent() : json_encode($data);
        $className = get_class($target);

        $context = new DeserializationContext();
        $context->attributes->set('target', $target);

        return $this->get('jms_serializer')
            ->deserialize($json, $className, 'json', $context);
    }

    /**
     * @param $data
     * @param int $statusCode
     *
     * @return Response
     */
    protected function createApiResponse($data, $statusCode = Response::HTTP_OK)
    {
        $json = $this->serialize($data);

        return new Response($json, $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @param $errors
     * @param int $statusCode
     *
     * @return JsonResponse
     */
    protected function createErrorApiResponse($errors, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $errorList = [];

        foreach ($errors as $error) {
            $errorList[$error->getPropertyPath()][] = $error->getMessage();
        }

        $data = [
            'type' => 'validation_error',
            'title' => 'There was a validation error',
            'errors' => $errorList
        ];

        return new JsonResponse($data, $statusCode);
    }
}