<?php

namespace App\Controller\Admin;

use App\Entity\Security\User;
use App\Form\Admin\CambiarClaveFormType;
use App\Form\Admin\UserChgPswFormType;
use App\Form\Admin\UserFormType;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


//use Export\Admin\ExportListUsersToPdf;

/**
 */
#[Route('/administracion/cambio_clave')]
class CambioClaveController extends AbstractController
{

    /**
     * @param Request $request
     * @param User $usuario
     * @param UserRepository $usuarioRepository
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route('/{id}/ejecutar', name: 'app_usuario_cambiar_clave', methods: ['GET', 'POST'])]
    public function cambiarContrasenna(Request $request, User $usuario, UserRepository $usuarioRepository, UserPasswordHasherInterface $encoder)
    {
        try {
            $form = $this->createForm(CambiarClaveFormType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                if ($form->getData()->getPasswordPlainText() != "") {
                    $encodedPassword = $encoder->hashPassword($usuario, $form->getData()->getPasswordPlainText());
                    $usuario->setPassword($encodedPassword);
                    $usuario->setPasswordChangeFirstTime(true);
                }

                $usuarioRepository->edit($usuario, true);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_dash_board', [], Response::HTTP_SEE_OTHER);
            }
            return $this->render('modules/admin/usuario/cambiar_clave.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception | NotFoundHttpException $exception) {

        }
    }
}
