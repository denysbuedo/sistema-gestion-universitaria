<?php

namespace App\Controller\Tramite;

use App\Entity\Personal\Persona;
use App\Entity\Tramite\ConceptoSalida;
use App\Entity\Tramite\DocumentoSalida;
use App\Entity\Security\User;
use App\Entity\Tramite\DocumentoSalidaConceptoGasto;
use App\Entity\Tramite\DocumentoSalidaEstado;
use App\Entity\Tramite\DocumentoSalidaTramite;
use App\Entity\Tramite\FichaSalida;
use App\Entity\Tramite\FichaSalidaEstado;
use App\Entity\Tramite\Pasaporte;
use App\Form\Tramite\AsignarFechaSalidaType;
use App\Form\Tramite\AsignarTramiteType;
use App\Form\Tramite\CambioEstadoSalidaType;
use App\Form\Tramite\ConceptoSalidaType;
use App\Form\Tramite\DocumentoSalidaType;
use App\Repository\Economia\ConceptoGastoRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Personal\ResponsableRepository;
use App\Repository\Tramite\DocumentoSalidaRepository;
use App\Repository\Tramite\DocumentoSalidaTramiteRepository;
use App\Repository\Tramite\EstadoDocumentoSalidaRepository;
use App\Repository\Tramite\DocumentoSalidaConceptoGastoRepository;
use App\Repository\Tramite\DocumentoSalidaEstadoEstadoRepository;
use App\Repository\Tramite\DocumentoSalidaEstadoRepository;
use App\Repository\Tramite\EstadoFichaSalidaRepository;
use App\Repository\Tramite\FichaSalidaEstadoRepository;
use App\Repository\Tramite\FichaSalidaRepository;
use App\Repository\Tramite\PasaporteRepository;
use App\Repository\Tramite\TramiteRepository;
use App\Services\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Tramite\DocumentoSalidaHistorialDocumento;
use App\Repository\Tramite\DocumentoSalidaHistorialDocumentoRepository;
use App\Repository\Tramite\FichaSalidaHistorialDocumentoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/tramite/documento_salida")
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_DOCUMENTO_SALIDA")
 */
class DocumentoSalidaController extends AbstractController
{
    /**
     * @Route("/{id}/historial-documentos", name="app_documento_salida_historial_documentos", methods={"GET"})
     */
    public function listHistorial(Request $request, DocumentoSalida $documentoSalida, DocumentoSalidaHistorialDocumentoRepository $repoDoc, FichaSalidaHistorialDocumentoRepository $repoFicha): JsonResponse
    {
        $host = $request->getSchemeAndHttpHost();

        // Documentos de Ficha de Salida
        $ficha = $documentoSalida->getFichaSalida();
        $fichaDocs = [];
        if ($ficha) {
            $docsFact = $repoFicha->findBy(['fichaSalida' => $ficha], ['fecha' => 'ASC']);
            foreach ($docsFact as $doc) {
                $path = $doc->getArchivo();
                if (strpos($path, '/') === false) {
                    $folder = (stripos($doc->getNombre(), 'Carta') !== false ? 'carta_invitacion/' : 'ficha_salida/');
                    $path = '/uploads/tramites/ficha_salida/' . $folder . $path;
                } elseif (strpos($path, '/') !== 0) {
                    $path = '/' . $path;
                }
                $fichaDocs[] = [
                    'nombre' => $doc->getNombre(),
                    'url' => $host . $path,
                    'fecha' => $doc->getFecha()->format('d/m/Y H:i A'),
                    'usuario' => $doc->getUsuario() ? $doc->getUsuario()->getUsername() : 'Sistema'
                ];
            }
        }

        // Documentos de Documento de Salida
        $documentoDocs = [];
        $docsDoc = $repoDoc->findBy(['documentoSalida' => $documentoSalida], ['fecha' => 'ASC']);
        foreach ($docsDoc as $doc) {
            $path = $doc->getArchivo();
            if (strpos($path, '/') === false) {
                $path = '/uploads/tramites/documento_salida/' . $path;
            } elseif (strpos($path, '/') !== 0) {
                $path = '/' . $path;
            }
            $documentoDocs[] = [
                'nombre' => $doc->getNombre(),
                'url' => $host . $path,
                'fecha' => $doc->getFecha()->format('d/m/Y H:i A'),
                'usuario' => $doc->getUsuario() ? $doc->getUsuario()->getUsername() : 'Sistema'
            ];
        }

        return new JsonResponse([
            'ficha' => $fichaDocs,
            'documento' => $documentoDocs
        ]);
    }

