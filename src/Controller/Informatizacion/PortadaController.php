<?php

namespace App\Controller\Informatizacion;

use App\Repository\Informatizacion\CentroDatoVirtualRepository;
use App\Repository\Informatizacion\EnlaceConectividadRepository;
use App\Repository\Informatizacion\LineaCelularRepository;
use App\Repository\Informatizacion\NautaHogarRepository;
use App\Repository\Informatizacion\ServicioRepository;
use App\Repository\Informatizacion\SistemaInformaticoRepository;
use App\Repository\Informatizacion\TelefonoCelularRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_INFORMATIZACION')"))]
#[Route('/informatizacion/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_informatizacion_portada', methods: ['GET'])]
    public function index(
        SistemaInformaticoRepository $sistemaRepo,
        ServicioRepository           $servicioRepo,
        LineaCelularRepository       $lineaRepo,
        TelefonoCelularRepository    $telefonoRepo,
        NautaHogarRepository         $nautaRepo,
        CentroDatoVirtualRepository  $centroRepo,
        EnlaceConectividadRepository $enlaceRepo
    ) {
        $por_tipo_sistema   = $sistemaRepo->getCountByTipoSistema();
        $por_visibilidad    = $sistemaRepo->getCountByVisibilidad();
        $por_publico        = $servicioRepo->getCountByPublicoObjetivo();
        $por_marca          = $telefonoRepo->getCountByMarca();
        $por_so_telefono    = $telefonoRepo->getCountBySistemaOperativo();
        $por_tipo_enlace    = $enlaceRepo->getCountByTipoConectividad();

        $parametros = [
            'total_sistemas'    => $sistemaRepo->count([]),
            'total_servicios'   => $servicioRepo->count([]),
            'total_lineas'      => $lineaRepo->count([]),
            'total_telefonos'   => $telefonoRepo->count([]),
            'total_nauta'       => $nautaRepo->count([]),
            'total_centros'     => $centroRepo->count([]),
            'total_enlaces'     => $enlaceRepo->count([]),

            'por_tipo_sistema'       => $por_tipo_sistema,
            'por_tipo_sistema_json'  => json_encode($por_tipo_sistema),
            'por_visibilidad_json'   => json_encode($por_visibilidad),
            'por_publico_json'       => json_encode($por_publico),
            'por_marca_json'         => json_encode($por_marca),
            'por_so_telefono'        => $por_so_telefono,
            'por_so_telefono_json'   => json_encode($por_so_telefono),
            'por_tipo_enlace_json'   => json_encode($por_tipo_enlace),
        ];

        return $this->render('modules/informatizacion/portada/index.html.twig', $parametros);
    }

}
