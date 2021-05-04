<?php

namespace App\BLL;

use App\Entity\CarroCompra;
use App\Entity\Compra;
use App\Entity\Videojuego;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class CompraBLL extends BaseBLL
{
    public function comprarVideojuegos()
    {
        $carroRepo = $this->em->getRepository(CarroCompra::class);
        $videojuegosCarro = $carroRepo->findVideojuegosCarroUsuario($this->getUser());

        //Obtener el valor máximo de la linea de compra
        $compraRepo = $this->em->getRepository(Compra::class);
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

            $this->guardaValidando($compra);
        }

        $carroRepo->borrarVideojuegosCarro($this->getUser());
    }
    
    public function nuevaCompra(Request $request, Videojuego $videojuego, array $data)
    {
        //Obtener el valor máximo de la linea de compra
        $compraRepo = $this->em->getRepository(Compra::class);
        $lineaCompra = $compraRepo->getMaxLineaCompra()['maxLineaCompra'];

        $compra = new Compra();
        $compra->setUsuario($this->getUser())
            ->setVideojuego($videojuego)
            ->setCantidad($data['cantidad'])
            ->setFechaCompra(new DateTime())
            ->setLineaCompra($lineaCompra+1)
            ->setPrecio($videojuego->getPrecio());

        $videojuego->setStock($videojuego->getStock() - $data['cantidad']);

        return $this->guardaValidando($compra);
    }

    public function getHistorialCompras()
    {
        $compraRepo = $this->em->getRepository(Compra::class);

        //Obtener compras de un usuario agrupadas por linea de compra
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