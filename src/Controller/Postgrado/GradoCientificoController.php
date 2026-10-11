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
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_GRADO_CIENTIFICO')"))]
#[Route('/postgrado/programas_aprobados')]
class GradoCientificoController extends AbstractController
{

    /**
     * @param SolicitudProgramaRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/grados_cientificos', name: 'app_posgrado_grados_cientificos_index', methods: ['GET'])]
    public function indexGradoCientifico(SolicitudProgramaRepository $solicitudProgramaRepository)
    {
        return $this->render('modules/postgrado/programas_aprobados/index.html.twig', [
            'registros' => $solicitudProgramaRepository->findBy(['tipoPrograma' => 6, 'estadoPrograma' => 7], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

}
