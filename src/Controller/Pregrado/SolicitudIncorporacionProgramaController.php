<?php

namespace App\Controller\Pregrado;

use App\Entity\Institucion\Institucion;
use App\Entity\Pregrado\SolicitudIncorporacionPrograma;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use App\Form\Pregrado\SolicitudIncorporacionProgramaType;
use App\Services\Utils;
use App\Services\UploadedDocumentStorage;
use App\Repository\Pregrado\SolicitudProgramaAcademicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/pregrado/solicitud_incorporacion')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SOLPROG') or is_granted('ROLE_PREGRADO_MIS_PROGRAMAS')"))]
final class SolicitudIncorporacionProgramaController extends AbstractController
{
    #[Route('/', name: 'app_pregrado_solicitud_incorporacion_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em, Utils $utils, SolicitudProgramaAcademicoRepository $programaRepository): Response
    {
        $global = $this->esAdministrador();
        $institucion = $global ? null : $this->institucionUsuario($utils, $em);
        if (!$global && !$institucion) {
            throw $this->createAccessDeniedException('Su usuario no está asociado a una universidad.');
        }
        $programasNacionales = $programaRepository->getSolicitudProgramaAcademicoAprobado([2, 4, 6, 7]);
        $criterios = $institucion ? ['institucion' => $institucion] : [];
        $registros = $em->getRepository(SolicitudIncorporacionPrograma::class)->findBy($criterios, ['creado' => 'DESC']);

        return $this->render('modules/pregrado/solicitud_incorporacion/index.html.twig', [
            'registros' => $registros,
            'global' => $global,
            'programasNacionales' => $programasNacionales,
            'universidades' => $global ? $em->getRepository(Institucion::class)->findBy(['activo' => true], ['nombre' => 'ASC']) : [],
        ]);
    }

