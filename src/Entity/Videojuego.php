<?php

namespace App\Entity;

use App\Helpers\EntityUrl;
use App\Repository\VideojuegoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideojuegoRepository::class)
 */
class Videojuego
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    private $precio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCreacion;

    /**
     * @ORM\ManyToOne(targetEntity=Plataforma::class, inversedBy="videojuegos")
     */
    private $plataforma;

    /**
     * @ORM\OneToMany(targetEntity=Comentario::class, mappedBy="videojuego", orphanRemoval=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="videojuego", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\ManyToOne(targetEntity=Usuario::class, inversedBy="videojuegos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="boolean")
     */
    private $liked;

    /**
     * @ORM\OneToMany(targetEntity=Compra::class, mappedBy="videojuego", orphanRemoval=true)
     */
    private $compras;

    /**
     * @ORM\OneToMany(targetEntity=Favorito::class, mappedBy="videojuego", orphanRemoval=true)
     */
    private $favoritos;

    /**
     * @ORM\Column(type="boolean")
     */
    private $favourite;

    /**
     * @ORM\Column(type="integer")
     */
    private $numLikes;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity=CarroCompra::class, mappedBy="videojuego", orphanRemoval=true)
     */
    private $carroCompras;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mine;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->compras = new ArrayCollection();
        $this->favoritos = new ArrayCollection();
        $this->carroCompras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

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

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getPlataforma(): ?Plataforma
    {
        return $this->plataforma;
    }

    public function setPlataforma(?Plataforma $plataforma): self
    {
        $this->plataforma = $plataforma;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getLiked(): ?bool
    {
        return $this->liked;
    }

    public function setLiked(bool $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    /**
     * @return Collection|Comentario[]
     */
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): self
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios[] = $comentario;
            $comentario->setVideojuego($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): self
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getVideojuego() === $this) {
                $comentario->setVideojuego(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setVideojuego($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getVideojuego() === $this) {
                $like->setVideojuego(null);
            }
        }

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

    /**
     * @return Collection|Compra[]
     */
    public function getCompras(): Collection
    {
        return $this->compras;
    }

    public function addCompra(Compra $compra): self
    {
        if (!$this->compras->contains($compra)) {
            $this->compras[] = $compra;
            $compra->setVideojuego($this);
        }

        return $this;
    }

    public function removeCompra(Compra $compra): self
    {
        if ($this->compras->removeElement($compra)) {
            // set the owning side to null (unless already changed)
            if ($compra->getVideojuego() === $this) {
                $compra->setVideojuego(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Favorito[]
     */
    public function getFavoritos(): Collection
    {
        return $this->favoritos;
    }

    public function addFavorito(Favorito $favorito): self
    {
        if (!$this->favoritos->contains($favorito)) {
            $this->favoritos[] = $favorito;
            $favorito->setVideojuego($this);
        }

        return $this;
    }

    public function removeFavorito(Favorito $favorito): self
    {
        if ($this->favoritos->removeElement($favorito)) {
            // set the owning side to null (unless already changed)
            if ($favorito->getVideojuego() === $this) {
                $favorito->setVideojuego(null);
            }
        }

        return $this;
    }

    public function getFavourite(): ?bool
    {
        return $this->favourite;
    }

    public function setFavourite(bool $favourite): self
    {
        $this->favourite = $favourite;

        return $this;
    }

    public function getNumLikes(): ?int
    {
        return $this->numLikes;
    }

    public function setNumLikes(int $numLikes): self
    {
        $this->numLikes = $numLikes;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection|CarroCompra[]
     */
    public function getCarroCompras(): Collection
    {
        return $this->carroCompras;
    }

    public function addCarroCompra(CarroCompra $carroCompra): self
    {
        if (!$this->carroCompras->contains($carroCompra)) {
            $this->carroCompras[] = $carroCompra;
            $carroCompra->setVideojuego($this);
        }

        return $this;
    }

    public function removeCarroCompra(CarroCompra $carroCompra): self
    {
        if ($this->carroCompras->removeElement($carroCompra)) {
            // set the owning side to null (unless already changed)
            if ($carroCompra->getVideojuego() === $this) {
                $carroCompra->setVideojuego(null);
            }
        }

        return $this;
    }

    public function getMine(): ?bool
    {
        return $this->mine;
    }

    public function setMine(bool $mine): self
    {
        $this->mine = $mine;

        return $this;
    }

    public function toArray()
    {
        $urlEntidad = new EntityUrl();

        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'descripcion' => $this->getDescripcion(),
            'plataforma' => $this->getPlataforma()->toArray(),
            'precio' => $this->getPrecio(),
            'imagen' => $urlEntidad->getUrlImagenVideojuego() . $this->getImagen(),
            'usuario' => $this->getUsuario()->toArray(),
            'liked' => $this->getLiked(),
            'favourite' => $this->getFavourite(),
            'numLikes' => $this->getNumLikes(),
            'mine' => $this->getMine(),
            'stock' => $this->getStock(),
            'fechaCreacion' => $this->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}
