<?php

namespace App\Controller\Economia;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_HOME_ECONOMIA")
 */
#[Route('/economia/portada')]
class PortadaController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('', name: 'app_economia_portada', methods: ['GET'])]
    public function index()
    {
        return $this->render('modules/economia/portada/index.html.twig');

    }


}
