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
            //Obtener nombre de la imagen formateando el nombre del videojuego
            $nombreArray = explode(' ', $videojuego->getNombre());
            $formatNombre = implode('-', $nombreArray);

            $fileName = $formatNombre . '-'. time() . '.jpg';
            $videojuego->setImagen($fileName);
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
            ->setPrecio($data['precio'])
            ->setImagen($data['imagen']);

        return $this->guardaImagen($request, $videojuego, $data);
    }

    public function nuevo(Request $request, array $data)
    {
        $plataforma = $this->em->getRepository(Plataforma::class)->find($data['plataforma']);

        $videojuego = new Videojuego();
        $videojuego->setNombre($data['nombre'])
            ->setDescripcion($data['descripcion'])
            ->setPlataforma($plataforma)
            ->setPrecio($data['precio'])
            ->setImagen($data['imagen'])
            ->setFechaCreacion(new DateTime());

        return $this->guardaImagen($request, $videojuego, $data);
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
            'plataforma' => $videojuego->getPlataforma()->toArray(),
            'precio' => $videojuego->getPrecio(),
            'imagen' => $videojuego->getImagen(),
            'fechaCreacion' => $videojuego->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}