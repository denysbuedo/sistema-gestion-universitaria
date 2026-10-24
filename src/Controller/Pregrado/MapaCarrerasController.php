<?php

namespace App\Controller\Pregrado;

use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/pregrado/mapa_carreras')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PROGAPROB') or is_granted('ROLE_PREGRADO_MIS_PROGRAMAS')"))]
final class MapaCarrerasController extends AbstractController
{
    #[Route('/', name: 'app_pregrado_mapa_carreras_index', methods: ['GET'])]
    public function index(SolicitudProgramaAcademicoInstitucionRepository $repository): Response
    {
        $asignaciones = $repository->getProgramasV3();
        $universidades = [];
        $programas = [];
        $porUniversidad = [];
        $porPrograma = [];
        $matriz = [];

        foreach ($asignaciones as $fila) {
            $universidadId = (int) $fila['id_universidad'];
            $programaId = (int) $fila['id_programa_academico'];
            $universidades[$universidadId] = $fila['nombre_universidad'];
            $programas[$programaId] = $fila['programa_academico'];
            $porUniversidad[$universidadId][] = $fila;
            $porPrograma[$programaId][] = $fila;
            $matriz[$programaId][$universidadId] = true;
        }
        asort($universidades, SORT_NATURAL | SORT_FLAG_CASE);
        asort($programas, SORT_NATURAL | SORT_FLAG_CASE);

        return $this->render('modules/pregrado/mapa_carreras/index.html.twig', [
            'universidades' => $universidades,
            'programas' => $programas,
            'porUniversidad' => $porUniversidad,
            'porPrograma' => $porPrograma,
            'matriz' => $matriz,
        ]);
    }
}
