<?php

namespace App\Controller\Tramite;

use App\Entity\Personal\Persona;
use App\Entity\Tramite\ConceptoSalida;
use App\Entity\Tramite\DocumentoSalida;
use App\Entity\Tramite\DocumentoSalidaTramite;
use App\Entity\Tramite\FichaSalida;
use App\Entity\Security\User;
use App\Entity\Tramite\FichaSalidaConceptoGasto;
use App\Entity\Tramite\FichaSalidaEstado;
use App\Entity\Tramite\Pasaporte;
use App\Form\Tramite\CambioEstadoSalidaType;
use App\Form\Tramite\ConceptoSalidaType;
use App\Form\Tramite\FichaSalidaType;
use App\Repository\Economia\ConceptoGastoRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Personal\ResponsableRepository;
use App\Repository\Tramite\DocumentoSalidaRepository;
use App\Repository\Tramite\DocumentoSalidaTramiteRepository;
use App\Repository\Tramite\EstadoFichaSalidaRepository;
use App\Repository\Tramite\FichaSalidaConceptoGastoRepository;
use App\Repository\Tramite\FichaSalidaEstadoEstadoRepository;
use App\Repository\Tramite\FichaSalidaEstadoRepository;
use App\Repository\Tramite\FichaSalidaRepository;
use App\Repository\Tramite\PasaporteRepository;
use App\Repository\Tramite\TramiteRepository;
use App\Services\PdfService;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Tramite\FichaSalidaHistorialDocumento;
use App\Entity\Tramite\DocumentoSalidaHistorialDocumento;
use App\Repository\Tramite\FichaSalidaHistorialDocumentoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_FICHA_SALIDA")
 */
