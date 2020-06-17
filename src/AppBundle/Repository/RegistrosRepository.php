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

    public function findMesesPorAnio($anio)
    {
        return $this->createQueryBuilder('m')
            ->select('MONTH(m.fechaHora)')
            ->distinct(true)
            ->where('YEAR(m.fechaHora) = :anio')
            ->setParameter('anio', $anio)
            ->orderBy('MONTH(m.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // CONSULTAS ANUALES TEMPERATURA
    public function findMediaDiurnaPorMesTemperatura($anio, $mes)
    {
        return $this->createQueryBuilder('d')
            ->select('AVG(d.temperatura)')
            ->where("(DATE_FORMAT(d.fechaHora, '%H:%i:%s') >= '08:00:00' AND DATE_FORMAT(d.fechaHora, '%H:%i:%s') < '20:00:00') AND YEAR(d.fechaHora) = :anio AND MONTH(d.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(d.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMediaNocturnaPorMesTemperatura($anio, $mes)
    {
        return $this->createQueryBuilder('n')
            ->select('AVG(n.temperatura)')
            ->where("(DATE_FORMAT(n.fechaHora, '%H:%i:%s') >= '20:00:00' OR DATE_FORMAT(n.fechaHora, '%H:%i:%s') < '08:00:00') AND YEAR(n.fechaHora) = :anio AND MONTH(n.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(n.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // CONSULTAS ANUALES HUMEDAD
    public function findMediaDiurnaPorMesHumedad($anio, $mes)
    {
        return $this->createQueryBuilder('d')
            ->select('AVG(d.humedad)')
            ->where("(DATE_FORMAT(d.fechaHora, '%H:%i:%s') >= '08:00:00' AND DATE_FORMAT(d.fechaHora, '%H:%i:%s') < '20:00:00') AND YEAR(d.fechaHora) = :anio AND MONTH(d.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(d.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMediaNocturnaPorMesHumedad($anio, $mes)
    {
        return $this->createQueryBuilder('n')
            ->select('AVG(n.humedad)')
            ->where("(DATE_FORMAT(n.fechaHora, '%H:%i:%s') >= '20:00:00' OR DATE_FORMAT(n.fechaHora, '%H:%i:%s') < '08:00:00') AND YEAR(n.fechaHora) = :anio AND MONTH(n.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(n.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // CONSULTAS ANUALES LLUVIA
    public function findMediaDiurnaPorMesLluvia($anio, $mes)
    {
        return $this->createQueryBuilder('d')
            ->select('AVG(d.lluvia)')
            ->where("(DATE_FORMAT(d.fechaHora, '%H:%i:%s') >= '08:00:00' AND DATE_FORMAT(d.fechaHora, '%H:%i:%s') < '20:00:00') AND YEAR(d.fechaHora) = :anio AND MONTH(d.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(d.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMediaNocturnaPorMesLluvia($anio, $mes)
    {
        return $this->createQueryBuilder('n')
            ->select('AVG(n.lluvia)')
            ->where("(DATE_FORMAT(n.fechaHora, '%H:%i:%s') >= '20:00:00' OR DATE_FORMAT(n.fechaHora, '%H:%i:%s') < '08:00:00') AND YEAR(n.fechaHora) = :anio AND MONTH(n.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(n.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // CONSULTAS ANUALES VIENTO
    public function findMediaDiurnaPorMesViento($anio, $mes)
    {
        return $this->createQueryBuilder('d')
            ->select('AVG(d.viento)')
            ->where("(DATE_FORMAT(d.fechaHora, '%H:%i:%s') >= '08:00:00' AND DATE_FORMAT(d.fechaHora, '%H:%i:%s') < '20:00:00') AND YEAR(d.fechaHora) = :anio AND MONTH(d.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(d.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMediaNocturnaPorMesViento($anio, $mes)
    {
        return $this->createQueryBuilder('n')
            ->select('AVG(n.viento)')
            ->where("(DATE_FORMAT(n.fechaHora, '%H:%i:%s') >= '20:00:00' OR DATE_FORMAT(n.fechaHora, '%H:%i:%s') < '08:00:00') AND YEAR(n.fechaHora) = :anio AND MONTH(n.fechaHora) = :mes")
            ->setParameter('anio', $anio)
            ->setParameter('mes', $mes)
            ->orderBy('MONTH(n.fechaHora)', 'ASC')
            ->getQuery()
            ->getResult();
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