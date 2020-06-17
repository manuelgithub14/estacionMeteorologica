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

    // CONSULTAS DE REGISTROS
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

    public function findUltimoRegistro()
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->orderBy('r.fechaHora', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllPorAnio($anio)
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->where('YEAR(r.fechaHora) = :anio')
            ->setParameter('anio', $anio)
            ->getQuery()
            ->getResult();
    }

    // CONSULTAS ANUALES
    public function findAnios()
    {
        return $this->createQueryBuilder('r')
            ->select('YEAR(r.fechaHora)')
            ->distinct(true)
            ->orderBy('YEAR(r.fechaHora)', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    // REGISTRO DE LOS DATOS OBTENIDOS AL LLAMAR AL COMANDO "app:obtener-datos"
    public function grabarDatos($fecha, $temperatura, $humedad, $lluvia, $viento, $dirViento){
        $nuevoRegistro = new RegistroEstacion();
        $nuevoRegistro->setFechaHora($fecha);
        $nuevoRegistro->setTemperatura((float) $temperatura);
        $nuevoRegistro->setLluvia((float) $lluvia);
        $nuevoRegistro->setHumedad((float) $humedad);
        $nuevoRegistro->setViento((float) $viento);
        $nuevoRegistro->setDirViento($dirViento);

        $validado = true;

        if(!checkdate($nuevoRegistro->getFechaHora()->format('m'), $nuevoRegistro->getFechaHora()->format('d'),
            $nuevoRegistro->getFechaHora()->format('Y'))){
            $validado = false;
        }

        if(!is_float($nuevoRegistro->getTemperatura()) || !is_float($nuevoRegistro->getLluvia()) ||
            !is_float($nuevoRegistro->getHumedad()) || !is_float($nuevoRegistro->getViento()) || $nuevoRegistro->getDirViento() === ''){
            $validado = false;
        }

        $em = $this->getEntityManager();
        $em->persist($nuevoRegistro);

        if($validado){
            $this->getEntityManager()->flush();
            return true;
        }
        return false;
    }
}