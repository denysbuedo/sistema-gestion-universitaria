<?php

namespace App\Controller\Traza;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\Traza\TrazaRepository;
use App\Repository\Traza\TipoTrazaRepository;
use App\Repository\Traza\AccionRepository;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_TRAZAS")
 */
#[Route('/traza/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_traza_portada', methods: ['GET'])]
    public function index(
        TrazaRepository $trazaRepo,
        TipoTrazaRepository $tipoRepo,
        AccionRepository $accionRepo
    ) {
        // 1. KPIs Generales
        $totalTrazas = $trazaRepo->count([]);
        
        $todayStart = (new \DateTime())->setTime(0, 0, 0);
        $todayEnd = (new \DateTime())->setTime(23, 59, 59);
        $qbHoy = $trazaRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.creado >= :start')
            ->andWhere('t.creado <= :end')
            ->setParameter('start', $todayStart)
            ->setParameter('end', $todayEnd);
        $trazasHoy = (int)$qbHoy->getQuery()->getSingleScalarResult();

        $totalTipos = $tipoRepo->count([]);
        $totalAcciones = $accionRepo->count([]);

        // 2. Gráfico de Dona: Distribución por Tipo de Traza
        $tipos = $tipoRepo->findAll();
        $tipoDist = [];
        foreach ($tipos as $tipo) {
            $count = $trazaRepo->count(['tipoTraza' => $tipo]);
            if ($count > 0) {
                $tipoDist[] = [
                    'label' => $tipo->getNombre(),
                    'value' => $count
                ];
            }
        }

        // 3. Gráfico de Tendencia: Actividad de los últimos 7 días
        $trendData = [];
        $trendLabels = [];
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = new \DateTime("-$i days");
            $start = (clone $date)->setTime(0, 0, 0);
            $end = (clone $date)->setTime(23, 59, 59);
            
            $trendLabels[] = $date->format('d') . ' ' . $meses[(int)$date->format('n') - 1];
            
            $qb = $trazaRepo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.creado >= :start')
                ->andWhere('t.creado <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
            
            $trendData[] = (int)$qb->getQuery()->getSingleScalarResult();
        }

        // 4. Actividad Reciente
        $actividadReciente = $trazaRepo->findBy([], ['creado' => 'DESC'], 10);

        return $this->render('modules/traza/portada/index.html.twig', [
            'kpis' => [
                'total' => $totalTrazas,
                'hoy' => $trazasHoy,
                'tipos' => $totalTipos,
                'acciones' => $totalAcciones
            ],
            'tipo_dist' => json_encode($tipoDist),
            'trend' => [
                'labels' => json_encode($trendLabels),
                'data' => json_encode($trendData)
            ],
            'actividad_reciente' => $actividadReciente
        ]);
    }


}
