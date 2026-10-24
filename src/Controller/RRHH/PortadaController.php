<?php

namespace App\Controller\RRHH;

use App\Repository\RRHH\AE2Repository;
use App\Repository\RRHH\AE3Repository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_RRHH')"))]
#[Route('/rrhh/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_rrhh_portada', methods: ['GET'])]
    public function index(Request $request, AE2Repository $ae2Repo, AE3Repository $ae3Repo, Utils $utils): Response
    {
        $datosEntidad = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $esAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
        $entidadId = $esAdmin ? null : (int) ($datosEntidad['id_entidad'] ?? -1);
        if (!$esAdmin && $entidadId <= 0) {
            throw $this->createAccessDeniedException('Su usuario no está asociado a una universidad.');
        }
        $alcance = $esAdmin ? 'Consolidado nacional' : ($datosEntidad['nombre_entidad'] ?: 'Su universidad');

        $mesesNombres = [
            1 => 'Enero', 2 => 'Febrero',  3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo',     6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto',   9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        // ── Período solicitado o, por defecto, el más reciente con datos ──
        $periodos = $ae2Repo->getPeriodosDisponibles($entidadId);
        $annoSolicitado = $request->query->getInt('anno');
        $mesSolicitado = $request->query->getInt('mes');
        $periodoSeleccionado = null;
        foreach ($periodos as $periodo) {
            if ((int) $periodo['anno'] === $annoSolicitado && (int) $periodo['mes'] === $mesSolicitado) {
                $periodoSeleccionado = $periodo;
                break;
            }
        }
        $periodoSeleccionado ??= $periodos[0] ?? null;
        $anno = $periodoSeleccionado ? (int) $periodoSeleccionado['anno'] : (int) date('Y');
        $mes = $periodoSeleccionado ? (int) $periodoSeleccionado['mes'] : (int) date('n');
        $periodoLabel = $periodoSeleccionado ? ($mesesNombres[$mes] . ' ' . $anno) : 'Sin datos';

        // ── Totales AE2 del período actual ──────────────────────────
        $filtrosAE2 = ['anno' => $anno, 'mes' => $mes];
        if ($entidadId !== null) {
            $filtrosAE2['entidad'] = $entidadId;
        }
        $ae2Data   = $ae2Repo->findFilteredAndTotalized($filtrosAE2);
        $t         = $ae2Data['totales'];

        $aprobada     = (int)($t['totalPlantillaAprobada']   ?? 0);
        $cubierta     = (int)($t['totalPlantillaCubierta']   ?? 0);
        $vacantes     = max(0, $aprobada - $cubierta);
        $coberturaPct = $aprobada > 0 ? round(($cubierta / $aprobada) * 100, 1) : 0;
        $profTC       = (int)($t['profesoresTiempoCompleto']  ?? 0);
        $contratos    = (int)($t['totalGeneralContratos']      ?? 0);

        // ── Distribución de contratos para donut ────────────────────
        $distribContratos = json_encode([
            ['name' => 'Prof. Tiempo Completo',   'y' => (int)($t['profesoresTiempoCompleto']                    ?? 0)],
            ['name' => 'Prof. Tiempo Determinado','y' => (int)($t['totalContratosProfesoresTiempoDeterminado']   ?? 0)],
            ['name' => 'No Docentes c/ Respaldo', 'y' => (int)($t['contratosNoDocentesConRespaldo']             ?? 0)],
            ['name' => 'No Docentes s/ Respaldo', 'y' => (int)($t['contratosNoDocentesSinRespaldo']             ?? 0)],
            ['name' => 'Estudiantes',              'y' => (int)($t['totalEstudiantesUniversidadContratados']     ?? 0)],
        ]);

        // ── Cobertura por entidad ────────────────────────────────────
        $coberturaEntidades = $ae2Repo->getCoberturaByEntidad($anno, $mes, $entidadId);
        $coberturaAprobada  = json_encode(array_map(function ($r) {
            return ['name' => $r['name'], 'y' => (int)$r['aprobada']];
        }, $coberturaEntidades));
        $coberturaCubierta  = json_encode(array_map(function ($r) {
            return ['name' => $r['name'], 'y' => (int)$r['cubierta']];
        }, $coberturaEntidades));
        $coberturaCategories = json_encode(array_column($coberturaEntidades, 'name'));

        // ── Tendencia mensual del año ────────────────────────────────
        $tendencia     = $ae2Repo->getTendenciaMensual($anno, $entidadId);
        $tendenciaCats = json_encode(array_map(function ($r) use ($mesesNombres) {
            return $mesesNombres[(int)$r['mes']] ?? 'Mes ' . $r['mes'];
        }, $tendencia));
        $tendenciaAprobada = json_encode(array_map(function ($r) { return (int)$r['aprobada']; }, $tendencia));
        $tendenciaCubierta = json_encode(array_map(function ($r) { return (int)$r['cubierta']; }, $tendencia));

        // ── Totales AE3 del período actual ──────────────────────────
        $ae3Totales  = $ae3Repo->getTotalesGlobales($anno, $mes, $entidadId);
        $ae3Cats     = $ae3Repo->getDistribucionCategorias($anno, $mes, $entidadId);

        $distribCategorias = json_encode([
            ['name' => 'Cuadros Docentes',   'y' => (int)($ae3Cats['docentes']        ?? 0)],
            ['name' => 'Prof. TC',           'y' => (int)($ae3Cats['prof_tc']          ?? 0)],
            ['name' => 'Técnicos',           'y' => (int)($ae3Cats['tecnicos']         ?? 0)],
            ['name' => 'Administrativos',    'y' => (int)($ae3Cats['administrativos']  ?? 0)],
            ['name' => 'Servicio',           'y' => (int)($ae3Cats['servicio']         ?? 0)],
            ['name' => 'Operarios',          'y' => (int)($ae3Cats['operarios']        ?? 0)],
        ]);

        $totalAE3     = (int)($ae3Totales['total_cubierta'] ?? 0);
        $totalFem     = (int)($ae3Totales['total_fem']      ?? 0);
        $totalJovenes = (int)($ae3Totales['total_jovenes']  ?? 0);
        $totalDr      = (int)($ae3Totales['total_dr']       ?? 0);
        $totalMsc     = (int)($ae3Totales['total_msc']      ?? 0);
        $otrosCalif   = max(0, $totalAE3 - $totalDr - $totalMsc);

        $distribCalificacion = json_encode([
            ['name' => 'Doctor (Dr.C)',  'y' => $totalDr],
            ['name' => 'Máster (MSc.)', 'y' => $totalMsc],
            ['name' => 'Otros',         'y' => $otrosCalif],
        ]);

        return $this->render('modules/rrhh/portada/index.html.twig', [
            // Período
            'periodo_label'       => $periodoLabel,
            'alcance'             => $alcance,
            'anno'                => $anno,
            'mes'                 => $mes,
            'meses'               => $mesesNombres,
            'periodos'            => $periodos,
            // KPIs AE2
            'aprobada'            => $aprobada,
            'cubierta'            => $cubierta,
            'vacantes'            => $vacantes,
            'cobertura_pct'       => $coberturaPct,
            'prof_tc'             => $profTC,
            'contratos'           => $contratos,
            // Gráficos AE2
            'distrib_contratos'        => $distribContratos,
            'cobertura_categories'     => $coberturaCategories,
            'cobertura_aprobada'       => $coberturaAprobada,
            'cobertura_cubierta'       => $coberturaCubierta,
            'tendencia_cats'           => $tendenciaCats,
            'tendencia_aprobada'       => $tendenciaAprobada,
            'tendencia_cubierta'       => $tendenciaCubierta,
            // KPIs AE3
            'total_personal'      => $totalAE3,
            'total_fem'           => $totalFem,
            'total_jovenes'       => $totalJovenes,
            'total_dr'            => $totalDr,
            'total_msc'           => $totalMsc,
            // Gráficos AE3
            'distrib_categorias'       => $distribCategorias,
            'distrib_calificacion'     => $distribCalificacion,
        ]);
    }

}
