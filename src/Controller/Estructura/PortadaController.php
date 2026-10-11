<?php

namespace App\Controller\Estructura;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_STRUCT')"))]
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