    private function logDocument(DocumentoSalida $documentoSalida, string $nombre, string $archivo): void
    {
        $historial = new DocumentoSalidaHistorialDocumento();
        $historial->setDocumentoSalida($documentoSalida);
        $historial->setNombre($nombre);
        $historial->setArchivo($archivo);
        $historial->setUsuario($this->getUser());
        $historial->setFecha(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($historial);
        $em->flush();
    }

    /**
     * @Route("/", name="app_documento_salida_index", methods={"GET", "POST"})
     * @param documentoSalidaRepository $documentoSalidaRepository
     * @return Response
     */
    public function index(Request $request, DocumentoSalidaRepository $documentoSalidaRepository, EntityManagerInterface $entityManager, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        $allPost = $request->request->all();

        if (isset($allPost['estado']) && !empty($allPost['estado'])) {
            $request->getSession()->set('documento_salida_fil_estado', $allPost['estado']);
            $request->getSession()->set('documento_salida_text_fil_estado', $estadoFichaSalidaRepository->find($allPost['estado'])->getNombre());
        }
        if (isset($allPost['estado']) && empty($allPost['estado'])) {
            $request->getSession()->remove('documento_salida_fil_estado');
            $request->getSession()->remove('documento_salida_text_fil_estado');
        }
        if (isset($allPost['clasificacion']) && !empty($allPost['clasificacion'])) {
            $request->getSession()->set('documento_salida_fil_clasificacion', $allPost['clasificacion']);
        }
        if (isset($allPost['clasificacion']) && empty($allPost['clasificacion'])) {
            $request->getSession()->remove('documento_salida_fil_clasificacion');
        }
        $filtros = [];
        if ($request->getSession()->has('documento_salida_fil_estado')) {
            $filtros['estadoDocumentoSalida'] = $request->getSession()->get('documento_salida_fil_estado');
        }
        if ($request->getSession()->has('documento_salida_fil_clasificacion')) {
            $filtros['clasificacion'] = $request->getSession()->get('documento_salida_fil_clasificacion');
        }
        $registros = $documentoSalidaRepository->findWithFiltros($filtros);
        $formAsignar = $this->createForm(AsignarTramiteType::class, null, ['action' => 'modificar']);
        $formFecha   = $this->createForm(AsignarFechaSalidaType::class, null, ['action' => 'modificar']);

        return $this->render('modules/tramite/documento_salida/index.html.twig', [
            'registros' => $registros,
            'estados' => $estadoFichaSalidaRepository->findBy(['activo' => true, 'documentoSalida' => true], ['nombre' => 'asc']),
            'fil_estado' => $request->getSession()->get('documento_salida_fil_estado'),
            'fil_clasificacion' => $request->getSession()->get('documento_salida_fil_clasificacion'),
            'text_fil' => $request->getSession()->has('documento_salida_text_fil_estado') ? " (Estado=" . $request->getSession()->get('documento_salida_text_fil_estado') . ")" : null,
            'formAsignar' => $formAsignar->createView(),
            'formFecha'   => $formFecha->createView(),
        ]);
    }

    /**
     * @Route("/{id}/asignar_tramite", name="app_documento_salida_asignar_tramite", methods={"GET", "POST"})
     * @param Request $request
     * @param DocumentoSalida $documentoSalida
     * @param DocumentoSalidaRepository $documentoSalidaRepository
     * @param EstadoFichaSalidaRepository $estadoFichaSalidaRepository
     * @return Response
     */
    public function asignarTramites(Request $request, PasaporteRepository $pasaporteRepository, TramiteRepository $tramiteRepository, DocumentoSalida $documentoSalida, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, DocumentoSalidaRepository $documentoSalidaRepository)
    {
        try {

            $form = $this->createForm(AsignarTramiteType::class, null, ['action' => 'modificar']);
            $form->handleRequest($request);
            $data = $documentoSalidaTramiteRepository->findBy(['documentoSalida' => $documentoSalida->getId()]);
            if ($form->isSubmitted() && $form->isValid()) {
                if (isset($_POST['asignar_tramite']['tramite'])) {
                    foreach ($data as $value) {
                        $documentoSalidaTramiteRepository->remove($value, true);
                    }
                    foreach ($_POST['asignar_tramite']['tramite'] as $value) {
                        $entidad = new DocumentoSalidaTramite();
                        $entidad->setDocumentoSalida($documentoSalida);
                        $entidad->setTramite($tramiteRepository->find($value));
                        $documentoSalidaTramiteRepository->add($entidad, true);
                        if ($this->getParameter('tramite_confeccion_pasaporte') == $value) {
                            $tipoPasaporte = $documentoSalida->getTipoPasaporte();
                            $ci = $documentoSalida->getPersona()->getCarnetIdentidad();
                            if (!$pasaporteRepository->findHabilitadoPorCiYTipo($ci, $tipoPasaporte)) {
                                if (!$pasaporteRepository->findPendientePorCiYTipo($ci, $tipoPasaporte)) {
                                    $solicitudPasaporte = new Pasaporte();
                                    $solicitudPasaporte->setPersona($documentoSalida->getPersona());
                                    $solicitudPasaporte->setTipoPasaporte($tipoPasaporte);
                                    $pasaporteRepository->add($solicitudPasaporte, true);
                                }
                            }
                        }
                    }
                }
                $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_tramite')));
                $documentoSalidaRepository->edit($documentoSalida, true);

                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }

            $tramitesAsignados = [];
            foreach ($data as $value) {
                if (!empty($documentoSalida->getNumeroPasaporte())) {
                    if ($value->getTramite()->getId() == $this->getParameter('tramite_confeccion_pasaporte')) {
                        continue;
                    }
                }
                $tramitesAsignados[] = $value->getTramite()->getId();
            }

            return $this->render('modules/tramite/documento_salida/asignarTramite.html.twig', [
                'form' => $form->createView(),
                'documentoSalida' => $documentoSalida,
                'persona' => $documentoSalida->getPersona(),
                'tramitesAsociados' => json_encode($tramitesAsignados)
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_asignar_tramite', ['id' => $documentoSalida->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/datos_asignar_tramite", name="app_documento_salida_datos_asignar_tramite", methods={"GET"})
     */
    public function datosAsignarTramite(DocumentoSalida $documentoSalida, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository, PasaporteRepository $pasaporteRepository)
    {
        $data = $documentoSalidaTramiteRepository->findBy(['documentoSalida' => $documentoSalida]);
        $tramitesAsignados = [];
        foreach ($data as $value) {
            $tramitesAsignados[] = $value->getTramite()->getId();
        }
        $tienePasaporteHabilitado = (bool) $pasaporteRepository->findHabilitadoPorCiYTipo(
            $documentoSalida->getPersona()->getCarnetIdentidad(),
            $documentoSalida->getTipoPasaporte()
        );
        return $this->json([
            'tramites'                => $tramitesAsignados,
            'persona'                 => $documentoSalida->getPersona()->getPrimerNombre() . ' ' . $documentoSalida->getPersona()->getPrimerApellido(),
            'tienePasaporteHabilitado' => $tienePasaporteHabilitado,
            'idTramiteConfeccion'      => (int) $this->getParameter('tramite_confeccion_pasaporte'),
        ]);
    }

    /**
     * @Route("/{id}/enviar_a_tramite", name="app_documento_salida_enviarA_tramite", methods={"GET"})
     * @param DocumentoSalida $documentoSalida
     * @param DocumentoSalidaRepository $documentoSalidaRepository
     * @param EstadoFichaSalidaRepository $estadoFichaSalidaRepository
     * @return Response
     */
    public function enviarATramites(DocumentoSalida $documentoSalida, DocumentoSalidaRepository $documentoSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        try {
            if ($documentoSalidaRepository->find($documentoSalida) instanceof DocumentoSalida) {
                $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_tramite')));
                $documentoSalidaRepository->edit($documentoSalida, true);
                $this->addFlash('success', 'El elemento ha sido modificado satisfactoriamente.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @Route("/{id}/asignar_fecha_salida_regreso", name="app_documento_salida_asignar_fecha_salida_regreso", methods={"GET", "POST"})
     * @param Request $request
     * @param DocumentoSalida $documentoSalida
     * @param EstadoFichaSalidaRepository $estadoFichaSalidaRepository
     * @param DocumentoSalidaRepository $documentoSalidaRepository
     * @return Response
     */
    public function asignarFechaSalidaRegreso(Request $request, DocumentoSalida $documentoSalida, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, DocumentoSalidaRepository $documentoSalidaRepository)
    {
        try {
            $form = $this->createForm(AsignarFechaSalidaType::class, $documentoSalida, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $temp = explode('/', $request->request->all()['asignar_fecha_salida']['fechaSalidaReal']);
                $documentoSalida->setFechaSalidaReal(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $temp = explode('/', $request->request->all()['asignar_fecha_salida']['fechaRegresoReal']);
                $documentoSalida->setFechaRegresoReal(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $fechaEntrega = $request->request->all()['asignar_fecha_salida']['fechaEntregaPasaporte'] ?? null;
                if (!empty($fechaEntrega)) {
                    $temp = explode('/', $fechaEntrega);
                    $documentoSalida->setFechaEntregaPasaporte(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));
                }

                $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_viajando')));
                $documentoSalidaRepository->edit($documentoSalida, true);

                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/tramite/documento_salida/asignarFechaSalidaRegreso.html.twig', [
                'form' => $form->createView(),
                'documentoSalida' => $documentoSalida,
                'persona' => $documentoSalida->getPersona(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/aprobar_tramites", name="app_documento_salida_aprobar_tramites", methods={"GET", "POST"})
     * @param DocumentoSalida $documentoSalida
     * @return Response
     */
    public function aprobarTramites(DocumentoSalida $documentoSalida, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository)
    {
        try {
            $listo = true;
            $tramites = $documentoSalidaTramiteRepository->findBy(['documentoSalida' => $documentoSalida], ['id' => 'asc']);
            foreach ($tramites as $value) {
                if (!$value->getListo()) {
                    $listo = false;
                    break;
                }
            }
            return $this->render('modules/tramite/documento_salida/aprobarTramites.html.twig', [
                'tramites' => $tramites,
                'listo' => $listo,
                'documentoSalida' => $documentoSalida,
                'persona' => $documentoSalida->getPersona(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/datos_aprobar_tramites", name="app_documento_salida_datos_aprobar_tramites", methods={"GET"})
     */
    public function datosAprobarTramites(DocumentoSalida $documentoSalida, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository, PasaporteRepository $pasaporteRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, EntityManagerInterface $entityManager)
    {
        $tramites = $documentoSalidaTramiteRepository->findBy(['documentoSalida' => $documentoSalida], ['id' => 'asc']);
        $listo = true;
        $items = [];
        $ci = $documentoSalida->getPersona()->getCarnetIdentidad();
        $tipoPasaporte = $documentoSalida->getTipoPasaporte();
        $pasaporteHabilitado = $pasaporteRepository->findHabilitadoPorCiYTipo($ci, $tipoPasaporte) !== null;
        $pasaportePendiente = !$pasaporteHabilitado && $pasaporteRepository->findCualquieraPorCiYTipo($ci, $tipoPasaporte) !== null;
        foreach ($tramites as $t) {
            $esConfeccionPasaporte = $t->getTramite()->getId() == $this->getParameter('tramite_confeccion_pasaporte');
            $listoEfectivo = $t->getListo();
            // Si el pasaporte ya está habilitado, marcar automáticamente como listo en BD
            if ($esConfeccionPasaporte && $pasaporteHabilitado && !$t->getListo()) {
                $listoEfectivo = true;
                $t->setListo(true);
                $documentoSalidaTramiteRepository->edit($t, true);
            }
            if (!$listoEfectivo) $listo = false;
            $items[] = [
                'id' => $t->getId(),
                'nombre' => $t->getTramite()->getNombre(),
                'listo' => $listoEfectivo,
                'bloqueado' => $esConfeccionPasaporte && $pasaportePendiente && !$listoEfectivo
            ];
        }

        // Si todos los trámites están listos y el documento no está ya en estado listo, actualizarlo
        $estadoListoId = $this->getParameter('estado_salida_listo');
        $estadoActualId = $documentoSalida->getEstadoDocumentoSalida() ? $documentoSalida->getEstadoDocumentoSalida()->getId() : null;
        if ($listo && $estadoActualId != $estadoListoId) {
            $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($estadoListoId));
            $entityManager->persist($documentoSalida);
            $entityManager->flush();
        }

        return $this->json([
            'tramites' => $items,
            'listo' => $listo,
            'estado' => $documentoSalida->getEstadoDocumentoSalida() ? $documentoSalida->getEstadoDocumentoSalida()->getNombre() : '',
            'persona' => $documentoSalida->getPersona()->getPrimerNombre() . ' ' . $documentoSalida->getPersona()->getPrimerApellido()
        ]);
    }


    /**
     * @Route("/guardar_estado_tramite", name="app_documento_salida_aprobar_tramites_guardar", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function guardarFiltro(Request $request, EntityManagerInterface $entityManager, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, PasaporteRepository $pasaporteRepository)
    {
        try {
            $allPost = $request->request->All();
            $registro = $documentoSalidaTramiteRepository->find($allPost['id']);

            // Bloquear si es confección de pasaporte y el pasaporte aún no está habilitado
            if ((int)$allPost['valor'] === 1
                && $registro->getTramite()->getId() == $this->getParameter('tramite_confeccion_pasaporte')
            ) {
                $ci = $registro->getDocumentoSalida()->getPersona()->getCarnetIdentidad();
                $tipoPasaporte = $registro->getDocumentoSalida()->getTipoPasaporte();
                if (!$pasaporteRepository->findHabilitadoPorCiYTipo($ci, $tipoPasaporte)) {
                    return $this->json(['error' => 'El pasaporte de esta persona aún no está habilitado. No se puede marcar este trámite como completado.'], 422);
                }
            }

            $registro->setListo($allPost['valor']);
            $documentoSalidaTramiteRepository->edit($registro, true);

            $listo = true;
            $tramites = $documentoSalidaTramiteRepository->findBy(['documentoSalida' => $registro->getDocumentoSalida()->getId()], ['id' => 'asc']);
            foreach ($tramites as $value) {
                if (!$value->getListo()) {
                    $listo = false;
                    break;
                }
            }

            if ($listo) {
                $registro->getDocumentoSalida()->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_listo')));
            } else {
                $registro->getDocumentoSalida()->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_tramite')));
            }
            $entityManager->persist($registro->getDocumentoSalida());
            $entityManager->flush();

            //falta el cambio de estado del documento
            return $this->json('OK');
        } catch (\Exception $exception) {
            return $this->json(null);
        }
    }

    /**
     * @Route("/{id}/finalizar_salida", name="app_documento_salida_finalizar_salida", methods={"GET", "POST"})
     * @param Request $request
     * @param DocumentoSalida $documentoSalida
     * @param DocumentoSalidaRepository $documentoSalidaRepository
     * @param EstadoFichaSalidaRepository $estadoFichaSalidaRepository
     * @return Response
     */
    public function finalizarSalida(Request $request, DocumentoSalida $documentoSalida, DocumentoSalidaRepository $documentoSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        try {
            if ($request->isMethod('POST')) {
                $fechaRecogida = $request->request->get('fechaEntregaPasaporte');
                if (empty($fechaRecogida)) {
                    $this->addFlash('error', 'La fecha de recogida del pasaporte es obligatoria.');
                    return $this->redirectToRoute('app_documento_salida_index');
                }
                $temp = explode('/', $fechaRecogida);
                $documentoSalida->setFechaRecogidaPasaporte(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_informe_ejecutivo')));
                $documentoSalidaRepository->edit($documentoSalida, true);

                $this->addFlash('success', 'El trámite ha pasado a estado Informe Ejecutivo.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_documento_salida_index');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/adjuntar_informe_ejecutivo", name="app_documento_salida_adjuntar_informe", methods={"GET", "POST"})
     */
    public function adjuntarInformeEjecutivo(Request $request, DocumentoSalida $documentoSalida, DocumentoSalidaRepository $documentoSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        try {
            if ($request->isMethod('POST')) {
                /** @var UploadedFile|null $file */
                $file = $request->files->get('informeEjecutivo');

                if (!$file) {
                    $this->addFlash('error', 'El informe ejecutivo es requerido para finalizar la salida.');
                    return $this->redirectToRoute('app_documento_salida_index');
                }

                $ci = $documentoSalida->getPersona()->getCarnetIdentidad();
                $newFilename = sprintf('informe_ejecutivo_%d_%s.%s', $ci, uniqid(), $file->guessExtension() ?? 'pdf');

                $uploadDir = 'uploads/tramites/documento_salida/informe_ejecutivo';
                $file->move($uploadDir, $newFilename);
                $fullPath = $uploadDir . '/' . $newFilename;

                $this->logDocument($documentoSalida, "Informe Ejecutivo", $fullPath);

                $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_finalizada')));
                $documentoSalida->setFechaFinalizado(new \DateTime());
                $documentoSalidaRepository->edit($documentoSalida, true);

                $this->addFlash('success', 'El trámite ha sido finalizado satisfactoriamente.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_documento_salida_index');
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/viajando", name="app_documento_salida_viajando", methods={"GET"})
     * @param DocumentoSalida $documentoSalida
     * @param DocumentoSalidaRepository $documentoSalidaRepository
     * @param EstadoFichaSalidaRepository $estadoFichaSalidaRepository
     * @return Response
     */
    public function viajando(DocumentoSalida $documentoSalida, DocumentoSalidaRepository $documentoSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        try {
            if ($documentoSalidaRepository->find($documentoSalida) instanceof DocumentoSalida) {
                $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find(9));
                $documentoSalida->setFechaSalidaReal(new \DateTime());
                $documentoSalidaRepository->edit($documentoSalida, true);
                $this->addFlash('success', 'El elemento ha sido modificado satisfactoriamente.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/{id}/firmar", name="app_documento_salida_firmar", methods={"GET"})
     * @param Request $request
     * @param DocumentoSalida $documentoSalida
     * @param DocumentoSalidaRepository $documentoSalidaRepository
     * @return Response
     */
    public function firmar(Request $request, DocumentoSalida $documentoSalida, DocumentoSalidaRepository $documentoSalidaRepository)
    {
        try {
            if ($documentoSalidaRepository->find($documentoSalida) instanceof DocumentoSalida) {
                $documentoSalida->setFechaFirmaDirectivo(new \DateTime());
                $documentoSalida->setDirectivoFirma($this->getUser());
                $documentoSalidaRepository->edit($documentoSalida, true);
                $this->addFlash('success', 'El elemento ha sido firmado satisfactoriamente.');
                return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_documento_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
