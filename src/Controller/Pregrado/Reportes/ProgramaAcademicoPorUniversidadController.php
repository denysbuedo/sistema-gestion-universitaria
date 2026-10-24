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
#[Route('/pregrado/reporte/programa_academico_por_universidad')]
class ProgramaAcademicoPorUniversidadController extends AbstractController
{

    /**
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaAcademicoInstitucionRepository
     * @return Response
     */
    #[Route('/', name: 'app_reporte_programa_academico_por_universidad_index', methods: ['GET', 'POST'])]
    public function index(Request $request, InstitucionRepository $institucionRepository, SolicitudProgramaAcademicoInstitucionRepository $solicitudProgramaAcademicoInstitucionRepository, SolicitudProgramaAcademicoPlanEstudioRepository $solicitudProgramaAcademicoPlanEstudioRepository)
    {
        $idUniversidad = $request->getSession()->has('pregrado_programa_por_universidad') ? $request->getSession()->get('pregrado_programa_por_universidad') : [];

        $idUniversidad = is_array($idUniversidad) ? array_values(array_filter(array_map('intval', $idUniversidad))) : [];
        $response = $solicitudProgramaAcademicoInstitucionRepository->getProgramasV3($idUniversidad);
        return $this->render('modules/pregrado/reportes/programa_academico/por_universidad/index.html.twig', [
            'registros' => $response,
            'universidad' => $institucionRepository->findBy([], ['nombre' => 'ASC']),
            'id_universidad' => $idUniversidad
        ]);
    }


    /**
     * @return Response
     */
    #[Route('/filter', name: 'app_reporte_programa_academico_por_universidad_filter', methods: ['POST'])]
    public function filter(Request $request)
    {
        $allPost = $request->request->all();
        $idUniversidad = $allPost['id_universidad'] ?? null;
        $request->getSession()->set('pregrado_programa_por_universidad', $idUniversidad);
        return $this->json(['response' => 'OK']);
    }

    #[Route('/exportar_excel', name: 'app_reporte_programa_academico_por_universidad_exportar_excel', methods: ['GET'])]
    public function exportarExcel(Request $request, SolicitudProgramaAcademicoInstitucionRepository $repository, ExcelReportExporter $exporter): Response
    {
        $ids = $request->getSession()->get('pregrado_programa_por_universidad', []);
        $ids = is_array($ids) ? array_values(array_filter(array_map('intval', $ids))) : [];
        $rows = array_map(static fn (array $item): array => [
            $item['nombre_universidad'] ?? '',
            $item['nombre_organismo'] ?? '',
            $item['programa_academico'] ?? '',
            $item['tipo_programa_academico'] ?? '',
            $item['rama_ciencia'] ?? '',
            $item['centro_rector'] ?? '',
            $item['categoria_acreditacion'] ?? '',
        ], $repository->getProgramasV3($ids));

        return $exporter->create(
            'programas-por-universidad-' . date('Y-m-d'),
            'Programas por universidad',
            ['Universidad', 'Organismo', 'Programa académico', 'Tipo de programa', 'Rama de la ciencia', 'Centro rector', 'Categoría de acreditación'],
            $rows
        );
    }
}
