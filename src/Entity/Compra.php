<?php

namespace App\Entity;

use App\Repository\CompraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompraRepository::class)
 */
class Compra
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="compras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Videojuego::class, inversedBy="compras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $videojuego;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCompra;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad;

    /**
     * @ORM\Column(type="integer")
     */
    private $precio;

    /**
     * @ORM\Column(type="integer")
     */
    private $lineaCompra;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVideojuego(): ?Videojuego
    {
        return $this->videojuego;
    }

    public function setVideojuego(?Videojuego $videojuego): self
    {
        $this->videojuego = $videojuego;

        return $this;
    }

    public function getFechaCompra(): ?\DateTimeInterface
    {
        return $this->fechaCompra;
    }

    public function setFechaCompra(\DateTimeInterface $fechaCompra): self
    {
        $this->fechaCompra = $fechaCompra;

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

    public function getPrecio(): ?int
    {
        return $this->precio;
    }

    public function setPrecio(int $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getLineaCompra(): ?int
    {
        return $this->lineaCompra;
    }

    public function setLineaCompra(int $lineaCompra): self
    {
        $this->lineaCompra = $lineaCompra;

        return $this;
    }
}
