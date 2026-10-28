<?php

namespace App\Controller\GradoCientifico;

use App\Repository\Postgrado\SolicitudProgramaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_GRADO_CIENTIFICO') or is_granted('ROLE_GEST_GRADO_CIENTIFICO') or is_granted('ROLE_GEST_REPORTE_GRADO_CIENTIFICO') or is_granted('ROLE_GEST_FORMACION_DOCTORES')"))]
#[Route('/grado-cientifico')]
class PortadaController extends AbstractController
{
    #[Route('/portada', name: 'app_grado_cientifico_portada', methods: ['GET'])]
    public function index(SolicitudProgramaRepository $repository): Response
    {
        $gradoCientifico = true;
        $aprobados = $repository->countByEstado([7], $gradoCientifico);
        $rechazados = $repository->countByEstado([3], $gradoCientifico);
        $desactivados = $repository->countByEstado([6], $gradoCientifico);
        $extintos = $repository->countByEstado([8], $gradoCientifico);
        $enProceso = max(0, count($repository->getSolicitudes($gradoCientifico)) - $rechazados - $desactivados - $extintos);
        $tipos = $repository->getProgramasPorTipoPrograma($gradoCientifico);
        $modalidades = $repository->getProgramasPorModalidad($gradoCientifico);

        return $this->render('modules/postgrado/portada/index.html.twig', [
            'module_title' => 'Grado Científico',
            'module_subtitle' => 'Doctorados, formación doctoral y programas autorizados',
            'module_icon' => 'microscope',
            'total_programas' => $aprobados,
            'aprobados' => $aprobados,
            'rechazados' => $rechazados,
            'desactivados' => $desactivados,
            'extintos' => $extintos,
            'en_proceso' => $enProceso,
            'tipos_programa' => $tipos,
            'tipos_programa_json' => json_encode($tipos),
            'por_modalidad' => $modalidades,
            'por_modalidad_json' => json_encode($modalidades),
            'por_rama_ciencia' => json_encode($repository->getProgramasPorRamaCiencia($gradoCientifico)),
            'por_universidad' => json_encode($repository->getProgramasPorUniversidad($gradoCientifico)),
            'por_categoria_acreditacion' => json_encode($repository->getProgramasPorCategoriaAcreditacion($gradoCientifico)),
        ]);
    }
}