#[Route('/tramite/ficha_salida')]
class FichaSalidaController extends AbstractController
{
    /**
     */
    #[Route('/{id}/historial-documentos', name: 'app_ficha_salida_historial_documentos', methods: ['GET'])]
    public function listDocumentos(Request $request, FichaSalida $fichaSalida, FichaSalidaHistorialDocumentoRepository $repo): JsonResponse
    {
        $host = $request->getSchemeAndHttpHost();
        $documentos = $repo->findBy(['fichaSalida' => $fichaSalida], ['fecha' => 'ASC']);
        $data = [];
        foreach ($documentos as $doc) {
            $path = $doc->getArchivo();
            $nombre = trim($doc->getNombre());

            if (strpos($path, '/') === false) {
                // Registro antiguo (solo nombre de archivo)
                // Usar comparación insensible a mayúsculas
                if (stripos($nombre, 'Carta') !== false) {
                    $folder = 'carta_invitacion/';
                } else {
                    $folder = 'ficha_salida/';
                }
                $path = '/uploads/tramites/ficha_salida/' . $folder . $path;
            } elseif (strpos($path, '/') !== 0) {
                $path = '/' . $path;
            }
            $data[] = [
                'nombre' => $nombre,
                'url' => $host . $path,
                'fecha' => $doc->getFecha()->format('d/m/Y H:i A'),
                'usuario' => $doc->getUsuario() ? $doc->getUsuario()->getUsername() : 'Sistema'
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @param fichaSalidaRepository $fichaSalidaRepository
     * @return Response
     */
    #[Route('/', name: 'app_ficha_salida_index', methods: ['GET', 'POST'])]
    public function index(Request $request, FichaSalidaRepository $fichaSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        $allPost = $request->request->all();

        if (isset($allPost['estado']) && !empty($allPost['estado'])) {
            $request->getSession()->set('ficha_salida_fil_estado', $allPost['estado']);
            $request->getSession()->set('ficha_salida_text_fil_estado', $estadoFichaSalidaRepository->find($allPost['estado'])->getNombre());
        }
        if (isset($allPost['estado']) && empty($allPost['estado'])) {
            $request->getSession()->remove('ficha_salida_fil_estado');
            $request->getSession()->remove('ficha_salida_text_fil_estado');
        }
        if (isset($allPost['clasificacion']) && !empty($allPost['clasificacion'])) {
            $request->getSession()->set('ficha_salida_fil_clasificacion', $allPost['clasificacion']);
        }
        if (isset($allPost['clasificacion']) && empty($allPost['clasificacion'])) {
            $request->getSession()->remove('ficha_salida_fil_clasificacion');
        }
        $filtros = [];
        if ($request->getSession()->has('ficha_salida_fil_estado')) {
            $filtros['estadoFichaSalida'] = $request->getSession()->get('ficha_salida_fil_estado');
        }
        if ($request->getSession()->has('ficha_salida_fil_clasificacion')) {
            $filtros['clasificacion'] = $request->getSession()->get('ficha_salida_fil_clasificacion');
        }
        return $this->render('modules/tramite/ficha_salida/index.html.twig', [
            'registros' => $fichaSalidaRepository->findBy($filtros, ['id' => 'desc']),
            'estados' => $estadoFichaSalidaRepository->findBy(['activo' => true, 'documentoSalida' => false], ['nombre' => 'asc']),
            'fil_estado' => $request->getSession()->get('ficha_salida_fil_estado'),
            'fil_clasificacion' => $request->getSession()->get('ficha_salida_fil_clasificacion'),
            'text_fil' => $request->getSession()->has('ficha_salida_text_fil_estado') ? " (Estado=" . $request->getSession()->get('ficha_salida_text_fil_estado') . ")" : null,
        ]);
    }

    /**
     * @param Request $request
     * @param PersonaRepository $personaRepository
     * @param ResponsableRepository $responsableRepository
     * @param Utils $utils
     * @return Response
     */
    #[Route('/registrar', name: 'app_ficha_salida_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, ResponsableRepository $responsableRepository, Utils $utils)
    {
        try {
            $allPost = $request->isMethod('POST') ? $request->request->all() : $request->query->all();
            $busqueda = $allPost['busqueda'] ?? null;
            $limit = 10;
            $offset = (int)($allPost['offset'] ?? 0);
            $registros = [];

            if (!empty($busqueda)) {
                $registros = $personaRepository->searchByFields($busqueda, $limit, $offset);
            }

            if ($request->isXmlHttpRequest()) {
                return $this->render('modules/tramite/ficha_salida/_results_list.html.twig', [
                    'registros' => $registros,
                ]);
            }

            return $this->render('modules/tramite/ficha_salida/new.html.twig', [
                'registros' => $registros,
                'busqueda' => $busqueda,
                'limit' => $limit
            ]);
        } catch (\Exception $exception) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['error' => $exception->getMessage()], 500);
            }
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_ficha_salida_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Persona $persona
     * @return Response
     */
    #[Route('/{id}/registrar-v2', name: 'app_ficha_salida_registrar_v2', methods: ['GET', 'POST'])]
    public function registrarV2(Request $request, PdfService $pdfService, DocumentoSalidaRepository $documentoSalidaRepository, PasaporteRepository $pasaporteRepository, Persona $persona, ConceptoGastoRepository $conceptoGastoRepository, FichaSalidaConceptoGastoRepository $fichaSalidaConceptoGastoRepository, FichaSalidaRepository $fichaSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, FichaSalidaEstadoRepository $fichaSalidaEstadoRepository)
    {
//        try {
        $entidad = new FichaSalida();
        $form = $this->createForm(FichaSalidaType::class, $entidad, ['action' => 'registrar-v2']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $allPost = $request->request->all();
            $entidad->setPersona($persona);

            if (!empty($request->request->all()['ficha_salida']['fechaSalidaPrevista'])) {
                $entidad->setFechaSalidaPrevista(\DateTime::createFromFormat('d/m/Y', $request->request->all()['ficha_salida']['fechaSalidaPrevista']));
            }
            if (!empty($request->request->all()['ficha_salida']['fechaRegresoPrevista'])) {
                $entidad->setFechaRegresoPrevista(\DateTime::createFromFormat('d/m/Y', $request->request->all()['ficha_salida']['fechaRegresoPrevista']));
            }
            if (!empty($request->request->all()['ficha_salida']['fechaEmisionPasaporte'])) {
                $entidad->setFechaEmisionPasaporte(\DateTime::createFromFormat('d/m/Y', $request->request->all()['ficha_salida']['fechaEmisionPasaporte']));
            }
            if (!empty($request->request->all()['ficha_salida']['fechaCaducidadPasaporte'])) {
                $entidad->setFechaCaducidadPasaporte(\DateTime::createFromFormat('d/m/Y', $request->request->all()['ficha_salida']['fechaCaducidadPasaporte']));
            }
            $estadoFicha = $estadoFichaSalidaRepository->find($this->getParameter('estado_salida_creada'));
            $entidad->setEstadoFichaSalida($estadoFicha);

            $invitationFile = null;
            if (!empty($_FILES['ficha_salida']['name']['cartaInvitacion'])) {
                $file = $form['cartaInvitacion']->getData();
                $invitationFile = $_FILES['ficha_salida']['name']['cartaInvitacion'];
                $entidad->setCartaInvitacion($invitationFile);
                $file->move("uploads/tramites/ficha_salida/carta_invitacion", $invitationFile);
            }

            // Después de guardar $entidad en BD:
            $responsabilidad = $persona->getResponsabilidad();
            $cargoNombre = $responsabilidad ? $responsabilidad->getNombre() : 'No definido';

            $institucion = $entidad->getInstitucionCubana();
            $institucionNombre = $institucion ? $institucion->getNombre() : 'No definido';
            $fechaSalida = $entidad->getFechaSalidaPrevista();
            $fechaRegreso = $entidad->getFechaRegresoPrevista();

            // Salidas anteriores en el año (tabla dinámica)
            $salidasAnterioresRaw = $documentoSalidaRepository->findAnterioresEnAño($persona, [
                $this->getParameter('estado_salida_viajando'),
                $this->getParameter('estado_salida_finalizada')
            ]);

            $salidasAnteriores = [];
            foreach ($salidasAnterioresRaw as $salida) {
                $salidasAnteriores[] = [
                    'pais' => $salida->getPais() ? $salida->getPais()->getNombre() : 'N/D',
                    'estancia' => $salida->getTiempoEstancia() . ' días',
                    'desdeHasta' => ($salida->getFechaSalidaPrevista() ? $salida->getFechaSalidaPrevista()->format('d/m/Y') : '?') .
                        ' al ' .
                        ($salida->getFechaRegresoPrevista() ? $salida->getFechaRegresoPrevista()->format('d/m/Y') : '?'),
                    'resultados' => $salida->getObjetivo()
                ];
            }

            $data = [
                // Cabecera
                'fechaSolicitud' => (new \DateTime())->format('d/m/Y'), // o la que tengas en BD
                'pasaporte' => $entidad->getNumeroPasaporte(),

                // Persona
                'persona' => trim(
                    $persona->getPrimerNombre() . ' ' .
                    $persona->getSegundoNombre() . ' ' .
                    $persona->getPrimerApellido() . ' ' .
                    $persona->getSegundoApellido()
                ),
                'cargo' => $cargoNombre,
                'institucionCubana' => $institucionNombre,
                'facultad' => $persona->getEstructura() ? $persona->getEstructura()->getNombre() : '',
                'pais' => $entidad->getPais()->getNombre(),
                'lugares' => $entidad->getProvinciaExtranjera() . ($entidad->getInstitucionExtranjera() ? ', ' . $entidad->getInstitucionExtranjera()->getNombre() : ''),

                // Fechas de viaje
                'fechaSalida' => $fechaSalida
                    ? $fechaSalida->format('d/m/Y')
                    : null,
                'fechaRegreso' => $fechaRegreso
                    ? $fechaRegreso->format('d/m/Y')
                    : null,
                'tiempoDuracion' => ($fechaSalida && $fechaRegreso)
                    ? $fechaSalida->diff($fechaRegreso)->days
                    : '',
                // Objetivos
                'objetivo' => $entidad->getObjetivo(),
                'mandatosMision' => $entidad->getMandatosMision(),

                // Profesor invitado (checkbox del adjunto)
                'profesorInvitado' => true, // true = SI, false = NO

                // Fuente de financiamiento
                'fuenteFinanciamiento' => [
                    'estancia' => '7 días',
                ],

                // Conceptos de gasto con responsable
                'gastos' => (function () use ($allPost, $request, $conceptoGastoRepository) {
                    $gastosData = [];
                    foreach ($allPost['ficha_salida']['conceptoGasto'] ?? [] as $cgId) {
                        $cg = $conceptoGastoRepository->find($cgId);
                        if ($cg) {
                            $asumido = $request->request->get('gasto_asume_' . $cgId) ?: null;
                            $monto = ($asumido === 'institucion') ? (float)$request->request->get('gasto_monto_' . $cgId) : null;
                            $gastosData[] = [
                                'nombre' => $cg->getNombre(),
                                'asumidoPor' => $asumido,
                                'monto' => $monto
                            ];
                        }
                    }
                    return $gastosData;
                })(),

                // Salidas anteriores en el año (tabla)
                'salidasAnteriores' => $salidasAnteriores,

                // Firmas y observaciones
                'firma' => '',
                'vistoBueno' => '',
                'observaciones' => '',
                'decisionRT' => ''
            ];
            $ci = $persona->getCarnetIdentidad();
            $pdfPath = "uploads/tramites/ficha_salida/ficha_salida/ficha_salida_" . $ci . "_" . uniqid() . ".pdf";

            $pdfService->generateFichaSalidaPdf($data, $pdfPath);

            $entidad->setFichaSalida($pdfPath);

            $fichaSalidaRepository->add($entidad, true);

            // Si no tiene número de pasaporte y sí tiene tipo, crear solicitud solo si no existe ningún registro
            $tipoPasaporte = $entidad->getTipoPasaporte();
            if (empty($entidad->getNumeroPasaporte()) && $tipoPasaporte !== null) {
                $ci = $persona->getCarnetIdentidad();
                if (!$pasaporteRepository->findCualquieraPorCiYTipo($ci, $tipoPasaporte)) {
                    $solicitudPasaporte = new Pasaporte();
                    $solicitudPasaporte->setPersona($persona);
                    $solicitudPasaporte->setTipoPasaporte($tipoPasaporte);
                    $pasaporteRepository->add($solicitudPasaporte, true);
                }
            }

            // Registrar historial después de persistir la ficha
            if ($invitationFile) {
                $this->logDocument($entidad, "Carta de invitación", 'uploads/tramites/ficha_salida/carta_invitacion/' . $invitationFile);
            }
            $this->logDocument($entidad, "Ficha Generada", $pdfPath);

            $fichaSalidaEstado = new FichaSalidaEstado();
            $fichaSalidaEstado->setFichaSalida($entidad);
            $fichaSalidaEstado->setEstadoFichaSalida($estadoFicha);
            $fichaSalidaEstado->setDescripcion('Creada');
            $fichaSalidaEstadoRepository->add($fichaSalidaEstado, true);

            foreach ($allPost['ficha_salida']['conceptoGasto'] as $value) {
                $conceptoGasto = new FichaSalidaConceptoGasto();
                $conceptoGasto->setFichaSalida($entidad);
                $conceptoGasto->setConceptoGasto($conceptoGastoRepository->find($value));
                $asumido = $request->request->get('gasto_asume_' . $value) ?: null;
                $conceptoGasto->setAsumidoPor($asumido);
                if ($asumido === 'institucion') {
                    $conceptoGasto->setMonto((float)$request->request->get('gasto_monto_' . $value));
                }
                $fichaSalidaConceptoGastoRepository->add($conceptoGasto, true);
            }

            $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
            return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modules/tramite/ficha_salida/newV2.html.twig', [
            'form' => $form->createView(),
            'persona' => $persona
        ]);
//        } catch (\Exception $exception) {
//            $this->addFlash('error', $exception->getMessage());
//            return $this->redirectToRoute('app_ficha_salida_registrar', [], Response::HTTP_SEE_OTHER);
//        }
    }


    /**
     * @param Request $request
     * @param FichaSalida $fichaSalida
     * @param fichaSalidaRepository $fichaSalidaRepository
     * @param ConceptoGastoRepository $conceptoGastoRepository
     * @param FichaSalidaConceptoGastoRepository $fichaSalidaConceptoGastoRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_ficha_salida_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, FichaSalida $fichaSalida, FichaSalidaRepository $fichaSalidaRepository, ConceptoGastoRepository $conceptoGastoRepository, FichaSalidaConceptoGastoRepository $fichaSalidaConceptoGastoRepository)
    {
        try {
            $form = $this->createForm(FichaSalidaType::class, $fichaSalida, ['action' => 'modificar']);
            $form->handleRequest($request);
            $allPost = $request->request->all();
            $conceptoGastosAsociados = $fichaSalidaConceptoGastoRepository->findBy(['fichaSalida' => $fichaSalida->getId()]);

            if ($form->isSubmitted()/* && $form->isValid()*/) {
                $temp = explode('/', $request->request->all()['ficha_salida']['fechaSalidaPrevista']);
                $fichaSalida->setFechaSalidaPrevista(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $temp = explode('/', $request->request->all()['ficha_salida']['fechaRegresoPrevista']);
                $fichaSalida->setFechaRegresoPrevista(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $temp = explode('/', $request->request->all()['ficha_salida']['fechaEmisionPasaporte']);
                $fichaSalida->setFechaEmisionPasaporte(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $temp = explode('/', $request->request->all()['ficha_salida']['fechaCaducidadPasaporte']);
                $fichaSalida->setFechaCaducidadPasaporte(new \DateTime($temp[2] . '/' . $temp[1] . '/' . $temp[0]));

                $invitationFile = null;
                if (!empty($_FILES['ficha_salida']['name']['cartaInvitacion'])) {
                    $file = $form['cartaInvitacion']->getData();
                    $invitationFile = pathinfo($_FILES['ficha_salida']['name']['cartaInvitacion'], PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->guessExtension();
                    $fichaSalida->setCartaInvitacion($invitationFile);
                    $file->move("uploads/tramites/ficha_salida/carta_invitacion", $invitationFile);
                }

                foreach ($conceptoGastosAsociados as $value) {
                    $fichaSalidaConceptoGastoRepository->remove($value, true);
                }

                foreach ($allPost['ficha_salida']['conceptoGasto'] as $value) {
                    $conceptoGasto = new FichaSalidaConceptoGasto();
                    $conceptoGasto->setFichaSalida($fichaSalida);
                    $conceptoGasto->setConceptoGasto($conceptoGastoRepository->find($value));
                    $asumido = $request->request->get('gasto_asume_' . $value) ?: null;
                    $conceptoGasto->setAsumidoPor($asumido);
                    if ($asumido === 'institucion') {
                        $conceptoGasto->setMonto((float)$request->request->get('gasto_monto_' . $value));
                    }
                    $fichaSalidaConceptoGastoRepository->add($conceptoGasto, true);
                }

                if ($invitationFile) {
                    $this->logDocument($fichaSalida, "Carta de invitación (Modificada)", 'uploads/tramites/ficha_salida/carta_invitacion/' . $invitationFile);
                }
                $fichaSalidaRepository->edit($fichaSalida);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            $conceptos = [];
            $conceptosDetalle = [];
            foreach ($conceptoGastosAsociados as $value) {
                $conceptos[] = $value->getConceptoGasto()->getId();
                $conceptosDetalle[] = [
                    'id' => $value->getConceptoGasto()->getId(),
                    'nombre' => $value->getConceptoGasto()->getNombre(),
                    'asumidoPor' => $value->getAsumidoPor(),
                    'monto' => $value->getMonto()
                ];
            }
            return $this->render('modules/tramite/ficha_salida/edit.html.twig', [
                'form' => $form->createView(),
                'fichaSalida' => $fichaSalida,
                'persona' => $fichaSalida->getPersona(),
                'conceptoGastosAsociados' => json_encode($conceptos),
                'conceptoGastosDetalle' => json_encode($conceptosDetalle)
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_ficha_salida_modificar', ['id' => $fichaSalida], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param fichaSalida $fichaSalida
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_ficha_salida_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, FichaSalida $fichaSalida)
    {
        return $this->render('modules/tramite/ficha_salida/detail.html.twig', [
            'item' => $fichaSalida,
        ]);
    }

    /**
     * @param Request $request
     * @param fichaSalida $fichaSalida
     * @param fichaSalidaRepository $fichaSalidaRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_ficha_salida_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, FichaSalida $fichaSalida, FichaSalidaRepository $fichaSalidaRepository)
    {
        try {
            if ($fichaSalidaRepository->find($fichaSalida) instanceof FichaSalida) {
                $fichaSalidaRepository->remove($fichaSalida, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param fichaSalida $fichaSalida
     * @param fichaSalidaRepository $fichaSalidaRepository
     * @return Response
     */
    #[Route('/{id}/firmar', name: 'app_ficha_salida_firmar', methods: ['GET','POST'])]

    public function firmar(
        Request               $request,
        FichaSalida           $fichaSalida,
        FichaSalidaRepository $fichaSalidaRepository
    ): Response
    {
        try {
            /** @var UploadedFile|null $file */
            $file = $request->files->get('file');

            if ($file instanceof UploadedFile) {
                // Nombre único para evitar colisiones y mantener historial
                $newFilename = sprintf(
                    'ficha_salida_%d_%s.%s',
                    $fichaSalida->getPersona()->getCarnetIdentidad(),
                    uniqid(),
                    $file->guessExtension() ?? $file->getClientOriginalExtension()
                );

                // Mover el archivo a la carpeta definida
                $fullPath = 'uploads/tramites/ficha_salida/ficha_salida/' . $newFilename;
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/tramites/ficha_salida/ficha_salida',
                    $newFilename
                );

                // Guardar nombre en la entidad
                $fichaSalida->setFichaSalida($newFilename);
                $this->logDocument($fichaSalida, "Ficha Firmada", $fullPath);
            }

            // Actualizar datos de firma
            $fichaSalida->setFechaFirmaDirectivo(new \DateTime());
            $fichaSalida->setDirectivoFirma($this->getUser());

            $fichaSalidaRepository->edit($fichaSalida, true);

            $this->addFlash('success', 'El elemento ha sido firmado satisfactoriamente.');

            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => true, 'message' => 'Documento firmado correctamente.']);
            }
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => false, 'message' => $exception->getMessage()], 500);
            }
        }

