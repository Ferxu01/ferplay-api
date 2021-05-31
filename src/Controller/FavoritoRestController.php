<?php

namespace App\Controller;

use App\BLL\FavoritoBLL;
use App\Entity\Favorito;
use App\Entity\Videojuego;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoritoRestController extends BaseApiController
{
    private function getFavoritosUsuario(): array
    {
        $favoritoRepo = $this->getDoctrine()->getRepository(Favorito::class);
        $favoritos = $favoritoRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);

        return $favoritos;
    }

    /**
     * @Route(
     *     "/videojuegos/favoritos.{_format}",
     *     name="get_videojuegos_favoritos_usuario",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getVideojuegosFavoritos(FavoritoBLL $favoritoBLL)
    {
        $videojuegos = $favoritoBLL->getVideojuegosFavoritos();

        if (count($videojuegos) < 1) {
            $errores['mensaje'] = 'No tienes videojuegos favoritos';
            $statusCode = Response::HTTP_NOT_FOUND;

            return $this->getErrorResponse($errores, $statusCode);
        }

        return $this->getResponse($favoritoBLL->entitiesToArray($videojuegos));
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/favourite.{_format}",
     *     name="add_favourites",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function addFavoritos(Validation $validation, Videojuego $videojuego = null, FavoritoBLL $favoritoBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $favoritos = $this->getFavoritosUsuario();

            foreach ($favoritos as $favorito) {
                if ($favorito->getVideojuego()->getId() === $videojuego->getId()) {
                    $errores['mensajes'] = 'Ya has añadido a favoritos a este videojuego';
                    $statusCode = Response::HTTP_BAD_REQUEST;
                }
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
    public function eliminaFavoritos(Validation $validation, Videojuego $videojuego = null, FavoritoBLL $favoritoBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $favoritoBLL->eliminaFavoritos($videojuego);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}