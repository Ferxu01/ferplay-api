<?php


namespace App\Controller;


use App\BLL\VideojuegoBLL;
use App\Entity\Videojuego;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideojuegoRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/videojuegos/{id}.{_format}",
     *     name="get_videojuego",
     *     requirements={"id": "\d+","_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getOne(Videojuego $videojuego, VideojuegoBLL $videojuegoBLL)
    {
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
        $data = $this->getContent($request);
        $videojuego = $videojuegoBLL->nuevo($request, $data);
        return $this->getResponse($videojuego, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/videojuegos/{id}.{_format}",
     *     name="update_videojuego",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PUT"}
     * )
     */
    public function editar(Request $request, Videojuego $videojuego, VideojuegoBLL $videojuegoBLL)
    {
        $data = $this->getContent($request);
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
    public function borrar(Videojuego $videojuego, VideojuegoBLL $videojuegoBLL)
    {
        $videojuegoBLL->borrar($videojuego);
        return $this->getResponse(null, Response::HTTP_NO_CONTENT);
    }
}