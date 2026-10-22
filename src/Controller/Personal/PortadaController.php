<?php

namespace App\Controller\Personal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Repository\Personal\PersonaRepository;
use App\Repository\Personal\CategoriaDocenteRepository;
use App\Repository\Personal\GradoAcademicoRepository;
use App\Repository\Personal\CategoriaInvestigativaRepository;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_PERSONAL')"))]
#[Route('/personal/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_personal_portada', methods: ['GET'])]
    public function index(
        PersonaRepository $personaRepo,
        CategoriaDocenteRepository $docenteRepo,
        GradoAcademicoRepository $gradoRepo,
        CategoriaInvestigativaRepository $invRepo
    ) {
        // 1. KPIs Generales
        $totalPersonas = $personaRepo->count([]);
        $totalDocentes = $docenteRepo->count([]);
        $totalGrados = $gradoRepo->count([]);
        $totalInvestigativas = $invRepo->count([]);

        // 2. Gráfico de Dona: Personal por Categoría Docente
        $docenteDist = $personaRepo->countGroupedBy('categoriaDocente');

        // 3. Gráfico de Barras: Personal por Grado Académico
        $gradoDist = $personaRepo->countGroupedBy('gradoAcademico');

        // 4. Últimos Registros
        $actividadReciente = $personaRepo->findBy([], ['id' => 'DESC'], 10);

        return $this->render('modules/personal/portada/index.html.twig', [
            'kpis' => [
                'personal' => $totalPersonas,
                'cat_docentes' => $totalDocentes,
                'grados' => $totalGrados,
                'cat_inv' => $totalInvestigativas
            ],
            'docente_dist' => json_encode($docenteDist),
            'grado_dist' => json_encode($gradoDist),
            'actividad_reciente' => $actividadReciente
        ]);
    }


}
