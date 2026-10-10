<?php

namespace App\Controller\Estructura;

use App\Entity\Estructura\TipoEntidad;
use App\Entity\Security\User;
use App\Form\Estructura\TipoEntidadType;
use App\Repository\Estructura\TipoEntidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_TYPENTY")
 */
#[Route('/rrhh/tipoEntidad')]
class TipoEntidadController extends AbstractController
{

    /**
     * @param TipoEntidadRepository $tipoEntidadRepository
     * @return Response
     */
    #[Route('/', name: 'app_tipo_entidad_index', methods: ['GET'])]
    public function index(TipoEntidadRepository $tipoEntidadRepository)
    {
        try {
            return $this->render('modules/estructura/tipo_entidad/index.html.twig', [
                'registros' => $tipoEntidadRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_entidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TipoEntidadRepository $tipoEntidadRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_tipo_entidad_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, TipoEntidadRepository $tipoEntidadRepository)
    {
        try {
            $catTipoEntidadEntity = new TipoEntidad();
            $form = $this->createForm(TipoEntidadType::class, $catTipoEntidadEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoEntidadRepository->add($catTipoEntidadEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_entidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/estructura/tipo_entidad/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_entidad_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $tipoEntidad
     * @param TipoEntidadRepository $tipoEntidadRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_tipo_entidad_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, TipoEntidad $tipoEntidad, TipoEntidadRepository $tipoEntidadRepository)
    {
        try {
            $form = $this->createForm(TipoEntidadType::class, $tipoEntidad, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tipoEntidadRepository->edit($tipoEntidad);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_entidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/estructura/tipo_entidad/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_entidad_modificar', ['id' => $tipoEntidad], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TipoEntidad $tipoEntidad
     * @param TipoEntidadRepository $tipoEntidadRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_tipo_entidad_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, TipoEntidad $tipoEntidad, TipoEntidadRepository $tipoEntidadRepository)
    {
        try {
            if ($tipoEntidadRepository->find($tipoEntidad) instanceof TipoEntidad) {
                $tipoEntidadRepository->remove($tipoEntidad, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_entidad_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_tipo_entidad_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_entidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TipoEntidad $tipoEntidad
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_tipo_entidad_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, TipoEntidad $tipoEntidad)
    {
        return $this->render('modules/estructura/tipo_entidad/detail.html.twig', [
            'item' => $tipoEntidad,
        ]);
    }
}
