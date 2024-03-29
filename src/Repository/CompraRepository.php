<?php

namespace App\Repository;

use App\Entity\Compra;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Compra|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compra|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compra[]    findAll()
 * @method Compra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compra::class);
    }

    public function getMaxLineaCompra()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('MAX(c.lineaCompra) AS maxLineaCompra');
        return $qb->getQuery()->getResult()[0];
    }

    public function getHistorialCompras(Usuario $usuario)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->innerJoin('c.videojuego', 'v');
        $qb->innerJoin('c.usuario', 'u');
        $qb->where('c.usuario = :usuario');
        $qb->setParameter('usuario', $usuario);
        $qb->groupBy('c.lineaCompra');

        return $qb->getQuery()->getResult();
    }

    public function getVideojuegosCompra(int $lineaCompra)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->innerJoin('c.videojuego', 'v');
        $qb->innerJoin('c.usuario', 'u');
        $qb->where('c.lineaCompra = :lineaCompra');
        $qb->setParameter('lineaCompra', $lineaCompra);

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Compra[] Returns an array of Compra objects
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
    public function findOneBySomeField($value): ?Compra
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
