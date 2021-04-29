<?php

namespace App\Controller;

use App\BLL\CompraBLL;
use App\BLL\UsuarioBLL;
use App\Entity\Usuario;
use App\Helpers\Validation;
use App\Interceptors\UserInterceptor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/auth/google",
     *     name="login_google"
     * )
     */
    public function googleLogin(Request $request, UsuarioBLL $usuarioBLL)
    {
        $data = $this->getContent($request);

        if (is_null($data['access_token']) || !isset($data['access_token'])
        || empty($data['access_token']))
            throw new BadRequestHttpException('No se ha recibido el token de google');

        $googleJwt = json_decode(file_get_contents(
            "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=" . $data['access_token']));

        $token = $usuarioBLL->getTokenByEmail($googleJwt->emails[0]->value);

        return $this->getResponse(['token' => $token]);
    }

    /**
     * @Route(
     *     "/auth/register.{_format}",
     *     name="register",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function register(Validation $validation, Request $request, UsuarioBLL $usuarioBLL)
    {
        $errores['mensaje'] = [];
        $data = $this->getContent($request);
        $usuarioRepo = $this->getDoctrine()->getRepository(Usuario::class);
        $usuarios = $usuarioRepo->findAll();

        if ($validation->datosUsuarioVacios(
            $data['nombre'], $data['apellidos'], $data['nickname'], $data['email'],
            $data['password'], $data['avatar'], $data['provincia']
        ))
            array_push($errores['mensaje'], 'Los campos no pueden estar vacíos');

        if (!$validation->esNumerico($data['provincia']))
            array_push($errores['mensaje'], 'La provincia debe ser un número');
        if ($validation->esNumeroNegativo($data['provincia']))
            array_push($errores['mensaje'], 'La provincia no puede ser 0 o menor de 0');

        foreach ($usuarios as $usuario) {
            if ($data['nickname'] === $usuario->getNickname()) {
                array_push($errores['mensaje'], 'Ya existe un usuario con ese nickname');
            }

            if ($data['email'] === $usuario->getEmail()) {
                array_push($errores['mensaje'], 'Ya existe un usuario con ese email');
            }
        }

        if (count($errores['mensaje']) > 0)
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);

        $user = $usuarioBLL->nuevo($request, $data);
        return $this->getResponse($user, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/profile/buy/history",
     *     name="get_historial_compras",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function getHistorialCompras(CompraBLL $compraBLL)
    {
        $compras = $compraBLL->getHistorialCompras();

        if (count($compras) === 0) {
            $errores['mensajes'] = 'No se ha realizado ninguna compra';
            return $this->getErrorResponse($errores, Response::HTTP_NOT_FOUND);
        }

        return $this->getResponse($compras);
    }

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
        //Comprobar error al editar el perfil, problema con el token
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

        $avatarsDirectory = $this->getParameter('avatars_directory');
        $avatarsUrl = $this->getParameter('avatars_url');
        $user = $usuarioBLL->editarAvatar($request, $data);

        return $this->getResponse($user);
    }
}