        return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
    }

    /*Variante a trabajar */
    /**
     */
    #[Route('/{id}/firmarV2', name: 'app_ficha_salida_firmarV2', methods: ['GET','POST'])]
    public function firmarV2(Request $request, FichaSalida $fichaSalida, FichaSalidaRepository $fichaSalidaRepository): Response
    {
        try {
            if (!$fichaSalidaRepository->find($fichaSalida)) {
                $this->addFlash('error', 'Error en la entrada de datos');
                return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
            }

            // Revisar si hay archivo adjunto
            $uploadedFile = $request->files->get('file');
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                // Mover archivo a la carpeta unificada
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/tramites/ficha_salida/ficha_salida';
                $uploadedFile->move($uploadDir, $newFilename);

                // Guardar ruta del archivo en la entidad y HISTORIAL
                $fullPath = 'uploads/tramites/ficha_salida/ficha_salida/' . $newFilename;
                $fichaSalida->setDocumentoFirmado($fullPath);
                $this->logDocument($fichaSalida, "Ficha Firmada (V2)", $fullPath);
            }

            // Marcar como firmado
            $fichaSalida->setFechaFirmaDirectivo(new \DateTime());
            $fichaSalida->setDirectivoFirma($this->getUser());

            $fichaSalidaRepository->edit($fichaSalida, true);

            $this->addFlash('success', 'El elemento ha sido firmado satisfactoriamente.');

            // Si es AJAX, podemos devolver JSON
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'status' => 'success',
                    'message' => 'Documento enviado y firmado correctamente'
                ]);
            }

            return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);

        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 500);
            }
            return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     */
    #[Route('/{id}/firmar-dri', name: 'app_ficha_salida_firmar_dri', methods: ['POST'])]
    public function firmarDRI(Request $request, PasaporteRepository $pasaporteRepository, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository, TramiteRepository $tramiteRepository, DocumentoSalidaRepository $documentoSalidaRepository, FichaSalida $fichaSalida, FichaSalidaRepository $fichaSalidaRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, FichaSalidaEstadoRepository $fichaSalidaEstadoRepository): JsonResponse
    {
        try {
            $uploadedFile = $request->files->get('file');
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/tramites/ficha_salida/ficha_salida';
                $uploadedFile->move($uploadDir, $newFilename);

                $fullPath = 'uploads/tramites/ficha_salida/ficha_salida/' . $newFilename;
                $this->logDocument($fichaSalida, "Ficha Firmada DRI", $fullPath);
            }

            // Cambiar estado a 3 (Firmada DRI)
            $nuevoEstado = $estadoFichaSalidaRepository->find(3);
            if ($nuevoEstado) {
                $fichaSalida->setEstadoFichaSalida($nuevoEstado);

                $historial = new FichaSalidaEstado();
                $historial->setFichaSalida($fichaSalida);
                $historial->setEstadoFichaSalida($nuevoEstado);
                $historial->setDescripcion("Ficha firmada por DRI");
                $fichaSalidaEstadoRepository->edit($historial);

                if ($fichaSalida->getEstadoFichaSalida()->getId() == $this->getParameter('estado_salida_aprobada_rd')) {//Aprobada por RD
                    $documentoSalida = new DocumentoSalida();
                    $documentoSalida->setFichaSalida($fichaSalida);
                    $documentoSalida->setConceptoSalida($fichaSalida->getConceptoSalida());
                    $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_tramite')));
                    $documentoSalida->setPais($fichaSalida->getPais());
                    $documentoSalida->setCartaInvitacion($fichaSalida->getCartaInvitacion());
                    $pasaporte = $fichaSalida->getNumeroPasaporte();
                    $documentoSalida->setNumeroPasaporte($pasaporte);
                    $documentoSalida->setTipoPasaporte($fichaSalida->getTipoPasaporte());
                    $documentoSalida->setFechaCaducidadPasaporte($fichaSalida->getFechaCaducidadPasaporte());
                    $documentoSalida->setFechaEmisionPasaporte($fichaSalida->getFechaEmisionPasaporte());
                    $documentoSalida->setFechaSalidaPrevista($fichaSalida->getFechaSalidaPrevista());
                    $documentoSalida->setFechaRegresoPrevista($fichaSalida->getFechaRegresoPrevista());
                    $documentoSalida->setObjetivo($fichaSalida->getObjetivo());
                    $documentoSalida->setMandatosMision($fichaSalida->getMandatosMision());
                    $documentoSalida->setInstitucionCubana($fichaSalida->getInstitucionCubana());
                    $documentoSalida->setInstitucionExtranjera($fichaSalida->getInstitucionExtranjera());
                    $documentoSalida->setTiempoEstancia($fichaSalida->getTiempoEstancia());
                    $documentoSalida->setPersona($fichaSalida->getPersona());
                    $documentoSalidaRepository->add($documentoSalida, true);


                    $tipoPasaporte = $documentoSalida->getTipoPasaporte();
                    $ci = $documentoSalida->getPersona()->getCarnetIdentidad();
                    if (empty($pasaporte) && $tipoPasaporte !== null && !$pasaporteRepository->findHabilitadoPorCiYTipo($ci, $tipoPasaporte)) {
                        $tramites = new DocumentoSalidaTramite();
                        $tramites->setDocumentoSalida($documentoSalida);
                        $tramites->setTramite($tramiteRepository->find($this->getParameter('tramite_confeccion_pasaporte')));
                        $documentoSalidaTramiteRepository->add($tramites, true);

                        if (!$pasaporteRepository->findPendientePorCiYTipo($ci, $tipoPasaporte)) {
                            $solicitudPasaporte = new Pasaporte();
                            $solicitudPasaporte->setPersona($documentoSalida->getPersona());
                            $solicitudPasaporte->setTipoPasaporte($tipoPasaporte);
                            $pasaporteRepository->add($solicitudPasaporte, true);
                        }
                    }
                }
            }

            $fichaSalidaRepository->edit($fichaSalida, true);

            return $this->json([
                'success' => true,
                'message' => 'Ficha firmada por DRI y actualizada correctamente'
            ]);

        } catch (\Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    /**
     * @param Request $request
     * @param FichaSalida $fichaSalida
     * @param FichaSalidaEstadoRepository $fichaSalidaEstadoRepository
     * @return Response
     */
    #[Route('/{id}/cambiar_estado', name: 'app_ficha_salida_cambiar_estado', methods: ['GET', 'POST'])]
    public function cambiarEstado(Request $request, FichaSalida $fichaSalida, PasaporteRepository $pasaporteRepository, DocumentoSalidaTramiteRepository $documentoSalidaTramiteRepository, TramiteRepository $tramiteRepository, EstadoFichaSalidaRepository $estadoFichaSalidaRepository, DocumentoSalidaRepository $documentoSalidaRepository, FichaSalidaEstadoRepository $fichaSalidaEstadoRepository, FichaSalidaRepository $fichaSalidaRepository)
    {
        try {
            $entidad = new FichaSalidaEstado();
            $estadoFinal = [1];
            $estadoActual = $fichaSalida->getEstadoFichaSalida()->getId();
            if ($estadoActual == 1) {
                $estadoFinal = [2, 9, 10];
            }
            if ($estadoActual == 2) {
                $estadoFinal = [3];
            }
            $form = $this->createForm(CambioEstadoSalidaType::class, $entidad, ['action' => 'modificar', 'estadoActual' => $estadoActual, 'estadoFinal' => $estadoFinal]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entidad->setFichaSalida($fichaSalida);
                $fichaSalidaEstadoRepository->edit($entidad);

                $fichaSalida->setEstadoFichaSalida($entidad->getEstadoFichaSalida());
                $fichaSalidaRepository->edit($fichaSalida, true);

                if ($entidad->getEstadoFichaSalida()->getId() == $this->getParameter('estado_salida_aprobada_rd')) {//Aprobada por RD
                    $documentoSalida = new DocumentoSalida();
                    $documentoSalida->setFichaSalida($fichaSalida);
                    $documentoSalida->setConceptoSalida($fichaSalida->getConceptoSalida());
                    $documentoSalida->setEstadoDocumentoSalida($estadoFichaSalidaRepository->find($this->getParameter('estado_salida_tramite')));
                    $documentoSalida->setPais($fichaSalida->getPais());
                    $documentoSalida->setCartaInvitacion($fichaSalida->getCartaInvitacion());
                    $pasaporte = $fichaSalida->getNumeroPasaporte();
                    $documentoSalida->setNumeroPasaporte($pasaporte);
                    $documentoSalida->setTipoPasaporte($fichaSalida->getTipoPasaporte());
                    $documentoSalida->setFechaCaducidadPasaporte($fichaSalida->getFechaCaducidadPasaporte());
                    $documentoSalida->setFechaEmisionPasaporte($fichaSalida->getFechaEmisionPasaporte());
                    $documentoSalida->setFechaSalidaPrevista($fichaSalida->getFechaSalidaPrevista());
                    $documentoSalida->setFechaRegresoPrevista($fichaSalida->getFechaRegresoPrevista());
                    $documentoSalida->setObjetivo($fichaSalida->getObjetivo());
                    $documentoSalida->setMandatosMision($fichaSalida->getMandatosMision());
                    $documentoSalida->setInstitucionCubana($fichaSalida->getInstitucionCubana());
                    $documentoSalida->setInstitucionExtranjera($fichaSalida->getInstitucionExtranjera());
                    $documentoSalida->setTiempoEstancia($fichaSalida->getTiempoEstancia());
                    $documentoSalida->setPersona($fichaSalida->getPersona());
                    $documentoSalidaRepository->add($documentoSalida, true);


                    $tipoPasaporte = $documentoSalida->getTipoPasaporte();
                    $ci = $documentoSalida->getPersona()->getCarnetIdentidad();
                    if (empty($pasaporte) && $tipoPasaporte !== null && !$pasaporteRepository->findHabilitadoPorCiYTipo($ci, $tipoPasaporte)) {
                        $tramites = new DocumentoSalidaTramite();
                        $tramites->setDocumentoSalida($documentoSalida);
                        $tramites->setTramite($tramiteRepository->find($this->getParameter('tramite_confeccion_pasaporte')));
                        $documentoSalidaTramiteRepository->add($tramites, true);

                        if (!$pasaporteRepository->findPendientePorCiYTipo($ci, $tipoPasaporte)) {
                            $solicitudPasaporte = new Pasaporte();
                            $solicitudPasaporte->setPersona($documentoSalida->getPersona());
                            $solicitudPasaporte->setTipoPasaporte($tipoPasaporte);
                            $pasaporteRepository->add($solicitudPasaporte, true);
                        }
                    }
                }
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_ficha_salida_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/tramite/ficha_salida/cambioEstado.html.twig', [
                'form' => $form->createView(),
                'fichaSalida' => $fichaSalida,
                'persona' => $fichaSalida->getPersona()
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_ficha_salida_index', ['id' => $fichaSalida->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     */
    #[Route('/{id}/consulta_embajada', name: 'app_ficha_salida_consulta_embajada', methods: ['POST'])]
    public function consultaEmbajada(Request $request, FichaSalida $fichaSalida, FichaSalidaRepository $fichaSalidaRepository): JsonResponse
    {
        $valor = $request->request->get('consulta_embajada');
        if ($valor === null || !in_array($valor, ['1', '0'], true)) {
            return $this->json(['success' => false, 'message' => 'Valor inválido.'], 400);
        }
        $fichaSalida->setConsultaEmbajada((bool)(int)$valor);
        $fichaSalidaRepository->edit($fichaSalida, true);
        return $this->json(['success' => true, 'message' => 'Guardado correctamente.']);
    }

    /**
     */
    #[Route('/{id}/datos_cambiar_estado', name: 'app_ficha_salida_datos_cambiar_estado', methods: ['GET'])]
    public function datosCambiarEstado(FichaSalida $fichaSalida, EstadoFichaSalidaRepository $estadoFichaSalidaRepository)
    {
        $estadoFinal = [1];
        $estadoActual = $fichaSalida->getEstadoFichaSalida()->getId();
        if ($estadoActual == 1) {
            $estadoFinal = [2, 9, 10];
        }
        if ($estadoActual == 2) {
            $estadoFinal = [3];
        }

        $estados = [];
        foreach ($estadoFinal as $id) {
            $est = $estadoFichaSalidaRepository->find($id);
            if ($est) {
                $estados[] = ['id' => $est->getId(), 'nombre' => $est->getNombre()];
            }
        }

        $persona = $fichaSalida->getPersona();
        return $this->json([
            'persona' => $persona->getPrimerNombre() . ' ' . $persona->getPrimerApellido(),
            'estadoActual' => $fichaSalida->getEstadoFichaSalida()->getNombre(),
            'estados' => $estados
        ]);
    }

    private function logDocument(FichaSalida $fichaSalida, string $nombre, string $archivo): void
    {
        $historial = new FichaSalidaHistorialDocumento();
        $historial->setFichaSalida($fichaSalida);
        $historial->setNombre($nombre);
        $historial->setArchivo($archivo);
        $historial->setUsuario($this->getUser());
        $historial->setFecha(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($historial);
        $em->flush();
    }

    private function logDocumentoSalida(DocumentoSalida $documentoSalida, string $nombre, string $archivo): void
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
}
