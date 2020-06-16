<?php

namespace AppBundle\Controller;

use AppBundle\Repository\RegistrosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PortadaController extends Controller
{
    /**
     * @Route("/", name="portada")
     */
    public function indexAction(RegistrosRepository $registrosRepository)
    {
        $ultimoRegistro = $registrosRepository->findUltimoRegistro();

        return $this->render('portada.html.twig',[
            'ultimoRegistro' => $ultimoRegistro
        ]);
    }
}
