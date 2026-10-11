<?php

namespace App\Controller\Evaluacion;

use App\Entity\Evaluacion\RolComision;
use App\Entity\Security\User;
use App\Form\Evaluacion\RolComisionType;
use App\Repository\Evaluacion\RolComisionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ROL_COMISION')"))]
#[Route('/evaluacion/rol_comision')]
class RolComisionController extends AbstractController
{

    /**
     * @param RolComisionRepository $rolComisionRepository
     * @return Response
     */
    #[Route('/', name: 'app_evaluacion_rol_comision_index', methods: ['GET'])]
    public function index(RolComisionRepository $rolComisionRepository)
    {
        return $this->render('modules/evaluacion/rol_comision/index.html.twig', [
            'registros' => $rolComisionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param RolComisionRepository $rolComisionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_evaluacion_rol_comision_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, RolComisionRepository $rolComisionRepository)
    {
        try {
            $catDocenteEntity = new RolComision();
            $form = $this->createForm(RolComisionType::class, $catDocenteEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $rolComisionRepository->add($catDocenteEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_evaluacion_rol_comision_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/evaluacion/rol_comision/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_evaluacion_rol_comision_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $rolComision
     * @param RolComisionRepository $rolComisionRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_evaluacion_rol_comision_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, RolComision $rolComision, RolComisionRepository $rolComisionRepository)
    {
        try {
            $form = $this->createForm(RolComisionType::class, $rolComision, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $rolComisionRepository->edit($rolComision);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_evaluacion_rol_comision_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/evaluacion/rol_comision/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_evaluacion_rol_comision_modificar', ['id' => $rolComision], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $rolComision
     * @param RolComisionRepository $rolComisionRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_evaluacion_rol_comision_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, RolComision $rolComision)
    {
        return $this->render('modules/evaluacion/rol_comision/detail.html.twig', [
            'item' => $rolComision,
        ]);
    }

    /**
     * @param Request $request
     * @param RolComision $rolComision
     * @param RolComisionRepository $rolComisionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_evaluacion_rol_comision_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, RolComision $rolComision, RolComisionRepository $rolComisionRepository)
    {
        try {
            if ($rolComisionRepository->find($rolComision) instanceof RolComision) {
                $rolComisionRepository->remove($rolComision, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_evaluacion_rol_comision_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_evaluacion_rol_comision_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_evaluacion_rol_comision_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
