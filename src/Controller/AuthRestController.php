<?php

namespace App\Controller;

use App\BLL\UsuarioBLL;
use App\Entity\Usuario;
use App\Helpers\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthRestController extends BaseApiController
{
    /**
     * @Route(
     *     "/auth/login.{_format}",
     *     requirements={"_format": "json"},
     *     defaults={"_format": "json"}
     * )
     */
    public function getTokenAction()
    {
        return new Response('', Response::HTTP_UNAUTHORIZED);
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
}