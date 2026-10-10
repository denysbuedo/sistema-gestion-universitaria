<?php

namespace App\Controller\Tramite;

use App\Entity\Tramite\ConceptoSalida;
use App\Entity\Security\User;
use App\Form\Tramite\ConceptoSalidaType;
use App\Repository\Tramite\ConceptoSalidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_CONCEPTO_SALIDA")
 */
#[Route('/tramite/concepto_salida')]
class ConceptoSalidaController extends AbstractController
{

    /**
     * @param conceptoSalidaRepository $conceptoSalidaRepository
     * @return Response
     */
    #[Route('/', name: 'app_concepto_salida_index', methods: ['GET'])]
    public function index(ConceptoSalidaRepository $conceptoSalidaRepository)
    {
        return $this->render('modules/tramite/concepto_salida/index.html.twig', [
            'registros' => $conceptoSalidaRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param conceptoSalidaRepository $conceptoSalidaRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_concepto_salida_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, ConceptoSalidaRepository $conceptoSalidaRepository)
    {
        try {
            $entidad = new conceptoSalida();
            $form = $this->createForm(ConceptoSalidaType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $conceptoSalidaRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_concepto_salida_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/tramite/concepto_salida/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_concepto_salida_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $conceptoSalida
     * @param conceptoSalidaRepository $conceptoSalidaRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_concepto_salida_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, ConceptoSalida $conceptoSalida, ConceptoSalidaRepository $conceptoSalidaRepository)
    {
        try {
            $form = $this->createForm(ConceptoSalidaType::class, $conceptoSalida, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $conceptoSalidaRepository->edit($conceptoSalida);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_concepto_salida_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/tramite/concepto_salida/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_concepto_salida_modificar', ['id' => $conceptoSalida], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param conceptoSalida $conceptoSalida
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_concepto_salida_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, ConceptoSalida $conceptoSalida)
    {
        return $this->render('modules/tramite/concepto_salida/detail.html.twig', [
            'item' => $conceptoSalida,
        ]);
    }

    /**
     * @param Request $request
     * @param conceptoSalida $conceptoSalida
     * @param conceptoSalidaRepository $conceptoSalidaRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_concepto_salida_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, ConceptoSalida $conceptoSalida, ConceptoSalidaRepository $conceptoSalidaRepository)
    {
        try {
            if ($conceptoSalidaRepository->find($conceptoSalida) instanceof ConceptoSalida) {
                $conceptoSalidaRepository->remove($conceptoSalida, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_concepto_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_concepto_salida_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_concepto_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
