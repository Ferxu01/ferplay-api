<?php

namespace App\Controller;

use App\BLL\VideojuegoBLL;
use App\Entity\Favorito;
use App\Entity\Like;
use App\Entity\Usuario;
use App\Entity\Videojuego;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class VideojuegoRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos.{_format}",
     *     name="get_videojuegos",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getAll(VideojuegoBLL $videojuegoBLL)
    {
        $videojuegoRepo = $this->getDoctrine()->getRepository(Videojuego::class);

        //Obtener todos los videojuegos
        $videojuegos = $videojuegoBLL->getAllVideojuegos();

        if (count($videojuegos) === 0) {
            $errores['mensaje'] = 'No se encontraron videojuegos';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($videojuegoBLL->entitiesToArray($videojuegos));
    }

    /**
     * @Route(
     *     "/videojuegos/{id}.{_format}",
     *     name="get_videojuego",
     *     requirements={"id": "\d+","_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getOne(Validation $validation, Videojuego $videojuego = null, VideojuegoBLL $videojuegoBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensaje'] = 'El videojuego no existe';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($videojuegoBLL->toArray($videojuego));
    }

    /**
     * @Route(
     *     "/videojuegos/usuario/{id}.{_format}",
     *     name="get_videojuegos_usuario",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getVideojuegosUsuario(Validation $validation, VideojuegoBLL $videojuegoBLL, Usuario $usuario = null)
    {
        if (!$validation->existeEntidad($usuario)) {
            $errores['mensaje'] = 'No existe el usuario';
            $statusCode = Response::HTTP_NOT_FOUND;

            return $this->getErrorResponse($errores, $statusCode);
        }

        $videojuegos = $videojuegoBLL->getVideojuegosUsuario($usuario);

        if (count($videojuegos) === 0) {
            $errores['mensaje'] = 'El usuario no ha subido videojuegos';
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        return $this->getResponse($videojuegoBLL->entitiesToArray($videojuegos));
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
    public function getVideojuegosFavoritos(VideojuegoBLL $videojuegoBLL)
    {
        $videojuegos = $videojuegoBLL->getVideojuegosFavoritos();

        return $this->getResponse($videojuegoBLL->entitiesToArray($videojuegos));
    }

    /**
     * @Route(
     *     "/videojuegos.{_format}",
     *     name="post_videojuego",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function nuevo(Validation $validation, Request $request, VideojuegoBLL $videojuegoBLL)
    {
        $errores['mensaje'] = [];

        $data = $this->getContent($request);
        if ($validation->datosVideojuegosVacios(
            $data['nombre'], $data['descripcion'], $data['precio'],
            $data['imagen'], $data['plataforma'], $data['stock']
        ))
            array_push($errores['mensaje'],'Los campos no pueden estar vacíos');

        if (!$validation->esNumerico($data['plataforma']))
            array_push($errores['mensaje'],'La plataforma debe ser un número');
        if ($validation->esNumeroNegativo($data['plataforma']))
            array_push($errores['mensaje'], 'La plataforma no puede ser 0 o menor que 0');

        if (!$validation->esNumerico($data['precio']))
            array_push($errores['mensaje'], 'El precio debe ser un número');
        if ($validation->esNumeroNegativo($data['precio']))
            array_push($errores['mensaje'], 'El precio no puede ser 0 o menor que 0');

        if (!$validation->esNumerico($data['stock']))
            array_push($errores['mensaje'], 'El stock debe ser un número');
        if ($validation->esNumeroNegativo($data['stock']))
            array_push($errores['mensaje'], 'El stock no puede ser 0 o menor que 0');

        if (count($errores['mensaje']) > 0)
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);

        $videojuego = $videojuegoBLL->nuevo($request, $data);
        return $this->getResponse($videojuego, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/videojuegos/edit/{id}.{_format}",
     *     name="update_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PUT"}
     * )
     */
    public function editar(Validation $validation, Request $request, Videojuego $videojuego = null, VideojuegoBLL $videojuegoBLL)
    {
        $data = $this->getContent($request);
        $errores['mensajes'] = [];

        if (!$validation->existeEntidad($videojuego)) {
            array_push($errores['mensajes'], 'No se ha encontrado el videojuego');
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($videojuego->getUsuario() !== $this->getUser()) {
                array_push($errores['mensajes'], 'No puedes editar un videojuego que no has creado');
                $statusCode = Response::HTTP_FORBIDDEN;
            } else {
                if ($validation->datosVideojuegosVacios(
                    $data['nombre'], $data['descripcion'], $data['precio'],
                    $data['imagen'], $data['plataforma'], $data['stock']
                ))
                    array_push($errores['mensajes'], 'Los campos no pueden estar vacíos');

                if (!$validation->esNumerico($data['plataforma']))
                    array_push($errores['mensajes'], 'La plataforma debe ser un número');
                if ($validation->esNumeroNegativo($data['plataforma']))
                    array_push($errores['mensajes'], 'La plataforma no puede ser 0 o menor que 0');

                if (!$validation->esNumerico($data['precio']))
                    array_push($errores['mensajes'], 'El precio debe ser un número');
                if ($validation->esNumeroNegativo($data['precio']))
                    array_push($errores['mensajes'], 'El precio no puede ser 0 o menor que 0');

                if (!$validation->esNumerico($data['stock']))
                    array_push($errores['mensajes'], 'El stock debe ser un número');
                if ($validation->esNumeroNegativo($data['stock']))
                    array_push($errores['mensajes'], 'El stock no puede ser 0 o menor que 0');

                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }

        if (count($errores['mensajes']) > 0)
            return $this->getErrorResponse($errores, $statusCode);

        $videojuego = $videojuegoBLL->editar($request, $videojuego, $data);
        return $this->getResponse($videojuego);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}.{_format}",
     *     name="delete_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"DELETE"}
     * )
     */
    public function borrar(Validation $validation, Videojuego $videojuego = null, VideojuegoBLL $videojuegoBLL)
    {
        if (!$validation->existeEntidad($videojuego)) {
            $errores['mensaje'] = 'El videojuego no se ha encontrado';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($this->getUser()->getId() !== $videojuego->getUsuario()->getId()) {
                $errores['mensaje'] = 'No puedes eliminar un videojuego que no has creado';
                $statusCode = Response::HTTP_FORBIDDEN;
            }
        }

        if (isset($errores))
            return $this->getErrorResponse($errores, $statusCode);

        $videojuegoBLL->borrar($videojuego);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}