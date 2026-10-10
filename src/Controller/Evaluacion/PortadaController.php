<?php

namespace App\Controller\Evaluacion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_EVALUACION")
 */
#[Route('/evaluacion/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_evaluacion_portada', methods: ['GET'])]
    public function index()
    {
        return $this->render('modules/evaluacion/portada/index.html.twig');

    }


}
