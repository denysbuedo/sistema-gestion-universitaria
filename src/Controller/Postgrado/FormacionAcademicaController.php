<?php

namespace App\Controller\Postgrado;

use App\Entity\Postgrado\SolicitudPrograma;
use App\Entity\Postgrado\SolicitudProgramaComision;
use App\Entity\Security\User;
use App\Form\Postgrado\AprobarProgramaType;
use App\Form\Postgrado\CambioEstadoProgramaType;
use App\Form\Postgrado\ComisionProgramaType;
use App\Form\Postgrado\NoAprobarProgramaType;
use App\Form\Postgrado\SolicitudProgramaType;
use App\Repository\Postgrado\ComisionRepository;
use App\Repository\Postgrado\EstadoProgramaRepository;
use App\Repository\Postgrado\SolicitudProgramaComisionRepository;
use App\Repository\Postgrado\SolicitudProgramaRepository;
use App\Services\TraceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_FORMACION_ACADEMICA")
 */
#[Route('/postgrado/programas_aprobados')]
class FormacionAcademicaController extends AbstractController
{


    /**
     * @param SolicitudProgramaRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/formacion_academica', name: 'app_posgrado_formacion_academica_index', methods: ['GET'])]
    public function indexFormacionAcademica(SolicitudProgramaRepository $solicitudProgramaRepository)
    {
        return $this->render('modules/postgrado/programas_aprobados/index.html.twig', [
            'registros' => $solicitudProgramaRepository->findBy(['tipoPrograma' => [4, 5], 'estadoPrograma' => 7], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }
}
