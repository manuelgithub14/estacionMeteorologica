<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Configurar;
use AppBundle\Form\Type\ConfigurarType;
use AppBundle\Repository\ConfigurarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigurarController extends Controller
{
    /**
     * @Route("/configurar", name="configurar", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function configurarAction(Request $request, ConfigurarRepository $configurarRepository)
    {
        /* @var Configurar */
        $configurar = $configurarRepository->findOneByNombre('inicioHorarioDiurno');

        if(!$configurar){
            $configurar = new Configurar();
            $em = $this->getDoctrine()->getManager();
            $em->persist($configurar);
        }

        $form = $this->createForm(ConfigurarType::class, $configurar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('success', 'Cambios en la configuración guardados con éxito');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('configurar/form.html.twig', [
            'form' => $form->createView(),
            'configurar' => $configurar
        ]);
    }
}
