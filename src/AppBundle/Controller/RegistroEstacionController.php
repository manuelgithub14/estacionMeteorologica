<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RegistroEstacion;
use AppBundle\Form\Type\AniosType;
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
     * @Route("/registro/{id}", name="registro_form",
     *     requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function formAction(Request $request, RegistroEstacion $registroEstacion)
    {
        $form = $this->createForm(RegistroEstacionType::class, $registroEstacion);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('registro/form.html.twig', [
            'form' => $form->createView(),
            'registro' => $registroEstacion
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

    // ELIMINAR REGISTROS POR AÑO
    /**
     * @Route("/registro/eliminar_anio/{anio}", name="registro_eliminar_anio", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function eliminarAnioAction(Request $request, RegistrosRepository $registrosRepository, $anio)
    {
        if ($request->getMethod() == 'POST') {
            $registrosAnio = $registrosRepository->findAllPorAnio($anio);
            $borrado = '';

            foreach ($registrosAnio as $registro){
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($registro);
                    $em->flush();
                    $borrado = true;
                }
                catch (\Exception $e) {
                    $borrado = false;
                    break;
                }
            }

            if($borrado){
                $this->addFlash('success', 'Registros eliminados con éxito');
                return $this->redirectToRoute('registro_listar');
            }else{
                $this->addFlash('error', 'Ha ocurrido un error al eliminar los registros');
                return $this->redirectToRoute('registro_listar');
            }
        }

        return $this->render('registro/eliminarAnio.html.twig', [
            'anio' => $anio
        ]);
    }

    /**
     * @Route("/registro/anio", name="registro_form_anio", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function formAnioAction(Request $request)
    {
        $datos = ['lista' => ''];
        $form = $this->createForm(AniosType::class, $datos);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            $anio = $form->get('lista')->getData();

            return $this->redirectToRoute('registro_eliminar_anio', ['anio' => $anio]);
        }

        return $this->render('registro/formAnio.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // RUTAS DE TEMPERATURA
    /**
     * @Route("/registro/temperaturaAnual", name="temperatura_anual")
     */
    public function temperaturaAnualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $datos = [];
        $anioElegido = '';

        if ($request->getMethod() == 'POST') {
            // submit con js al elegir el año, desde "registro/graficas/graficaTemperaturaAnual.html.twig"
            $anioElegido = $_POST['select'];

            $meses = $registrosRepository->findMesesPorAnio($anioElegido);
            foreach ($meses as $mes){
                $mediaDiurna = $registrosRepository->findMediaDiurnaPorMesTemperatura($anioElegido, $mes);
                $mediaNocturna = $registrosRepository->findMediaNocturnaPorMesTemperatura($anioElegido, $mes);
                $datos[] = [
                    'mes' => $mes,
                    'mediaDiurna' => $mediaDiurna,
                    'mediaNocturna' => $mediaNocturna
                ];
            }

            dump($datos);
        }

        return $this->render('registro/graficas/graficaTemperaturaAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    // RUTAS DE HUMEDAD
    /**
     * @Route("/registro/humedadAnual", name="humedad_anual")
     */
    public function humedadAnualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $datos = [];
        $anioElegido = '';

        if ($request->getMethod() == 'POST') {
            // submit con js al elegir el año
            $anioElegido = $_POST['select'];

            $meses = $registrosRepository->findMesesPorAnio($anioElegido);
            foreach ($meses as $mes){
                $mediaDiurna = $registrosRepository->findMediaDiurnaPorMesHumedad($anioElegido, $mes);
                $mediaNocturna = $registrosRepository->findMediaNocturnaPorMesHumedad($anioElegido, $mes);
                $datos[] = [
                    'mes' => $mes,
                    'mediaDiurna' => $mediaDiurna,
                    'mediaNocturna' => $mediaNocturna
                ];
            }
        }

        return $this->render('registro/graficas/graficaHumedadAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    // RUTAS DE LLUVIA
    /**
     * @Route("/registro/lluviaAnual", name="lluvia_anual")
     */
    public function lluviaAnualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $datos = [];
        $anioElegido = '';

        if ($request->getMethod() == 'POST') {
            // submit con js al elegir el año
            $anioElegido = $_POST['select'];

            $meses = $registrosRepository->findMesesPorAnio($anioElegido);
            foreach ($meses as $mes){
                $mediaDiurna = $registrosRepository->findMediaDiurnaPorMesLluvia($anioElegido, $mes);
                $mediaNocturna = $registrosRepository->findMediaNocturnaPorMesLluvia($anioElegido, $mes);
                $datos[] = [
                    'mes' => $mes,
                    'mediaDiurna' => $mediaDiurna,
                    'mediaNocturna' => $mediaNocturna
                ];
            }
        }

        return $this->render('registro/graficas/graficaLluviaAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    // RUTAS DE VIENTO
    /**
     * @Route("/registro/vientoAnual", name="viento_anual")
     */
    public function vientoAnualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $datos = [];
        $anioElegido = '';

        if ($request->getMethod() == 'POST') {
            // submit con js al elegir el año
            $anioElegido = $_POST['select'];

            $meses = $registrosRepository->findMesesPorAnio($anioElegido);
            foreach ($meses as $mes){
                $mediaDiurna = $registrosRepository->findMediaDiurnaPorMesViento($anioElegido, $mes);
                $mediaNocturna = $registrosRepository->findMediaNocturnaPorMesViento($anioElegido, $mes);
                $datos[] = [
                    'mes' => $mes,
                    'mediaDiurna' => $mediaDiurna,
                    'mediaNocturna' => $mediaNocturna
                ];
            }
        }

        return $this->render('registro/graficas/graficaVientoAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }
}
