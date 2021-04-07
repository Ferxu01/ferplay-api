<?php

namespace App\Controller;

use App\BLL\CompraBLL;
use App\Entity\Videojuego;
use App\Helpers\Validation;
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
    public function nueva(Validation $validation, Request $request, Videojuego $videojuego = null, CompraBLL $compraBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensaje'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $data = $this->getContent($request);

            if (!$validation->esNumerico($data['cantidad'])) {
                $errores['mensaje'] = 'La cantidad debe ser un número';
            } elseif ($validation->esNumeroNegativo($data['cantidad'])) {
                $errores['mensaje'] = 'La cantidad debe ser mayor que 0';
            }

            if (!$validation->stockValido($videojuego, $data['cantidad'])) {
                $errores['mensaje'] = 'No hay stock disponible para esta compra';
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