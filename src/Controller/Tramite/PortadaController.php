<?php

namespace App\Controller\Tramite;

use App\Repository\Tramite\DocumentoSalidaRepository;
use App\Repository\Tramite\EstadoFichaSalidaRepository;
use App\Repository\Tramite\FichaSalidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_TRAMITES", "ROLE_HOME_ECONOMIA")
 */
#[Route('/tramite/portada')]
class PortadaController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('', name: 'app_tramite_portada', methods: ['GET'])]
    public function index(
        FichaSalidaRepository $fichaRepo,
        DocumentoSalidaRepository $docRepo,
        EstadoFichaSalidaRepository $estadoRepo
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
        $estadosFicha = $estadoRepo->findBy(['documentoSalida' => false, 'activo' => true]);
        $statusDistribution = [];
        foreach ($estadosFicha as $est) {
            $count = $fichaRepo->count(['estadoFichaSalida' => $est->getId()]);
            if ($count > 0) {
                $statusDistribution[] = [
                    'label' => $est->getNombre(),
                    'value' => $count
                ];
            }
        }

        // 3. Tendencia de últimos 6 meses (Fichas creadas)
        $trendData = [];
        $monthsLabels = [];
        $fichasTrend = [];
        
        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = new \DateTime("first day of -$i month");
            $start = (clone $date)->setTime(0, 0, 0);
            $end = (clone $date)->modify('last day of this month')->setTime(23, 59, 59);
            
            $monthNum = (int)$date->format('n');
            $monthsLabels[] = $mesesNombres[$monthNum];
            
            $qb = $fichaRepo->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->where('f.creado >= :start')
                ->andWhere('f.creado <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
            
            $fichasTrend[] = (int)$qb->getQuery()->getSingleScalarResult();
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
