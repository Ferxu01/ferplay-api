<?php

namespace App\Entity;

use App\Repository\CarroCompraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarroCompraRepository::class)
 */
class CarroCompra
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Videojuego::class, inversedBy="carroCompras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $videojuego;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="carroCompras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideojuego(): ?Videojuego
    {
        return $this->videojuego;
    }

    public function setVideojuego(?Videojuego $videojuego): self
    {
        $this->videojuego = $videojuego;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }
}
