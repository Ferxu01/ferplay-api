<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario implements UserInterface, \Serializable
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCreacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @ORM\ManyToOne(targetEntity=Provincia::class, inversedBy="usuarios")
     */
    private $provincia;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Videojuego::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $videojuegos;

    /**
     * @ORM\OneToMany(targetEntity=Compra::class, mappedBy="usuario", orphanRemoval=true)
     */
    private $compras;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->videojuegos = new ArrayCollection();
        $this->compras = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     * @return Usuario
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->nombre,
            $this->apellidos,
            $this->nickname,
            $this->email,
            $this->password,
            $this->avatar,
            $this->provincia
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->nombre,
            $this->apellidos,
            $this->nickname,
            $this->email,
            $this->password,
            $this->avatar,
            $this->provincia
            ) = $this->unserialize($serialized);
    }

    public function getRoles()
    {
        return [];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

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
            $like->setUsuario($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUsuario() === $this) {
                $like->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Videojuego[]
     */
    public function getVideojuegos(): Collection
    {
        return $this->videojuegos;
    }

    public function addVideojuego(Videojuego $videojuego): self
    {
        if (!$this->videojuegos->contains($videojuego)) {
            $this->videojuegos[] = $videojuego;
            $videojuego->setUsuario($this);
        }

        return $this;
    }

    public function removeVideojuego(Videojuego $videojuego): self
    {
        if ($this->videojuegos->removeElement($videojuego)) {
            // set the owning side to null (unless already changed)
            if ($videojuego->getUsuario() === $this) {
                $videojuego->setUsuario(null);
            }
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'apellidos' => $this->getApellidos(),
            'nickname' => $this->getNickname(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'avatar' => $this->getAvatar(),
            'provincia' => $this->getProvincia()->toArray(),
            'fechaCreacion' => $this->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
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
            $compra->setUsuario($this);
        }

        return $this;
    }

    public function removeCompra(Compra $compra): self
    {
        if ($this->compras->removeElement($compra)) {
            // set the owning side to null (unless already changed)
            if ($compra->getUsuario() === $this) {
                $compra->setUsuario(null);
            }
        }

        return $this;
    }
}
