<?php

namespace App\BLL;

use App\Entity\Favorito;
use App\Entity\Like;
use App\Entity\Plataforma;
use App\Entity\Usuario;
use App\Entity\Videojuego;
use App\Helpers\EntityUrl;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VideojuegoBLL extends BaseBLL
{
    private $urlDirVideojuegos = __DIR__ . '\..\..\public\img\videogames\\';

    private function guardaImagen($request, $videojuego, $data) {
        $arr_imagen = explode (',', $data['imagen']);
        if (count($arr_imagen) < 2)
            throw new BadRequestHttpException('Formato de imagen incorrecto');

        $imgFoto = base64_decode($arr_imagen[1]);

        if (!is_null($imgFoto))
        {
            //Obtener nombre de la imagen formateando el nombre del videojuego
            $nombreArray = explode(' ', $videojuego->getNombre());
            $formatNombre = implode('-', $nombreArray);

            $fileName = $formatNombre . '-'. time() . '.jpg';
            $videojuego->setImagen($fileName);

            $ifp = fopen($this->urlDirVideojuegos . $fileName, "wb");
            if ($ifp)
            {
                $ok = fwrite ($ifp, $imgFoto);
                fclose ($ifp);
                if ($ok)
                    return $this->guardaValidando($videojuego);
            }
        }

        throw new Exception('No se ha podido cargar la imagen del videojuego');
    }

    private function actualizaVideojuego(Request $request, Videojuego $videojuego, array $data)
    {
        $plataforma = $this->em->getRepository(Plataforma::class)->find($data['plataforma']);

        $imagenCambiada = strpos($data['imagen'], 'http://');

        if ($imagenCambiada === false) {
            $nombreImagen = EntityUrl::getNombreImagenVideojuego($videojuego);
            unlink($this->urlDirVideojuegos . $nombreImagen);

            $videojuego->setNombre($data['nombre'])
                ->setDescripcion($data['descripcion'])
                ->setPlataforma($plataforma)
                ->setPrecio($data['precio'])
                ->setImagen($data['imagen'])
                ->setStock($data['stock']);

            return $this->guardaImagen($request, $videojuego, $data);
        }

        $videojuego->setNombre($data['nombre'])
            ->setDescripcion($data['descripcion'])
            ->setPlataforma($plataforma)
            ->setPrecio($data['precio'])
            ->setStock($data['stock']);

        return $this->guardaValidando($videojuego);
    }

    public function getAllVideojuegos()
    {
        $videojuegoRepo = $this->em->getRepository(Videojuego::class);
        $likeRepo = $this->em->getRepository(Like::class);
        $favoritoRepo = $this->em->getRepository(Favorito::class);

        $videojuegos = $videojuegoRepo->getAllVideojuegos();
        $likes = $likeRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);
        $favoritos = $favoritoRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);

        foreach ($videojuegos as $videojuego) {
            $videojuego = $this->videojuegoHelper->setVideojuegoMine($videojuego, $this->getUser());

            foreach ($likes as $like) {
                if ($videojuego->getId() === $like->getVideojuego()->getId()) {
                    $videojuego = $this->videojuegoHelper
                        ->setVideojuegoLiked($videojuego);
                }
            }

            foreach ($favoritos as $favorito) {
                if ($videojuego->getId() === $favorito->getVideojuego()->getId()) {
                    $videojuego = $this->videojuegoHelper
                        ->setVideojuegoFavorito($videojuego);
                }
            }
        }

        return $videojuegos;
    }

    public function getDetallesVideojuego(Videojuego $videojuego)
    {
        $likeRepo = $this->em->getRepository(Like::class);
        $favoritoRepo = $this->em->getRepository(Favorito::class);

        $likes = $likeRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);
        $favoritos = $favoritoRepo->findBy([
            'usuario' => $this->getUser()->getId()
        ]);

        foreach ($likes as $like) {
            if ($videojuego->getId() === $like->getVideojuego()->getId()) {
                $videojuego = $this->videojuegoHelper
                    ->setVideojuegoLiked($videojuego);
            }
        }

        foreach ($favoritos as $favorito) {
            if ($videojuego->getId() === $favorito->getVideojuego()->getId()) {
                $videojuego = $this->videojuegoHelper
                    ->setVideojuegoFavorito($videojuego);
            }
        }

        $videojuego = $this->videojuegoHelper->setVideojuegoMine($videojuego, $this->getUser());

        return $videojuego->toArray();
    }

    public function getVideojuegosUsuario(Usuario $usuario)
    {
        $videojuegoRepo = $this->em->getRepository(Videojuego::class);

        return $videojuegoRepo->getVideojuegosUsuario($usuario->getId());
    }

    public function nuevo(Request $request, array $data)
    {
        $plataforma = $this->em->getRepository(Plataforma::class)->find($data['plataforma']);

        $videojuego = new Videojuego();
        $videojuego->setNombre($data['nombre'])
            ->setDescripcion($data['descripcion'])
            ->setPlataforma($plataforma)
            ->setPrecio($data['precio'])
            ->setUsuario($this->getUser())
            ->setFechaCreacion(new DateTime())
            ->setLiked(false)
            ->setFavourite(false)
            ->setMine(false)
            ->setNumLikes(0)
            ->setStock($data['stock']);

        return $this->guardaImagen($request, $videojuego, $data);
    }

    public function editar(Request $request, Videojuego $videojuego, array $data)
    {
        return $this->actualizaVideojuego($request, $videojuego, $data);
    }

    public function borrar($videojuego)
    {
        $nombreImagen = EntityUrl::getNombreImagenVideojuego($videojuego);
        unlink($this->urlDirVideojuegos . $nombreImagen);

        $this->em->remove($videojuego);
        $this->em->flush();
    }

    public function toArray(Videojuego $videojuego)
    {
        if (is_null($videojuego))
            return null;

        return [
            'id' => $videojuego->getId(),
            'nombre' => $videojuego->getNombre(),
            'descripcion' => $videojuego->getDescripcion(),
            'plataforma' => $videojuego->getPlataforma()->toArray(),
            'precio' => $videojuego->getPrecio(),
            'imagen' => $this->server_url . $this->videojuegosUrl . $videojuego->getImagen(),
            'usuario' => $videojuego->getUsuario()->toArray(),
            'liked' => $videojuego->getLiked(),
            'favourite' => $videojuego->getFavourite(),
            'numLikes' => $videojuego->getNumLikes(),
            'mine' => $videojuego->getMine(),
            'stock' => $videojuego->getStock(),
            'fechaCreacion' => $videojuego->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}