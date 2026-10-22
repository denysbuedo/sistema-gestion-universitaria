<?php

namespace App\Controller\Tramite;

use App\Repository\Tramite\DocumentoSalidaRepository;
use App\Repository\Tramite\FichaSalidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_TRAMITES') or is_granted('ROLE_HOME_ECONOMIA')"))]
#[Route('/tramite/portada')]
class PortadaController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('', name: 'app_tramite_portada', methods: ['GET'])]
    public function index(
        FichaSalidaRepository $fichaRepo,
        DocumentoSalidaRepository $docRepo
    ): Response {
        // 1. Estadísticas Generales (KPIs)
        $totalFichas = $fichaRepo->count([]);
        $totalDocSalida = $docRepo->count([]);
        
        // Fichas por estado (IDs típicos basados en .env y lógica previa)
        // Asumiendo: 1: Creada, 4: Revisión, 2: Firmada/Aprobada
        $fichasNuevas = $fichaRepo->count(['estadoFichaSalida' => 1]);
        $fichasRevision = $fichaRepo->count(['estadoFichaSalida' => 4]);
        
        // Documentos de Salida por estado
        // Asumiendo: 7: En trámite, 6/10: Finalizada
        $docsEnTramite = $docRepo->count(['estadoDocumentoSalida' => 7]);
        $docsFinalizados = $docRepo->count(['estadoDocumentoSalida' => 6]);

        // 2. Datos para el Gráfico de Dona: Distribución de Fichas
        $statusDistribution = $fichaRepo->countGroupedByStatus();

        // 3. Tendencia de últimos 6 meses (Fichas creadas)
        $trendData = [];
        $monthsLabels = [];
        $fichasTrend = [];
        
        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $trendStart = (new \DateTimeImmutable('first day of -5 months'))->setTime(0, 0);
        $trendEnd = (new \DateTimeImmutable('first day of next month'))->setTime(0, 0);
        $countsByMonth = $fichaRepo->countByMonth($trendStart, $trendEnd);
        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime("first day of -$i month");
            $monthNum = (int)$date->format('n');
            $monthsLabels[] = $mesesNombres[$monthNum];
            $fichasTrend[] = (int)($countsByMonth[$date->format('Y-m')] ?? 0);
        }

        return $this->render('modules/tramite/portada/index.html.twig', [
            'kpis' => [
                'total_fichas' => $totalFichas,
                'total_docs' => $totalDocSalida,
                'nuevas' => $fichasNuevas,
                'revision' => $fichasRevision,
                'tramite' => $docsEnTramite,
                'finalizados' => $docsFinalizados
            ],
            'status_dist' => json_encode($statusDistribution),
            'trend' => [
                'labels' => json_encode($monthsLabels),
                'data' => json_encode($fichasTrend)
            ]
        ]);
    }
}
