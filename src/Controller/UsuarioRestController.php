<?php


namespace App\Controller;


use App\BLL\UsuarioBLL;
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
}