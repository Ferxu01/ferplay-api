<?php

namespace App\Controller;

use App\BLL\ProvinciaBLL;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProvinciaRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/provincias.{_format}",
     *     name="get_provincias",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getProvincias(Validation $validation, ProvinciaBLL $provinciaBLL)
    {
        $provincias = $provinciaBLL->getAll();

        if (!$validation->existeEntidad($provincias)) {
            $errores['mensaje'] = 'No se han encontrado provincias';

            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($provincias);
    }
}