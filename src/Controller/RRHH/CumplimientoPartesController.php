<?php

namespace App\Controller\RRHH;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_REPORTE_AE2_CONSOLIDADO') or is_granted('ROLE_RRHH_REPORTE_AE3_CONSOLIDADO')"))]
final class CumplimientoPartesController extends AbstractController
{
    #[Route('/rrhh/reportes/cumplimiento-partes', name: 'app_rrhh_reporte_cumplimiento_partes', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $anno = $request->query->getInt('anno', (int) date('Y'));
        if ($anno < 2000 || $anno > (int) date('Y') + 1) {
            $anno = (int) date('Y');
        }

        $connection = $entityManager->getConnection();
        $entidades = $connection->fetchAllAssociative(<<<'SQL'
            SELECT id, siglas, nombre
            FROM estructura.tbd_estructura
            WHERE activo = true
              AND es_entidad = true
              AND categoria_estructura_id IN (5, 6, 8, 24)
            ORDER BY nombre
            SQL);
        $ae2 = $connection->fetchAllAssociative(
            'SELECT entidad_id, mes FROM rrhh.tbd_ae2 WHERE anno = :anno ORDER BY mes',
            ['anno' => $anno]
        );
        $ae3 = $connection->fetchAllAssociative(
            'SELECT entidad_id, mes FROM rrhh.tbd_ae3 WHERE anno = :anno AND mes IN (3, 6, 9, 12) ORDER BY mes',
            ['anno' => $anno]
        );

        $enviosAE2 = [];
        foreach ($ae2 as $envio) {
            $enviosAE2[(int) $envio['entidad_id']][(int) $envio['mes']] = true;
        }
        $enviosAE3 = [];
        foreach ($ae3 as $envio) {
            $enviosAE3[(int) $envio['entidad_id']][(int) $envio['mes']] = true;
        }

        return $this->render('modules/rrhh/reporte/cumplimiento/index.html.twig', [
            'anno' => $anno,
            'annos' => range((int) date('Y'), (int) date('Y') - 8),
            'entidades' => $entidades,
            'enviosAE2' => $enviosAE2,
            'enviosAE3' => $enviosAE3,
        ]);
    }
}
