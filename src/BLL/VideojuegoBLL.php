<?php

namespace App\BLL;

use App\Entity\Plataforma;
use App\Entity\Videojuego;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VideojuegoBLL extends BaseBLL
{
    private function guardaImagen($request, $videojuego, $data) {
        $arr_imagen = explode (',', $data['imagen']);
        if (count($arr_imagen) < 2)
            throw new BadRequestHttpException('Formato de imagen incorrecto');

        $imgFoto = base64_decode($arr_imagen[1]);
        if (!is_null($imgFoto))
        {
            $fileName = $data['nombreFoto'] . '-'. time() . '.jpg';
            $videojuego->setFoto($fileName);
            $ifp = fopen($this->videojuegosDirectory . '/' . $fileName, "wb");
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

        $videojuego->setNombre($data['nombre'])
            ->setDescripcion($data['descripcion'])
            ->setPlataforma($plataforma)
            ->setPrecio($data['precio']);

        return $this->guardaValidando($videojuego);
        //return $this->guardaImagen($request, $videojuego, $data);
    }

    public function nuevo(array $data)
    {
        $plataforma = $this->em->getRepository(Plataforma::class)->find($data['plataforma']);

        $videojuego = new Videojuego();
        $videojuego->setNombre($data['nombre'])
            ->setDescripcion($data['descripcion'])
            ->setPlataforma($plataforma)
            ->setPrecio($data['precio'])
            ->setFechaCreacion(new DateTime());

        return $this->guardaValidando($videojuego);
    }

    public function editar(Request $request, Videojuego $videojuego, array $data)
    {
        return $this->actualizaVideojuego($request, $videojuego, $data);
    }

    public function borrar($videojuego)
    {
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
            'plataforma' => $videojuego->getPlataforma(),
            'precio' => $videojuego->getPrecio(),
            'fechaCreacion' => $videojuego->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}