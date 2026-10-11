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
        
        // Actividad de Hoy (Trazas)
        $todayStart = (new \DateTime())->setTime(0, 0, 0);
        $todayEnd = (new \DateTime())->setTime(23, 59, 59);
        $qbTrazaToday = $trazaRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.creado >= :start')
            ->andWhere('t.creado <= :end')
            ->setParameter('start', $todayStart)
            ->setParameter('end', $todayEnd);
        $actividadHoy = (int)$qbTrazaToday->getQuery()->getSingleScalarResult();

        // 2. Gráfico de Dona: Usuarios por Rol (Estimado simplificado)
        $roles = $rolRepo->findAll();
        $rolesDistribution = [];
        // Dado que un usuario puede tener múltiples roles, contamos cuántos usuarios están asignados a cada rol
        foreach ($roles as $rol) {
            $count = (int)$userRepo->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->join('u.userRoles', 'r')
                ->where('r.id = :rolId')
                ->setParameter('rolId', $rol->getId())
                ->getQuery()
                ->getSingleScalarResult();
            
            if ($count > 0) {
                $rolesDistribution[] = [
                    'label' => $rol->getNombre(),
                    'value' => $count
                ];
            }
        }

        // 3. Gráfico de Tendencia: Actividad del sistema (últimos 7 días)
        $trendData = [];
        $trendLabels = [];
        
        $mesesNombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = new \DateTime("-$i days");
            $start = (clone $date)->setTime(0, 0, 0);
            $end = (clone $date)->setTime(23, 59, 59);
            
            $trendLabels[] = $date->format('d') . ' ' . $mesesNombres[(int)$date->format('n') - 1];
            
            $qb = $trazaRepo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.creado >= :start')
                ->andWhere('t.creado <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
            
            $trendData[] = (int)$qb->getQuery()->getSingleScalarResult();
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
