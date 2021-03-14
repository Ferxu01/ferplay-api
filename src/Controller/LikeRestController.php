<?php

namespace App\Controller;

use App\BLL\LikeBLL;
use App\Entity\Videojuego;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/{id}/like.{_format}",
     *     name="like_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function darLikeVideojuego(Validation $validation, Videojuego $videojuego = null, LikeBLL $likeBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($videojuego->getLiked() === true) {
                $errores['mensajes'] = 'Ya has dado like a este videojuego';
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $like = $likeBLL->darLike($videojuego);
        return $this->getResponse($like, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/like.{_format}",
     *     name="delete_like_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function eliminarLikeVideojuego(Validation $validation, Videojuego $videojuego = null, LikeBLL $likeBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensajes'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($videojuego->getLiked() === false) {
                $errores['mensajes'] = 'No has dado like a este videojuego';
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $likeBLL->eliminarLike($videojuego);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}