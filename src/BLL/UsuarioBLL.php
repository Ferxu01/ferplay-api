<?php

namespace App\BLL;

use App\Entity\Provincia;
use App\Entity\Usuario;
use App\Helpers\EntityUrl;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioBLL extends BaseBLL
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    private $urlDirUsuarios = '..\public\img\users\\';

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

    private function updateUserData(Request $request, array $data, Usuario $user = null)
    {
        $provinciaRepository = $this->em->getRepository(Provincia::class);
        $provincia = $provinciaRepository->findOneBy([
            'id' => $data['provincia']
        ]);

        if (is_null($user)) {
            $user = new Usuario();
            $user->setAvatar($data['avatar'])
                ->setPassword($this->encoder->encodePassword($user, $data['password']));

            $user->setNombre($data['nombre'])
                ->setApellidos($data['apellidos'])
                ->setNickname($data['nickname'])
                ->setEmail($data['email'])
                ->setProvincia($provincia)
                ->setMe(false)
                ->setFechaCreacion(new DateTime());

            return $this->guardaAvatar($request, $user, $data);
        }

        $user->setNombre($data['nombre'])
            ->setApellidos($data['apellidos'])
            ->setNickname($data['nickname'])
            ->setEmail($data['email'])
            ->setProvincia($provincia)
            ->setFechaCreacion(new DateTime());

        return $this->guardaValidando($user);
    }

    public function nuevo(Request $request, array $data)
    {
        return $this->updateUserData($request, $data);
    }

    public function miPerfil()
    {
        $user = $this->getUser();
        $user = $this->userHelper->setLoggedUser($user);

        return $this->toArray($user);
    }

    public function perfil(Usuario $usuario)
    {
        $usuario = $this->userHelper->setUser($this->getUser(), $usuario);

        return $this->toArray($usuario);
    }

    public function editarPerfil(Request $request, array $data, Usuario $user)
    {
        return $this->updateUserData($request, $data, $user);
    }

    public function editarPassword(string $password)
    {
        $user = $this->getUser();
        $user->setPassword($this->encoder->encodePassword($user, $password));

        return $this->guardaValidando($user);
    }

    public function editarAvatar(Request $request, array $data)
    {
        $strImagen = EntityUrl::getNombreImagen($this->getUser());
        if ($this->getUser()->getAvatar() !== '')
            unlink($this->urlDirUsuarios . $strImagen);

        return $this->guardaAvatar($request, $this->getUser(), $data);
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
            'avatar' => $usuario->getAvatar(),
            'provincia' => $usuario->getProvincia()->toArray(),
            'me' => $usuario->getMe(),
            'fechaCreacion' => $usuario->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}