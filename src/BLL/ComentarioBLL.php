<?php

namespace App\BLL;

use App\Entity\Comentario;
use App\Entity\Videojuego;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class ComentarioBLL extends BaseBLL
{
    public function obtener(Videojuego $videojuego)
    {
        $comentarioRepo = $this->em->getRepository(Comentario::class);
        $comentarios = $comentarioRepo->findBy([
            'videojuego' => $videojuego
        ]);

        return $this->entitiesToArray($comentarios);
    }

    public function nuevo(Request $request, Videojuego $videojuego, array $data)
    {
        $comentario = new Comentario();
        $comentario->setTexto($data['comentario'])
            ->setUsuario($this->getUser())
            ->setVideojuego($videojuego)
            ->setFechaCreacion(new DateTime());

        return $this->guardaValidando($comentario);
    }

    public function delete($comentario)
    {
        $this->em->remove($comentario);
        $this->em->flush();
    }

    public function toArray(Comentario $comentario)
    {
        if (is_null($comentario))
            return null;

        return [
            'id' => $comentario->getId(),
            'texto' => $comentario->getTexto(),
            'fechaCreacion' => $comentario->getFechaCreacion()->format('Y-m-d H:i:s'),
            'usuario' => $comentario->getUsuario()->toArray(),
            'videojuego' => $comentario->getVideojuego()->getId()
        ];
    }
}