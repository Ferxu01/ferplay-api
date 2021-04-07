<?php

namespace App\Helpers;

use App\Entity\Compra;
use App\Entity\Videojuego;

class Validation
{
    public function existeEntidad($entidad)
    {
        return !is_null($entidad);
    }

    public function datosVideojuegosVacios(
        string $nombre, string $desc, int $precio, string $imagen,
        int $plataforma, int $stock
    ) {
        return empty($nombre) || empty($desc) || empty($precio)
            || empty($imagen) || empty($plataforma) || empty($stock);
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

    public function stockValido(Videojuego $videojuego, int $stock)
    {
        return $videojuego->getStock() >= $stock;
    }
}