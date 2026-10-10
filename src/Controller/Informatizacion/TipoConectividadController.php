<?php

namespace App\Controller\Informatizacion;

use App\Entity\Informatizacion\TipoConectividad;
use App\Form\Informatizacion\TipoConectividadType;
use App\Repository\Informatizacion\TipoConectividadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_TIPO_CONECTIVIDAD")
 */
#[Route('/informatizacion/tipo_conectividad')]
class TipoConectividadController extends AbstractController
{

    /**
     * @param TipoConectividadRepository $TipoConectividadRepository
     * @return Response
     */
    #[Route('/', name: 'app_tipo_conectividad_index', methods: ['GET'])]
    public function index(TipoConectividadRepository $TipoConectividadRepository)
    {
        return $this->render('modules/informatizacion/tipoConectividad/index.html.twig', [
            'registros' => $TipoConectividadRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param TipoConectividadRepository $tipoConectividadRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_tipo_conectividad_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, TipoConectividadRepository $tipoConectividadRepository)
    {
        try {
            $entidad = new TipoConectividad();
            $form = $this->createForm(TipoConectividadType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoConectividadRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_conectividad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/tipoConectividad/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_conectividad_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param TipoConectividad $tipoConectividad
     * @param TipoConectividadRepository $tipoConectividadRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_tipo_conectividad_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, TipoConectividad $tipoConectividad, TipoConectividadRepository $tipoConectividadRepository)
    {
        try {
            $form = $this->createForm(TipoConectividadType::class, $tipoConectividad, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tipoConectividadRepository->edit($tipoConectividad);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_conectividad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/tipoConectividad/edit.html.twig', [
                'form' => $form->createView(),
                'tipoConectividad' => $tipoConectividad
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_conectividad_modificar', ['id' => $tipoConectividad->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param tipoConectividad $tipoConectividad
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_tipo_conectividad_detail', methods: ['GET', 'POST'])]
    public function detail(TipoConectividad $tipoConectividad)
    {
        return $this->render('modules/informatizacion/tipoConectividad/detail.html.twig', [
            'item' => $tipoConectividad,
        ]);
    }

    /**
     * @param TipoConectividad $tipoConectividad
     * @param TipoConectividadRepository $tipoConectividadRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_tipo_conectividad_eliminar', methods: ['GET'])]
    public function eliminar(TipoConectividad $tipoConectividad, TipoConectividadRepository $tipoConectividadRepository)
    {
        try {
            if ($tipoConectividadRepository->find($tipoConectividad) instanceof TipoConectividad) {
                $tipoConectividadRepository->remove($tipoConectividad, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_conectividad_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_tipo_conectividad_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_conectividad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
