<?php

namespace App\Controller;

use App\BLL\ComentarioBLL;
use App\Entity\Comentario;
use App\Entity\Videojuego;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComentarioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/{id}/comentarios.{_format}",
     *     name="get_comentarios",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getComentarios(Validation $validation, Videojuego $videojuego, ComentarioBLL $comentarioBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensaje'] = 'No se ha encontrado el videojuego';
        } else {
            $comentarios = $comentarioBLL->obtener($videojuego);

            if (count($comentarios) === 0)
                $errores['mensaje'] = 'No se encontraron comentarios';
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);

        return $this->getResponse($comentarios);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/comentario.{_format}",
     *     name="post_comentario",
     *     requirements={"_format": "json", "id": "\d+"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function post(Validation $validation, Request $request, Videojuego $videojuego = null, ComentarioBLL $comentarioBLL)
    {
        $statusCode = Response::HTTP_BAD_REQUEST;
        $data = $this->getContent($request);

        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensaje'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else if (empty($data['comentario'])) {
            $errores['mensaje'] = 'El comentario no puede estar vacío';
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $comentario = $comentarioBLL->nuevo($request, $videojuego, $data);

        return $this->getResponse($comentario, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}/comentario/{idComentario}.{_format}",
     *     name="delete_comentario",
     *     requirements={"_format": "json", "id": "\d+", "idComentario": "\d+"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function delete(Validation $validation, int $idComentario, Videojuego $videojuego = null, ComentarioBLL $comentarioBLL)
    {
        $comentarioRepository = $this->getDoctrine()->getRepository(Comentario::class);

        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensaje'] = 'No se ha encontrado el videojuego';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $comentario = $comentarioRepository->find($idComentario);

            if (!$validation->existeEntidad($comentario)) {
                $errores['mensaje'] = 'El comentario no existe';
                $statusCode = Response::HTTP_NOT_FOUND;
            } else {
                if ($this->getUser()->getId() !== $comentario->getUsuario()->getId()) {
                    $errores['mensaje'] = 'No puedes eliminar comentarios que no hayas creado';
                    $statusCode = Response::HTTP_FORBIDDEN;
                }
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $comentarioBLL->delete($comentario);

        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}