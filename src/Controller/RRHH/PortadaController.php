<?php

namespace App\Controller\RRHH;

use App\Repository\RRHH\AE2Repository;
use App\Repository\RRHH\AE3Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/rrhh/portada")
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_RRHH")
 */
class PortadaController extends AbstractController
{

    /**
     * @Route("", name="app_rrhh_portada", methods={"GET"})
     * @return Response
     */
    public function index(AE2Repository $ae2Repo, AE3Repository $ae3Repo)
    {
        $mesesNombres = [
            1 => 'Enero', 2 => 'Febrero',  3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo',     6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto',   9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        // ── Período más reciente con datos AE2 ──────────────────────
        $latestAE2 = $ae2Repo->getLatestPeriod();
        $anno      = $latestAE2 ? (int)$latestAE2['anno'] : (int)date('Y');
        $mes       = $latestAE2 ? (int)$latestAE2['mes']  : (int)date('n');
        $periodoLabel = $latestAE2
            ? ($mesesNombres[$mes] . ' ' . $anno)
            : 'Sin datos';

        // ── Totales AE2 del período actual ──────────────────────────
        $ae2Data   = $ae2Repo->findFilteredAndTotalized(['anno' => $anno, 'mes' => $mes]);
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
        $coberturaEntidades = $ae2Repo->getCoberturaByEntidad($anno, $mes);
        $coberturaAprobada  = json_encode(array_map(function ($r) {
            return ['name' => $r['name'], 'y' => (int)$r['aprobada']];
        }, $coberturaEntidades));
        $coberturaCubierta  = json_encode(array_map(function ($r) {
            return ['name' => $r['name'], 'y' => (int)$r['cubierta']];
        }, $coberturaEntidades));
        $coberturaCategories = json_encode(array_column($coberturaEntidades, 'name'));

        // ── Tendencia mensual del año ────────────────────────────────
        $tendencia     = $ae2Repo->getTendenciaMensual($anno);
        $tendenciaCats = json_encode(array_map(function ($r) use ($mesesNombres) {
            return $mesesNombres[(int)$r['mes']] ?? 'Mes ' . $r['mes'];
        }, $tendencia));
        $tendenciaAprobada = json_encode(array_map(function ($r) { return (int)$r['aprobada']; }, $tendencia));
        $tendenciaCubierta = json_encode(array_map(function ($r) { return (int)$r['cubierta']; }, $tendencia));

        // ── Totales AE3 del período actual ──────────────────────────
        $latestAE3   = $ae3Repo->getLatestPeriod();
        $annoAE3     = $latestAE3 ? (int)$latestAE3['anno'] : $anno;
        $mesAE3      = $latestAE3 ? (int)$latestAE3['mes']  : $mes;

        $ae3Totales  = $ae3Repo->getTotalesGlobales($annoAE3, $mesAE3);
        $ae3Cats     = $ae3Repo->getDistribucionCategorias($annoAE3, $mesAE3);

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
            'anno'                => $anno,
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
