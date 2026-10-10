<?php

namespace App\Controller\Personal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Repository\Personal\PersonaRepository;
use App\Repository\Personal\CategoriaDocenteRepository;
use App\Repository\Personal\GradoAcademicoRepository;
use App\Repository\Personal\CategoriaInvestigativaRepository;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_PERSONAL")
 */
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
        $catDocentes = $docenteRepo->findAll();
        $docenteDist = [];
        $sinDocenteCount = $personaRepo->count(['categoriaDocente' => null]);
        if ($sinDocenteCount > 0) {
            $docenteDist[] = ['label' => 'Sin Categoría Docente', 'value' => $sinDocenteCount];
        }

        foreach ($catDocentes as $cat) {
            $count = $personaRepo->count(['categoriaDocente' => $cat]);
            if ($count > 0) {
                $docenteDist[] = ['label' => $cat->getNombre(), 'value' => $count];
            }
        }

        // 3. Gráfico de Barras: Personal por Grado Académico
        $grados = $gradoRepo->findAll();
        $gradoDist = [];
        $sinGradoCount = $personaRepo->count(['gradoAcademico' => null]);
        if ($sinGradoCount > 0) {
            $gradoDist[] = ['label' => 'Sin Grado / Bachiller', 'value' => $sinGradoCount];
        }

        foreach ($grados as $grado) {
            $count = $personaRepo->count(['gradoAcademico' => $grado]);
            if ($count > 0) {
                $gradoDist[] = ['label' => $grado->getNombre(), 'value' => $count];
            }
        }

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
