<?php

namespace App\Controller\RRHH;

use App\Controller\RRHH\CategoriaDocenteRepository;
use App\Entity\RRHH\Especialidad;
use App\Entity\Security\User;
use App\Form\RRHH\EspecialidadType;
use App\Repository\RRHH\EspecialidadRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_GEST_ESPECIALIDAD')"))]
#[Route('/rrhh/especialidad')]
class EspecialidadController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('/', name: 'app_rrhh_especialidad_index', methods: ['GET'])]
    public function index(EspecialidadRepository $especialidadRepository)
    {
        try {
            return $this->render('modules/rrhh/especialidad/index.html.twig', [
                'registros' => $especialidadRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_especialidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param EspecialidadRepository $especialidadRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_rrhh_especialidad_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, EspecialidadRepository $especialidadRepository)
    {
        try {
            $catDocenteEntity = new Especialidad();
            $form = $this->createForm(EspecialidadType::class, $catDocenteEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $especialidadRepository->add($catDocenteEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_especialidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/rrhh/especialidad/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_especialidad_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Especialidad $especialidad
     * @param EspecialidadRepository $especialidadRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_rrhh_especialidad_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Especialidad $especialidad, EspecialidadRepository $especialidadRepository)
    {
        try {
            $form = $this->createForm(EspecialidadType::class, $especialidad, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $especialidadRepository->edit($especialidad);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_especialidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/rrhh/especialidad/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_especialidad_modificar', ['id' => $especialidad], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Especialidad $especialidad
     * @param EspecialidadRepository $especialidadRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_rrhh_especialidad_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Especialidad $especialidad, EspecialidadRepository $especialidadRepository)
    {
        try {
            if ($especialidadRepository->find($especialidad) instanceof Especialidad) {
                $especialidadRepository->remove($especialidad, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_especialidad_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_rrhh_especialidad_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_especialidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Especialidad $especialidad
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_rrhh_especialidad_detail', methods: ['GET', 'POST'])]
    public function detail(Especialidad $especialidad)
    {
        return $this->render('modules/rrhh/especialidad/detail.html.twig', [
            'item' => $especialidad,
        ]);
    }
}
