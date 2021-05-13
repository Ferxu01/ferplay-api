<?php

namespace App\BLL;

use App\Entity\Provincia;

class ProvinciaBLL extends BaseBLL
{
    public function getAll()
    {
        $provinciaRepo = $this->em->getRepository(Provincia::class);
        $provincias = $provinciaRepo->findAll();

        return $this->entitiesToArray($provincias);
    }

    public function getOne(Provincia $provincia)
    {
        $provinciaRepo = $this->em->getRepository(Provincia::class);
        $provincia = $provinciaRepo->find($provincia);

        return $this->toArray($provincia);
    }

    public function toArray(Provincia $provincia)
    {
        if (is_null($provincia))
            return null;

        return [
            'id' => $provincia->getId(),
            'nombre' => $provincia->getNombre()
        ];
    }
}