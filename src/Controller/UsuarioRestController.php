<?php

namespace App\Controller;

use App\BLL\UsuarioBLL;
use App\Entity\Usuario;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/profile/me.{_format}",
     *     name="profile_logged",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function profile(UsuarioBLL $usuarioBLL)
    {
        $user = $usuarioBLL->miPerfil();
        return $this->getResponse($user);
    }

    /**
     * @Route(
     *     "/profile/{id}.{_format}",
     *     name="profile",
     *     requirements={"id": "\d+", "_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function profileUsuario(Validation $validation, Usuario $usuario = null, UsuarioBLL $usuarioBLL)
    {
        if (!$validation->existeEntidad($usuario)) {
            $errores['mensajes'] = 'No se ha encontrado el usuario';
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        if (isset($errores['mensajes']))
            return $this->getErrorResponse($errores, $statusCode);

        $user = $usuarioBLL->perfil($usuario);
        return $this->getResponse($user);
    }

    /**
     * @Route(
     *     "/profile/me.{_format}",
     *     name="update_profile",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PUT"}
     * )
     */
    public function editarPerfil(Validation $validation, Request $request, UsuarioBLL $usuarioBLL)
    {
        $errores['mensajes'] = [];

        $data = $this->getContent($request);
        if ($validation->datosUsuarioVacios(
            $data['nombre'], $data['apellidos'], $data['nickname'],
            $data['email'], $this->getUser()->getPassword(), $this->getUser()->getAvatar(), $data['provincia']
        ))
            array_push($errores['mensajes'], 'Los campos no pueden estar vacíos');

        if (!$validation->esNumerico($data['provincia']))
            array_push($errores['mensajes'], 'La provincia debe ser un número');

        if ($validation->esNumeroNegativo($data['provincia']))
            array_push($errores['mensajes'], 'La provincia no puede ser negativo');

        if (count($errores['mensajes']) > 0)
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);

        $usuario = $usuarioBLL->editarPerfil($request, $data, $this->getUser());

        return $this->getResponse($usuario);
    }

    /**
     * @Route(
     *     "/profile/edit/password.{_format}",
     *     name="update_password",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     */
    public function editarPassword(Request $request, UsuarioBLL $usuarioBLL)
    {
        $data = $this->getContent($request);

        if (is_null($data['password']) || !isset($data['password'])
            || empty($data['password'])) {
            $errores['mensaje'] = 'La contraseña no puede estar vacía';
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);
        }

        $user = $usuarioBLL->editarPassword($data['password']);

        return $this->getResponse($user);
    }

    /**
     * @Route(
     *     "/profile/edit/avatar.{_format}",
     *     name="update_avatar",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     */
    public function editarAvatar(Request $request, UsuarioBLL $usuarioBLL)
    {
        $data = $this->getContent($request);

        if (is_null($data['avatar']) || !isset($data['avatar'])
            || empty($data['avatar'])) {
            $errores['mensaje'] = 'El avatar no puede estar vacío';
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);
        }

        $user = $usuarioBLL->editarAvatar($request, $data);

        return $this->getResponse($user);
    }
}