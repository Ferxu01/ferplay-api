<?php


namespace App\Controller;


use App\BLL\UsuarioBLL;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        $data = $this->getContent($request);
        $user = $usuarioBLL->nuevo($request, $data);
        return $this->getResponse($user, Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/profile.{_format}",
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
     *     "/profile/password.{_format}",
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
            throw new BadRequestHttpException('No se ha recibido la contraseña');
        }

        $user = $usuarioBLL->editarPassword($data['password']);
        return $this->getResponse($user);
    }

    /**
     * @Route(
     *     "/profile/avatar.{_format}",
     *     name="update_avatar",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"},
     *     methods={"PATCH"}
     * )
     */
    public function editarAvatar(Request $request, UsuarioBLL $usuarioBLL)
    {
        $data = $this->getContent($request);
        if (is_null($data['avatar']))
            throw new BadRequestHttpException('No se ha recibido el avatar');

        $avatarsDirectory = $this->getParameter('avatars_directory');
        $avatarsUrl = $this->getParameter('avatars_url');
        $user = $usuarioBLL->editarAvatar($request, $data);

        return $this->getResponse($user);
    }
}