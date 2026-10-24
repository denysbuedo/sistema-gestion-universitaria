<?php

namespace App\Controller\Pregrado;

use App\Entity\Institucion\Institucion;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion;
use App\Entity\Security\User;
use App\Export\Pregrado\ExportListSolicitudProgramaAcademicoToPdf;
use App\Form\Pregrado\AprobarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\NoAprobarSolicitudProgramaAcademicoType;
use App\Form\Pregrado\SolicitudProgramaAcademicoType;
use App\Repository\Pregrado\EstadoProgramaAcademicoRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use App\Services\DoctrineHelper;
use App\Services\Utils;
use App\Services\UploadedDocumentStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG') or is_granted('ROLE_PREGRADO_MIS_PROGRAMAS')"))]
#[Route('/pregrado/solicitud_programa_academico')]
class SolicitudProgramaAcademicoController extends AbstractController
{

    /**
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/', name: 'app_solicitud_programa_academico_index', methods: ['GET'])]
    public function index(SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, Utils $utils, EntityManagerInterface $em)
    {
        $global = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_GEST_SOLPROG');
        $institucion = $global ? null : $this->institucionUsuario($utils, $em);
        if (!$global && !$institucion) {
            throw $this->createAccessDeniedException('Su usuario no está asociado a una universidad.');
        }
        $registros = $solicitudProgramaRepository->getSolicitudProgramaAcademico([1, 3, 5], $institucion?->getId());
        return $this->render('modules/pregrado/solicitud_programa_academico/index.html.twig', [
            'registros' => $registros,
            'accesoGlobal' => $global,
        ]);
    }

    /**
     * @param Request $request
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_solicitud_programa_academico_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, Utils $utils, SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, EstadoProgramaAcademicoRepository $estadoProgramaAcademicoRepository, UploadedDocumentStorage $storage, EntityManagerInterface $em)
    {
        try {
            $solicitudProgramaAcademico = new SolicitudProgramaAcademico();
            $global = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_GEST_SOLPROG');
            $institucion = $global ? null : $this->institucionUsuario($utils, $em);
            if (!$global && !$institucion) {
                throw $this->createAccessDeniedException('Su usuario no está asociado a una universidad.');
            }
            if ($institucion) {
                $solicitudProgramaAcademico->setCentroSolicitante($institucion);
            }
            $form = $this->createForm(SolicitudProgramaAcademicoType::class, $solicitudProgramaAcademico, ['action' => 'registrar', 'institucion_fija' => $institucion !== null]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                if (($file = $form['solicitud']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $solicitudProgramaAcademico->setSolicitud($storage->storeName($file, 'uploads/pregrado/solicitud_programa_academico/solicitud'));
                }
                $solicitudProgramaAcademico->setEstadoProgramaAcademico($estadoProgramaAcademicoRepository->find(1));//Solicitado
                $solicitudProgramaRepository->add($solicitudProgramaAcademico, true);

                $utils->guardarHistoricoEstadoProgramaAcademico($solicitudProgramaAcademico->getId(), 1);

                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/solicitud_programa_academico/new.html.twig', [
                'form' => $form->createView()
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_solicitud_programa_academico_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param SolicitudProgramaAcademico $solicitudPrograma
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_solicitud_programa_academico_modificar', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG')"))]
    public function modificar(Request $request, SolicitudProgramaAcademico $solicitudPrograma, SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, UploadedDocumentStorage $storage)
    {
        try {
            $form = $this->createForm(SolicitudProgramaAcademicoType::class, $solicitudPrograma, ['action' => 'modificar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if (($file = $form['solicitud']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $solicitudPrograma->setSolicitud($storage->replace($file, 'uploads/pregrado/solicitud_programa_academico/solicitud', $solicitudPrograma->getSolicitud()));
                }
                $solicitudProgramaRepository->edit($solicitudPrograma);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/solicitud_programa_academico/edit.html.twig', [
                'form' => $form->createView(),
                'solicitudProgramaAcademico' => $solicitudPrograma
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_solicitud_programa_academico_modificar', ['id' => $solicitudPrograma], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param SolicitudProgramaAcademico $solicitudProgramaAcademico
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_solicitud_programa_academico_detail', methods: ['GET', 'POST'])]
    public function detail(SolicitudProgramaAcademico $solicitudProgramaAcademico, Utils $utils, EntityManagerInterface $em)
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_GEST_SOLPROG')) {
            $institucion = $this->institucionUsuario($utils, $em);
            if (!$institucion || $solicitudProgramaAcademico->getCentroSolicitante()?->getId() !== $institucion->getId()) {
                throw $this->createAccessDeniedException('No puede consultar solicitudes de otra universidad.');
            }
        }
        return $this->render('modules/pregrado/solicitud_programa_academico/detail.html.twig', [
            'item' => $solicitudProgramaAcademico,
        ]);
    }

    /**
     * @param SolicitudProgramaAcademico $solicitudPrograma
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_solicitud_programa_academico_eliminar', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG')"))]
    public function eliminar(SolicitudProgramaAcademico $solicitudPrograma, SolicitudProgramaAcademicoRepository $solicitudProgramaRepository)
    {
        try {
            if ($solicitudProgramaRepository->find($solicitudPrograma) instanceof SolicitudProgramaAcademico) {
                $solicitudProgramaRepository->remove($solicitudPrograma, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param EstadoProgramaAcademicoRepository $estadoProgramaRepository
     * @param SolicitudProgramaAcademico $solicitudPrograma
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/{id}/aprobar', name: 'app_solicitud_programa_academico_aprobar', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG')"))]
    public function aprobar(Request $request, Utils $utils, EstadoProgramaAcademicoRepository $estadoProgramaRepository, SolicitudProgramaAcademico $solicitudPrograma, SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, SolicitudProgramaAcademicoInstitucionRepository $solicitudProgramaAcademicoInstitucionRepository, UploadedDocumentStorage $storage)
    {
        try {
            $choices = [
                'cartaAprobacion' => empty($solicitudPrograma->getCartaAprobacion()) ? 'registrar' : 'modificar'
            ];

            $form = $this->createForm(AprobarSolicitudProgramaAcademicoType::class, $solicitudPrograma, $choices);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $solicitudPrograma->setFechaAprobacion(\DateTime::createFromFormat('d/m/Y', $request->request->all()['aprobar_solicitud_programa_academico']['fechaAprobacion']));
                $solicitudPrograma->setEstadoProgramaAcademico($estadoProgramaRepository->find(2));
                $solicitudPrograma->setDescripcion($solicitudPrograma->getDescripcionAprobacion());
                $utils->guardarHistoricoEstadoProgramaAcademico($solicitudPrograma->getId(), 2);

                if (($file = $form['cartaAprobacion']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $solicitudPrograma->setCartaAprobacion($storage->replace($file, 'uploads/pregrado/carta_aprobacion', $solicitudPrograma->getCartaAprobacion()));
                }

                $solicitudProgramaRepository->edit($solicitudPrograma, true);

                $exist = $solicitudProgramaAcademicoInstitucionRepository->findBy(['institucion' => $solicitudPrograma->getCentroRector()->getId(), 'solicitudProgramaAcademico' => $solicitudPrograma->getId()]);
                if (empty($exist)) {
                    $newSolicitud = new SolicitudProgramaAcademicoInstitucion();
                    $newSolicitud->setInstitucion($solicitudPrograma->getCentroRector());
                    $newSolicitud->setSolicitudProgramaAcademico($solicitudPrograma);
                    $newSolicitud->setCategoriaAcreditacion($solicitudPrograma->getCategoriaAcreditacion());
                    $newSolicitud->setModalidadDiurno($solicitudPrograma->getModalidadDiurno());
                    $newSolicitud->setModalidadADistancia($solicitudPrograma->getModalidadADistancia());
                    $newSolicitud->setModalidadPorEncuentro($solicitudPrograma->getModalidadPorEncuentro());
                    $newSolicitud->setDuracionCursoDiurno($solicitudPrograma->getDuracionCursoDiurno());
                    $newSolicitud->setDuracionCursoADistancia($solicitudPrograma->getDuracionCursoADistancia());
                    $newSolicitud->setDuracionCursoPorEncuentro($solicitudPrograma->getDuracionCursoPorEncuentro());

                    $solicitudProgramaAcademicoInstitucionRepository->add($newSolicitud, true);
                }


                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/solicitud_programa_academico/aprobar.html.twig', [
                'form' => $form->createView(),
                'solicitudPrograma' => $solicitudPrograma
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param EstadoProgramaAcademicoRepository $estadoProgramaRepository
     * @param SolicitudProgramaAcademico $solicitudPrograma
     * @param SolicitudProgramaAcademicoRepository $solicitudProgramaRepository
     * @return Response
     */
    #[Route('/{id}/rechazar', name: 'app_solicitud_programa_academico_rechazar', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG')"))]
    public function rechazar(Request $request, Utils $utils, EstadoProgramaAcademicoRepository $estadoProgramaRepository, SolicitudProgramaAcademico $solicitudPrograma, SolicitudProgramaAcademicoRepository $solicitudProgramaRepository, UploadedDocumentStorage $storage)
    {
        try {
            $choices = [
                'dictamen' => empty($solicitudPrograma->getDictamen()) ? 'registrar' : 'modificar'
            ];
            $form = $this->createForm(NoAprobarSolicitudProgramaAcademicoType::class, $solicitudPrograma, $choices);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $solicitudPrograma->setEstadoProgramaAcademico($estadoProgramaRepository->find(3));

                $utils->guardarHistoricoEstadoProgramaAcademico($solicitudPrograma->getId(), 3);

                if (($file = $form['dictamen']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $solicitudPrograma->setDictamen($storage->replace($file, 'uploads/pregrado/dictamen', $solicitudPrograma->getDictamen()));
                }


                $solicitudProgramaRepository->edit($solicitudPrograma, true);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/solicitud_programa_academico/no_aprobar.html.twig', [
                'form' => $form->createView(),
                'solicitudPrograma' => $solicitudPrograma
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_solicitud_programa_academico_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/exportar_pdf', name: 'app_solicitud_programa_academico_exportar_pdf', methods: ['GET', 'POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG')"))]
    public function exportarPdf(Request $request, \App\Services\HandlerFop $handFop, SolicitudProgramaAcademicoRepository $solicitudProgramaAcademicoRepository)
    {
        $export = $solicitudProgramaAcademicoRepository->getSolicitudProgramaAcademico([1, 3]);
        $export = DoctrineHelper::toArray($export);
        return $handFop->exportToPdf(new ExportListSolicitudProgramaAcademicoToPdf($export));
    }

    private function institucionUsuario(Utils $utils, EntityManagerInterface $em): ?Institucion
    {
        $datos = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $idEntidad = $datos['id_entidad'] ?? null;
        return $idEntidad && $idEntidad > 0 ? $em->getRepository(Institucion::class)->findOneBy(['estructura' => $idEntidad]) : null;
    }
}
