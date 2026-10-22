<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\Security\UserRepository;
use App\Repository\Security\RolRepository;
use App\Repository\Security\ModuloRepository;
use App\Repository\Traza\TrazaRepository;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_ADMIN')"))]
#[Route('/administracion/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_administracion_portada', methods: ['GET'])]
    public function index(
        UserRepository $userRepo,
        RolRepository $rolRepo,
        ModuloRepository $moduloRepo,
        TrazaRepository $trazaRepo
    ) {
        // 1. KPIs Generales
        $totalUsuarios = $userRepo->count([]);
        $totalRoles = $rolRepo->count([]);
        $totalModulos = $moduloRepo->count([]);
        
        // 2. Gráfico de Dona: Usuarios por Rol (Estimado simplificado)
        $rolesDistribution = $userRepo->countGroupedByRole();

        // 3. Gráfico de Tendencia: Actividad del sistema (últimos 7 días)
        $trendData = [];
        $trendLabels = [];
        
        $mesesNombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        $trendStart = (new \DateTimeImmutable('-6 days'))->setTime(0, 0);
        $trendEnd = (new \DateTimeImmutable('tomorrow'))->setTime(0, 0);
        $countsByDay = $trazaRepo->countByDay($trendStart, $trendEnd);
        $actividadHoy = (int)($countsByDay[(new \DateTimeImmutable())->format('Y-m-d')] ?? 0);
        for ($i = 6; $i >= 0; $i--) {
            $date = new \DateTime("-$i days");
            $trendLabels[] = $date->format('d') . ' ' . $mesesNombres[(int)$date->format('n') - 1];
            $trendData[] = (int)($countsByDay[$date->format('Y-m-d')] ?? 0);
        }

        // 4. Actividad Reciente (Últimas 10 acciones)
        $actividadReciente = $trazaRepo->findBy([], ['creado' => 'DESC'], 10);

        return $this->render('modules/admin/portada/index.html.twig', [
            'kpis' => [
                'usuarios' => $totalUsuarios,
                'roles' => $totalRoles,
                'modulos' => $totalModulos,
                'actividad_hoy' => $actividadHoy
            ],
            'roles_dist' => json_encode($rolesDistribution),
            'trend' => [
                'labels' => json_encode($trendLabels),
                'data' => json_encode($trendData)
            ],
            'actividad_reciente' => $actividadReciente
        ]);
    }
}
