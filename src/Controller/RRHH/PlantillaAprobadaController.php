<?php

namespace App\Controller\RRHH;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_GEST_PLANTILLA')"))]
#[Route('/rrhh/plantilla-aprobada')]
final class PlantillaAprobadaController extends AbstractController
{
    #[Route('/', name: 'app_rrhh_plantilla_aprobada_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('actualizar_plantilla', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('La operación ha expirado.');
            }
            $entidadId = $request->request->getInt('entidad');
            $total = $request->request->getInt('total');
            if ($entidadId > 0 && $total >= 0) {
                $connection->executeStatement(<<<'SQL'
                    INSERT INTO rrhh.tbd_plantilla_aprobada (entidad_id, total, actualizado)
                    VALUES (:entidad, :total, CURRENT_TIMESTAMP)
                    ON CONFLICT (entidad_id) DO UPDATE SET total = EXCLUDED.total, actualizado = CURRENT_TIMESTAMP
                    SQL, ['entidad' => $entidadId, 'total' => $total]);
                $this->addFlash('success', 'La plantilla aprobada fue actualizada.');
            }
            return $this->redirectToRoute('app_rrhh_plantilla_aprobada_index');
        }

        $registros = $connection->fetchAllAssociative(<<<'SQL'
            SELECT e.id, e.siglas, e.nombre, COALESCE(p.total, 0) total, p.actualizado
            FROM estructura.tbd_estructura e
            LEFT JOIN rrhh.tbd_plantilla_aprobada p ON p.entidad_id = e.id
            WHERE e.activo = true AND e.es_entidad = true AND e.categoria_estructura_id IN (5,6,8,24)
            ORDER BY e.nombre
            SQL);
        return $this->render('modules/rrhh/plantilla_aprobada/index.html.twig', ['registros' => $registros]);
    }
}
