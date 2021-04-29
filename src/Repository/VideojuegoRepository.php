<?php

namespace App\Repository;

use App\Entity\Usuario;
use App\Entity\Videojuego;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Videojuego|null find($id, $lockMode = null, $lockVersion = null)
 * @method Videojuego|null findOneBy(array $criteria, array $orderBy = null)
 * @method Videojuego[]    findAll()
 * @method Videojuego[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideojuegoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Videojuego::class);
    }

    public function getVideojuegosUsuario(int $idUsuario)
    {
        $qb = $this->createQueryBuilder('v');
        $qb->innerJoin('v.plataforma', 'plataforma');
        $qb->innerJoin('v.usuario', 'usuario');
        $qb->where($qb->expr()->eq('usuario.id', $idUsuario));
        return $qb->getQuery()->getResult();
    }

    public function getAllVideojuegos()
    {
        $qb = $this->createQueryBuilder('v');
        $qb->innerJoin('v.plataforma', 'plataforma');
        $qb->innerJoin('v.usuario', 'usuario');
        $qb->orderBy('v.fechaCreacion', 'DESC');
        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Videojuego[] Returns an array of Videojuego objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Videojuego
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
