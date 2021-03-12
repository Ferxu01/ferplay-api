<?php

namespace App\Controller;

use App\BLL\PlataformaBLL;
use App\Entity\Plataforma;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlataformaRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/plataformas.{_format}",
     *     name="get_plataformas",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getAll(PlataformaBLL $plataformaBLL)
    {
        $plataformas = $plataformaBLL->getAll();

        if (is_null($plataformas)) {
            $errores['mensaje'] = 'No se han encontrado plataformas';

            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($plataformas);
    }
}