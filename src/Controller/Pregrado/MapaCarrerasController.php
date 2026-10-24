<?php

namespace App\Controller\Pregrado;

use App\Entity\Institucion\Institucion;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionCumRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Services\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/pregrado/mapa_carreras')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PROGAPROB') or is_granted('ROLE_PREGRADO_MIS_PROGRAMAS')"))]
final class MapaCarrerasController extends AbstractController
{
    #[Route('/', name: 'app_pregrado_mapa_carreras_index', methods: ['GET'])]
    public function index(Request $request, SolicitudProgramaAcademicoInstitucionRepository $repository, SolicitudProgramaAcademicoInstitucionCumRepository $cumRepository, EntityManagerInterface $em, Utils $utils): Response
    {
        $global = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_GEST_PROGAPROB');
        if (!$global) {
            return $this->mapaInstitucional($request, $repository, $cumRepository, $em, $utils);
        }

        $asignaciones = $repository->getProgramasV3();
        $universidades = [];
        $programas = [];
        $matriz = [];
        foreach ($asignaciones as $fila) {
            $universidadId = (int) $fila['id_universidad'];
            $programaId = (int) $fila['id_programa_academico'];
            $universidades[$universidadId] = ['nombre' => $fila['nombre_universidad'], 'siglas' => $fila['siglas_universidad'] ?: $fila['nombre_universidad']];
            $programas[$programaId] = $fila['programa_academico'];
            $matriz[$programaId][$universidadId] = true;
        }
        uasort($universidades, static fn (array $a, array $b): int => strnatcasecmp($a['nombre'], $b['nombre']));
        asort($programas, SORT_NATURAL | SORT_FLAG_CASE);

        $universidadSeleccionada = $request->query->getInt('universidad');
        $programaSeleccionado = $request->query->getInt('programa');
        $carrerasUniversidad = $universidadSeleccionada > 0 ? array_values(array_filter($asignaciones, static fn (array $fila): bool => (int) $fila['id_universidad'] === $universidadSeleccionada)) : [];
        $universidadesCarrera = $programaSeleccionado > 0 ? array_values(array_filter($asignaciones, static fn (array $fila): bool => (int) $fila['id_programa_academico'] === $programaSeleccionado)) : [];

        return $this->render('modules/pregrado/mapa_carreras/index.html.twig', compact('universidades', 'programas', 'matriz', 'universidadSeleccionada', 'programaSeleccionado', 'carrerasUniversidad', 'universidadesCarrera') + ['global' => true]);
    }

    private function mapaInstitucional(Request $request, SolicitudProgramaAcademicoInstitucionRepository $repository, SolicitudProgramaAcademicoInstitucionCumRepository $cumRepository, EntityManagerInterface $em, Utils $utils): Response
    {
        $datos = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $institucion = ($datos['id_entidad'] ?? -1) > 0 ? $em->getRepository(Institucion::class)->findOneBy(['estructura' => $datos['id_entidad']]) : null;
        if (!$institucion) {
            throw $this->createAccessDeniedException('Su usuario no está asociado a una universidad.');
        }

        $sedes = ['central' => 'Sede central'];
        $filas = [];
        foreach ($repository->findBy(['institucion' => $institucion]) as $oferta) {
            $programa = $oferta->getSolicitudProgramaAcademico();
            if ($oferta->getModalidadesSedeCentral() !== []) {
                $filas[] = ['sedeId' => 'central', 'sede' => 'Sede central', 'programa' => $programa->getNombre(), 'modalidades' => $oferta->getModalidadesSedeCentral()];
            }
            $porCum = [];
            foreach ($cumRepository->findBy(['solicitudProgramaAcademicoInstitucion' => $oferta]) as $asignacion) {
                $cum = $asignacion->getInstitucionCum();
                $clave = 'cum_' . $cum->getId();
                $sedes[$clave] = $cum->getNombreFacultad();
                $porCum[$clave]['nombre'] = $cum->getNombreFacultad();
                $porCum[$clave]['modalidades'][] = $asignacion->getModalidad();
            }
            foreach ($porCum as $clave => $cum) {
                $filas[] = ['sedeId' => $clave, 'sede' => $cum['nombre'], 'programa' => $programa->getNombre(), 'modalidades' => array_values(array_unique($cum['modalidades']))];
            }
        }
        asort($sedes, SORT_NATURAL | SORT_FLAG_CASE);
        $sedeSeleccionada = (string) $request->query->get('sede', '');
        $filasSede = $sedeSeleccionada !== '' ? array_values(array_filter($filas, static fn (array $fila): bool => $fila['sedeId'] === $sedeSeleccionada)) : [];

        return $this->render('modules/pregrado/mapa_carreras/index.html.twig', compact('institucion', 'sedes', 'sedeSeleccionada', 'filasSede') + ['global' => false]);
    }
}
