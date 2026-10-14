<?php

namespace App\Controller\Evaluacion;

use App\Entity\Evaluacion\AplazamientoSolicitud;
use App\Entity\Evaluacion\EstadoSolicitud;
use App\Entity\Evaluacion\Solicitud;
use App\Entity\Institucion\Institucion;
use App\Entity\Postgrado\SolicitudPrograma;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use App\Entity\Security\User;
use App\Form\Evaluacion\AprobarSolicitudType;
use App\Form\Evaluacion\RechazarSolicitudType;
use App\Form\Evaluacion\SolicitudType;
use App\Repository\Evaluacion\AplazamientoSolicitudRepository;
use App\Repository\Evaluacion\ComisionRepository;
use App\Repository\Evaluacion\EstadoAplazamientoRepository;
use App\Repository\Evaluacion\EstadoSolicitudRepository;
use App\Repository\Evaluacion\SolicitudRepository;
use App\Services\UploadedDocumentStorage;
use App\Repository\Institucion\InstitucionRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Postgrado\SolicitudProgramaRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use App\Repository\Security\UserRepository;
use App\Repository\Tramite\InstitucionExtranjeraRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PLAN_TRABAJO')"))]
#[Route('/evaluacion/plan_trabajo')]
class PlanTrabajoController extends AbstractController
{

    /**
     * @param SolicitudRepository $solicitudRepository
     * @return Response
     */
    #[Route('/', name: 'app_plan_trabajo_index', methods: ['GET'])]
    public function index(SolicitudRepository $solicitudRepository)
    {
        $registros = $solicitudRepository->getPlanTrabajo();
        $respose = [];
        if (is_array($registros)) {
            foreach ($registros as $value) {
                $class = null;
                if ($value->getEstadoPlanTrabajo() == 'Aprobado') {
                    $class = 'ms-status bg-green';
                }
                if ($value->getEstadoPlanTrabajo() == 'Rechazado') {
                    $class = 'ms-status bg-red';
                }
                $value->classParams = $class;
                $respose[] = $value;
            }
        }
        return $this->render('modules/evaluacion/plan_trabajo/index.html.twig', [
            'registros' => $respose
        ]);
    }

    /**
     * @param Solicitud $solicitud
     * @param SolicitudRepository $solicitudRepository
     * @return Response
     */
    #[Route('/{id}/aprobar', name: 'app_plan_trabajo_aprobar', methods: ['GET'])]
    public function aprobar(Solicitud $solicitud, EntityManagerInterface $entityManager, EstadoSolicitudRepository $estadoSolicitudRepository, SolicitudRepository $solicitudRepository)
    {
        try {
            $solicitud->setEstadoPlanTrabajo('Aprobado');
            $solicitudRepository->edit($solicitud, true);
            $solicitud->setEstadoSolicitud($estadoSolicitudRepository->find($this->getParameter('estado_evaluacion_plan_trabajo_aprobado')));
            $entityManager->flush();
            $this->addFlash('success', 'El elemento ha sido modificado satisfactoriamente.');
            return $this->redirectToRoute('app_plan_trabajo_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_trabajo_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Solicitud $solicitud
     * @param SolicitudRepository $solicitudRepository
     * @return Response
     */
    #[Route('/{id}/rechazar', name: 'app_plan_trabajo_rechazar', methods: ['POST'])]
    public function rechazar(Request $request, EntityManagerInterface $entityManager, Solicitud $solicitud, SolicitudRepository $solicitudRepository, EstadoSolicitudRepository $estadoSolicitudRepository, UploadedDocumentStorage $storage)
    {
        try {
            $file = $request->files->get('additionalFile');
            if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                throw new \InvalidArgumentException('Debe adjuntar el documento con el motivo del rechazo.');
            }
            $storedName = $storage->storeName($file, 'uploads/evaluacion/solicitud/plan_trabajo/motivos_rechazos');

            $solicitud->setEstadoPlanTrabajo('Rechazado');
            $solicitud->setEstadoSolicitud($estadoSolicitudRepository->find($this->getParameter('estado_evaluacion_plan_trabajo_rechazado')));
            $solicitud->setMotivoRechazo($storedName);

            $solicitudRepository->edit($solicitud, true);
            $entityManager->flush();
            $this->addFlash('success', 'El elemento ha sido modificado satisfactoriamente.');
            return $this->json('OK');
        } catch (\Exception $exception) {
            return $this->json($exception->getMessage());
        }
    }

    /**
     * @param Solicitud $solicitud
     * @param SolicitudRepository $solicitudRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_plan_trabajo_eliminar', methods: ['GET'])]
    public function eliminar(Solicitud $solicitud, SolicitudRepository $solicitudRepository)
    {
        try {
            $solicitud->setEstadoPlanTrabajo(null);
            $solicitud->setPlanTrabajo(null);
            $solicitudRepository->edit($solicitud, true);
            $this->addFlash('success', 'El elemento ha sido modificado satisfactoriamente.');
            return $this->redirectToRoute('app_plan_trabajo_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_trabajo_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
