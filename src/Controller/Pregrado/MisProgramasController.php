<?php

namespace App\Controller\Pregrado;

use App\Entity\Estructura\Estructura;
use App\Entity\Institucion\Institucion;
use App\Entity\Institucion\InstitucionCum;
use App\Entity\NotificacionesUsuario;
use App\Entity\Pregrado\MiembrosComisionNacional;
use App\Entity\Pregrado\ProgramaAcademicoDesactivado;
use App\Entity\Pregrado\ProgramaAcademicoReabierto;
use App\Entity\Pregrado\ProgramaAcademicoReabiertoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use App\Entity\Pregrado\SolicitudProgramaAcademicoComisionNacional;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucionCum;
use App\Entity\Pregrado\SolicitudProgramaAcademicoPlanEstudio;
use App\Export\Personal\ExportListPersonaToPdf;
use App\Export\Pregrado\ExportListSolicitudProgramaAcademicoToPdf;
use App\Export\Pregrado\ExportListSolicitudUniversidadesAsignadasToPdf;
use App\Form\Pregrado\AprobarModificarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\AprobarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\ProgramaAcademicoDesactivadoType;
use App\Form\Pregrado\ProgramaAcademicoReabiertoType;
use App\Form\Pregrado\SolicitudProgramaComisionType;
use App\Form\Pregrado\SolicitudProgramaInstitucionType;
use App\Form\Pregrado\SolicitudProgramaPlanEstudioType;
use App\Repository\Institucion\CategoriaAcreditacionRepository;
use App\Repository\Institucion\InstitucionCumRepository;
use App\Repository\Institucion\InstitucionRepository;
use App\Repository\NotificacionesUsuarioRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Pregrado\EstadoProgramaAcademicoRepository;
use App\Repository\Pregrado\MiembrosComisionNacionalRepository;
use App\Repository\Pregrado\ModificacionPlanEstudioRepository;
use App\Repository\Pregrado\ProgramaAcademicoDesactivadoRepository;
use App\Repository\Pregrado\ProgramaAcademicoReabiertoInstitucionRepository;
use App\Repository\Pregrado\ProgramaAcademicoReabiertoRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoComisionNacionalRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionCumRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoPlanEstudioRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use App\Services\DoctrineHelper;
use App\Services\HandlerFop;
use App\Services\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/pregrado/mis_programas")
 * @IsGranted("ROLE_ADMIN", "ROLE_PREGRADO_MIS_PROGRAMAS")
 * @ORM\Entity
 * @ORM\Table(name="solicitud_programa_academico_aprobado_controller")
 */
class MisProgramasController extends AbstractController
{

    /**
     * @Route("/asignados", name="app_pregrado_mis_programas_asignados", methods={"GET"})
     * @return Response
     */
    public function misProgramasAsignados(Utils $utils, EntityManagerInterface $entityManager, SolicitudProgramaAcademicoInstitucionRepository $solicitudProgramaAcademicoInstitucionRepository)
    {
        // 1. Validar usuario autenticado
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Usuario no autenticado.');
        }
        $isAdmin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
        $datosEstructuraUsuarioAutenticado = $utils->obtenerDatosEstructuraUsuarioAutenticado($user->getId());
        $idEntidad = $datosEstructuraUsuarioAutenticado['id_entidad'] ?? null;

        // 2. Buscar la institución
        $institucionUsuarioAutenticado = $entityManager->getRepository(Institucion::class)->findOneBy(['estructura' => $idEntidad]);

        // 3. Validación compatible con PHP 7.4
        // Si la institución existe, sacamos el ID, si no, asignamos null
        $institucionId = ($institucionUsuarioAutenticado !== null) ? $institucionUsuarioAutenticado->getId() : null;

        $registros = [];
        if ($institucionId !== null) {
            $registros = $solicitudProgramaAcademicoInstitucionRepository->findBy(
                ['institucion' => $institucionId],
                ['id' => 'asc']
            );
        }

