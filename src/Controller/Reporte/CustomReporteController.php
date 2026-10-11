<?php

namespace App\Controller\Reporte;

use App\Entity\InformePersonalizado;
use App\Form\Reportes\InformePersonalizadoType;
use App\Repository\InformePersonalizadoRepository;
use App\Repository\PlanActividades\TipoActividadRepository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_CUSTOM_REP')"))]
#[Route('/reporte/custom')]
class CustomReporteController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_reporte_custom', methods: ['GET', 'POST'])]
    public function index(InformePersonalizadoRepository $informePersonalizadoRepository, Utils $utils)
    {
        $registros = [];

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $registros = $informePersonalizadoRepository->findAll();
        } else {
            $registros = $informePersonalizadoRepository->obtenerTodosDadoUsuario($this->getUser());
        }

        $registrosFinal = [];

        foreach ($registros as $rep){
            $aux['reporte'] = $rep;
            $aux['persona'] = $utils->getDatosPersonaDadoIdUsuario($rep->getUsuario()->getId());
            $registrosFinal[] = $aux;
        }



        return $this->render('modules/reporte/custom/index.html.twig', [
            'registros' => $registrosFinal,
        ]);
    }

    /**
     * @param Request $request
     * @param TipoActividadRepository $tipoActividadRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_reporte_custom_create', methods: ['GET', 'POST'])]
    public function registrar(Request $request, InformePersonalizadoRepository $informePersonalizadoRepository)
    {
//        try {
        $report = new InformePersonalizado();
        $form = $this->createForm(\App\Form\Reportes\InformePersonalizadoType::class, $report);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $report->setUsuario($this->getUser());
            $informePersonalizadoRepository->add($report, true);
            $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
            return $this->redirectToRoute('app_reporte_custom', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modules/reporte/custom/new.html.twig', [
            'form' => $form->createView(),
        ]);
//        } catch (\Exception $exception) {
//            $this->addFlash('error', $exception->getMessage());
//            return $this->redirectToRoute('app_reporte_custom_create', [], Response::HTTP_SEE_OTHER);
//        }
    }

    /**
     * @param Request $request
     * @param TipoActividadRepository $tipoActividadRepository
     * @return Response
     */
    #[Route('/modificar/{id}', name: 'app_reporte_custom_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, InformePersonalizadoRepository $informePersonalizadoRepository, InformePersonalizado $informePersonalizado)
    {
        try {
            $report = $informePersonalizado;
            $form = $this->createForm(InformePersonalizadoType::class, $report);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $informePersonalizadoRepository->add($report, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_reporte_custom', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/reporte/custom/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_reporte_custom_modificar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TipoActividadRepository $tipoActividadRepository
     * @return Response
     */
    #[Route('/detalles/{id}', name: 'app_reporte_custom_detalles', methods: ['GET', 'POST'])]
    public function detalles(Request $request, InformePersonalizadoRepository $informePersonalizadoRepository, InformePersonalizado $informePersonalizado)
    {
        try {


            return $this->render('modules/reporte/custom/detalles.html.twig', [
                'reporte' => $informePersonalizado
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_reporte_custom_detalles', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param InformePersonalizadoRepository $informePersonalizadoRepository
     * @param InformePersonalizado $id
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_reporte_custom_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, InformePersonalizadoRepository $informePersonalizadoRepository, InformePersonalizado $id)
    {
        try {

            $informePersonalizadoRepository->remove($id, true);
            $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
            return $this->redirectToRoute('app_reporte_custom', [], Response::HTTP_SEE_OTHER);

        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_municipio_index', [], Response::HTTP_SEE_OTHER);
        }
    }


}
