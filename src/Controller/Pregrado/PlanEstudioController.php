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
use App\Services\UploadedDocumentStorage;
use App\Services\Utils;
use Cassandra\Timestamp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PLANEST')"))]
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
    public function registrar(Request $request, EntityManagerInterface $em, RequestStack $requestStack, SolicitudProgramaAcademicoRepository $solicitudProgramaAcademicoRepository, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository, DocumentoRepository $documentoRepository, CursoAcademicoRepository $cursoAcademicoRepository, UploadedDocumentStorage $storage)
    {
        try {
            $planEstudioEntity = new PlanEstudio();
            $form = $this->createForm(PlanEstudioType::class, $planEstudioEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $planEstudioEntity->setNombre('Plan de estudio ' . $cursoAcademicoRepository->find($request->request->all()['plan_estudio']['cursoAcademico'])->getNombre());
                $planEstudioEntity->setFechaAprobacion(\DateTime::createFromFormat('d/m/Y', $request->request->all()['plan_estudio']['fechaAprobacion']));

                if (($file = $form['planEstudio']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $planEstudioEntity->setPlanEstudio($storage->storeName($file, 'uploads/pregrado/plan_estudio/plan_estudio'));
                }
                if (($file = $form['documentoEjecutivo']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $planEstudioEntity->setDocumentoEjecutivo($storage->storeName($file, 'uploads/pregrado/plan_estudio/documento_ejecutivo'));
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
    public function registroTemporalDeDocumento(Request $request, RequestStack $requestStack, UploadedDocumentStorage $storage): Response
    {
        try {
            $post = $request->request->all();
            $file = $request->files->get('file');
            if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                return $this->json(['error' => 'Debe seleccionar un archivo.'], Response::HTTP_BAD_REQUEST);
            }

            $stored = $storage->store($file, 'uploads/pregrado/plan_estudio/documentos');
            $documentosPlanEstudio = $requestStack->getSession()->get('documentosPlanEstudio', []);
            $documentosPlanEstudio[] = [
                'id' => bin2hex(random_bytes(8)),
                'documento' => $stored['path'],
                'id_documento' => $post['documento'] ?? null,
                'nombre_documento' => $stored['originalName'],
            ];
            $requestStack->getSession()->set('documentosPlanEstudio', $documentosPlanEstudio);

            return $this->json($requestStack->getSession()->get('documentosPlanEstudio'), Response::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param PlanEstudio $planEstudio
     * @param PlanEstudioRepository $planEstudioRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_plan_estudio_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, RequestStack $requestStack, planEstudio $planEstudio, DocumentoRepository $documentoRepository, PlanEstudioRepository $planEstudioRepository, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository, UploadedDocumentStorage $storage)
    {
        try {
            $form = $this->createForm(PlanEstudioType::class, $planEstudio, ['action' => 'modificar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $temp = explode('/', $request->request->all()['plan_estudio']['fechaAprobacion']);
                $planEstudio->setFechaAprobacion(new \DateTime($temp[1] . '/' . $temp[0] . '/' . $temp[2]));

                if (($file = $form['planEstudio']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $planEstudio->setPlanEstudio($storage->replace($file, 'uploads/pregrado/plan_estudio/plan_estudio', $planEstudio->getPlanEstudio()));
                }
                if (($file = $form['documentoEjecutivo']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $planEstudio->setDocumentoEjecutivo($storage->replace($file, 'uploads/pregrado/plan_estudio/documento_ejecutivo', $planEstudio->getDocumentoEjecutivo()));
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
    public function modificaciones(Request $request, planEstudio $planEstudio, ModificacionPlanEstudioRepository $modificacionPlanEstudioRepository, UploadedDocumentStorage $storage)
    {
        try {
            $modificacion = new ModificacionPlanEstudio();
            $form = $this->createForm(ModificacionPlanEstudioType::class, $modificacion);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $temp = explode('/', $request->request->all()['modificacion_plan_estudio']['fechaAprobacion']);
                $modificacion->setFechaAprobacion(new \DateTime($temp[1] . '/' . $temp[0] . '/' . $temp[2]));
                $modificacion->setPlanEstudio($planEstudio);
                if (($file = $form['planEstudioDoc']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $modificacion->setPlanEstudioDoc($storage->storeName($file, 'uploads/pregrado/plan_estudio/modificaciones/plan_estudio'));
                }
                if (($file = $form['dictamen']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $modificacion->setDictamen($storage->storeName($file, 'uploads/pregrado/plan_estudio/modificaciones/dictamen'));
                }
                if (($file = $form['documentoEjecutivo']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $modificacion->setDocumentoEjecutivo($storage->storeName($file, 'uploads/pregrado/plan_estudio/modificaciones/documento_ejecutivo'));
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
    public function eliminarModificacion(ModificacionPlanEstudio $modificacionPlanEstudio, ModificacionPlanEstudioRepository $modificacionPlanEstudioRepository, UploadedDocumentStorage $storage)
    {
        try {
            if ($modificacionPlanEstudio instanceof ModificacionPlanEstudio) {
                $storage->delete('uploads/pregrado/plan_estudio/modificaciones/plan_estudio', $modificacionPlanEstudio->getPlanEstudioDoc());
                $storage->delete('uploads/pregrado/plan_estudio/modificaciones/dictamen', $modificacionPlanEstudio->getDictamen());
                $storage->delete('uploads/pregrado/plan_estudio/modificaciones/documento_ejecutivo', $modificacionPlanEstudio->getDocumentoEjecutivo());
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
    public function eliminarDocumentoEditar(PlanEstudioDocumento $planEstudioDocumento, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository, UploadedDocumentStorage $storage)
    {
        try {
            if ($planEstudioDocumentoRepository->find($planEstudioDocumento) instanceof PlanEstudioDocumento) {
                $storage->delete('uploads/pregrado/plan_estudio/documentos', basename($planEstudioDocumento->getDocumentoFisico()));
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
    public function registroTemporalDeDocumentoEdit(Request $request, RequestStack $requestStack, DocumentoRepository $documentoRepository, PlanEstudioRepository $planEstudioRepository, PlanEstudioDocumentoRepository $planEstudioDocumentoRepository, UploadedDocumentStorage $storage)
    {
        try {
            $post = $request->request->all();

            $file = $request->files->get('file');
            if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                return $this->json(['error' => 'Debe seleccionar un archivo.'], Response::HTTP_BAD_REQUEST);
            }

            $stored = $storage->store($file, 'uploads/pregrado/plan_estudio/documentos');
            $new = new PlanEstudioDocumento();
            $new->setNombre($stored['originalName']);
            $new->setPlanEstudio($planEstudioRepository->find($requestStack->getSession()->get('planEstudioId')));
            $new->setDocumentoFisico($stored['path']);
            $new->setDocumento($documentoRepository->find($post['documento']));
            $planEstudioDocumentoRepository->add($new, true);

            return $this->json('OK', Response::HTTP_OK);
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @return Response
     */
    #[Route('/{id}/eliminar-documento-crear', name: 'app_plan_estudio_eliminar_documento_crear', methods: ['GET', 'POST'])]
    public function eliminarDocumentoCrear($id, RequestStack $requestStack, UploadedDocumentStorage $storage)
    {
        try {
            $documentos = $requestStack->getSession()->get('documentosPlanEstudio');
            $response = [];
            if (is_array($documentos)) {
                foreach ($documentos as $value) {
                    if ($value['id'] !== $id) {
                        $response[] = $value;
                    } else {
                        $storage->delete('uploads/pregrado/plan_estudio/documentos', basename($value['documento']));
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
