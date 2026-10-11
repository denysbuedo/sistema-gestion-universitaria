<?php

namespace App\Controller\Pregrado;

use App\Entity\NotificacionesUsuario;
use App\Entity\Pregrado\HistoricoEstadoProgramaAcademico;
use App\Entity\Pregrado\ProgramaAcademicoDesactivado;
use App\Entity\Pregrado\ProgramaAcademicoReabierto;
use App\Entity\Pregrado\ProgramaAcademicoReabiertoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use App\Entity\Pregrado\SolicitudProgramaAcademicoComisionNacional;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademicoPlanEstudio;
use App\Form\Pregrado\AprobarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\ProgramaAcademicoDesactivadoType;
use App\Form\Pregrado\ProgramaAcademicoReabiertoType;
use App\Form\Pregrado\SolicitudProgramaComisionType;
use App\Form\Pregrado\SolicitudProgramaInstitucionType;
use App\Form\Pregrado\SolicitudProgramaPlanEstudioType;
use App\Repository\Institucion\InstitucionRepository;
use App\Repository\NotificacionesUsuarioRepository;
use App\Repository\Pregrado\EstadoProgramaAcademicoRepository;
use App\Repository\Pregrado\HistoricoEstadoProgramaAcademicoRepository;
use App\Repository\Pregrado\MiembrosComisionNacionalRepository;
use App\Repository\Pregrado\ModificacionPlanEstudioRepository;
use App\Repository\Pregrado\ProgramaAcademicoDesactivadoRepository;
use App\Repository\Pregrado\ProgramaAcademicoReabiertoInstitucionRepository;
use App\Repository\Pregrado\ProgramaAcademicoReabiertoRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoComisionNacionalRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoPlanEstudioRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PROGEXTINT')"))]
#[Route('/pregrado/solicitud_programa_academico_extinto')]
class SolicitudProgramaAcademicoExtintosController extends AbstractController
{

    /**
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/', name: 'app_solicitud_programa_academico_extinto_index', methods: ['GET'])]
    public function index(SolicitudProgramaAcademicoRepository $solicitudProgramaRepository)
    {
        $registros = $solicitudProgramaRepository->getSolicitudProgramaAcademicoAprobado([8]);
        return $this->render('modules/pregrado/solicitud_programa_academico_extinto/index.html.twig', [
            'registros' => $registros,
        ]);
    }

    /**
     * @param Utils $utils
     * @param ProgramaAcademicoDesactivadoRepository $programaAcademicoDesactivadoRepository
     * @param SolicitudProgramaAcademico $solicitudPrograma
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @param EstadoProgramaAcademicoRepository $estadoProgramaAcademicoRepository
     * @return Response
     */
    #[Route('/{id}/activar', name: 'app_solicitud_programa_academico_extinto_activar', methods: ['GET'])]
    public function activar(Utils $utils, ProgramaAcademicoDesactivadoRepository $programaAcademicoDesactivadoRepository, SolicitudProgramaAcademico $solicitudPrograma, SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, EstadoProgramaAcademicoRepository $estadoProgramaAcademicoRepository)
    {
        try {
            if ($solicitudProgramaRepository->find($solicitudPrograma) instanceof SolicitudProgramaAcademico) {
                $solicitudPrograma->setEstadoProgramaAcademico($estadoProgramaAcademicoRepository->find(5));
                $solicitudProgramaRepository->edit($solicitudPrograma, true);

                $utils->guardarHistoricoEstadoProgramaAcademico($solicitudPrograma->getId(), 5);

                $progDesactivado = $programaAcademicoDesactivadoRepository->findBy(['solicitudProgramaAcademico' => $solicitudPrograma->getId()]);
                if (is_array($progDesactivado)) {
                    foreach ($progDesactivado as $value) {
                        $programaAcademicoDesactivadoRepository->remove($value, true);
                    }
                }

                $this->addFlash('success', 'El elemento ha sido modificado satisfactoriamente.');
                return $this->redirectToRoute('app_solicitud_programa_academico_extinto_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_solicitud_programa_academico_extinto_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_solicitud_programa_academico_extinto_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     *
     * /**
     * @param SolicitudProgramaAcademico $solicitudProgramaAcademico
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_solicitud_programa_academico_extinto_detail', methods: ['GET', 'POST'])]
    public function detail(HistoricoEstadoProgramaAcademicoRepository $historicoEstadoProgramaAcademicoRepository, SolicitudProgramaAcademico $solicitudProgramaAcademico)
    {
        return $this->render('modules/pregrado/solicitud_programa_academico_extinto/detail.html.twig', [
            'item' => $solicitudProgramaAcademico,
            'format' => 'col2',
            'extenciones' => $historicoEstadoProgramaAcademicoRepository->getExtenciones($solicitudProgramaAcademico->getId())
        ]);
    }

}
