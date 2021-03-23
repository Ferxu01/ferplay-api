<?php

namespace App\BLL;

use App\Entity\Provincia;
use App\Entity\Usuario;
use DateTime;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioBLL extends BaseBLL
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /** @var JWTTokenManagerInterface */
    private $jwtManager;

    public function setJWTManager(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function getTokenByEmail($email)
    {
        $user = $this->em->getRepository(Usuario::class)
            ->findOneBy(['email' => $email]);

        if (is_null($user))
            throw new AccessDeniedHttpException('Usuario no autorizado');

        return $this->jwtManager->create($user);
    }

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
        }

        $user->setNombre($data['nombre'])
            ->setApellidos($data['apellidos'])
            ->setNickname($data['nickname'])
            ->setEmail($data['email'])
            ->setProvincia($provincia)
            ->setFechaCreacion(new DateTime())
            ->setMe(true);

        if (is_null($user))
            return $this->guardaAvatar($request, $user, $data);

        return $this->guardaValidando($user);
    }

    public function nuevo(Request $request, array $data)
    {
        return $this->updateUserData($request, $data);
    }

    public function miPerfil()
    {
        $user = $this->getUser();

        return $this->toArray($user);
    }

    public function perfil(Usuario $usuario)
    {
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