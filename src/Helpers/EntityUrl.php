<?php

namespace App\Helpers;

use App\Entity\Usuario;
use App\Entity\Videojuego;

class EntityUrl
{
    private string $server_url;

    private string $urlImagenVideojuego;

    private string $urlAvatarUsuario;

    public function __construct()
    {
        $this->server_url = 'http://'.$_SERVER['SERVER_NAME'].':'
            .$_SERVER['SERVER_PORT'];
        $this->urlImagenVideojuego = $this->server_url . '/img/videogames/';
        $this->urlAvatarUsuario = $this->server_url . '/img/users/';
    }

    /**
     * @return string
     */
    public function getUrlImagenVideojuego(): string
    {
        return $this->urlImagenVideojuego;
    }

    /**
     * @return string
     */
    public function getUrlAvatarUsuario(): string
    {
        return $this->urlAvatarUsuario;
    }

    public static function getNombreImagen(Usuario $usuario): string
    {
        $index = strrpos($usuario->getAvatar(), '/');
        return substr($usuario->getAvatar(), $index+1);
    }

    public static function getNombreImagenVideojuego(Videojuego $videojuego): string
    {
        $index = strrpos($videojuego->getImagen(), '/');
        return substr($videojuego->getImagen(), $index);
    }
}