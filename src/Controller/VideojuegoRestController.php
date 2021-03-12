<?php

namespace App\Controller;

use App\BLL\VideojuegoBLL;
use App\Entity\Like;
use App\Entity\Videojuego;
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
        $videojuegos = $videojuegoRepo->findAll();

        if (count($videojuegos) === 0) {
            $errores['mensaje'] = 'No se encontraron videojuegos';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        //Obtener y asignar like a los videojuegos que el usuario haya dado like
        $videojuegosUsuario = $videojuegoRepo->getVideojuegosUsuario($this->getUser()->getId());
        $likeRepo = $this->getDoctrine()->getRepository(Like::class);
        $likes = $likeRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);

        foreach ($likes as $like) {
            foreach ($videojuegosUsuario as $videojuego) {
                /*if ($like->getUsuario() === $this->getUser()) {
                    $videojuego->setLiked(true);
                }*/

                if ($like->getVideojuego() === $videojuego) {
                    $videojuego->setLiked(true);
                }
            }
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
    public function getOne(Videojuego $videojuego = null, VideojuegoBLL $videojuegoBLL)
    {
        if (is_null($videojuego)) {
            $errores['mensaje'] = 'El videojuego no existe';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($videojuegoBLL->toArray($videojuego));
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
    public function nuevo(Request $request, VideojuegoBLL $videojuegoBLL)
    {
        $errores['mensaje'] = [];

        $data = $this->getContent($request);
        if (empty($data['nombre']) || empty($data['descripcion'])
            || empty($data['precio']) || empty($data['imagen'])
            || empty($data['plataforma'])
        )
            array_push($errores['mensaje'],'Los campos no pueden estar vacíos');

        if (!is_int($data['plataforma']))
            array_push($errores['mensaje'],'La plataforma debe ser un número');
        if ($data['plataforma'] <= 0)
            array_push($errores['mensaje'], 'La plataforma no puede ser 0 o menor que 0');

        if (!is_int($data['precio']))
            array_push($errores['mensaje'], 'El precio debe ser un número');
        if ($data['precio'] <= 0)
            array_push($errores['mensaje'], 'El precio no puede ser 0 o menor que 0');

        if (count($errores) > 0)
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
    public function editar(Request $request, Videojuego $videojuego = null, VideojuegoBLL $videojuegoBLL)
    {
        $data = $this->getContent($request);
        $errores['mensajes'] = [];

        if (is_null($videojuego)) {
            array_push($errores['mensajes'], 'No se ha encontrado el videojuego');
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($videojuego->getUsuario() !== $this->getUser()) {
                array_push($errores['mensajes'], 'No puedes editar un videojuego que no has creado');
                $statusCode = Response::HTTP_FORBIDDEN;
            } else {
                if (empty($data['nombre']) || empty($data['descripcion']) || empty($data['plataforma'])
                    || empty($data['precio']) || empty($data['imagen']))
                    array_push($errores['mensajes'], 'Los campos no pueden estar vacíos');

                if (!is_int($data['plataforma']))
                    array_push($errores['mensajes'], 'La plataforma debe ser un número');
                if ($data['plataforma'] <= 0)
                    array_push($errores['mensajes'], 'La plataforma no puede ser 0 o menor que 0');

                if (!is_int($data['precio']))
                    array_push($errores['mensajes'], 'El precio debe ser un número');
                if ($data['precio'] <= 0)
                    array_push($errores['mensajes'], 'El precio no puede ser 0 o menor que 0');

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
    public function borrar(Videojuego $videojuego = null, VideojuegoBLL $videojuegoBLL)
    {
        if (is_null($videojuego)) {
            $errores['mensaje'] = 'El videojuego no se ha encontrado';
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            if ($this->getUser()->getId() !== $videojuego->getId()) {
                $errores['mensaje'] = 'No puedes eliminar un videojuego que no has creado';
                $statusCode = Response::HTTP_FORBIDDEN;
            }
        }

        if (count($errores) > 0)
            return $this->getErrorResponse($errores, $statusCode);

        $videojuegoBLL->borrar($videojuego);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}