<?php

namespace App\Controller\Postgrado;

use App\Repository\Postgrado\SolicitudProgramaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/postgrado/portada")
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_POSTG")
 */
class PortadaController extends AbstractController
{

    /**
     * @Route("", name="app_postgrado_portada", methods={"GET"})
     * @return Response
     */
    public function index(SolicitudProgramaRepository $solicitudProgramaRepository)
    {
        $total_programas = count($solicitudProgramaRepository->getSolicitudes());
        $aprobados       = $solicitudProgramaRepository->countByEstado([2]);
        $rechazados      = $solicitudProgramaRepository->countByEstado([3]);
        $desactivados    = $solicitudProgramaRepository->countByEstado([6]);
        $extintos        = $solicitudProgramaRepository->countByEstado([8]);
        $en_proceso      = max(0, $total_programas - $aprobados - $rechazados - $desactivados - $extintos);

        $tipos_programa = $solicitudProgramaRepository->getProgramasPorTipoPrograma();
        $por_modalidad  = $solicitudProgramaRepository->getProgramasPorModalidad();

        $parametros = [
            'total_programas'            => $total_programas,
            'aprobados'                  => $aprobados,
            'rechazados'                 => $rechazados,
            'desactivados'               => $desactivados,
            'extintos'                   => $extintos,
            'en_proceso'                 => $en_proceso,
            'tipos_programa'             => $tipos_programa,
            'tipos_programa_json'        => json_encode($tipos_programa),
            'por_modalidad'              => $por_modalidad,
            'por_modalidad_json'         => json_encode($por_modalidad),
            'por_rama_ciencia'           => json_encode($solicitudProgramaRepository->getProgramasPorRamaCiencia()),
            'por_universidad'            => json_encode($solicitudProgramaRepository->getProgramasPorUniversidad()),
            'por_categoria_acreditacion' => json_encode($solicitudProgramaRepository->getProgramasPorCategoriaAcreditacion()),
        ];

        return $this->render('modules/postgrado/portada/index.html.twig', $parametros);
    }

}
