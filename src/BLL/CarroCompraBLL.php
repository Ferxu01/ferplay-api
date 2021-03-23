<?php

namespace App\BLL;

use App\Entity\CarroCompra;
use App\Entity\Videojuego;

class CarroCompraBLL extends BaseBLL
{
    public function obtenerVideojuegosCarro()
    {
        $carroRepo = $this->em->getRepository(CarroCompra::class);
        $videojuegos = $carroRepo->findBy([
            'usuario' => $this->getUser()
        ]);
        return $this->entitiesToArray($videojuegos);
    }

    public function nuevoVideojuegoCarro(array $data, Videojuego $videojuego)
    {
        $carroCompra = new CarroCompra();
        $carroCompra->setVideojuego($videojuego)
            ->setUsuario($this->getUser())
            ->setCantidad($data['cantidad']);

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