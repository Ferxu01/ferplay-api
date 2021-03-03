<?php


namespace App\BLL;


use App\Entity\Usuario;
use DateTime;
use Exception;
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
            $fileName = $data['nombreFoto'];
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

    public function nuevo(array $data)
    {
        $user = new Usuario();
        $user->setEmail($data['email'])
            ->setPassword($this->encoder->encodePassword($user, $data['password']))
            ->setAvatar($data['avatar'])
            ->setFechaCreacion(new DateTime());

        return $this->guardaValidando($user);
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
            'email' => $usuario->getEmail(),
            'password' => $usuario->getPassword(),
            'avatar' => $usuario->getAvatar(),
            'fechaCreacion' => $usuario->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}