<?php

namespace App\Controller\Admin;

use App\Entity\Security\User;
use App\Form\Admin\UserFormType;
use App\Repository\Security\UserRepository;
use App\Services\HandlerFop;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


//use Export\Admin\ExportListUsersToPdf;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_USER")
 */
#[Route('/administracion/usuario')]
class UserController extends AbstractController
{

    /**
     * @param UserRepository $usuarioRepository
     * @return Response
     */
    #[Route('/', name: 'app_usuario_index', methods: ['GET'])]
    public function index(UserRepository $usuarioRepository)
    {
        try {
            return $this->render('modules/admin/usuario/index.html.twig', [
                'usuarios' => $usuarioRepository->getUsuarios(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param UserRepository $usuarioRepository
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    #[Route('/registrar', name: 'app_usuario_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, UserRepository $usuarioRepository, UserPasswordHasherInterface $encoder)
    {
        try {
            $usuario = new User();
            $form = $this->createForm(UserFormType::class, $usuario, ['action' => 'registrar']);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $encodedPassword = $encoder->hashPassword($usuario, $form->getData()->getPasswordPlainText());
                $usuario->setPassword($encodedPassword);
                $usuarioRepository->add($usuario);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/admin/usuario/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_usuario_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $usuario
     * @param UserRepository $usuarioRepository
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_usuario_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, User $usuario, UserRepository $usuarioRepository, UserPasswordHasherInterface $encoder)
    {
        try {
            $form = $this->createForm(UserFormType::class, $usuario, ['action' => 'modificar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if (!empty($_POST['passwordPlainText']['first']) && !empty($_POST['passwordPlainText']['second']) &&
                   $_POST['passwordPlainText']['first'] === $_POST['passwordPlainText']['second']) {
                    $encodedPassword = $encoder->hashPassword($usuario, $form->getData()->getPasswordPlainText());
                    $usuario->setPassword($encodedPassword);
//                    $usuario->setPasswordChangeFirstTime(false);
                }

                $usuario = $usuarioRepository->edit($usuario);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');

                return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/admin/usuario/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_usuario_modificar', ['id' => $usuario], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param User $usuario
     * @param UserRepository $usuarioRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_usuario_eliminar', methods: ['GET'])]
    public function eliminar(User $usuario, UserRepository $usuarioRepository)
    {
        try {
            if ($usuarioRepository->find($usuario) instanceof User) {
                $usuarioRepository->remove($usuario);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param User $user
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_usuario_detail', methods: ['GET', 'POST'])]
    public function detail(User $user)
    {
        return $this->render('modules/admin/usuario/detail.html.twig', [
            'item' => $user,
        ]);
    }

    /**
     * @param User $user
     *
     * @return Response
     */
    #[Route('/exportar_pdf', name: 'app_usuario_exportar_pdf', methods: ['GET', 'POST'])]
    public function exportarPdf(HandlerFop $handFop, UserRepository $usuarioRepository)
    {
        $export = $usuarioRepository->findBy([], ['id' => 'desc']);

        $export = \App\Services\DoctrineHelper::toArray($export);

        return $handFop->exportToPdf(new \App\Export\Admin\ExportListUsersToPdf($export));

    }

    /**
     * @param HandlerFop $handFop
     * @param UserRepository $usuarioRepository
     * @return void
     */
    #[Route('/exportar_excel', name: 'app_usuario_exportar_excel', methods: ['GET', 'POST'])]
    public function exportarExcel(HandlerFop $handFop, UserRepository $usuarioRepository)
    {
        $export = $usuarioRepository->findBy([], ['id' => 'desc']);
        $html = $this->render('modules/admin/usuario/index_excel.html.twig', ['datos' => $export])->getContent();
        $handFop->exportToExcel($html, 'Listado de usuarios');
    }
}