        return $this->render('modules/pregrado/mis_programas/programas_asignados.twig', [
            'registros' => $registros,
            'entidad' => $idEntidad ? $entityManager->getRepository(Estructura::class)->find($idEntidad) : null,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * @Route("/asignados_en_cum", name="app_pregrado_mis_programas_asignados_en_cum", methods={"GET"})
     * @return Response
     */
    public function misProgramasAsignadosEnCum(
        Utils                                              $utils,
        EntityManagerInterface                             $entityManager,
        SolicitudProgramaAcademicoInstitucionCumRepository $solicitudProgramaAcademicoInstitucionCumRepository
    )
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Usuario no autenticado.');
        }

        $datosEstructuraUsuarioAutenticado = $utils->obtenerDatosEstructuraUsuarioAutenticado($user->getId());
        $idEntidad = $datosEstructuraUsuarioAutenticado['id_entidad'] ?? null;

        // Buscar la institución
        $institucionUsuarioAutenticado = $entityManager->getRepository(Institucion::class)->findOneBy(['estructura' => $idEntidad]);
        $idInstitucion = ($institucionUsuarioAutenticado !== null) ? $institucionUsuarioAutenticado->getId() : null;
        $registros = [];
        if ($idInstitucion !== null) {
            $registros = $solicitudProgramaAcademicoInstitucionCumRepository->misProgramasAsignadosEnCum($idInstitucion);
        }
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());
        return $this->render('modules/pregrado/mis_programas/programas_asignados_en_cum.twig', [
            'registros' => $registros,
            'entidad' => $idEntidad ? $entityManager->getRepository(Estructura::class)->find($idEntidad) : null,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * @Route("/{id}/asignar_cum", name="app_pregrado_mis_programas_asignados_cum", methods={"GET", "POST"})
     * @param SolicitudProgramaAcademicoInstitucionRepository $solicitudProgramaAcademicoInstitucionRepository
     * @param SolicitudProgramaAcademico $solicitudProgramaAcademicoInstitucion
     * @return Response
     */
    public function asignarCum(Utils $utils, SolicitudProgramaAcademicoInstitucionCumRepository $solicitudProgramaAcademicoInstitucionCumRepository, EntityManagerInterface $entityManager, InstitucionCumRepository $institucionCumRepository, SolicitudProgramaAcademicoInstitucion $solicitudProgramaAcademicoInstitucion, SolicitudProgramaAcademicoInstitucionRepository $solicitudProgramaAcademicoInstitucionRepository)
    {
        $datosEstructuraUsuarioAutenticado = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $idEntidad = $datosEstructuraUsuarioAutenticado['id_entidad'] ?? null;
        try {
            $modalidades = [];
            if ($solicitudProgramaAcademicoInstitucion->getSolicitudProgramaAcademico()->getModalidadDiurno()) {
                $item['id'] = 'Diurno';
                $item['nombre'] = 'Diurno';
                $modalidades[] = $item;
            }
            if ($solicitudProgramaAcademicoInstitucion->getSolicitudProgramaAcademico()->getModalidadPorEncuentro()) {
                $item['id'] = 'Por encuentro';
                $item['nombre'] = 'Por encuentro';
                $modalidades[] = $item;
            }

            if ($solicitudProgramaAcademicoInstitucion->getSolicitudProgramaAcademico()->getModalidadADistancia()) {
                $item['id'] = 'A distancia';
                $item['nombre'] = 'A distancia';
                $modalidades[] = $item;
            }
            $insitucion = $solicitudProgramaAcademicoInstitucion->getInstitucion();
            $cum = $institucionCumRepository->findBy(['institucion' => $insitucion->getId()]);

            $registros = $solicitudProgramaAcademicoInstitucionCumRepository->findBy(['solicitudProgramaAcademicoInstitucion' => $solicitudProgramaAcademicoInstitucion->getId()]);
            return $this->render('modules/pregrado/mis_programas/asignar_cum.html.twig', [
                'modalidades' => $modalidades,
                'registros' => $registros,
                'cum' => $cum,
                'entidad' => $entityManager->getRepository(Estructura::class)->find($idEntidad),
                'programa' => $solicitudProgramaAcademicoInstitucion->getSolicitudProgramaAcademico()->getNombre(),
                'solicitud_programa_academico_institucion_id' => $solicitudProgramaAcademicoInstitucion->getId()
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_pregrado_mis_programas_asignados', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @Route("/pregrado/mis-programas/asignar-cum-ajax", name="app_pregrado_mis_programas_asignar_cum_ajax", methods={"POST"})
     */
    public function asignarCumAjax(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $cumIds = $request->request->get('cum');
        $modalidadNombre = $request->request->get('modalidad'); // Recibido como string
        $solicitudInstitucionId = $request->request->get('solicitud_programa_academico_institucion_id');

        if (empty($cumIds) || !$modalidadNombre || !$solicitudInstitucionId) {
            return new JsonResponse(['success' => false, 'message' => 'Datos incompletos.'], 400);
        }

        try {
            $repoRelacion = $entityManager->getRepository(SolicitudProgramaAcademicoInstitucionCum::class);
            $repoInstitucionCum = $entityManager->getRepository(InstitucionCum::class);
            $repoSolicitudInstitucion = $entityManager->getRepository(SolicitudProgramaAcademicoInstitucion::class);

            // Buscamos el objeto de la solicitud una sola vez fuera del bucle
            $solicitudInstitucion = $repoSolicitudInstitucion->find($solicitudInstitucionId);

            if (!$solicitudInstitucion) {
                return new JsonResponse(['success' => false, 'message' => 'La solicitud de institución no existe.'], 404);
            }

            $nuevosRegistros = 0;

            foreach ($cumIds as $cumId) {
                $institucionCum = $repoInstitucionCum->find($cumId);

                if (!$institucionCum) {
                    continue;
                }

                // VALIDACIÓN DE DUPLICADOS
                // Buscamos si ya existe la combinación exacta
                $existe = $repoRelacion->findOneBy([
                    'institucionCum' => $institucionCum,
                    'modalidad' => $modalidadNombre,
                    'solicitudProgramaAcademicoInstitucion' => $solicitudInstitucion
                ]);

                if (!$existe) {
                    $relacion = new SolicitudProgramaAcademicoInstitucionCum();
                    $relacion->setInstitucionCum($institucionCum);
                    $relacion->setSolicitudProgramaAcademicoInstitucion($solicitudInstitucion);
                    $relacion->setModalidad($modalidadNombre); // Se setea el string directamente

                    $entityManager->persist($relacion);
                    $nuevosRegistros++;
                }
            }

            if ($nuevosRegistros === 0) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Todas las asociaciones seleccionadas ya existen.'
                ], 200); // Usamos 200 o 400 según prefieras manejar el aviso
            }

            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => "Se han asociado $nuevosRegistros nuevos registros correctamente."
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * @Route("/pregrado/mis-programas/eliminar-cum/{id}/{return}", name="app_pregrado_mis_programas_eliminar_cum")
     */
    public function eliminarUniversidadAjax(int $id, $return, EntityManagerInterface $em)
    {

        $registro = $em->getRepository(SolicitudProgramaAcademicoInstitucionCum::class)->find($id);
        $em->remove($registro);
        $em->flush();
        $this->addFlash('success', 'CUM eliminado correctamente.');
        return $this->redirectToRoute('app_pregrado_mis_programas_asignados_cum', ['id' => $return], Response::HTTP_SEE_OTHER);

    }
}
