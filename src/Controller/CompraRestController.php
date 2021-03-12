<?php

namespace App\Controller;

use App\BLL\CompraBLL;
use App\Entity\Videojuego;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompraRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/{id}/buy.{_format}",
     *     name="buy_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function nueva(Request $request, Videojuego $videojuego = null, CompraBLL $compraBLL)
    {
        if (is_null($videojuego)) {
            $errores['mensaje'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $data = $this->getContent($request);

            if (!is_int($data['cantidad'])) {
                $errores['mensaje'] = 'La cantidad debe ser un número';
            } elseif ($data['cantidad'] <= 0) {
                $errores['mensaje'] = 'La cantidad debe ser mayor que 0';
            }

            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        if (isset($errores['mensaje']))
            return $this->getErrorResponse($errores, $statusCode);

        $data = $this->getContent($request);
        $compraBLL->nuevaCompra($request, $videojuego, $data);
        return $this->getResponse();
    }
}