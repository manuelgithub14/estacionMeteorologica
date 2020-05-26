<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RegistroEstacion;
use AppBundle\Form\Type\RegistroEstacionType;
use AppBundle\Repository\RegistrosRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistroEstacionController extends Controller
{
    /**
     * @Route("/registros/{page}", name="registro_listar")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function indexAction(RegistrosRepository $registrosRepository, $page = 1)
    {
        $registros = $registrosRepository->findAllOrdenadosFechaQueryBuilder();

        $adaptador = new DoctrineORMAdapter($registros, false);
        $pager = new Pagerfanta($adaptador);
        try {
            $pager
                ->setMaxPerPage(20)
                ->setCurrentPage($page);
        }catch (OutOfRangeCurrentPageException $e){
            $pager->setCurrentPage(1);
        }

        return $this->render('registro/listar.html.twig', [
            'paginador' => $pager
        ]);
    }

    /**
     * @Route("/registro/nuevo", name="registro_nuevo", methods={"GET", "POST"})
     */
    public function nuevoAction(Request $request)
    {
        $nuevoRegistro = new RegistroEstacion();
        $nuevoRegistro->setFechaHora(new \DateTime('now'));
        $nuevoRegistro->setSemanaAnio($nuevoRegistro->imprimirSemanaAnio());
        $em = $this->getDoctrine()->getManager();
        $em->persist($nuevoRegistro);

        $esNuevo = true;

        return $this->formAction($request, $nuevoRegistro, $esNuevo);
    }

    /**
     * @Route("/registro/{id}", name="registro_form",
     *     requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function formAction(Request $request, RegistroEstacion $registroEstacion, $esNuevo = false)
    {
        $form = $this->createForm(RegistroEstacionType::class, $registroEstacion);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('registro/form.html.twig', [
            'form' => $form->createView(),
            'registro' => $registroEstacion,
            'esNuevo' => $esNuevo
        ]);
    }

    /**
     * @Route("/registro/eliminar/{id}", name="registro_eliminar", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function eliminarAction(Request $request, RegistroEstacion $registroEstacion)
    {
        if ($request->getMethod() == 'POST') {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($registroEstacion);
                $em->flush();
                $this->addFlash('success', 'Registro eliminado con éxito');
                return $this->redirectToRoute('registro_listar');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al eliminar el registro');
                return $this->redirectToRoute('registro_form', ['id' => $registroEstacion->getId()]);
            }
        }
        return $this->render('registro/eliminar.html.twig', [
            'registro' => $registroEstacion
        ]);
    }
}
