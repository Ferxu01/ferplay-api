<?php

namespace App\BLL;

use App\Entity\Compra;
use App\Entity\Videojuego;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class CompraBLL extends BaseBLL
{
    public function nuevaCompra(Request $request, Videojuego $videojuego, array $data)
    {
        $compra = new Compra();
        $compra->setUsuario($this->getUser())
            ->setVideojuego($videojuego)
            ->setCantidad($data['cantidad'])
            ->setFechaCompra(new DateTime());

        return $this->guardaValidando($compra);
    }

    public function toArray(Compra $compra)
    {
        if (is_null($compra))
            return null;

        return [
            'id' => $compra->getId(),
            'usuario' => $compra->getUsuario(),
            'videojuego' => $compra->getVideojuego(),
            'cantidad' => $compra->getCantidad(),
            'fechaCompra' => $compra->getFechaCompra()->format('Y-m-d H:i:s')
        ];
    }
}