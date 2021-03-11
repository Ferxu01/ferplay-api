<?php

namespace App\BLL;

use App\Entity\Like;
use App\Entity\Videojuego;

class LikeBLL extends BaseBLL
{
    public function darLike(Videojuego $videojuego)
    {
        $like = new Like();
        $like->setVideojuego($videojuego);
        $like->setUsuario($this->getUser());
        $videojuego->setLiked(true);

        return $this->guardaValidando($like);
    }

    public function eliminarLike(Videojuego $videojuego)
    {
        $likeRepository = $this->em->getRepository(Like::class);
        $like = $likeRepository->findOneBy([
            'videojuego' => $videojuego,
            'usuario' => $this->getUser()
        ]);
        $videojuego->setLiked(false);

        $this->em->remove($like);
        $this->em->flush();
    }

    public function toArray()
    {
    }
}