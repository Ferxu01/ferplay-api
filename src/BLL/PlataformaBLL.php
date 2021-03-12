<?php

namespace App\BLL;

use App\Entity\Plataforma;

class PlataformaBLL extends BaseBLL
{
    public function getAll()
    {
        $plataformaRepo = $this->em->getRepository(Plataforma::class);
        $plataformas = $plataformaRepo->findAll();

        return $this->entitiesToArray($plataformas);
    }

    public function toArray(Plataforma $plataforma)
    {
        if (is_null($plataforma))
            return null;

        return [
            'id' => $plataforma->getId(),
            'nombre' => $plataforma->getNombre()
        ];
    }
}