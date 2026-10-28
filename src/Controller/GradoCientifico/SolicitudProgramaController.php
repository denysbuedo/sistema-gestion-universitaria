<?php

namespace App\Controller\GradoCientifico;

use App\Repository\Postgrado\SolicitudProgramaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_GRADO_CIENTIFICO') or is_granted('ROLE_GEST_GRADO_CIENTIFICO')"))]
#[Route('/grado-cientifico/solicitudes')]
class SolicitudProgramaController extends AbstractController
{
    #[Route('/', name: 'app_grado_cientifico_solicitud_index', methods: ['GET'])]
    public function index(SolicitudProgramaRepository $repository): Response
    {
        return $this->render('modules/postgrado/solicitud_programa/index.html.twig', [
            'registros' => $repository->getSolicitudes(true),
            'academic_domain' => 'grado_cientifico',
        ]);
    }
}
