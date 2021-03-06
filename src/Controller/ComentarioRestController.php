<?php

namespace App\Controller;

use App\BLL\ComentarioBLL;
use App\Entity\Comentario;
use App\Entity\Videojuego;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComentarioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/{id}/comentario.{_format}",
     *     name="post_comentario",
     *     requirements={"_format": "json", "id": "\d+"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function post(Request $request, Videojuego $videojuego, ComentarioBLL $comentarioBLL)
    {
        $data = $this->getContent($request);
        $comentario = $comentarioBLL->nuevo($request, $videojuego, $data);
        return $this->getResponse($comentario, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/comentario/{idComentario}.{_format}",
     *     name="delete_comentario",
     *     requirements={"_format": "json", "id": "\d+"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function delete(int $idComentario, Videojuego $videojuego, ComentarioBLL $comentarioBLL)
    {
        $comentarioRepository = $this->getDoctrine()->getRepository(Comentario::class);
        $comentario = $comentarioRepository->findOneBy([
            'id' => $idComentario
        ]);

        $comentarioBLL->delete($comentario);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}