<?php

namespace App\Interceptors;

use App\Entity\Usuario;

class UserInterceptor
{
    public function setLoggedUser(Usuario $usuario): Usuario
    {
        $usuario->setMe(true);
        return $usuario;
    }

    public function setUser($loggedUser, Usuario $usuario): Usuario
    {
        $usuario->setMe($loggedUser->getId() === $usuario->getId());
        return $usuario;
    }
}