<?php

namespace App\BLL;

use App\Entity\Favorito;
use App\Entity\Videojuego;

class FavoritoBLL extends BaseBLL
{
    public function getVideojuegosFavoritos()
    {
        $favoritoRepo = $this->em->getRepository(Favorito::class);
        $favoritos = $favoritoRepo->findBy([
            'usuario' => $this->getUser()
        ]);

        $videojuegoRepo = $this->em->getRepository(Videojuego::class);
        $videojuegos = $videojuegoRepo->findAll();

        $videojuegosFavoritos = [];

        foreach ($videojuegos as $videojuego) {
            foreach ($favoritos as $favorito) {
                if ($videojuego->getId() === $favorito->getVideojuego()->getId()) {
                    array_push($videojuegosFavoritos, $videojuego);
                }
            }
        }

        return $videojuegosFavoritos;
    }

    public function addFavoritos(Videojuego $videojuego)
    {
        $favorito = new Favorito();
        $favorito->setUsuario($this->getUser());
        $favorito->setVideojuego($videojuego);

        return $this->guardaValidando($favorito);
    }

    public function eliminaFavoritos(Videojuego $videojuego)
    {
        $favoritoRepo = $this->em->getRepository(Favorito::class);
        $favorito = $favoritoRepo->findOneBy([
            'videojuego' => $videojuego,
            'usuario' => $this->getUser()
        ]);

        $this->em->remove($favorito);
        $this->em->flush();
    }

    public function toArray()
    {
    }
}