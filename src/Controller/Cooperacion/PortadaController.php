<?php

namespace App\Controller\Cooperacion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_INSTIT')"))]
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
