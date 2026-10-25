<?php

namespace App\Controller\RRHH;

use App\Repository\Personal\CategoriaDocenteRepository;
use App\Repository\Personal\PersonaRepository;
use App\Services\Utils;
use App\Services\UploadedDocumentStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_GEST_CAT_DOC')"))]
#[Route('/rrhh/gestion-categorias-docentes')]
final class GestionCategoriaDocenteController extends AbstractController
{
    #[Route('/', name: 'app_rrhh_gestion_categoria_docente_index', methods: ['GET'])]
    public function index(PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        $entidadId = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        $scope = !$esAdminRRHH ? ' WHERE p.entidad_id = :entidad' : '';
        $params = !$esAdminRRHH ? ['entidad' => $entidadId ?: -1] : [];
        $expedientes = $entityManager->getConnection()->fetchAllAssociative(<<<SQL
            SELECT p.id, p.numero_expediente, p.fecha_solicitud, p.estado, p.nivel_actual,
                   c.nombre categoria, c.requiere_aprobacion_mes,
                   e.nombre entidad, e.siglas,
                   trim(concat_ws(' ', pe.primer_nombre, pe.segundo_nombre, pe.primer_apellido, pe.segundo_apellido)) persona
            FROM rrhh.tbd_proceso_categoria_docente p
            JOIN personal.tbd_persona pe ON pe.id = p.persona_id
            JOIN personal.tbn_categoria_docente c ON c.id = p.categoria_docente_id
            JOIN estructura.tbd_estructura e ON e.id = p.entidad_id
            {$scope}
            ORDER BY p.creado DESC
            SQL, $params);

        return $this->render('modules/rrhh/gestion_categoria_docente/index.html.twig', [
            'expedientes' => $expedientes,
            'administrador_rrhh' => $esAdminRRHH,
        ]);
    }

    #[Route('/catalogo', name: 'app_rrhh_gestion_categoria_docente_catalogo', methods: ['GET'])]
    public function catalogo(CategoriaDocenteRepository $repository): Response
    {
        return $this->render('modules/rrhh/gestion_categoria_docente/catalogo.html.twig', [
            'registros' => $repository->findBy(['activo' => true], ['orden' => 'ASC', 'nombre' => 'ASC']),
        ]);
    }

