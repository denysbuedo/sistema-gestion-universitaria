<?php

namespace App\Controller\Institucion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Repository\Institucion\InstitucionRepository;
use App\Repository\Institucion\TipoInstitucionRepository;
use App\Repository\Institucion\CategoriaAcreditacionRepository;
use App\Repository\Institucion\InstitucionRevistaCientificaRepository;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_INSTIT')"))]
#[Route('/institucion/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_institucion_portada', methods: ['GET'])]
    public function index(
        InstitucionRepository $institucionRepo,
        TipoInstitucionRepository $tipoInstRepo,
        CategoriaAcreditacionRepository $categoriaRepo,
        InstitucionRevistaCientificaRepository $revistaRepo
    ) {
        // 1. KPIs Generales
        $totalInstituciones = $institucionRepo->count([]);
        $totalTipos = $tipoInstRepo->count([]);
        $totalCategorias = $categoriaRepo->count([]);
        $totalRevistas = $revistaRepo->count([]);

        // 2. Gráfico de Dona: Instituciones por Tipo
        $tipos = $tipoInstRepo->findAll();
        $tipoDistribution = [];
        foreach ($tipos as $tipo) {
            $count = $institucionRepo->count(['tipoInstitucion' => $tipo]);
            if ($count > 0) {
                $tipoDistribution[] = [
                    'label' => $tipo->getNombre(),
                    'value' => $count
                ];
            }
        }

        // 3. Gráfico de Barras/Dona: Instituciones por Categoría de Acreditación
        $categorias = $categoriaRepo->findAll();
        $categoriaDistribution = [];
        // También contamos aquellas sin categoría si existen
        $countSinCategoria = $institucionRepo->count(['categoriaAcreditacion' => null]);
        if ($countSinCategoria > 0) {
            $categoriaDistribution[] = [
                'label' => 'Sin Categoría / No Definido',
                'value' => $countSinCategoria
            ];
        }

        foreach ($categorias as $cat) {
            $count = $institucionRepo->count(['categoriaAcreditacion' => $cat]);
            if ($count > 0) {
                $categoriaDistribution[] = [
                    'label' => $cat->getNombre(),
                    'value' => $count
                ];
            }
        }

        // 4. Actividad Reciente (Últimas 10 instituciones agregadas, ordenando por id asumiendo autoincremental, 
        // o si tiene campo creado se puede usar, pero el id es seguro para el orden de inserción si no hay un campo específico seguro o se usa id)
        $actividadReciente = $institucionRepo->findBy([], ['id' => 'DESC'], 10);

        return $this->render('modules/institucion/portada/index.html.twig', [
            'kpis' => [
                'total' => $totalInstituciones,
                'tipos' => $totalTipos,
                'categorias' => $totalCategorias,
                'revistas' => $totalRevistas
            ],
            'tipo_dist' => json_encode($tipoDistribution),
            'cat_dist' => json_encode($categoriaDistribution),
            'actividad_reciente' => $actividadReciente
        ]);
    }


}
