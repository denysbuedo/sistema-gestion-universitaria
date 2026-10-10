<?php

namespace App\Controller\Cooperacion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_INSTIT")
 */
#[Route('/cooperacion/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_cooperacion_portada', methods: ['GET'])]
    public function index()
    {
        return $this->render('modules/cooperacion/portada/index.html.twig');

    }


}
