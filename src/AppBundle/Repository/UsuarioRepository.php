<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UsuarioRepository extends ServiceEntityRepository
{

    /**
     * UsuarioRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function findAllOrdenadosQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->orderBy('u.apellidos');

    }

    public function findAllOrdenados()
    {
        return $this->findAllOrdenadosQueryBuilder()
            ->getQuery()
            ->getResult();
    }
}