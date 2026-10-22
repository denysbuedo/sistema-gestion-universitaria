<?php

namespace App\Controller\Traza;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Repository\Traza\TrazaRepository;
use App\Repository\Traza\TipoTrazaRepository;
use App\Repository\Traza\AccionRepository;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_TRAZAS')"))]
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
        
        $trendStart = (new \DateTimeImmutable('-6 days'))->setTime(0, 0);
        $trendEnd = (new \DateTimeImmutable('tomorrow'))->setTime(0, 0);
        $countsByDay = $trazaRepo->countByDay($trendStart, $trendEnd);
        $trazasHoy = (int)($countsByDay[(new \DateTimeImmutable())->format('Y-m-d')] ?? 0);

        $totalTipos = $tipoRepo->count([]);
        $totalAcciones = $accionRepo->count([]);

        // 2. Gráfico de Dona: Distribución por Tipo de Traza
        $tipoDist = $trazaRepo->countGroupedByType();

        // 3. Gráfico de Tendencia: Actividad de los últimos 7 días
        $trendData = [];
        $trendLabels = [];
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = new \DateTime("-$i days");
            $trendLabels[] = $date->format('d') . ' ' . $meses[(int)$date->format('n') - 1];
            $trendData[] = (int)($countsByDay[$date->format('Y-m-d')] ?? 0);
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
