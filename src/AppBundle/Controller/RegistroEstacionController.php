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
    private $nombreMeses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    /**
     * @return string[]
     */
    public function getNombreMeses()
    {
        return $this->nombreMeses;
    }

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
        }

        return $this->render('registro/graficas/anuales/graficaTemperaturaAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    /**
     * @Route("/registro/temperaturaMensual", name="temperatura_mensual")
     */
    public function temperaturaMensualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];

                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);
                    foreach ($semanas as $semana){
                        $mediaDiurna = $registrosRepository->findMediaDiurnaPorSemanaTemperatura($anioElegido, $mesElegido, $semana);
                        $mediaNocturna = $registrosRepository->findMediaNocturnaPorSemanaTemperatura($anioElegido, $mesElegido, $semana);
                        $datos[] = [
                            'semana' => $semana,
                            'mediaDiurna' => $mediaDiurna,
                            'mediaNocturna' => $mediaNocturna
                        ];
                    }
                }
            }
        }

        return $this->render('registro/graficas/mensuales/graficaTemperaturaMensual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido
        ]);
    }

    /**
     * @Route("/registro/temperaturaSemanal", name="temperatura_semanal")
     */
    public function temperaturaSemanalAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $semanas = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';
        $semanaElegida = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];
                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);

                    if(!empty($_POST['selectSemana'])){
                        $semanaElegida = $_POST['selectSemana'];

                        $dias = $registrosRepository->findDiasPorSemanaMesAnio($semanaElegida, $mesElegido, $anioElegido);
                        foreach ($dias as $dia){
                            $mediaDiurna = $registrosRepository->findMediaDiurnaPorDiaTemperatura($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $mediaNocturna = $registrosRepository->findMediaNocturnaPorDiaTemperatura($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $datos[] = [
                                'dia' => $dia,
                                'mediaDiurna' => $mediaDiurna,
                                'mediaNocturna' => $mediaNocturna
                            ];
                        }
                    }
                }
            }
        }

        return $this->render('registro/graficas/semanales/graficaTemperaturaSemanal.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'semanas' => $semanas,
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido,
            'semanaElegida' => $semanaElegida
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

        return $this->render('registro/graficas/anuales/graficaHumedadAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    /**
     * @Route("/registro/humedadMensual", name="humedad_mensual")
     */
    public function humedadMensualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];

                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);
                    foreach ($semanas as $semana){
                        $mediaDiurna = $registrosRepository->findMediaDiurnaPorSemanaHumedad($anioElegido, $mesElegido, $semana);
                        $mediaNocturna = $registrosRepository->findMediaNocturnaPorSemanaHumedad($anioElegido, $mesElegido, $semana);
                        $datos[] = [
                            'semana' => $semana,
                            'mediaDiurna' => $mediaDiurna,
                            'mediaNocturna' => $mediaNocturna
                        ];
                    }
                }
            }
        }

        return $this->render('registro/graficas/mensuales/graficaHumedadMensual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido
        ]);
    }

    /**
     * @Route("/registro/humedadSemanal", name="humedad_semanal")
     */
    public function humedadSemanalAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $semanas = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';
        $semanaElegida = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];
                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);

                    if(!empty($_POST['selectSemana'])){
                        $semanaElegida = $_POST['selectSemana'];

                        $dias = $registrosRepository->findDiasPorSemanaMesAnio($semanaElegida, $mesElegido, $anioElegido);
                        foreach ($dias as $dia){
                            $mediaDiurna = $registrosRepository->findMediaDiurnaPorDiaHumedad($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $mediaNocturna = $registrosRepository->findMediaNocturnaPorDiaHumedad($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $datos[] = [
                                'dia' => $dia,
                                'mediaDiurna' => $mediaDiurna,
                                'mediaNocturna' => $mediaNocturna
                            ];
                        }
                    }
                }
            }
        }

        return $this->render('registro/graficas/semanales/graficaHumedadSemanal.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'semanas' => $semanas,
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido,
            'semanaElegida' => $semanaElegida
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

        return $this->render('registro/graficas/anuales/graficaLluviaAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    /**
     * @Route("/registro/lluviaMensual", name="lluvia_mensual")
     */
    public function lluviaMensualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];

                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);
                    foreach ($semanas as $semana){
                        $mediaDiurna = $registrosRepository->findMediaDiurnaPorSemanaLluvia($anioElegido, $mesElegido, $semana);
                        $mediaNocturna = $registrosRepository->findMediaNocturnaPorSemanaLluvia($anioElegido, $mesElegido, $semana);
                        $datos[] = [
                            'semana' => $semana,
                            'mediaDiurna' => $mediaDiurna,
                            'mediaNocturna' => $mediaNocturna
                        ];
                    }
                }
            }
        }

        return $this->render('registro/graficas/mensuales/graficaLluviaMensual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido
        ]);
    }

    /**
     * @Route("/registro/lluviaSemanal", name="lluvia_semanal")
     */
    public function lluviaSemanalAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $semanas = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';
        $semanaElegida = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];
                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);

                    if(!empty($_POST['selectSemana'])){
                        $semanaElegida = $_POST['selectSemana'];

                        $dias = $registrosRepository->findDiasPorSemanaMesAnio($semanaElegida, $mesElegido, $anioElegido);
                        foreach ($dias as $dia){
                            $mediaDiurna = $registrosRepository->findMediaDiurnaPorDiaLluvia($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $mediaNocturna = $registrosRepository->findMediaNocturnaPorDiaLluvia($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $datos[] = [
                                'dia' => $dia,
                                'mediaDiurna' => $mediaDiurna,
                                'mediaNocturna' => $mediaNocturna
                            ];
                        }
                    }
                }
            }
        }

        return $this->render('registro/graficas/semanales/graficaLluviaSemanal.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'semanas' => $semanas,
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido,
            'semanaElegida' => $semanaElegida
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

        return $this->render('registro/graficas/anuales/graficaVientoAnual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'anioElegido' => $anioElegido
        ]);
    }

    /**
     * @Route("/registro/vientoMensual", name="viento_mensual")
     */
    public function vientoMensualAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];

                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);
                    foreach ($semanas as $semana){
                        $mediaDiurna = $registrosRepository->findMediaDiurnaPorSemanaViento($anioElegido, $mesElegido, $semana);
                        $mediaNocturna = $registrosRepository->findMediaNocturnaPorSemanaViento($anioElegido, $mesElegido, $semana);
                        $datos[] = [
                            'semana' => $semana,
                            'mediaDiurna' => $mediaDiurna,
                            'mediaNocturna' => $mediaNocturna
                        ];
                    }
                }
            }
        }

        return $this->render('registro/graficas/mensuales/graficaVientoMensual.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido
        ]);
    }

    /**
     * @Route("/registro/vientoSemanal", name="viento_semanal")
     */
    public function vientoSemanalAction(Request $request, RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        $meses = '';
        $semanas = '';
        $datos = [];
        $anioElegido = '';
        $mesElegido = '';
        $semanaElegida = '';

        if ($request->getMethod() == 'POST') {
            if(!empty($_POST['selectAnio'])){
                $anioElegido = $_POST['selectAnio'];
                $meses = $registrosRepository->findMesesPorAnio($anioElegido);

                if(!empty($_POST['selectMes'])){
                    $mesElegido = $_POST['selectMes'];
                    $semanas = $registrosRepository->findSemanasPorMesAnio($mesElegido, $anioElegido);

                    if(!empty($_POST['selectSemana'])){
                        $semanaElegida = $_POST['selectSemana'];

                        $dias = $registrosRepository->findDiasPorSemanaMesAnio($semanaElegida, $mesElegido, $anioElegido);
                        foreach ($dias as $dia){
                            $mediaDiurna = $registrosRepository->findMediaDiurnaPorDiaViento($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $mediaNocturna = $registrosRepository->findMediaNocturnaPorDiaViento($anioElegido, $mesElegido, $semanaElegida, $dia);
                            $datos[] = [
                                'dia' => $dia,
                                'mediaDiurna' => $mediaDiurna,
                                'mediaNocturna' => $mediaNocturna
                            ];
                        }
                    }
                }
            }
        }

        return $this->render('registro/graficas/semanales/graficaVientoSemanal.html.twig', [
            'datos' => $datos,
            'anios' => $anios,
            'meses' => $meses,
            'nombreMeses' => $this->getNombreMeses(),
            'semanas' => $semanas,
            'anioElegido' => $anioElegido,
            'mesElegido' => $mesElegido,
            'semanaElegida' => $semanaElegida
        ]);
    }
}
