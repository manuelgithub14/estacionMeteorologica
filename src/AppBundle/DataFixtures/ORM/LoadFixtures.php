<?php


namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadFixtures extends Fixture
{
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {

        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager, ['providers' => [$this]]);
    }

    public function codificaClave($clave)
    {
        return $this->userPasswordEncoder->encodePassword(new Usuario(), $clave);
    }

    public function direccionAleatoria()
    {
        $direcciones = ["ESE","ENE","E  ","SSE","SE ","SSO","S  ","NNE","NE ","OSO","SO ","NNO","N  ","ONO","NO ","O  "];

        return $direcciones[array_rand($direcciones, 1)];
    }
}