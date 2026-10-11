<?php

namespace App\Controller\Postgrado\Reportes;

use App\Form\Postgrado\CambioEstadoProgramaType;
use App\Repository\Postgrado\SolicitudProgramaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_REPORTE_FORMACION_ACADEMICA')"))]
#[Route('/postgrado/reporte/formacion_academica')]
class FormacionAcademicaController extends AbstractController
{

    /**
     * @param SolicitudProgramaRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/', name: 'app_reporte_postgrado_formacion_academica_index', methods: ['GET'])]
    public function indexFormacionAcademica(SolicitudProgramaRepository $solicitudProgramaRepository)
    {
        return $this->render('modules/postgrado/reportes/formacion_academica/index.html.twig', [
            'registros' => $solicitudProgramaRepository->findBy(['tipoPrograma' => [4, 5], 'estadoPrograma' => 7], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

}
