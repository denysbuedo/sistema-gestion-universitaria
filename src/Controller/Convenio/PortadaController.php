<?php

namespace App\Controller\Convenio;

use App\Repository\Convenio\ConvenioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_CONVENIO')"))]
#[Route('/convenio/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_convenio_portada', methods: ['GET'])]
    public function index(ConvenioRepository $convenioRepository)
    {
        // Estadísticas generales de convenios
        $parametros['total_convenios'] = $convenioRepository->count([]);
        
        // Convenios por estado (vigentes, vencidos, próximos a vencer)
        $parametros['convenios_vigentes'] = count($convenioRepository->getConveniosVigentes());
        $parametros['convenios_vencidos'] = count($convenioRepository->getConveniosVencidos());
        $parametros['convenios_proximos_vencer'] = count($convenioRepository->getConveniosProximosVencer(90)); // Próximos 90 días
        
        // Convenios por tipo - obtener desde el método que retorna todos los tipos
        $tiposConvenios = $convenioRepository->getConveniosPorTipo();
        $parametros['convenios_bilaterales'] = 0;
        $parametros['convenios_multilaterales'] = 0;
        
        foreach ($tiposConvenios as $tipo) {
            if (stripos($tipo['name'], 'bilateral') !== false) {
                $parametros['convenios_bilaterales'] = $tipo['y'];
            } elseif (stripos($tipo['name'], 'multilateral') !== false) {
                $parametros['convenios_multilaterales'] = $tipo['y'];
            }
        }
        
        // Convenios por país
        $parametros['convenios_por_pais'] = json_encode($convenioRepository->getConveniosPorPais());
        
        // Convenios por año de firma
        $parametros['convenios_por_anio'] = json_encode($convenioRepository->getConveniosPorAnio());
        
        // Datos para gráficos
        $parametros['convenios_por_institucion_cubana'] = json_encode($convenioRepository->getConveniosPorInstitucionCubana());
        $parametros['convenios_por_institucion_extranjera'] = json_encode($convenioRepository->getConveniosPorInstitucionExtranjera());
        
        return $this->render('modules/convenio/portada/index.html.twig', $parametros);
    }


}
