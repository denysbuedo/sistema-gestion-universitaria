<?php

namespace App\Controller\Estructura;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_STRUCT")
 */
#[Route('/estructura/portada')]
class PortadaController extends AbstractController
{

    /**
     * @param AreaResultadoClaveRepository $areaResultadoRepository
     * @return Response
     */
    #[Route('', name: 'app_estructura_portada', methods: ['GET'])]
    public function index()
    {
        return $this->render('modules/estructura/portada/index.html.twig');

    }


}
