<?php

namespace App\Controller;

use App\BLL\LikeBLL;
use App\Entity\Like;
use App\Entity\Videojuego;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeRestController extends BaseApiController
{
    private function getLikesUsuario(): array
    {
        $likeRepo = $this->getDoctrine()->getRepository(Like::class);
        $likes = $likeRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);

        return $likes;
    }

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
            $likes = $this->getLikesUsuario();

            foreach ($likes as $like) {
                if ($like->getVideojuego()->getId() === $videojuego->getId()) {
                    $errores['mensajes'] = 'Ya has dado like a este videojuego';
                    $statusCode = Response::HTTP_BAD_REQUEST;
                }
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
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $likeBLL->eliminarLike($videojuego);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}