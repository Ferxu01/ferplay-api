<?php

namespace App\Controller;

use App\BLL\FavoritoBLL;
use App\Entity\Videojuego;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoritoRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/{id}/favourite.{_format}",
     *     name="add_favourites",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function addFavoritos(Videojuego $videojuego = null, FavoritoBLL $favoritoBLL)
    {
        if (is_null($videojuego)) {
            $errores['mensajes'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($videojuego->getFavourite() === true) {
                $errores['mensajes'] = 'Ya has dado like a este videojuego';
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $favorito = $favoritoBLL->addFavoritos($videojuego);
        return $this->getResponse($favorito, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/favourite.{_format}",
     *     name="delete_favourites",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function eliminaFavoritos(Videojuego $videojuego = null, FavoritoBLL $favoritoBLL)
    {
        if (is_null($videojuego)) {
            $errores['mensajes'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($videojuego->getFavourite() === false) {
                $errores['mensajes'] = 'No has añadido a favoritos este videojuego';
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $favoritoBLL->eliminaFavoritos($videojuego);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}