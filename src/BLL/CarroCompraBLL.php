<?php

namespace App\BLL;

use App\Entity\CarroCompra;
use App\Entity\Videojuego;

class CarroCompraBLL extends BaseBLL
{
    public function obtenerVideojuegosCarro()
    {
        $carroRepo = $this->em->getRepository(CarroCompra::class);
        /*$videojuegos = $carroRepo->findBy([
            'usuario' => $this->getUser()
        ]);*/
        $videojuegos = $carroRepo->findVideojuegosCarroUsuario($this->getUser());
        return $this->entitiesToArray($videojuegos);
    }

    public function nuevoVideojuegoCarro(array $data, Videojuego $videojuego)
    {
        $carroCompraRepo = $this->em->getRepository(CarroCompra::class);
        $carroCompra = $carroCompraRepo->findOneBy([
            'videojuego' => $videojuego->getId(),
            'usuario' => $this->getUser()
        ]);

        if (is_null($carroCompra)) {
            $carroCompra = new CarroCompra();

            $carroCompra->setVideojuego($videojuego)
                ->setUsuario($this->getUser())
                ->setCantidad(1);
        } else {
            $carroCompra->setCantidad($carroCompra->getCantidad() + 1);
        }

        return $this->guardaValidando($carroCompra);
    }

    public function eliminarVideojuegoCarro($videojuegoId)
    {
        $carroRepo = $this->em->getRepository(CarroCompra::class);
        $videojuego = $carroRepo->findOneBy([
            'videojuego' => $videojuegoId,
            'usuario' => $this->getUser()
        ]);

        $this->em->remove($videojuego);
        $this->em->flush();
    }

    public function cambiarStockVideojuegoCarro(array $data, $idVideojuegoCarro)
    {
        $carroCompraRepo = $this->em->getRepository(CarroCompra::class);
        $videojuegoCarro = $carroCompraRepo->findOneBy([
            'id' => $idVideojuegoCarro
        ]);

        $videojuegoCarro->setCantidad($data['stock']);

        return $this->guardaValidando($videojuegoCarro);
    }

    public function toArray(CarroCompra $carroCompra)
    {
        if (is_null($carroCompra))
            return null;

        return [
            'id' => $carroCompra->getId(),
            'videojuego' => $carroCompra->getVideojuego()->toArray(),
            'usuario' => $carroCompra->getUsuario()->toArray(),
            'cantidad' => $carroCompra->getCantidad()
        ];
    }
}