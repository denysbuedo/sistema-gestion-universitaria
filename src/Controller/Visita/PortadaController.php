<?php

namespace App\Controller\Visita;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_INSTIT")
 */
#[Route('/visita/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_visita_portada', methods: ['GET'])]
    public function index()
    {
        return $this->render('modules/visita/portada/index.html.twig');

    }


}
