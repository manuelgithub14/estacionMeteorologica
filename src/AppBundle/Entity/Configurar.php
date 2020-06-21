<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="configurar")
 */
class Configurar
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @var string
     */
    private $nombre;

    /**
     * @ORM\Column(type="time", nullable=false)
     * @Assert\Time()
     */
    private $valor;

    /**
     * Configurar constructor.
     */
    public function __construct()
    {
        $this->nombre = 'inicioHorarioDiurno';
        $fecha = new \DateTime("now");
        $this->setValor($fecha->setTime(8,0,0));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     * @return Configurar
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param \DateTime $valor
     * @return Configurar
     */
    public function setValor(\DateTime $valor)
    {
        $this->valor = $valor;
        return $this;
    }


}