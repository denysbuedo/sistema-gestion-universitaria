<?php

namespace App\Controller\RRHH;

use App\Entity\RRHH\AE2;
use App\Entity\RRHH\CategoriaDocente;
use App\Entity\Security\User;
use App\Form\RRHH\AE2Type;
use App\Form\RRHH\CategoriaDocenteType;
use App\Repository\Estructura\EstructuraRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\RRHH\AE2Repository;
use App\Repository\RRHH\CategoriaDocenteRepository;
use App\Repository\Security\UserRepository;
use App\Services\Utils;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\String\u;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_REPORTE_AE2_CONSOLIDADO')"))]
#[Route('/rrhh/ae2/consolidado')]
class AE2ConsolidadoController extends AbstractController
{

    /**
     * @param AE2Repository $AE2Repository
     * @return Response
     */
    #[Route('/', name: 'app_rrhh_reporte_ae2_consolidado_index', methods: ['GET', 'POST'])]
    public function index(Request $request, AE2Repository $AE2Repository, Utils $utils)
    {
        $allPost = $request->request->all();

        if (isset($allPost['mes']) && !empty($allPost['mes'])) {
            $filMes = $allPost['mes'];
            $request->getSession()->set('ae2_mes', $filMes);
        } else {
            if (!$request->getSession()->has('ae2_mes')) {
                $filMes = date('n');
                $request->getSession()->set('ae2_mes', $filMes);
            }

        }
        if (isset($allPost['anno']) && !empty($allPost['anno'])) {
            $filMes = $allPost['anno'];
            $request->getSession()->set('ae2_anno', $filMes);
        } else {
            if (!$request->getSession()->has('ae2_anno')) {
                $filAnno = date('Y');
                $request->getSession()->set('ae2_anno', $filAnno);
            }
        }
        $datosEstructuraUsuarioAutenticado = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $idEntidad = $datosEstructuraUsuarioAutenticado['id_entidad'] ?? null;
        $isAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        if ($isAdminRRHH) {
            $idEntidad = null;
        }

        $meses = $utils->getMesesNombres();
        $response = $this->render('modules/rrhh/reporte/ae2/consolidado/index.html.twig', [
            'registros' => $AE2Repository->findDistinctEntidades($idEntidad, $request->getSession()->get('ae2_mes'), $request->getSession()->get('ae2_anno')),
            'fil_mes' => $request->getSession()->get('ae2_mes'),
            'fil_anno' => $request->getSession()->get('ae2_anno'),
            'meses' => $meses,
            'text_fil' => "Período: " . ($meses[$request->getSession()->get('ae2_mes')] ?? '') . " de " . $request->getSession()->get('ae2_anno'),
        ]);
        return $response;
    }

    /**
     * @param $id
     * @param AE2Repository $ae2Repository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_rrhh_reporte_ae2_consolidado_detail', methods: ['GET', 'POST'])]
    public function detail($id, AE2Repository $ae2Repository)
    {
        $temp = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $meses = [];
        $ae2 = $ae2Repository->findBy(['entidad' => $id], ['anno' => 'DESC', 'mes' => 'ASC']);
        $annos = [];
        foreach ($ae2 ?? [] as $item) {
            $meses[$item->getMes()] = $temp[$item->getMes()];
            $annos[$item->getAnno()] = $item->getAnno();
        }
        return $this->render('modules/rrhh/reporte/ae2/consolidado/detail.html.twig', [
            'item' => $ae2,
            'mesesB' => $meses,
            'annos' => array_values($annos),
            'annoSeleccionado' => $annos ? max($annos) : null,
        ]);
    }

}
