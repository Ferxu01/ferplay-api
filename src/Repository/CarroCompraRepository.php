<?php

namespace App\Repository;

use App\Entity\CarroCompra;
use App\Entity\Usuario;
use App\Entity\Videojuego;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CarroCompra|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarroCompra|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarroCompra[]    findAll()
 * @method CarroCompra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarroCompraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarroCompra::class);
    }

    public function findVideojuegosCarroUsuario(Usuario $usuario)
    {
        $qb = $this->createQueryBuilder('vC')
            ->innerJoin('vC.videojuego', 'v')
            ->innerJoin('vC.usuario', 'u')
            ->where('u = :usuario')
            ->setParameter('usuario', $usuario);

        return $qb->getQuery()->getResult();
    }

    public function borrarVideojuegosCarro(Usuario $usuario)
    {
        $qb = $this->createQueryBuilder('vC');
        $qb->where('vC.usuario = :usuario');
        $qb->setParameter('usuario', $usuario->getId());
        $qb->delete();

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return CarroCompra[] Returns an array of CarroCompra objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CarroCompra
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
