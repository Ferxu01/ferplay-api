<?php

namespace App\Helpers;

class Validation
{
    public function existeEntidad($entidad)
    {
        return !is_null($entidad);
    }

    public function datosVideojuegosVacios(
        string $nombre, string $desc, int $precio, string $imagen, int $plataforma
    ) {
        return empty($nombre) || empty($desc) || empty($precio) || empty($imagen) || empty($plataforma);
    }

    public function datosUsuarioVacios(
        string $nombre, string $apellidos, string $nick, string $email,
        string $password, string $avatar, int $provincia
    )
    {
        return empty($nombre) || empty($apellidos) || empty($nick)
            || empty($email) || empty($password) || empty($avatar)
            || empty($provincia);
    }

    public function esNumerico($dato)
    {
        return is_int($dato);
    }

    public function esNumeroNegativo(int $dato)
    {
        return $dato <= 0;
    }
}