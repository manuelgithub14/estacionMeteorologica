<?php


namespace AppBundle\Repository;

use AppBundle\Entity\RegistroEstacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RegistrosRepository extends ServiceEntityRepository
{

    /**
     * RegistrosRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistroEstacion::class);
    }

    public function findAllOrdenadosFechaQueryBuilder()
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->orderBy('r.fechaHora');
    }

    public function findAllOrdenadosFecha()
    {
        return $this->findAllOrdenadosFechaQueryBuilder()
            ->getQuery()
            ->getResult();
    }
}