    #[Route('/expedientes/nuevo', name: 'app_rrhh_gestion_categoria_docente_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        if ($esAdminRRHH) {
            throw $this->createAccessDeniedException('El registro corresponde a la universidad solicitante.');
        }
        $entidadId = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        $params = [];
        $scope = ' WHERE pe.activo = true';
        if (!$esAdminRRHH) {
            $scope .= ' AND pe.entidad_id = :entidad';
            $params['entidad'] = $entidadId ?: -1;
        }
        $personas = $connection->fetchAllAssociative(<<<SQL
            SELECT pe.id, pe.entidad_id,
                   trim(concat_ws(' ', pe.primer_nombre, pe.segundo_nombre, pe.primer_apellido, pe.segundo_apellido)) nombre,
                   e.siglas
            FROM personal.tbd_persona pe
            LEFT JOIN estructura.tbd_estructura e ON e.id = pe.entidad_id
            {$scope}
            ORDER BY pe.primer_apellido, pe.primer_nombre
            SQL, $params);
        $categorias = $connection->fetchAllAssociative('SELECT id, codigo, nombre, clasificacion, requiere_aprobacion_mes FROM personal.tbn_categoria_docente WHERE activo=true ORDER BY orden');

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('nuevo_expediente_categoria', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('La operación ha expirado.');
            }
            $personaId = $request->request->getInt('persona');
            $categoriaId = $request->request->getInt('categoria');
            $fecha = (string) $request->request->get('fecha_solicitud');
            $personaSeleccionada = array_values(array_filter($personas, static fn (array $p): bool => (int) $p['id'] === $personaId))[0] ?? null;
            $categoriaSeleccionada = array_values(array_filter($categorias, static fn (array $c): bool => (int) $c['id'] === $categoriaId))[0] ?? null;
            if (!$personaSeleccionada || !$categoriaSeleccionada || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $this->addFlash('error', 'Debe seleccionar una persona, categoría y fecha válidas.');
            } else {
                $id = (int) $connection->fetchOne(<<<'SQL'
                    INSERT INTO rrhh.tbd_proceso_categoria_docente
                        (persona_id, categoria_docente_id, entidad_id, fecha_solicitud, estado, nivel_actual, requiere_aprobacion_mes, observaciones, creado, actualizado)
                    VALUES (:persona, :categoria, :entidad, :fecha, 'REVISION_UNIVERSIDAD', 'UNIVERSIDAD', CAST(:requiere_mes AS BOOLEAN), :observaciones, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING id
                    SQL, [
                        'persona' => $personaId,
                        'categoria' => $categoriaId,
                        'entidad' => (int) $personaSeleccionada['entidad_id'],
                        'fecha' => $fecha,
                        'requiere_mes' => $categoriaSeleccionada['requiere_aprobacion_mes'] ? 'true' : 'false',
                        'observaciones' => trim((string) $request->request->get('observaciones')) ?: null,
                    ]);
                $numero = sprintf('CAT-%s-%05d', date('Y', strtotime($fecha)), $id);
                $connection->update('rrhh.tbd_proceso_categoria_docente', ['numero_expediente' => $numero], ['id' => $id]);
                $this->registrarMovimiento($connection, $id, null, 'REVISION_UNIVERSIDAD', 'Expediente registrado', trim((string) $request->request->get('observaciones')) ?: null);
                $this->addFlash('success', "El expediente {$numero} fue creado para revisión universitaria.");
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_index');
            }
        }

        return $this->render('modules/rrhh/gestion_categoria_docente/new.html.twig', [
            'personas' => $personas,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/expedientes/{id}', name: 'app_rrhh_gestion_categoria_docente_detalle', methods: ['GET'])]
    public function detalle(int $id, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative(<<<'SQL'
            SELECT p.*, c.nombre categoria, c.codigo categoria_codigo, e.nombre entidad, e.siglas,
                   trim(concat_ws(' ', pe.primer_nombre, pe.segundo_nombre, pe.primer_apellido, pe.segundo_apellido)) persona
            FROM rrhh.tbd_proceso_categoria_docente p
            JOIN personal.tbd_persona pe ON pe.id=p.persona_id
            JOIN personal.tbn_categoria_docente c ON c.id=p.categoria_docente_id
            JOIN estructura.tbd_estructura e ON e.id=p.entidad_id
            WHERE p.id=:id
            SQL, ['id' => $id]);
        if (!$proceso) {
            throw $this->createNotFoundException();
        }
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        $entidadUsuario = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        if (!$esAdminRRHH && (int) $proceso['entidad_id'] !== $entidadUsuario) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('modules/rrhh/gestion_categoria_docente/detail.html.twig', [
            'proceso' => $proceso,
            'documentos' => $connection->fetchAllAssociative('SELECT * FROM rrhh.tbd_documento_proceso_categoria WHERE proceso_id=:id ORDER BY creado DESC', ['id' => $id]),
            'movimientos' => $connection->fetchAllAssociative('SELECT m.*, u.email usuario FROM rrhh.tbd_movimiento_proceso_categoria m JOIN seguridad.tbd_usuario u ON u.id=m.realizado_por_id WHERE m.proceso_id=:id ORDER BY m.creado DESC', ['id' => $id]),
            'administrador_rrhh' => $esAdminRRHH,
        ]);
    }

    #[Route('/expedientes/{id}/documentos', name: 'app_rrhh_gestion_categoria_docente_documento', methods: ['POST'])]
    public function documento(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager, UploadedDocumentStorage $storage): Response
    {
        if (!$this->isCsrfTokenValid('documento_categoria_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('La operación ha expirado.');
        }
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative('SELECT * FROM rrhh.tbd_proceso_categoria_docente WHERE id=:id', ['id' => $id]);
        if (!$proceso) {
            throw $this->createNotFoundException();
        }
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        $entidadUsuario = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        if ($esAdminRRHH || (int) $proceso['entidad_id'] !== $entidadUsuario || !in_array($proceso['estado'], ['REVISION_UNIVERSIDAD', 'SUBSANACION'], true)) {
            throw $this->createAccessDeniedException('El expediente no admite documentos de la universidad en este estado.');
        }
        $archivo = $request->files->get('documento');
        if (!$archivo instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $this->addFlash('error', 'Debe seleccionar un documento.');
            return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
        }
        try {
            $guardado = $storage->store($archivo, 'uploads/rrhh/categorias-docentes/evidencias');
            $connection->insert('rrhh.tbd_documento_proceso_categoria', [
                'proceso_id' => $id,
                'tipo' => (string) $request->request->get('tipo', 'OTRA_EVIDENCIA'),
                'nombre_original' => $guardado['originalName'],
                'archivo' => $guardado['path'],
                'descripcion' => trim((string) $request->request->get('descripcion')) ?: null,
                'creado_por_id' => $this->getUser()->getId(),
            ]);
            $this->registrarMovimiento($connection, $id, $proceso['estado'], $proceso['estado'], 'Documento incorporado', $guardado['originalName']);
            $this->addFlash('success', 'La evidencia fue incorporada al expediente.');
        } catch (\InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
    }

    #[Route('/expedientes/{id}/tramitar', name: 'app_rrhh_gestion_categoria_docente_tramitar', methods: ['POST'])]
    public function tramitar(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('tramitar_categoria_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('La operación ha expirado.');
        }
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative('SELECT * FROM rrhh.tbd_proceso_categoria_docente WHERE id=:id', ['id' => $id]);
        if (!$proceso || $proceso['estado'] !== 'REVISION_UNIVERSIDAD') {
            throw $this->createNotFoundException('El expediente no está disponible para tramitar.');
        }
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        if ($esAdminRRHH) {
            throw $this->createAccessDeniedException('La tramitación universitaria no corresponde al administrador del MES.');
        }
        $entidadUsuario = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        if (!$esAdminRRHH && (int) $proceso['entidad_id'] !== $entidadUsuario) {
            throw $this->createAccessDeniedException();
        }
        $requiereMes = filter_var($proceso['requiere_aprobacion_mes'], FILTER_VALIDATE_BOOL);
        $connection->update('rrhh.tbd_proceso_categoria_docente', [
            'estado' => $requiereMes ? 'PENDIENTE_MES' : 'PENDIENTE_RECTOR',
            'nivel_actual' => $requiereMes ? 'MES' : 'UNIVERSIDAD',
            'actualizado' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
        $this->registrarMovimiento($connection, $id, 'REVISION_UNIVERSIDAD', $requiereMes ? 'PENDIENTE_MES' : 'PENDIENTE_RECTOR', $requiereMes ? 'Expediente elevado al MES' : 'Expediente preparado para decisión rectoral');
        $this->addFlash('success', $requiereMes ? 'El expediente fue elevado al MES.' : 'El expediente quedó listo para decisión rectoral.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_index');
    }

    #[Route('/expedientes/{id}/aprobar-mes', name: 'app_rrhh_gestion_categoria_docente_aprobar_mes', methods: ['POST'])]
    public function aprobarMes(int $id, Request $request, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        if (!$utils->esAdminRRHH($this->getUser()->getId())) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('aprobar_mes_categoria_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('La operación ha expirado.');
        }
        $actualizados = $entityManager->getConnection()->executeStatement(<<<'SQL'
            UPDATE rrhh.tbd_proceso_categoria_docente
            SET estado='APROBADO_MES', nivel_actual='UNIVERSIDAD', fecha_aprobacion_mes=CURRENT_DATE, actualizado=CURRENT_TIMESTAMP
            WHERE id=:id AND estado='PENDIENTE_MES' AND requiere_aprobacion_mes=true
            SQL, ['id' => $id]);
        if ($actualizados) {
            $this->registrarMovimiento($entityManager->getConnection(), $id, 'PENDIENTE_MES', 'APROBADO_MES', 'Aprobación registrada por el MES');
            $this->addFlash('success', 'La categoría fue aprobada por el MES y devuelta a la universidad para su otorgamiento.');
        }
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_index');
    }

    private function registrarMovimiento(\Doctrine\DBAL\Connection $connection, int $procesoId, ?string $origen, string $destino, string $accion, ?string $observaciones = null): void
    {
        $connection->insert('rrhh.tbd_movimiento_proceso_categoria', [
            'proceso_id' => $procesoId,
            'estado_origen' => $origen,
            'estado_destino' => $destino,
            'accion' => $accion,
            'observaciones' => $observaciones,
            'realizado_por_id' => $this->getUser()->getId(),
        ]);
    }
}
