<?php

namespace App\Controller\Evaluacion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_EVALUACION')"))]
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
