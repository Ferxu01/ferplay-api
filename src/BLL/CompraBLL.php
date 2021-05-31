<?php

namespace App\BLL;

use App\Entity\CarroCompra;
use App\Entity\Compra;
use DateTime;

class CompraBLL extends BaseBLL
{
    public function comprarVideojuegos()
    {
        $carroRepo = $this->em->getRepository(CarroCompra::class);
        $videojuegosCarro = $carroRepo->findVideojuegosCarroUsuario($this->getUser());
        $compraRepo = $this->em->getRepository(Compra::class);

        //Obtener el valor máximo de la linea de compra
        $lineaCompra = $compraRepo->getMaxLineaCompra()['maxLineaCompra'];
        $lineaCompra += 1;

        foreach ($videojuegosCarro as $videojuegoCarro) {
            $compra = new Compra();
            $compra->setLineaCompra($lineaCompra)
                ->setUsuario($videojuegoCarro->getUsuario())
                ->setVideojuego($videojuegoCarro->getVideojuego())
                ->setCantidad($videojuegoCarro->getCantidad())
                ->setFechaCompra(new DateTime())
                ->setPrecio($videojuegoCarro->getVideojuego()->getPrecio());

            $videojuegoCarro->getVideojuego()->setStock($videojuegoCarro->getVideojuego()->getStock() - $videojuegoCarro->getCantidad());

            $this->guardaValidando($compra);
        }

        $carroRepo->borrarVideojuegosCarro($this->getUser());
    }

    public function getHistorialCompras()
    {
        $compraRepo = $this->em->getRepository(Compra::class);
        $compras = $compraRepo->getHistorialCompras($this->getUser());

        return $this->entitiesToArray($compras);
    }

    public function getDetallesCompra(int $lineaCompra)
    {
        $compraRepo = $this->em->getRepository(Compra::class);
        $videojuegosCompra = $compraRepo->getVideojuegosCompra($lineaCompra);

        return $this->entitiesToArray($videojuegosCompra);
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
            'lineaCompra' => $compra->getLineaCompra(),
            'fechaCompra' => $compra->getFechaCompra()->format('Y-m-d H:i:s')
        ];
    }
}