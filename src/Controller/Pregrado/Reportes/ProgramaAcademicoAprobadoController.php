<?php

namespace App\Controller\Pregrado\Reportes;

use App\Entity\NotificacionesUsuario;
use App\Entity\Pregrado\MiembrosComisionNacional;
use App\Entity\Pregrado\ProgramaAcademicoDesactivado;
use App\Entity\Pregrado\ProgramaAcademicoReabierto;
use App\Entity\Pregrado\ProgramaAcademicoReabiertoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use App\Entity\Pregrado\SolicitudProgramaAcademicoComisionNacional;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademicoPlanEstudio;
use App\Export\Personal\ExportListPersonaToPdf;
use App\Export\Pregrado\ExportListSolicitudProgramaAcademicoToPdf;
use App\Export\Pregrado\ExportListSolicitudUniversidadesAsignadasToPdf;
use App\Form\Pregrado\AprobarModificarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\AprobarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\ProgramaAcademicoDesactivadoType;
use App\Form\Pregrado\ProgramaAcademicoReabiertoType;
use App\Form\Pregrado\SolicitudProgramaComisionType;
use App\Form\Pregrado\SolicitudProgramaInstitucionType;
use App\Form\Pregrado\SolicitudProgramaPlanEstudioType;
use App\Repository\Institucion\CategoriaAcreditacionRepository;
use App\Repository\Institucion\InstitucionRepository;
use App\Repository\NotificacionesUsuarioRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Pregrado\EstadoProgramaAcademicoRepository;
use App\Repository\Pregrado\MiembrosComisionNacionalRepository;
use App\Repository\Pregrado\ModificacionPlanEstudioRepository;
use App\Repository\Pregrado\ProgramaAcademicoDesactivadoRepository;
use App\Repository\Pregrado\ProgramaAcademicoReabiertoInstitucionRepository;
use App\Repository\Pregrado\ProgramaAcademicoReabiertoRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoComisionNacionalRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoPlanEstudioRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use App\Services\DoctrineHelper;
use App\Services\HandlerFop;
use App\Services\Utils;
use App\Services\ExcelReportExporter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PROGAPROB')"))]
#[Route('/pregrado/reporte/programa_academico_aprobado')]
class ProgramaAcademicoAprobadoController extends AbstractController
{

    /**
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/', name: 'app_reporte_programa_academico_aprobado_index', methods: ['GET'])]
    public function index(SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, SolicitudProgramaAcademicoPlanEstudioRepository $solicitudProgramaAcademicoPlanEstudioRepository)
    {
        $response = [];
        $registros = $solicitudProgramaRepository->getSolicitudProgramaAcademicoAprobado([2, 4, 6, 7]);
        if (is_array($registros)) {
            foreach ($registros as $value) {
                $temp = $solicitudProgramaAcademicoPlanEstudioRepository->findBy(['solicitudProgramaAcademico' => $value->getId()]);
                if (isset($temp[0])) {
                    $value->plan_estudio = $temp[0]->getPlanEstudio()->getPlanEstudio();
                }
                $response[] = $value;
            }
        }
        return $this->render('modules/pregrado/reportes/programa_academico_aprobado/index.html.twig', [
            'registros' => $response,
        ]);
    }

    #[Route('/exportar_excel', name: 'app_reporte_programa_academico_aprobado_exportar_excel', methods: ['GET'])]
    public function exportarExcel(SolicitudProgramaAcademicoRepository $repository, ExcelReportExporter $exporter): Response
    {
        $rows = [];
        foreach ($repository->getSolicitudProgramaAcademicoAprobado([2, 4, 6, 7]) as $item) {
            $rows[] = [
                $item->getOrganismoFormador()?->getNombre() ?? '',
                $item->getNombre() ?? '',
                $item->getTipoProgramaAcademico()?->getNombre() ?? '',
                $item->getRamaCiencia()?->getNombre() ?? '',
                $item->getCentroRector()?->getNombre() ?? '',
                $item->getCategoriaAcreditacion()?->getNombre() ?? '',
            ];
        }

        return $exporter->create(
            'programas-aprobados-' . date('Y-m-d'),
            'Programas aprobados',
            ['Organismo', 'Programa académico', 'Tipo de programa', 'Rama de la ciencia', 'Centro rector', 'Categoría de acreditación'],
            $rows
        );
    }

}
