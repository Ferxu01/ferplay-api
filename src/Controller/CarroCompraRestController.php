<?php

namespace App\Controller;

use App\BLL\CarroCompraBLL;
use App\Entity\CarroCompra;
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
    public function getVideojuegosCarro(CarroCompraBLL $carroCompraBLL)
    {
        $videojuegos = $carroCompraBLL->obtenerVideojuegosCarro();
        if (count($videojuegos) === 0) {
            $errores['mensajes'] = 'No has añadido ningún videojuego';
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
     *     "/videojuegos/{id}/carro/{idCarroCompra}.{_format}",
     *     name="delete_videojuego_carro",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function eliminarVideojuegoCarro(Validation $validation, int $idCarroCompra, Videojuego $videojuego = null, CarroCompraBLL $carroCompraBLL)
    {
        $carroRepo = $this->getDoctrine()->getRepository(CarroCompra::class);

        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No existe el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $videojuegoCarro = $carroRepo->find($idCarroCompra);

            if (!$validation->existeEntidad($videojuegoCarro)) {
                $errores['mensajes'] = 'El videojuego no existe en el carro';
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                if ($this->getUser()->getId() !== $videojuegoCarro->getUsuario()->getId()) {
                    $errores['mensajes'] = 'No puedes eliminar videojuego del carro que no hayas añadido';
                    $statusCode = Response::HTTP_FORBIDDEN;
                }
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $carroCompraBLL->eliminarVideojuegoCarro($videojuego->getId());

        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/carro/{idCarroCompra}.{_format}",
     *     name="update_stock_videojuego_carro",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     */
    public function cambiarStockVideojuegoCarro(Validation $validation, Request $request, Videojuego $videojuego = null, CarroCompraBLL $carroCompraBLL, int $idCarroCompra)
    {
        $data = $this->getContent($request);

        if (!$validation->esNumerico($data['stock'])) {
            $errores['mensaje'] = 'El stock debe ser un número';
        } else if ($validation->esNumeroNegativo($data['stock'])) {
            $errores['mensaje'] = 'El stock no puede ser negativo';
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);

        $videojuegoCarro = $carroCompraBLL->cambiarStockVideojuegoCarro($data, $idCarroCompra);

        return $this->getResponse($videojuegoCarro);
    }
}