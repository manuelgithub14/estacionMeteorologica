<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use AppBundle\Form\Model\CambioClave;
use AppBundle\Form\Type\CambioClaveType;
use AppBundle\Form\Type\MiUsuarioType;
use AppBundle\Form\Type\UsuarioType;
use AppBundle\Repository\UsuarioRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UsuarioController extends Controller
{
    /**
     * @Route("/usuarios/{page}", name="usuario_listar")
     */
    public function indexAction(UsuarioRepository $usuarioRepository, $page = 1)
    {
        $usuarios = $usuarioRepository->findAllOrdenadosQueryBuilder();

        $adaptador = new DoctrineORMAdapter($usuarios, false);
        $pager = new Pagerfanta($adaptador);
        try {
            $pager
                ->setMaxPerPage(5)
                ->setCurrentPage($page);
        }catch (OutOfRangeCurrentPageException $e){
            $pager->setCurrentPage(1);
        }

        return $this->render('usuario/listar.html.twig', [
            'paginador' => $pager
        ]);
    }

    /**
     * @Route("/usuario/nuevo", name="usuario_nuevo", methods={"GET", "POST"})
     */
    public function nuevoAction(Request $request)
    {
        $nuevousuario = new Usuario();
        $em = $this->getDoctrine()->getManager();
        $em->persist($nuevousuario);

        return $this->formAction($request, $nuevousuario);
    }

    /**
     * @Route("/usuario/{id}", name="usuario_form",
     *     requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function formAction(Request $request, Usuario $usuario)
    {
        $form = $this->createForm(UsuarioType::class, $usuario, [
            'nuevo' => $usuario->getId() ===  null
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Cambios en usuario guardados');
                return $this->redirectToRoute('usuario_listar');
            }catch (\Exception $e){
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }

        return $this->render('usuario/form.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario
        ]);
    }

    /**
     * @Route("/usuario/eliminar/{id}", name="usuario_eliminar", methods={"GET", "POST"})
     */
    public function eliminarAction(Request $request, Usuario $usuario)
    {
        if ($request->getMethod() == 'POST') {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($usuario);
                $em->flush();
                $this->addFlash('success', 'Usuario eliminado con éxito');
                return $this->redirectToRoute('usuario_listar');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al eliminar el usuario');
                return $this->redirectToRoute('usuario_form', ['id' => $usuario->getId()]);
            }
        }
        return $this->render('usuario/eliminar.html.twig', [
            'usuario' => $usuario
        ]);
    }

    /**
     * @Route("/perfil", name="usuario_perfil", methods={"GET", "POST"})
     */
    public function perfilAction(Request $request)
    {
        $usuario = $this->getUser();
        $form = $this->createForm(MiUsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('success', 'Cambios en el usuario guardados con éxito');
                return $this->redirectToRoute('portada');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('usuario/perfil_form.html.twig', [
            'formulario' => $form->createView(),
            'usuario' => $usuario
        ]);
    }

    /**
     * @Route("/perfil/clave", name="usuario_cambiar_clave", methods={"GET", "POST"})
     */
    public function cambioClaveAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $cambioClave = new CambioClave();

        $form = $this->createForm(CambioClaveType::class, $cambioClave);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                /** @var Usuario $user */
                $user = $this->getUser();
                $user->setClave(
                    $encoder->encodePassword($user, $cambioClave->getNuevaClave())
                );
                $em->flush();
                $this->addFlash('success', 'Cambios en la contraseña guardados con éxito');
                return $this->redirectToRoute('usuario_perfil');
            }
            catch(\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar la contraseña');
            }
        }
        return $this->render('usuario/cambio_clave_form.html.twig', [
            'formulario' => $form->createView()
        ]);
    }
}