    #[Route('/nueva', name: 'app_pregrado_solicitud_incorporacion_nueva', methods: ['GET', 'POST'])]
    public function nueva(Request $request, EntityManagerInterface $em, Utils $utils, SolicitudProgramaAcademicoRepository $programaRepository, UploadedDocumentStorage $storage): Response
    {
        $global = $this->esAdministrador();
        $institucion = $global
            ? $em->getRepository(Institucion::class)->find($request->query->getInt('institucion'))
            : $this->institucionUsuario($utils, $em);
        if (!$institucion) {
            $this->addFlash('warning', 'Seleccione la universidad que solicita impartir el programa.');
            return $this->redirectToRoute('app_pregrado_solicitud_incorporacion_index');
        }
        $solicitud = (new SolicitudIncorporacionPrograma())->setInstitucion($institucion);
        $programaId = $request->query->getInt('programa');
        if ($programaId > 0) {
            $programa = $programaRepository->find($programaId);
            if ($programa && in_array($programa->getEstadoProgramaAcademico()?->getId(), [2, 4, 6, 7], true)) {
                $solicitud->setPrograma($programa);
            }
        }
        if (!$solicitud->getPrograma()) {
            $this->addFlash('warning', 'Seleccione primero el programa nacional que desea impartir.');
            return $this->redirectToRoute('app_pregrado_solicitud_incorporacion_index');
        }
        $form = $this->createForm(SolicitudIncorporacionProgramaType::class, $solicitud, ['programa_fijo' => $solicitud->getPrograma()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $programa = $solicitud->getPrograma();
            $invalida = ($solicitud->getModalidadDiurno() && !$programa->getModalidadDiurno())
                || ($solicitud->getModalidadPorEncuentro() && !$programa->getModalidadPorEncuentro())
                || ($solicitud->getModalidadADistancia() && !$programa->getModalidadADistancia());
            if ($invalida) {
                $this->addFlash('error', 'Solo puede solicitar modalidades autorizadas para el programa nacional.');
            } elseif ($em->getRepository(SolicitudProgramaAcademicoInstitucion::class)->findOneBy(['institucion' => $institucion, 'solicitudProgramaAcademico' => $programa])) {
                $this->addFlash('error', 'Este programa ya forma parte de la oferta académica de la universidad.');
            } elseif ($em->getRepository(SolicitudIncorporacionPrograma::class)->findOneBy(['institucion' => $institucion, 'programa' => $programa, 'estado' => SolicitudIncorporacionPrograma::PENDIENTE])) {
                $this->addFlash('error', 'Ya existe una solicitud pendiente para incorporar este programa.');
            } else {
                if (($archivo = $form['documentoSolicitud']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $solicitud->setDocumentoSolicitud($storage->storeName($archivo, 'uploads/pregrado/solicitud_incorporacion/documento'));
                }
                $em->persist($solicitud);
                $em->flush();
                $this->addFlash('success', 'La solicitud de incorporación fue registrada.');
                return $this->redirectToRoute('app_pregrado_solicitud_incorporacion_index');
            }
        }

        return $this->render('modules/pregrado/solicitud_incorporacion/new.html.twig', [
            'form' => $form->createView(),
            'institucion' => $institucion,
        ]);
    }

    #[Route('/{id}/aprobar', name: 'app_pregrado_solicitud_incorporacion_aprobar', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function aprobar(Request $request, SolicitudIncorporacionPrograma $solicitud, EntityManagerInterface $em): Response
    {
        $this->exigirAdministrador();
        if (!$this->isCsrfTokenValid('aprobar_incorporacion_' . $solicitud->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('El formulario ha expirado.');
        }
        if ($solicitud->getEstado() !== SolicitudIncorporacionPrograma::PENDIENTE) {
            $this->addFlash('warning', 'La solicitud ya fue procesada.');
            return $this->redirectToRoute('app_pregrado_solicitud_incorporacion_index');
        }
        $existente = $em->getRepository(SolicitudProgramaAcademicoInstitucion::class)->findOneBy([
            'institucion' => $solicitud->getInstitucion(),
            'solicitudProgramaAcademico' => $solicitud->getPrograma(),
        ]);
        if (!$existente) {
            $oferta = new SolicitudProgramaAcademicoInstitucion();
            $oferta->setInstitucion($solicitud->getInstitucion());
            $oferta->setSolicitudProgramaAcademico($solicitud->getPrograma());
            $oferta->setCategoriaAcreditacion(null);
            $oferta->setModalidadDiurno($solicitud->getModalidadDiurno());
            $oferta->setModalidadPorEncuentro($solicitud->getModalidadPorEncuentro());
            $oferta->setModalidadADistancia($solicitud->getModalidadADistancia());
            $oferta->setDuracionCursoDiurno($solicitud->getPrograma()->getDuracionCursoDiurno());
            $oferta->setDuracionCursoPorEncuentro($solicitud->getPrograma()->getDuracionCursoPorEncuentro());
            $oferta->setDuracionCursoADistancia($solicitud->getPrograma()->getDuracionCursoADistancia());
            $em->persist($oferta);
        }
        $solicitud->setEstado(SolicitudIncorporacionPrograma::APROBADA);
        $em->flush();
        $this->addFlash('success', 'Solicitud aprobada. El programa fue incorporado a la oferta de la universidad.');

        return $this->redirectToRoute('app_pregrado_solicitud_incorporacion_index');
    }

    #[Route('/{id}/rechazar', name: 'app_pregrado_solicitud_incorporacion_rechazar', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function rechazar(Request $request, SolicitudIncorporacionPrograma $solicitud, EntityManagerInterface $em): Response
    {
        $this->exigirAdministrador();
        if (!$this->isCsrfTokenValid('rechazar_incorporacion_' . $solicitud->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('El formulario ha expirado.');
        }
        if ($solicitud->getEstado() === SolicitudIncorporacionPrograma::PENDIENTE) {
            $solicitud->setEstado(SolicitudIncorporacionPrograma::RECHAZADA);
            $solicitud->setObservaciones(trim((string) $request->request->get('observaciones')) ?: null);
            $em->flush();
        }
        $this->addFlash('success', 'Solicitud rechazada.');
        return $this->redirectToRoute('app_pregrado_solicitud_incorporacion_index');
    }

    private function esAdministrador(): bool
    {
        return $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_GEST_SOLPROG');
    }

    private function exigirAdministrador(): void
    {
        if (!$this->esAdministrador()) {
            throw $this->createAccessDeniedException('Solo el administrador de Pregrado puede procesar solicitudes.');
        }
    }

    private function institucionUsuario(Utils $utils, EntityManagerInterface $em): ?Institucion
    {
        $datos = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $idEntidad = $datos['id_entidad'] ?? null;
        return $idEntidad && $idEntidad > 0 ? $em->getRepository(Institucion::class)->findOneBy(['estructura' => $idEntidad]) : null;
    }
}
