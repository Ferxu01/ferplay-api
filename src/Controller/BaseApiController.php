<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BaseApiController extends AbstractController
{
    public function getContent(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (is_null($data))
            throw new BadRequestHttpException('No se han recibido los datos');

        return $data;
    }

    protected function getResponse(array $data=null, int $statusCode=Response::HTTP_OK)
    {
        $response = new JsonResponse();
        if (!is_null($data)) {
            $result['data'] = $data;
            $response->setContent(json_encode($result));
        }
        $response->setStatusCode($statusCode);

        return $response;
    }

    protected function getErrorResponse(array $errores, int $statusCode)
    {
        $errorResponse = new JsonResponse();
        $result['errores'] = $errores;
        $errorResponse->setContent(json_encode($result));
        $errorResponse->setStatusCode($statusCode);

        return $errorResponse;
    }
}