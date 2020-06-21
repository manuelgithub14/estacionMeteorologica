<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Configurar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ConfigurarRepository extends ServiceEntityRepository
{
    /**
     * ConfigurarRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configurar::class);
    }

    public function findById()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.id is not null')
            ->getQuery()
            ->getResult();
    }
}