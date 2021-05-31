<?php

namespace App\Controller;

use App\BLL\CompraBLL;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompraRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/buy.{_format}",
     *     name="buy_videojuegos_carro",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function nuevaCompra(CompraBLL $compraBLL)
    {
        $compraBLL->comprarVideojuegos();
        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/profile/buy/history",
     *     name="get_historial_compras",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getHistorialCompras(CompraBLL $compraBLL)
    {
        $compras = $compraBLL->getHistorialCompras();

        if (count($compras) === 0) {
            $errores['mensajes'] = 'No se ha realizado ninguna compra';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($compras);
    }

    /**
     * @Route(
     *     "/profile/buy/{lineaCompra}.{_format}",
     *     name="get_detalles_compra",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getDetallesCompra(int $lineaCompra, CompraBLL $compraBLL)
    {
        $videojuegosCompra = $compraBLL->getDetallesCompra($lineaCompra);

        return $this->getResponse($videojuegosCompra);
    }
}