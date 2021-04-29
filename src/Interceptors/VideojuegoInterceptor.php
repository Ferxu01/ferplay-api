<?php

namespace App\Interceptors;

use App\Entity\Favorito;
use App\Entity\Like;
use App\Entity\Usuario;
use App\Entity\Videojuego;

class VideojuegoInterceptor
{
    public function setVideojuegoLiked(Videojuego $videojuego): Videojuego
    {
        $videojuego->setLiked(true);
        return $videojuego;
    }

    public function setVideojuegoFavorito(Videojuego $videojuego): Videojuego
    {
        $videojuego->setFavourite(true);
        return $videojuego;
    }

    public function setVideojuegoMine(Videojuego $videojuego, Usuario $usuario): Videojuego
    {
        $videojuego->setMine(
            $usuario->getId() === $videojuego->getUsuario()->getId()
        );
        return $videojuego;
    }
}