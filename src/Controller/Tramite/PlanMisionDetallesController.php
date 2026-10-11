<?php

namespace App\Controller\Tramite;

use App\Entity\Personal\Persona;
use App\Entity\Postgrado\MiembrosComision;
use App\Entity\Tramite\PlanMision;
use App\Entity\Tramite\PlanMisionDetalles;
use App\Form\Tramite\PlanMisionType;
use App\Repository\Estructura\PaisRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Security\UserRepository;
use App\Repository\Tramite\PlanMisionDetallesRepository;
use App\Repository\Tramite\PlanMisionRepository;
use App\Services\HandlerFop;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PLAN_MISION_DETALLES')"))]
#[Route('/tramite/plan_mision_detalles')]
class PlanMisionDetallesController extends AbstractController
{

    /**
     * @param planMisionRepository $planMisionRepository
     * @return Response
     */
    #[Route('/', name: 'app_plan_mision_detalles_index', methods: ['GET'])]
    public function index(Request $request, PlanMisionRepository $planMisionRepository, PlanMisionDetallesRepository $planMisionDetallesRepository)
    {
        $planesMision = $planMisionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']);
        $last = $planesMision[count($planesMision) - 1]->getId();
        if (!$request->getSession()->has('filter_plan_mision')) {
            $request->getSession()->set('filter_plan_mision', $last);
        }

        return $this->render('modules/tramite/plan_mision_detalles/index.html.twig', [
            'registros' => $planesMision,
            'last' => $request->getSession()->get('filter_plan_mision'),
            'registrosAsignados' => $planMisionDetallesRepository->findBy(['planMision' => $request->getSession()->get('filter_plan_mision')]),
        ]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/cambio/filtro/plan_plan/{id}', name: 'app_cambio_filtro_plan')]
    public function cambiarFiltroPlan(Request $request, $id)
    {
        $request->getSession()->set('filter_plan_mision', $id);
        return $this->json(['status' => 200]);
    }

}
