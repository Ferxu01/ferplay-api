<?php


namespace App\Controller;


use App\BLL\UsuarioBLL;
use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/auth/register.{_format}",
     *     name="register",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"POST"}
     * )
     */
    public function register(Request $request, UsuarioBLL $usuarioBLL)
    {
        $errores['mensaje'] = [];
        $data = $this->getContent($request);
        $usuarioRepo = $this->getDoctrine()->getRepository(Usuario::class);
        $usuarios = $usuarioRepo->findAll();

        if (empty($data['nombre']) || empty($data['apellidos']) || empty($data['nickname'])
        || empty($data['email']) || empty($data['password']) || empty($data['avatar'])
        || empty($data['provincia']))
            array_push($errores['mensaje'], 'Los campos no pueden estar vacíos');

        if (!is_int($data['provincia']))
            array_push($errores['mensaje'], 'La provincia debe ser un número');
        if ($data['provincia'] <= 0)
            array_push($errores['mensaje'], 'La provincia no puede ser 0 o menor de 0');

        foreach ($usuarios as $usuario) {
            if ($data['nickname'] === $usuario->getNickname()) {
                array_push($errores['mensaje'], 'Ya existe un usuario con ese nickname');
            }
        }

        if (count($errores['mensaje']) > 0)
            return $this->getErrorResponse($errores, Response::HTTP_BAD_REQUEST);

        $user = $usuarioBLL->nuevo($request, $data);
        return $this->getResponse($user, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/profile/me.{_format}",
     *     name="profile",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"GET"}
     * )
     */
    public function profile(UsuarioBLL $usuarioBLL)
    {
        $user = $usuarioBLL->perfil();
        return $this->getResponse($user);
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