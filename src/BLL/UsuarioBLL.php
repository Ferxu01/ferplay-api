<?php

namespace App\BLL;

use App\Entity\Provincia;
use App\Entity\Usuario;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioBLL extends BaseBLL
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    public function setEncoder(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    private function guardaAvatar($request, $usuario, $data)
    {
        $arr_avatar = explode(',', $data['avatar']);
        if (count($arr_avatar) < 2)
            throw new BadRequestHttpException('Formato de imagen incorrecto');

        $imgAvatar = base64_decode($arr_avatar[1]);
        if (!is_null($imgAvatar)) {
            $fileName = $usuario->getNickname() . '-' . time() . '.jpg';
            $usuario->setAvatar($fileName);
            $ifp = fopen($this->avatarsDirectory . '/' . $fileName, 'wb');
            if ($ifp) {
                $ok = fwrite($ifp, $imgAvatar);
                fclose($ifp);
                if ($ok)
                    return $this->guardaValidando($usuario);
            }
        }

        throw new Exception('No se ha podido cargar la imagen del usuario');
    }

    public function nuevo(Request $request, array $data)
    {
        $provinciaRepository = $this->em->getRepository(Provincia::class);
        $provincia = $provinciaRepository->findOneBy([
            'id' => $data['provincia']
        ]);

        $user = new Usuario();
        $user->setNombre($data['nombre'])
            ->setApellidos($data['apellidos'])
            ->setNickname($data['nickname'])
            ->setEmail($data['email'])
            ->setPassword($this->encoder->encodePassword($user, $data['password']))
            ->setAvatar($data['avatar'])
            ->setProvincia($provincia)
            ->setFechaCreacion(new DateTime());

        return $this->guardaAvatar($request, $user, $data);
    }

    public function perfil()
    {
        $user = $this->getUser();

        return $this->toArray($user);
    }

    public function toArray(Usuario $usuario)
    {
        if (is_null($usuario))
            return null;

        return [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'apellidos' => $usuario->getApellidos(),
            'nickname' => $usuario->getNickname(),
            'email' => $usuario->getEmail(),
            'password' => $usuario->getPassword(),
            'avatar' => $usuario->getAvatar(),
            'provincia' => $usuario->getProvincia()->getId(),
            'fechaCreacion' => $usuario->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}