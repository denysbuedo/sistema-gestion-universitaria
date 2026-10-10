<?php

namespace App\Controller\Pregrado;

use App\Entity\Pregrado\Documento;
use App\Entity\Pregrado\ModificacionPlanEstudio;
use App\Entity\Pregrado\PlanEstudio;
use App\Entity\Pregrado\PlanEstudioDocumento;
use App\Entity\Pregrado\SolicitudProgramaAcademicoPlanEstudio;
use App\Entity\Security\User;
use App\Export\Pregrado\ExportListPlanEstudioToPdf;
use App\Form\Pregrado\ModificacionPlanEstudioType;
use App\Form\Pregrado\PlanEstudioType;
use App\Repository\Pregrado\CursoAcademicoRepository;
use App\Repository\Pregrado\DocumentoRepository;
use App\Repository\Pregrado\ModificacionPlanEstudioRepository;
use App\Repository\Pregrado\PlanEstudioDocumentoRepository;
use App\Repository\Pregrado\PlanEstudioRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use App\Services\HandlerFop;
use App\Services\Utils;
use Cassandra\Timestamp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_PLANEST")
 */
#[Route('/pregrado/plan_estudio')]
class PlanEstudioController extends AbstractController
{

    /**
     * @param PlanEstudioRepository $planEstudioRepository
     * @return Response
     */
    #[Route('/', name: 'app_plan_estudio_index', methods: ['GET'])]
    public function index(PlanEstudioRepository $planEstudioRepository, RequestStack $requestStack)
    {
        $requestStack->getSession()->remove('documentosPlanEstudio');
        return $this->render('modules/pregrado/plan_estudio/index.html.twig', [
            'registros' => $planEstudioRepository->getPlanesEstudio(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/registrar', name: 'app_plan_estudio_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, EntityManagerInterface $em, RequestStack $requestStack, SolicitudProgramaAcademicoRepository $solicitudProgramaAcademicoRepository, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository, DocumentoRepository $documentoRepository, CursoAcademicoRepository $cursoAcademicoRepository)
    {
        try {
            $planEstudioEntity = new PlanEstudio();
            $form = $this->createForm(PlanEstudioType::class, $planEstudioEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $planEstudioEntity->setNombre('Plan de estudio ' . $cursoAcademicoRepository->find($request->request->all()['plan_estudio']['cursoAcademico'])->getNombre());
                $planEstudioEntity->setFechaAprobacion(\DateTime::createFromFormat('d/m/Y', $request->request->all()['plan_estudio']['fechaAprobacion']));

                if (!empty($_FILES['plan_estudio']['name']['planEstudio'])) {
                    $file = $form['planEstudio']->getData();
                    $file_name = $_FILES['plan_estudio']['name']['planEstudio'];
                    $planEstudioEntity->setPlanEstudio($file_name);
                    $file->move("uploads/pregrado/plan_estudio/plan_estudio", $file_name);
                }
                if (!empty($_FILES['plan_estudio']['name']['documentoEjecutivo'])) {
                    $file = $form['documentoEjecutivo']->getData();
                    $file_name = $_FILES['plan_estudio']['name']['documentoEjecutivo'];
                    $planEstudioEntity->setDocumentoEjecutivo($file_name);
                    $file->move("uploads/pregrado/plan_estudio/documento_ejecutivo", $file_name);
                }
                $documentos = $requestStack->getSession()->get('documentosPlanEstudio');
                if (is_array($documentos)) {
                    foreach ($documentos as $value) {
                        $docEntity = new PlanEstudioDocumento();
                        $docEntity->setDocumentoFisico($value['documento']);
                        $docEntity->setNombre($value['nombre_documento']);
                        $docEntity->setPlanEstudio($planEstudioEntity);
                        $docEntity->setDocumento($documentoRepository->find($value['id_documento']));
                        $planEstudioDocumentoRepository->add($docEntity, true);
                    }
                    $requestStack->getSession()->remove('documentosPlanEstudio');
                }
                $em->persist($planEstudioEntity);
                $em->flush();
//                $planEstudioRepository->add($planEstudioEntity, true);


                $entidad = new SolicitudProgramaAcademicoPlanEstudio();
                $entidad->setSolicitudProgramaAcademico($planEstudioEntity->getCarrera());
                $entidad->setPlanEstudio($planEstudioEntity);

                $em->persist($entidad);
                $em->flush();

//                $utils->guardarHistoricoEstadoProgramaAcademico($entidad->getSolicitudProgramaAcademico()->getId(), 5);

                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_plan_estudio_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/plan_estudio/new.html.twig', [
                'form' => $form->createView(),
                'registros' => $requestStack->getSession()->get('documentosPlanEstudio'),
                'documentos' => $documentoRepository->findBy(['activo' => true], ['nombre' => 'asc'])
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/registo-temporal-doc', name: 'app_plan_estudio_registro_temporal_doc', methods: ['GET', 'POST'])]
    public function registroTemporalDeDocumento(Request $request, RequestStack $requestStack)
    {
        try {
            $post = $request->request->all();
            $uploadPath = 'uploads/pregrado/plan_estudio/documentos/';
            $url = null;
            $path = "uploads/pregrado/plan_estudio/documentos";
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/pregrado/plan_estudio/documentos/' . $_FILES['file']['name'])) {
                $url = $uploadPath . $_FILES['file']['name'];
            }

            if (!empty($url)) {
                $documentosPlanEstudio = $requestStack->getSession()->has('documentosPlanEstudio') ? $requestStack->getSession()->get('documentosPlanEstudio') : null;
                $item['id'] = uniqid();
                $item['documento'] = $url;
                $item['id_documento'] = $post['documento'];
                $item['nombre_documento'] = $_FILES['file']['name'];
                $documentosPlanEstudio[] = $item;
                $requestStack->getSession()->set('documentosPlanEstudio', $documentosPlanEstudio);
            }
            return $this->json($requestStack->getSession()->get('documentosPlanEstudio'), Response::HTTP_OK);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param PlanEstudio $planEstudio
     * @param PlanEstudioRepository $planEstudioRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_plan_estudio_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, RequestStack $requestStack, planEstudio $planEstudio, DocumentoRepository $documentoRepository, PlanEstudioRepository $planEstudioRepository, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository)
    {
        try {
            $form = $this->createForm(PlanEstudioType::class, $planEstudio, ['action' => 'modificar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() /*&& $form->isValid()*/) {
                $temp = explode('/', $request->request->all()['plan_estudio']['fechaAprobacion']);
                $planEstudio->setFechaAprobacion(new \DateTime($temp[1] . '/' . $temp[0] . '/' . $temp[2]));

                if (!empty($_FILES['plan_estudio']['name']['planEstudio'])) {
                    if ($planEstudio->getPlanEstudio() != null) {
                        if (file_exists('uploads/pregrado/plan_estudio/' . $planEstudio->getPlanEstudio())) {
                            unlink('uploads/pregrado/plan_estudio/plan_estudio/' . $planEstudio->getPlanEstudio());
                        }
                    }
                    $file = $form['planEstudio']->getData();
                    $file_name = $_FILES['plan_estudio']['name']['planEstudio'];
                    $planEstudio->setPlanEstudio($file_name);
                    $file->move("uploads/pregrado/plan_estudio", $file_name);
                }
                if (!empty($_FILES['plan_estudio']['name']['documentoEjecutivo'])) {
                    if ($planEstudio->getPlanEstudio() != null) {
                        if (file_exists('uploads/pregrado/plan_estudio/documento_ejecutivo/' . $planEstudio->getDocumentoEjecutivo())) {
                            unlink('uploads/pregrado/plan_estudio/documento_ejecutivo/' . $planEstudio->getDocumentoEjecutivo());
                        }
                    }
                    $file = $form['documentoEjecutivo']->getData();
                    $file_name = $_FILES['plan_estudio']['name']['documentoEjecutivo'];
                    $planEstudio->setDocumentoEjecutivo($file_name);
                    $file->move("uploads/pregrado/plan_estudio/documento_ejecutivo", $file_name);
                }

                $planEstudioRepository->edit($planEstudio);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_plan_estudio_index', [], Response::HTTP_SEE_OTHER);
            }
            $requestStack->getSession()->set('planEstudioId', $planEstudio->getId());
            return $this->render('modules/pregrado/plan_estudio/edit.html.twig', [
                'form' => $form->createView(),
                'planEstudio' => $planEstudio,
                'registros' => $planEstudioDocumentoRepository->findBy(['planEstudio' => $planEstudio->getId()]),
                'documentos' => $documentoRepository->findBy(['activo' => true], ['nombre' => 'asc'])

            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_modificar', ['id' => $planEstudio], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param PlanEstudio $tipoPrograma
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_plan_estudio_detail', methods: ['GET', 'POST'])]
    public function detail(PlanEstudio $planEstudio, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository)
    {
        return $this->render('modules/pregrado/plan_estudio/detail.html.twig', [
            'item' => $planEstudio,
            'registros' => $planEstudioDocumentoRepository->findBy(['planEstudio' => $planEstudio->getId()]),
        ]);
    }

    /**
     * @param planEstudio $planEstudio
     * @param PlanEstudioRepository $planEstudioRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_plan_estudio_eliminar', methods: ['GET'])]
    public function eliminar(PlanEstudio $planEstudio, PlanEstudioRepository $planEstudioRepository)
    {
        try {
            if ($planEstudioRepository->find($planEstudio) instanceof PlanEstudio) {
                $planEstudioRepository->remove($planEstudio, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_plan_estudio_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_plan_estudio_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param PlanEstudio $planEstudio
     * @param ModificacionPlanEstudioRepository $modificacionPlanEstudioRepository
     * @return Response
     */
    #[Route('/{id}/modificaciones', name: 'app_plan_estudio_modificaciones', methods: ['GET', 'POST'])]
    public function modificaciones(Request $request, planEstudio $planEstudio, ModificacionPlanEstudioRepository $modificacionPlanEstudioRepository)
    {
        try {
            $modificacion = new ModificacionPlanEstudio();
            $form = $this->createForm(ModificacionPlanEstudioType::class, $modificacion);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $temp = explode('/', $request->request->all()['modificacion_plan_estudio']['fechaAprobacion']);
                $modificacion->setFechaAprobacion(new \DateTime($temp[1] . '/' . $temp[0] . '/' . $temp[2]));
                $modificacion->setPlanEstudio($planEstudio);
                if (!empty($_FILES['modificacion_plan_estudio']['name']['planEstudioDoc'])) {
                    $file = $form['planEstudioDoc']->getData();
                    $file_name = $_FILES['modificacion_plan_estudio']['name']['planEstudioDoc'];
                    $modificacion->setPlanEstudioDoc($file_name);
                    $file->move("uploads/pregrado/plan_estudio/modificaciones/plan_estudio", $file_name);
                }
                if (!empty($_FILES['modificacion_plan_estudio']['name']['dictamen'])) {
                    $file = $form['dictamen']->getData();
                    $file_name = $_FILES['modificacion_plan_estudio']['name']['dictamen'];
                    $modificacion->setDictamen($file_name);
                    $file->move("uploads/pregrado/plan_estudio/modificaciones/dictamen", $file_name);
                }
                if (!empty($_FILES['modificacion_plan_estudio']['name']['documentoEjecutivo'])) {
                    $file = $form['documentoEjecutivo']->getData();
                    $file_name = $_FILES['modificacion_plan_estudio']['name']['documentoEjecutivo'];
                    $modificacion->setDocumentoEjecutivo($file_name);
                    $file->move("uploads/pregrado/plan_estudio/modificaciones/documento_ejecutivo", $file_name);
                }

                $modificacionPlanEstudioRepository->add($modificacion, true);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_plan_estudio_modificaciones', ['id' => $planEstudio->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/plan_estudio/modificaciones.html.twig', [
                'form' => $form->createView(),
                'planEstudio' => $planEstudio,
                'registros' => $modificacionPlanEstudioRepository->findBy(['planEstudio' => $planEstudio->getId()]),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_modificaciones', ['id' => $planEstudio->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param ModificacionPlanEstudio $modificacionPlanEstudio
     * @param ModificacionPlanEstudioRepository $modificacionPlanEstudioRepository
     * @return Response
     */
    #[Route('/{id}/eliminar_modificacion', name: 'app_plan_estudio_eliminar_modificacion', methods: ['GET'])]
    public function eliminarModificacion(ModificacionPlanEstudio $modificacionPlanEstudio, ModificacionPlanEstudioRepository $modificacionPlanEstudioRepository)
    {
        try {
            if ($modificacionPlanEstudio instanceof ModificacionPlanEstudio) {
                $modificacionPlanEstudioRepository->remove($modificacionPlanEstudio, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_plan_estudio_modificaciones', ['id' => $modificacionPlanEstudio->getPlanEstudio()->getId()], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_plan_estudio_modificaciones', ['id' => $modificacionPlanEstudio->getPlanEstudio()->getId()], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_modificaciones', ['id' => $modificacionPlanEstudio->getPlanEstudio()->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param User $user
     * @return Response
     */
    #[Route('/exportar_pdf', name: 'app_plan_estudio_exportar_pdf', methods: ['GET', 'POST'])]
    public function exportarPdf(HandlerFop $handFop, PlanEstudioRepository $planEstudioRepository)
    {
        $export = $planEstudioRepository->getPlanesEstudio();
        return $handFop->exportToPdf(new ExportListPlanEstudioToPdf($export));
    }


    /**
     * @param PlanEstudioDocumento $planEstudioDocumento
     * @param PlanEstudioDocumentoRepository $planEstudioDocumentoRepository
     * @return Response
     */
    #[Route('/{id}/eliminar-documento-editar', name: 'app_plan_estudio_eliminar_documento_editar', methods: ['GET'])]
    public function eliminarDocumentoEditar(PlanEstudioDocumento $planEstudioDocumento, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository)
    {
        try {
            if ($planEstudioDocumentoRepository->find($planEstudioDocumento) instanceof PlanEstudioDocumento) {
                $planEstudioDocumentoRepository->remove($planEstudioDocumento, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_plan_estudio_modificar', ['id' => $planEstudioDocumento->getPlanEstudio()->getId()], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_plan_estudio_modificar', ['id' => $planEstudioDocumento->getPlanEstudio()->getId()], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_modificar', ['id' => $planEstudioDocumento->getPlanEstudio()->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/registo-temporal-doc-edit', name: 'app_plan_estudio_registro_temporal_doc_edit', methods: ['GET', 'POST'])]
    public function registroTemporalDeDocumentoEdit(Request $request, RequestStack $requestStack, DocumentoRepository $documentoRepository, PlanEstudioRepository $planEstudioRepository, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository)
    {
        try {
            $post = $request->request->all();

            $uploadPath = 'uploads/pregrado/plan_estudio/documentos/';
            $url = null;
            $path = "uploads/pregrado/plan_estudio/documentos";
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/pregrado/plan_estudio/documentos/' . $_FILES['file']['name'])) {
                $url = $uploadPath . $_FILES['file']['name'];
            }
            if (!empty($url)) {
                $new = new PlanEstudioDocumento();
                $new->setNombre($_FILES['file']['name']);
                $new->setPlanEstudio($planEstudioRepository->find($requestStack->getSession()->get('planEstudioId')));
                $new->setDocumentoFisico($url);
                $new->setDocumento($documentoRepository->find($post['documento']));
                $planEstudioDocumentoRepository->add($new, true);
            }
            return $this->json('OK', Response::HTTP_OK);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @return Response
     */
    #[Route('/{id}/eliminar-documento-crear', name: 'app_plan_estudio_eliminar_documento_crear', methods: ['GET', 'POST'])]
    public function eliminarDocumentoCrear($id, RequestStack $requestStack)
    {
        try {
            $documentos = $requestStack->getSession()->get('documentosPlanEstudio');
            $response = [];
            if (is_array($documentos)) {
                foreach ($documentos as $value) {
                    if ($value['id'] !== $id) {
                        $response[] = $value;
                    }
                }
            }
            $requestStack->getSession()->set('documentosPlanEstudio', $response);
            $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
            return $this->redirectToRoute('app_plan_estudio_registrar');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_plan_estudio_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }
}
