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
        $scope = !$esAdminRRHH ? ' AND p.entidad_id = :entidad' : '';
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
            WHERE c.codigo IN ('PT','PA','PI','PE','DHC')
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
            'registros' => array_values(array_filter($repository->findBy(['activo' => true], ['orden' => 'ASC', 'nombre' => 'ASC']), static fn ($categoria): bool => in_array($categoria->getCodigo(), ['PT', 'PA', 'PI', 'PE', 'DHC'], true))),
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
        $categorias = $connection->fetchAllAssociative("SELECT id, codigo, nombre, clasificacion, requiere_aprobacion_mes FROM personal.tbn_categoria_docente WHERE activo=true AND codigo IN ('PT','PA','PI','PE','DHC') ORDER BY orden");
        $paises = $connection->fetchAllAssociative("SELECT id, nombre FROM estructura.tbn_pais WHERE activo=true AND upper(iso2)<>'CU' ORDER BY nombre");

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('nuevo_expediente_categoria', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('La operación ha expirado.');
            }
            $personaId = $request->request->getInt('persona');
            $categoriaId = $request->request->getInt('categoria');
            $esExtranjero = $request->request->getBoolean('es_extranjero');
            $paisId = $request->request->getInt('pais');
            $fecha = (string) $request->request->get('fecha_solicitud');
            $personaSeleccionada = array_values(array_filter($personas, static fn (array $p): bool => (int) $p['id'] === $personaId))[0] ?? null;
            $categoriaSeleccionada = array_values(array_filter($categorias, static fn (array $c): bool => (int) $c['id'] === $categoriaId))[0] ?? null;
            $paisValido = !$esExtranjero || array_filter($paises, static fn (array $pais): bool => (int) $pais['id'] === $paisId);
            if (!$personaSeleccionada || !$categoriaSeleccionada || !$paisValido || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $this->addFlash('error', 'Debe seleccionar una persona, categoría y fecha válidas.');
            } else {
                $expedienteActivo = $connection->fetchAssociative(<<<'SQL'
                    SELECT p.numero_expediente, c.nombre categoria
                    FROM rrhh.tbd_proceso_categoria_docente p
                    JOIN personal.tbn_categoria_docente c ON c.id=p.categoria_docente_id
                    JOIN personal.tbn_categoria_docente solicitada ON solicitada.id=:categoria
                    WHERE p.persona_id=:persona
                      AND p.estado NOT IN ('RECHAZADO', 'NO_APROBADO', 'DESISTIDO')
                      AND c.orden <= solicitada.orden
                    ORDER BY c.orden ASC, p.creado DESC
                    LIMIT 1
                    SQL, ['persona' => $personaId, 'categoria' => $categoriaId]);
                if ($expedienteActivo) {
                    $this->addFlash('error', sprintf('Ya existe el expediente activo %s para %s. Solo puede solicitarse una categoría superior.', $expedienteActivo['numero_expediente'], $expedienteActivo['categoria']));
                    return $this->render('modules/rrhh/gestion_categoria_docente/new.html.twig', ['personas' => $personas, 'categorias' => $categorias, 'paises' => $paises]);
                }
                $id = (int) $connection->fetchOne(<<<'SQL'
                    INSERT INTO rrhh.tbd_proceso_categoria_docente
                        (persona_id, categoria_docente_id, entidad_id, fecha_solicitud, estado, nivel_actual, requiere_aprobacion_mes, es_extranjero, pais_id, observaciones, creado, actualizado)
                    VALUES (:persona, :categoria, :entidad, :fecha, 'PREPARACION_EXPEDIENTE', 'UNIVERSIDAD', TRUE, CAST(:extranjero AS BOOLEAN), :pais, :observaciones, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                    RETURNING id
                    SQL, [
                        'persona' => $personaId,
                        'categoria' => $categoriaId,
                        'entidad' => (int) $personaSeleccionada['entidad_id'],
                        'fecha' => $fecha,
                        'extranjero' => $esExtranjero ? 'true' : 'false',
                        'pais' => $esExtranjero ? $paisId : null,
                        'observaciones' => trim((string) $request->request->get('observaciones')) ?: null,
                    ]);
                $numero = sprintf('CAT-%s-%05d', date('Y', strtotime($fecha)), $id);
                $connection->update('rrhh.tbd_proceso_categoria_docente', ['numero_expediente' => $numero], ['id' => $id]);
                $this->registrarMovimiento($connection, $id, null, 'PREPARACION_EXPEDIENTE', 'Expediente MES registrado', trim((string) $request->request->get('observaciones')) ?: null);
                $this->addFlash('success', "El expediente {$numero} fue creado. Incorpore los documentos requeridos antes de enviarlo al MES.");
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
        }

        return $this->render('modules/rrhh/gestion_categoria_docente/new.html.twig', [
            'personas' => $personas,
            'categorias' => $categorias,
            'paises' => $paises,
        ]);
    }

    #[Route('/expedientes/{id}', name: 'app_rrhh_gestion_categoria_docente_detalle', methods: ['GET'])]
    public function detalle(int $id, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative(<<<'SQL'
            SELECT p.*, c.nombre categoria, c.codigo categoria_codigo, e.nombre entidad, e.siglas, pais.nombre pais,
                   ia.tipo instrumento_tipo, ia.numero instrumento_numero, ia.fecha instrumento_fecha,
                   ia.documento instrumento_documento, ia.nombre_original instrumento_nombre,
                   trim(concat_ws(' ', pe.primer_nombre, pe.segundo_nombre, pe.primer_apellido, pe.segundo_apellido)) persona
            FROM rrhh.tbd_proceso_categoria_docente p
            JOIN personal.tbd_persona pe ON pe.id=p.persona_id
            JOIN personal.tbn_categoria_docente c ON c.id=p.categoria_docente_id
            JOIN estructura.tbd_estructura e ON e.id=p.entidad_id
            LEFT JOIN estructura.tbn_pais pais ON pais.id=p.pais_id
            LEFT JOIN rrhh.tbd_instrumento_aprobacion_categoria ia ON ia.id=p.instrumento_aprobacion_id
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
            'requisitos' => $this->requisitosExpediente($proceso['categoria_codigo'], filter_var($proceso['es_extranjero'], FILTER_VALIDATE_BOOL)),
            'instrumentos' => $esAdminRRHH && $proceso['estado'] === 'PENDIENTE_MES'
                ? $connection->fetchAllAssociative("SELECT id, tipo, numero, fecha, nombre_original, (SELECT count(*) FROM rrhh.tbd_proceso_categoria_docente p WHERE p.instrumento_aprobacion_id=i.id) expedientes FROM rrhh.tbd_instrumento_aprobacion_categoria i ORDER BY fecha DESC, id DESC")
                : [],
            'administrador_rrhh' => $esAdminRRHH,
        ]);
    }

    #[Route('/tribunales', name: 'app_rrhh_gestion_tribunal_index', methods: ['GET'])]
    public function tribunales(PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $admin = $utils->esAdminRRHH($this->getUser()->getId());
        $entidad = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        $scope = $admin ? '' : ' WHERE t.entidad_id=:entidad';
        return $this->render('modules/rrhh/gestion_categoria_docente/tribunales.html.twig', [
            'tribunales' => $entityManager->getConnection()->fetchAllAssociative("SELECT t.*, e.nombre entidad, e.siglas, (SELECT count(*) FROM rrhh.tbd_proceso_categoria_docente p WHERE p.tribunal_id=t.id) procesos FROM rrhh.tbd_tribunal_categoria_docente t JOIN estructura.tbd_estructura e ON e.id=t.entidad_id {$scope} ORDER BY e.nombre,t.nombre", $admin ? [] : ['entidad' => $entidad ?: -1]),
            'administrador_rrhh' => $admin,
        ]);
    }

    #[Route('/tribunales/nuevo', name: 'app_rrhh_gestion_tribunal_nuevo', methods: ['GET', 'POST'])]
    public function nuevoTribunal(Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        if ($utils->esAdminRRHH($this->getUser()->getId())) throw $this->createAccessDeniedException('Los tribunales son gestionados por las universidades.');
        $entidad = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()])?->getEntidad()?->getId();
        if (!$entidad) throw $this->createAccessDeniedException('Su usuario no tiene una entidad asociada.');
        if ($request->isMethod('POST')) {
            $this->validarCsrf($request, 'nuevo_tribunal_categoria');
            $datos = ['nombre' => trim((string)$request->request->get('nombre')), 'fecha_constitucion' => $request->request->get('fecha_constitucion'), 'presidente' => trim((string)$request->request->get('presidente')), 'secretario' => trim((string)$request->request->get('secretario')), 'vocales' => trim((string)$request->request->get('vocales'))];
            if (in_array('', $datos, true)) {
                $this->addFlash('error', 'Debe completar todos los datos del tribunal.');
            } else {
                $entityManager->getConnection()->insert('rrhh.tbd_tribunal_categoria_docente', $datos + ['entidad_id' => $entidad, 'estado' => 'ACTIVO']);
                $this->addFlash('success', 'El tribunal fue creado y ya puede asignarse a expedientes.');
                return $this->redirectToRoute('app_rrhh_gestion_tribunal_index');
            }
        }
        return $this->render('modules/rrhh/gestion_categoria_docente/tribunal_new.html.twig');
    }

    #[Route('/expedientes/{id}/tribunal', name: 'app_rrhh_gestion_categoria_docente_tribunal', methods: ['POST'])]
    public function tribunal(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager): Response
    {
        $this->validarCsrf($request, 'tribunal_categoria_' . $id);
        [$proceso, $connection] = $this->procesoUniversidad($id, $personaRepository, $utils, $entityManager);
        $tribunalId = $request->request->getInt('tribunal');
        $valido = $connection->fetchOne("SELECT count(*) FROM rrhh.tbd_tribunal_categoria_docente WHERE id=:id AND entidad_id=:entidad AND estado='ACTIVO'", ['id' => $tribunalId, 'entidad' => $proceso['entidad_id']]);
        if (!$valido) throw $this->createAccessDeniedException('El tribunal no pertenece a la universidad del expediente.');
        $connection->update('rrhh.tbd_proceso_categoria_docente', ['tribunal_id' => $tribunalId, 'estado' => 'EJERCICIOS', 'actualizado' => date('Y-m-d H:i:s')], ['id' => $id]);
        $this->registrarMovimiento($connection, $id, $proceso['estado'], 'EJERCICIOS', 'Tribunal asignado al expediente');
        $this->addFlash('success', 'El tribunal fue asignado al expediente.');
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
        } elseif ($accion === 'DESISTIR' && !$admin && (int)$proceso['entidad_id'] === $entidad && in_array($proceso['estado'], ['PENDIENTE_MES', 'SUBSANACION'], true) && $observaciones) {
            $this->cambiarEstado($connection, $id, $proceso['estado'], 'DESISTIDO', 'La universidad desistió del expediente', $observaciones, 'CERRADO');
        } elseif ($accion === 'RECHAZAR' && (($admin && $proceso['estado'] === 'PENDIENTE_MES') || (!$admin && (int)$proceso['entidad_id'] === $entidad && !in_array($proceso['estado'], ['OTORGADO', 'APROBADO_MES'], true)))) {
            $this->cambiarEstado($connection, $id, $proceso['estado'], 'NO_APROBADO', 'Expediente no aprobado', $observaciones, $admin ? 'MES' : 'UNIVERSIDAD');
        } elseif ($accion === 'ELEVAR' && !$admin && (int)$proceso['entidad_id'] === $entidad) {
            $codigo = (string) $connection->fetchOne('SELECT codigo FROM personal.tbn_categoria_docente WHERE id=:id', ['id' => $proceso['categoria_docente_id']]);
            $requisitos = $this->requisitosExpediente($codigo, filter_var($proceso['es_extranjero'], FILTER_VALIDATE_BOOL));
            $presentados = $connection->fetchFirstColumn('SELECT DISTINCT tipo FROM rrhh.tbd_documento_proceso_categoria WHERE proceso_id=:id', ['id' => $id]);
            $faltantes = array_diff(array_keys($requisitos), $presentados);
            if ($faltantes !== []) {
                $this->addFlash('error', 'No puede enviarse al MES. Faltan: ' . implode(', ', array_map(fn (string $tipo): string => $requisitos[$tipo], $faltantes)) . '.');
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
            $this->cambiarEstado($connection, $id, $proceso['estado'], 'PENDIENTE_MES', 'Expediente enviado al MES', $observaciones, 'MES');
        } else {
            throw $this->createAccessDeniedException('La acción no corresponde al estado o nivel del expediente.');
        }
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
    }

    #[Route('/expedientes/{id}/eliminar', name: 'app_rrhh_gestion_categoria_docente_eliminar', methods: ['POST'])]
    public function eliminar(int $id, Request $request, PersonaRepository $personaRepository, Utils $utils, EntityManagerInterface $entityManager, UploadedDocumentStorage $storage): Response
    {
        $this->validarCsrf($request, 'eliminar_categoria_' . $id);
        [$proceso, $connection] = $this->procesoUniversidad($id, $personaRepository, $utils, $entityManager);
        if (!in_array($proceso['estado'], ['PREPARACION_EXPEDIENTE', 'REVISION_UNIVERSIDAD'], true)) {
            throw $this->createAccessDeniedException('Después del envío el expediente solo puede desistirse, no eliminarse.');
        }
        $archivos = $connection->fetchFirstColumn('SELECT archivo FROM rrhh.tbd_documento_proceso_categoria WHERE proceso_id=:id', ['id' => $id]);
        $connection->transactional(static function ($connection) use ($id): void {
            $connection->executeStatement('DELETE FROM rrhh.tbd_proceso_categoria_docente WHERE id=:id', ['id' => $id]);
        });
        foreach ($archivos as $archivo) {
            $storage->delete(dirname((string) $archivo), basename((string) $archivo));
        }
        $this->addFlash('success', 'El expediente en preparación fue eliminado.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_index');
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
        $autoridad = (string) $connection->fetchOne('SELECT autoridad_otorgamiento FROM personal.tbn_categoria_docente WHERE id=:id', ['id' => $proceso['categoria_docente_id']]);
        $connection->transactional(function ($connection) use ($id, $proceso, $numero, $fecha, $autoridad): void {
            $connection->update('rrhh.tbd_proceso_categoria_docente', ['estado' => 'OTORGADO', 'nivel_actual' => 'CERRADO', 'numero_resolucion' => $numero, 'fecha_resolucion' => $fecha, 'resultado_final' => 'APROBADO', 'actualizado' => date('Y-m-d H:i:s')], ['id' => $id]);
            $connection->update('personal.tbd_persona', ['categoria_docente_id' => $proceso['categoria_docente_id']], ['id' => $proceso['persona_id']]);
            $connection->executeStatement('UPDATE rrhh.tbd_historial_categoria_docente SET activo=false, actualizado=CURRENT_TIMESTAMP WHERE persona_id=:persona AND activo=true', ['persona' => $proceso['persona_id']]);
            $connection->insert('rrhh.tbd_historial_categoria_docente', ['proceso_id' => $id, 'persona_id' => $proceso['persona_id'], 'categoria_docente_id' => $proceso['categoria_docente_id'], 'entidad_id' => $proceso['entidad_id'], 'fecha_otorgamiento' => $fecha, 'numero_resolucion' => $numero, 'autoridad' => $autoridad ?: 'Rector', 'activo' => true, 'creado' => date('Y-m-d H:i:s'), 'actualizado' => date('Y-m-d H:i:s')]);
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
        if ($esAdminRRHH || (int) $proceso['entidad_id'] !== $entidadUsuario || !in_array($proceso['estado'], ['PREPARACION_EXPEDIENTE', 'SUBSANACION'], true)) {
            throw $this->createAccessDeniedException('El expediente no admite documentos de la universidad en este estado.');
        }
        $archivo = $request->files->get('documento');
        if (!$archivo instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $this->addFlash('error', 'Debe seleccionar un documento.');
            return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
        }
        try {
            $guardado = $storage->store($archivo, 'uploads/rrhh/categorias-docentes/evidencias');
            $tipo = (string) $request->request->get('tipo', 'OTRA_EVIDENCIA');
            $codigo = (string) $connection->fetchOne('SELECT codigo FROM personal.tbn_categoria_docente WHERE id=:id', ['id' => $proceso['categoria_docente_id']]);
            $tiposPermitidos = [...array_keys($this->requisitosExpediente($codigo, filter_var($proceso['es_extranjero'], FILTER_VALIDATE_BOOL))), 'OTRA_EVIDENCIA'];
            if (!in_array($tipo, $tiposPermitidos, true)) {
                throw new \InvalidArgumentException('El tipo de documento no corresponde a este expediente.');
            }
            $connection->insert('rrhh.tbd_documento_proceso_categoria', [
                'proceso_id' => $id,
                'tipo' => $tipo,
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
    public function aprobarMes(int $id, Request $request, Utils $utils, EntityManagerInterface $entityManager, UploadedDocumentStorage $storage): Response
    {
        if (!$utils->esAdminRRHH($this->getUser()->getId())) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('aprobar_mes_categoria_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('La operación ha expirado.');
        }
        $connection = $entityManager->getConnection();
        $proceso = $connection->fetchAssociative("SELECT * FROM rrhh.tbd_proceso_categoria_docente WHERE id=:id AND estado='PENDIENTE_MES' AND requiere_aprobacion_mes=true", ['id' => $id]);
        if (!$proceso) {
            throw $this->createAccessDeniedException('El expediente no está pendiente de aprobación ministerial.');
        }

        $instrumentoId = $request->request->getInt('instrumento_existente');
        $descripcionInstrumento = null;
        if ($instrumentoId > 0) {
            $instrumento = $connection->fetchAssociative('SELECT * FROM rrhh.tbd_instrumento_aprobacion_categoria WHERE id=:id', ['id' => $instrumentoId]);
            if (!$instrumento) {
                $this->addFlash('error', 'El instrumento ministerial seleccionado no existe.');
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
            $descripcionInstrumento = $this->descripcionInstrumento($instrumento);
        } else {
            $tipo = strtoupper(trim((string) $request->request->get('tipo_instrumento')));
            $numero = trim((string) $request->request->get('numero_instrumento')) ?: null;
            $fecha = (string) $request->request->get('fecha_instrumento');
            $archivo = $request->files->get('documento_instrumento');
            if (!in_array($tipo, ['RESOLUCION', 'CARTA'], true) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || ($tipo === 'RESOLUCION' && !$numero) || !$archivo instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                $this->addFlash('error', 'Seleccione un instrumento existente o complete el tipo, la fecha, el número de resolución cuando corresponda y el documento escaneado.');
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
            if ($tipo === 'RESOLUCION' && $connection->fetchOne('SELECT id FROM rrhh.tbd_instrumento_aprobacion_categoria WHERE tipo=:tipo AND numero=:numero AND fecha=:fecha', ['tipo' => $tipo, 'numero' => $numero, 'fecha' => $fecha])) {
                $this->addFlash('error', 'Esa resolución ya está registrada. Selecciónela en «Instrumento ya registrado» para reutilizar el documento.');
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
            try {
                $guardado = $storage->store($archivo, 'uploads/rrhh/categorias-docentes/aprobaciones-mes');
                $instrumentoId = (int) $connection->fetchOne(<<<'SQL'
                    INSERT INTO rrhh.tbd_instrumento_aprobacion_categoria
                        (tipo, numero, fecha, documento, nombre_original, observaciones, creado_por_id)
                    VALUES (:tipo, :numero, :fecha, :documento, :nombre, :observaciones, :usuario)
                    RETURNING id
                    SQL, [
                        'tipo' => $tipo,
                        'numero' => $numero,
                        'fecha' => $fecha,
                        'documento' => $guardado['path'],
                        'nombre' => $guardado['originalName'],
                        'observaciones' => trim((string) $request->request->get('observaciones_instrumento')) ?: null,
                        'usuario' => $this->getUser()->getId(),
                    ]);
                $descripcionInstrumento = $tipo === 'RESOLUCION' ? "Resolución {$numero}" : 'Carta del Ministro';
            } catch (\InvalidArgumentException $exception) {
                $this->addFlash('error', $exception->getMessage());
                return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
            }
        }

        $connection->transactional(function ($connection) use ($id, $instrumentoId, $descripcionInstrumento): void {
            $actualizados = $connection->executeStatement(<<<'SQL'
                UPDATE rrhh.tbd_proceso_categoria_docente
                SET estado='APROBADO_MES', nivel_actual='UNIVERSIDAD', fecha_aprobacion_mes=CURRENT_DATE,
                    instrumento_aprobacion_id=:instrumento, actualizado=CURRENT_TIMESTAMP
                WHERE id=:id AND estado='PENDIENTE_MES' AND requiere_aprobacion_mes=true
                SQL, ['id' => $id, 'instrumento' => $instrumentoId]);
            if ($actualizados !== 1) {
                throw new \RuntimeException('El expediente cambió de estado durante la aprobación.');
            }
            $this->registrarMovimiento($connection, $id, 'PENDIENTE_MES', 'APROBADO_MES', 'Aprobación registrada por el MES', $descripcionInstrumento);
        });
        $this->addFlash('success', 'La categoría fue aprobada por el MES y vinculada al instrumento ministerial.');
        return $this->redirectToRoute('app_rrhh_gestion_categoria_docente_detalle', ['id' => $id]);
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

    /** @return array<string,string> */
    private function requisitosExpediente(string $codigo, bool $esExtranjero): array
    {
        $requisitos = in_array($codigo, ['PT', 'PA'], true)
            ? ['RESOLUCION_RECTORAL' => 'Resolución rectoral de aprobación del tribunal', 'ACTAS_EJERCICIOS' => 'Actas firmadas de todos los ejercicios']
            : ['FOTO' => 'Foto carné en colores', 'CURRICULUM' => 'Síntesis del currículum vitae', 'FUNDAMENTACION' => 'Fundamentación de la propuesta'];
        if ($esExtranjero && in_array($codigo, ['PE', 'PI'], true)) {
            $requisitos['AVAL_DRI_MES'] = 'Aval de la Dirección de Relaciones Internacionales del MES';
        }
        return $requisitos;
    }

    /** @param array<string,mixed> $instrumento */
    private function descripcionInstrumento(array $instrumento): string
    {
        return $instrumento['tipo'] === 'RESOLUCION'
            ? 'Resolución ' . $instrumento['numero']
            : 'Carta del Ministro de ' . (new \DateTimeImmutable((string) $instrumento['fecha']))->format('d/m/Y');
    }
}
