<?php

namespace App\BLL;

use App\Helpers\UserHelper;
use App\Helpers\VideojuegoHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseBLL
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ValidatorInterface */
    private $validator;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var string */
    protected $avatarsDirectory;

    /** @var string */
    protected $avatarsUrl;

    /** @var string */
    protected $videojuegosDirectory;

    /** @var string */
    protected $videojuegosUrl;

    /** @var string */
    protected $server_url;

    /** @var UserHelper */
    protected $userHelper;

    /** @var VideojuegoHelper */
    protected $videojuegoHelper;

    function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TokenStorageInterface $tokenStorage,
        string $avatarsDirectory,
        string $avatarsUrl,
        string $videojuegosDirectory,
        string $videojuegosUrl
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->tokenStorage = $tokenStorage;
        $this->avatarsDirectory = $avatarsDirectory;
        $this->avatarsUrl = $avatarsUrl;
        $this->videojuegosDirectory = $videojuegosDirectory;
        $this->videojuegosUrl = $videojuegosUrl;
        $this->server_url = 'http://'.$_SERVER['SERVER_NAME'].':'
            .$_SERVER['SERVER_PORT'];
        $this->userHelper = new UserHelper();
        $this->videojuegoHelper = new VideojuegoHelper();
    }

    private function validate($entity)
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            $strError = '';
            foreach ($errors as $error) {
                if (!empty($strError))
                    $strError .= '\n';
                $strError .= $error->getMessage();
            }

            throw new BadRequestHttpException($strError);
        }
    }

    protected function guardaValidando($entity)
    {
        $this->validate($entity);
        $this->em->persist($entity);
        $this->em->flush();

        return $this->toArray($entity);
    }

    public function entitiesToArray(array $entities) : ?array
    {
        if (is_null($entities))
            return null;
        $arr = [];
        foreach ($entities as $entity)
            $arr[] = $this->toArray($entity);
        return $arr;
    }

    protected function getUser() : ?UserInterface
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}