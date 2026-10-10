<?php

namespace App\Controller\Estructura;

use App\Entity\Estructura\TipoEstructura;
use App\Entity\Security\User;
use App\Form\Estructura\TipoEstructuraType;
use App\Repository\Estructura\TipoEstructuraRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_TYPEESTR")
 */
#[Route('/rrhh/tipo_estructura')]
class TipoEstructuraController extends AbstractController
{

    /**
     * @param TipoEstructuraRepository $tipoEstructuraRepository
     * @return Response
     */
    #[Route('/', name: 'app_tipo_estructura_index', methods: ['GET'])]
    public function index(TipoEstructuraRepository $tipoEstructuraRepository)
    {
        try {
            return $this->render('modules/estructura/tipo_estructura/index.html.twig', [
                'registros' => $tipoEstructuraRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_estructura_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TipoEstructuraRepository $tipoEstructuraRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_tipo_estructura_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, TipoEstructuraRepository $tipoEstructuraRepository)
    {
        try {
            $catEstructuraEntity = new TipoEstructura();
            $form = $this->createForm(TipoEstructuraType::class, $catEstructuraEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoEstructuraRepository->add($catEstructuraEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_estructura_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/estructura/tipo_estructura/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_estructura_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $tipoEstructura
     * @param TipoEstructuraRepository $tipoEstructuraRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_tipo_estructura_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, TipoEstructura $tipoEstructura, TipoEstructuraRepository $tipoEstructuraRepository)
    {
        try {
            $form = $this->createForm(TipoEstructuraType::class, $tipoEstructura, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tipoEstructuraRepository->edit($tipoEstructura);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_estructura_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/estructura/tipo_estructura/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_estructura_modificar', ['id' => $tipoEstructura], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TipoEstructura $tipoEstructura
     * @param TipoEstructuraRepository $tipoEstructuraRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_tipo_estructura_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, TipoEstructura $tipoEstructura, TipoEstructuraRepository $tipoEstructuraRepository)
    {
        try {
            if ($tipoEstructuraRepository->find($tipoEstructura) instanceof TipoEstructura) {
                $tipoEstructuraRepository->remove($tipoEstructura, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_estructura_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_tipo_estructura_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_estructura_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param User $tipoEstructura
     * @param TipoDocenteRepository $tipoDocenteRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_tipo_estructura_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, TipoEstructura $tipoEstructura)
    {
        return $this->render('modules/estructura/tipo_estructura/detail.html.twig', [
            'item' => $tipoEstructura,
        ]);
    }
}
