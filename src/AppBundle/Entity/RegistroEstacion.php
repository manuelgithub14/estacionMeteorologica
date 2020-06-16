<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="registro_estacion")
 */
class RegistroEstacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\DateTime
     * @var /DateTime
     */
    private $fechaHora;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotBlank()
     * @var float
     */
    private $temperatura;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotBlank()
     * @var float
     */
    private $humedad;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotBlank()
     * @var float
     */
    private $lluvia;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotBlank()
     * @var float
     */
    private $viento;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @var string
     */
    private $dirViento;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="registros")
     * @var Usuario
     */
    private $usuario;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * @param \DateTime $fechaHora
     * @return RegistroEstacion
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;
        return $this;
    }

    /**
     * @return float
     */
    public function getTemperatura()
    {
        return $this->temperatura;
    }

    /**
     * @param float $temperatura
     * @return RegistroEstacion
     */
    public function setTemperatura($temperatura)
    {
        $this->temperatura = $temperatura;
        return $this;
    }

    /**
     * @return float
     */
    public function getHumedad()
    {
        return $this->humedad;
    }

    /**
     * @param float $humedad
     * @return RegistroEstacion
     */
    public function setHumedad($humedad)
    {
        $this->humedad = $humedad;
        return $this;
    }

    /**
     * @return float
     */
    public function getLluvia()
    {
        return $this->lluvia;
    }

    /**
     * @param float $lluvia
     * @return RegistroEstacion
     */
    public function setLluvia($lluvia)
    {
        $this->lluvia = $lluvia;
        return $this;
    }

    /**
     * @return float
     */
    public function getViento()
    {
        return $this->viento;
    }

    /**
     * @param float $viento
     * @return RegistroEstacion
     */
    public function setViento($viento)
    {
        $this->viento = $viento;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirViento()
    {
        return $this->dirViento;
    }

    /**
     * @param string $dirViento
     * @return RegistroEstacion
     */
    public function setDirViento($dirViento)
    {
        $this->dirViento = $dirViento;
        return $this;
    }
}