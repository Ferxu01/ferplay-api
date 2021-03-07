<?php

namespace App\Controller;

use App\BLL\LikeBLL;
use App\Entity\Videojuego;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
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
    public function darLikeVideojuego(Videojuego $videojuego, LikeBLL $likeBLL)
    {
        $like = $likeBLL->darLike($videojuego);
        return $this->getResponse($like, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/delete/like.{_format}",
     *     name="like_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function eliminarLikeVideojuego(Videojuego $videojuego, LikeBLL $likeBLL)
    {
        $likeBLL->eliminarLike($videojuego);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}