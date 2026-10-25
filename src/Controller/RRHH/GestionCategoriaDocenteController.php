<?php

namespace App\Controller\RRHH;

use App\Repository\Personal\CategoriaDocenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_GEST_CAT_DOC')"))]
#[Route('/rrhh/gestion-categorias-docentes')]
final class GestionCategoriaDocenteController extends AbstractController
{
    #[Route('/', name: 'app_rrhh_gestion_categoria_docente_index', methods: ['GET'])]
    public function index(CategoriaDocenteRepository $repository): Response
    {
        $registros = $repository->findBy(['activo' => true], ['orden' => 'ASC', 'nombre' => 'ASC']);
        $resumen = ['UNIVERSIDAD' => 0, 'MES' => 0];
        foreach ($registros as $registro) {
            $resumen[$registro->isRequiereAprobacionMes() ? 'MES' : 'UNIVERSIDAD']++;
        }

        return $this->render('modules/rrhh/gestion_categoria_docente/index.html.twig', [
            'registros' => $registros,
            'resumen' => $resumen,
        ]);
    }
}
