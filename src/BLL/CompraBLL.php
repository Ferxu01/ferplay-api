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
            ->setFechaCompra(new DateTime())
            ->setPrecio($videojuego->getPrecio());

        $videojuego->setStock($videojuego->getStock() - $data['cantidad']);

        return $this->guardaValidando($compra);
    }

    public function getHistorialCompras()
    {
        $compraRepo = $this->em->getRepository(Compra::class);
        $compras = $compraRepo->findBy([
            'usuario' => $this->getUser()
        ]);
        return $this->entitiesToArray($compras);
    }

    public function toArray(Compra $compra)
    {
        if (is_null($compra))
            return null;

        return [
            'id' => $compra->getId(),
            'usuario' => $compra->getUsuario()->toArray(),
            'videojuego' => $compra->getVideojuego()->toArray(),
            'cantidad' => $compra->getCantidad(),
            'precio' => $compra->getPrecio(),
            'fechaCompra' => $compra->getFechaCompra()->format('Y-m-d H:i:s')
        ];
    }
}