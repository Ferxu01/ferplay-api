<?php

namespace App\Controller;

use App\BLL\CarroCompraBLL;
use App\Entity\Videojuego;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarroCompraRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/carro.{_format}",
     *     name="get_videojuego_carro",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getVideojuegosCarro(Validation $validation, CarroCompraBLL $carroCompraBLL)
    {
        $videojuegos = $carroCompraBLL->obtenerVideojuegosCarro();
        if (count($videojuegos) === 0) {
            $errores['mensajes'] = 'No has añadido ningún videojuego aún';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($videojuegos);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/carro.{_format}",
     *     name="add_videojuego_carro",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function nuevoVideojuegoCarro(Validation $validation, Request $request, Videojuego $videojuego = null, CarroCompraBLL $carroCompraBLL)
    {
        $data = $this->getContent($request);

        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No existe el videojuego';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        $videojuego = $carroCompraBLL->nuevoVideojuegoCarro($data, $videojuego);
        return $this->getResponse($videojuego);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/carro.{_format}",
     *     name="delete_videojuego_carro",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function eliminarVideojuegoCarro(Validation $validation, Videojuego $videojuego = null, CarroCompraBLL $carroCompraBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No has añadido el videojuego en el carro aún';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        $carroCompraBLL->eliminarVideojuegoCarro($videojuego->getId());
        return $this->getResponse();
    }
}