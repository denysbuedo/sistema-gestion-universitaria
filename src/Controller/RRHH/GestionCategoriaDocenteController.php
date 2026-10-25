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
                $expedienteActivo = $connection->fetchAssociative(<<<'SQL'
                    SELECT p.numero_expediente, c.nombre categoria
                    FROM rrhh.tbd_proceso_categoria_docente p
                    JOIN personal.tbn_categoria_docente c ON c.id=p.categoria_docente_id
                    JOIN personal.tbn_categoria_docente solicitada ON solicitada.id=:categoria
                    WHERE p.persona_id=:persona
                      AND p.estado NOT IN ('RECHAZADO', 'NO_APROBADO', 'OTORGADO')
                      AND c.orden <= solicitada.orden
                    ORDER BY c.orden ASC, p.creado DESC
                    LIMIT 1
                    SQL, ['persona' => $personaId, 'categoria' => $categoriaId]);
                if ($expedienteActivo) {
                    $this->addFlash('error', sprintf('Ya existe el expediente activo %s para %s. Solo puede solicitarse una categoría superior.', $expedienteActivo['numero_expediente'], $expedienteActivo['categoria']));
                    return $this->render('modules/rrhh/gestion_categoria_docente/new.html.twig', ['personas' => $personas, 'categorias' => $categorias]);
                }
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
            'tribunal' => $connection->fetchAssociative('SELECT * FROM rrhh.tbd_tribunal_categoria_docente WHERE proceso_id=:id', ['id' => $id]),
            'ejercicios' => $connection->fetchAllAssociative('SELECT * FROM rrhh.tbd_ejercicio_categoria_docente WHERE proceso_id=:id ORDER BY fecha', ['id' => $id]),
            'dictamenes' => $connection->fetchAllAssociative('SELECT * FROM rrhh.tbd_dictamen_categoria_docente WHERE proceso_id=:id ORDER BY fecha', ['id' => $id]),
            'administrador_rrhh' => $esAdminRRHH,
        ]);
    }

    #[Route('/expedientes/{id}/tribunal', name: 'app_rrhh_gestion_categoria_docente_tribunal', methods: ['POST'])]
    public function tribunal(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager, UploadedDocumentStorage $storage): Response
    {
        $this->validarCsrf($request, 'tribunal_categoria_' . $id);
        [$proceso, $connection] = $this->procesoUniversidad($id, $personaRepository, $utils, $entityManager);
        foreach (['fecha_constitucion', 'presidente', 'secretario', 'vocales'] as $campo) {
            if (!trim((string) $request->request->get($campo))) {
                $this->addFlash('error', 'Debe completar todos los datos del tribunal.');
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
        }
        $acta = $request->files->get('acta');
        $actaPath = $acta instanceof \Symfony\Component\HttpFoundation\File\UploadedFile ? $storage->store($acta, 'uploads/rrhh/categorias-docentes/tribunales')['path'] : null;
        $connection->executeStatement(<<<'SQL'
            INSERT INTO rrhh.tbd_tribunal_categoria_docente (proceso_id, fecha_constitucion, presidente, secretario, vocales, estado, acta)
            VALUES (:proceso, :fecha, :presidente, :secretario, :vocales, 'CONSTITUIDO', :acta)
            ON CONFLICT (proceso_id) DO UPDATE SET fecha_constitucion=EXCLUDED.fecha_constitucion, presidente=EXCLUDED.presidente, secretario=EXCLUDED.secretario, vocales=EXCLUDED.vocales, estado='CONSTITUIDO', acta=COALESCE(EXCLUDED.acta, rrhh.tbd_tribunal_categoria_docente.acta), actualizado=CURRENT_TIMESTAMP
            SQL, ['proceso' => $id, 'fecha' => $request->request->get('fecha_constitucion'), 'presidente' => trim((string) $request->request->get('presidente')), 'secretario' => trim((string) $request->request->get('secretario')), 'vocales' => trim((string) $request->request->get('vocales')), 'acta' => $actaPath]);
        $this->cambiarEstado($connection, $id, $proceso['estado'], 'EJERCICIOS', 'Tribunal constituido');
        $this->addFlash('success', 'El tribunal quedó registrado.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
    }

    #[Route('/expedientes/{id}/ejercicios', name: 'app_rrhh_gestion_categoria_docente_ejercicio', methods: ['POST'])]
    public function ejercicio(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager, UploadedDocumentStorage $storage): Response
    {
        $this->validarCsrf($request, 'ejercicio_categoria_' . $id);
        [$proceso, $connection] = $this->procesoUniversidad($id, $personaRepository, $utils, $entityManager);
        $acta = $request->files->get('acta');
        $actaPath = $acta instanceof \Symfony\Component\HttpFoundation\File\UploadedFile ? $storage->store($acta, 'uploads/rrhh/categorias-docentes/actas')['path'] : null;
        $connection->insert('rrhh.tbd_ejercicio_categoria_docente', ['proceso_id' => $id, 'tipo' => trim((string) $request->request->get('tipo')), 'fecha' => $request->request->get('fecha'), 'resultado' => $request->request->get('resultado'), 'calificacion' => trim((string) $request->request->get('calificacion')) ?: null, 'acta' => $actaPath, 'observaciones' => trim((string) $request->request->get('observaciones')) ?: null]);
        $this->registrarMovimiento($connection, $id, $proceso['estado'], 'EJERCICIOS', 'Ejercicio evaluativo registrado', (string) $request->request->get('tipo'));
        $this->addFlash('success', 'El ejercicio y su resultado quedaron registrados.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
    }

    #[Route('/expedientes/{id}/dictamenes', name: 'app_rrhh_gestion_categoria_docente_dictamen', methods: ['POST'])]
    public function dictamen(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager, UploadedDocumentStorage $storage): Response
    {
        $this->validarCsrf($request, 'dictamen_categoria_' . $id);
        [$proceso, $connection] = $this->procesoUniversidad($id, $personaRepository, $utils, $entityManager);
        $documento = $request->files->get('documento');
        $documentoPath = $documento instanceof \Symfony\Component\HttpFoundation\File\UploadedFile ? $storage->store($documento, 'uploads/rrhh/categorias-docentes/dictamenes')['path'] : null;
        $connection->insert('rrhh.tbd_dictamen_categoria_docente', ['proceso_id' => $id, 'area' => $request->request->get('area'), 'fecha' => $request->request->get('fecha'), 'resultado' => $request->request->get('resultado'), 'documento' => $documentoPath, 'observaciones' => trim((string) $request->request->get('observaciones')) ?: null]);
        $this->registrarMovimiento($connection, $id, $proceso['estado'], $proceso['estado'], 'Dictamen registrado', (string) $request->request->get('area'));
        $this->addFlash('success', 'El dictamen quedó incorporado.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
    }

    #[Route('/expedientes/{id}/decision', name: 'app_rrhh_gestion_categoria_docente_decision', methods: ['POST'])]
    public function decision(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $this->validarCsrf($request, 'decision_categoria_' . $id);
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative('SELECT * FROM rrhh.tbd_proceso_categoria_docente WHERE id=:id', ['id' => $id]);
        if (!$proceso) throw $this->createNotFoundException();
        $admin = $utils->esAdminRRHH($this->getUser()->getId());
        $entidad = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        $accion = (string) $request->request->get('accion');
        $observaciones = trim((string) $request->request->get('observaciones')) ?: null;
        if ($accion === 'DEVOLVER' && $admin && $proceso['estado'] === 'PENDIENTE_MES') {
            $this->cambiarEstado($connection, $id, $proceso['estado'], 'SUBSANACION', 'Expediente devuelto para subsanación', $observaciones, 'UNIVERSIDAD');
        } elseif ($accion === 'RECHAZAR' && (($admin && $proceso['estado'] === 'PENDIENTE_MES') || (!$admin && (int)$proceso['entidad_id'] === $entidad && !in_array($proceso['estado'], ['OTORGADO', 'APROBADO_MES'], true)))) {
            $this->cambiarEstado($connection, $id, $proceso['estado'], 'NO_APROBADO', 'Expediente no aprobado', $observaciones, $admin ? 'MES' : 'UNIVERSIDAD');
        } elseif ($accion === 'ELEVAR' && !$admin && (int)$proceso['entidad_id'] === $entidad) {
            $tribunal = (int) $connection->fetchOne('SELECT count(*) FROM rrhh.tbd_tribunal_categoria_docente WHERE proceso_id=:id', ['id' => $id]);
            $apto = (int) $connection->fetchOne("SELECT count(*) FROM rrhh.tbd_ejercicio_categoria_docente WHERE proceso_id=:id AND resultado='APTO'", ['id' => $id]);
            if (!$tribunal || !$apto) {
                $this->addFlash('error', 'Debe registrar el tribunal y al menos un ejercicio con resultado Apto.');
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
            $destino = filter_var($proceso['requiere_aprobacion_mes'], FILTER_VALIDATE_BOOL) ? 'PENDIENTE_MES' : 'PENDIENTE_RECTOR';
            $this->cambiarEstado($connection, $id, $proceso['estado'], $destino, $destino === 'PENDIENTE_MES' ? 'Expediente elevado al MES' : 'Expediente remitido para resolución rectoral', $observaciones, $destino === 'PENDIENTE_MES' ? 'MES' : 'UNIVERSIDAD');
        } else {
            throw $this->createAccessDeniedException('La acción no corresponde al estado o nivel del expediente.');
        }
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
    }

    #[Route('/expedientes/{id}/resolucion', name: 'app_rrhh_gestion_categoria_docente_resolucion', methods: ['POST'])]
    public function resolucion(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $this->validarCsrf($request, 'resolucion_categoria_' . $id);
        [$proceso, $connection] = $this->procesoUniversidad($id, $personaRepository, $utils, $entityManager);
        if (!in_array($proceso['estado'], ['PENDIENTE_RECTOR', 'APROBADO_MES'], true)) throw $this->createAccessDeniedException();
        $numero = trim((string) $request->request->get('numero_resolucion'));
        $fecha = (string) $request->request->get('fecha_resolucion');
        if (!$numero || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $this->addFlash('error', 'Debe indicar número y fecha de la resolución.');
            return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
        }
        $connection->transactional(function ($connection) use ($id, $proceso, $numero, $fecha): void {
            $connection->update('rrhh.tbd_proceso_categoria_docente', ['estado' => 'OTORGADO', 'nivel_actual' => 'CERRADO', 'numero_resolucion' => $numero, 'fecha_resolucion' => $fecha, 'resultado_final' => 'APROBADO', 'actualizado' => date('Y-m-d H:i:s')], ['id' => $id]);
            $connection->update('personal.tbd_persona', ['categoria_docente_id' => $proceso['categoria_docente_id']], ['id' => $proceso['persona_id']]);
            $connection->executeStatement('UPDATE rrhh.tbd_historial_categoria_docente SET activo=false, actualizado=CURRENT_TIMESTAMP WHERE persona_id=:persona AND activo=true', ['persona' => $proceso['persona_id']]);
            $connection->insert('rrhh.tbd_historial_categoria_docente', ['proceso_id' => $id, 'persona_id' => $proceso['persona_id'], 'categoria_docente_id' => $proceso['categoria_docente_id'], 'entidad_id' => $proceso['entidad_id'], 'fecha_otorgamiento' => $fecha, 'numero_resolucion' => $numero, 'autoridad' => 'Rector', 'activo' => true, 'creado' => date('Y-m-d H:i:s'), 'actualizado' => date('Y-m-d H:i:s')]);
        });
        $this->registrarMovimiento($connection, $id, $proceso['estado'], 'OTORGADO', 'Categoría otorgada mediante resolución', $numero);
        $this->addFlash('success', 'La categoría fue otorgada y actualizada en la ficha del profesor.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
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

    private function validarCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('La operación ha expirado.');
        }
    }

    /** @return array{0: array<string,mixed>, 1: \Doctrine\DBAL\Connection} */
    private function procesoUniversidad(int $id, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): array
    {
        if ($utils->esAdminRRHH($this->getUser()->getId())) {
            throw $this->createAccessDeniedException('La acción corresponde a la universidad.');
        }
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative('SELECT * FROM rrhh.tbd_proceso_categoria_docente WHERE id=:id', ['id' => $id]);
        $entidad = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        if (!$proceso || !$entidad || (int) $proceso['entidad_id'] !== $entidad) {
            throw $this->createAccessDeniedException();
        }
        return [$proceso, $connection];
    }

    private function cambiarEstado(\Doctrine\DBAL\Connection $connection, int $id, string $origen, string $destino, string $accion, ?string $observaciones = null, ?string $nivel = null): void
    {
        $datos = ['estado' => $destino, 'actualizado' => date('Y-m-d H:i:s')];
        if ($nivel !== null) $datos['nivel_actual'] = $nivel;
        if ($destino === 'NO_APROBADO') $datos['resultado_final'] = 'NO_APROBADO';
        $connection->update('rrhh.tbd_proceso_categoria_docente', $datos, ['id' => $id]);
        $this->registrarMovimiento($connection, $id, $origen, $destino, $accion, $observaciones);
        $this->addFlash('success', $accion . '.');
    }
}
