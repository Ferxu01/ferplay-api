<?php

namespace App\Helpers;

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
}