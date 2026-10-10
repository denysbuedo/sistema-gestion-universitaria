<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261010004147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial Akademos schema baseline generated from the validated Doctrine metadata';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'This migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('CREATE SCHEMA rrhh');
        $this->addSql('CREATE SCHEMA evaluacion');
        $this->addSql('CREATE SCHEMA informatizacion');
        $this->addSql('CREATE SCHEMA postgrado');
        $this->addSql('CREATE SCHEMA pregrado');
        $this->addSql('CREATE SCHEMA traza');
        $this->addSql('CREATE SCHEMA convenio');
        $this->addSql('CREATE SCHEMA tramite');
        $this->addSql('CREATE SCHEMA estructura');
        $this->addSql('CREATE SCHEMA seguridad');
        $this->addSql('CREATE SCHEMA institucion');
        $this->addSql('CREATE SCHEMA personal');
        $this->addSql('CREATE SCHEMA configuracion');
        $this->addSql('CREATE SEQUENCE informe_personalizado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notificaciones_usuario_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rrhh.tbd_ae2_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rrhh.tbd_ae3_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbd_aplazamiento_solicitud_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_centro_dato_virtual_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbd_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbd_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbd_comision_nacional_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE traza.tbd_configuracion_traza_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbd_convenio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbd_convocatoria_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbd_copep_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_documento_salida_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_documento_salida_historial_documento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_enlace_conectividad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbd_entidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbd_estructura_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_ficha_salida_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_ficha_salida_historial_documento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seguridad.tbd_funcionalidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbd_institucion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_institucion_extranjera_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_linea_celular_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seguridad.tbd_modulo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_nauta_hogar_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_pasaporte_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbd_persona_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbd_plan_estudio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbd_plan_mision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbd_plantilla_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbd_programa_academico_desactivado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbd_programa_academico_reabierto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbd_responsable_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seguridad.tbd_rol_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_servicio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_sistema_informatico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbd_solicitud_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbd_solicitud_programa_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbd_solicitud_programa_academico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbd_telefono_celular_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE traza.tbd_traza_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbd_universidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seguridad.tbd_usuario_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE traza.tbn_accion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_carrera_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbn_categoria_acreditacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_categoria_categorizacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rrhh.tbn_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_categoria_estructura_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_categoria_investigativa_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_categoria_responsabilidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_clasificacion_persona_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbn_concepto_gasto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbn_concepto_salida_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_curso_academico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_documento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rrhh.tbn_especialidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbn_estado_aplazamiento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbn_estado_ficha_salida_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_estado_programa_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_estado_programa_academico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbn_estado_solicitud_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_grado_academico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rrhh.tbn_grupo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE configuracion.tbn_idioma_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_marca_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbn_modalida_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_modalidad_programa_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_modelo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_municipio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_nivel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_nivel_escolar_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_oace_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE traza.tbn_objeto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbn_obsequio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_organismo_demandante_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_organismo_formador_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_organizacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_pais_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_presencialidad_programa_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_proceso_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_profesion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_provincia_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_publico_objetivo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_rama_ciencia_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbn_recursos_humanos_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbn_red_social_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbn_region_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_responsabilidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbn_rol_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_rol_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbn_rol_redes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_servicio_contratado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_sexo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_sistema_operativo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbn_tipo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_tipo_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_tipo_conectividad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_tipo_entidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbn_tipo_estructura_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbn_tipo_institucion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_tipo_organismo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbn_tipo_organizacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbn_tipo_pasaporte_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_tipo_programa_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbn_tipo_programa_academico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_tipo_sistema_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbn_tipo_solicitud_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE traza.tbn_tipo_traza_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbn_tramite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbn_visibilidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbn_visibilidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE convenio.tbr_convenio_accion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbr_documento_salida_tramite_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbr_ficha_salida_concepto_gasto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbr_ficha_salida_estado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_historico_estado_programa_academico_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_centros_estudios_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_cum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_editorial_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_facultades_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_fum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_mecanismo_colaboracion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_membresia_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_programa_colaboracion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_proyecto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_recurso_humano_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_redes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_redes_sociales_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_revista_cientifica_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE institucion.tbr_institucion_sedes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbr_linea_celular_recargas_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbr_linea_celular_responsable_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbr_miembros_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_miembros_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_miembros_comision_nacional_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_miembros_copep_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_modificacion_plan_estudio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE personal.tbr_persona_organizacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_plan_estudio_documento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tramite.tbr_plan_mision_detalles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE estructura.tbr_plaza_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_programa_academico_reabierto_institucion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE seguridad.tbr_rol_estructura_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbr_sistema_informatico_proceso_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.tbr_solicitud_dictamen_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_solicitud_programa_academico_comision_nacional_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_solicitud_programa_academico_institucion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_solicitud_programa_academico_institucion_cum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pregrado.tbr_solicitud_programa_academico_plan_estudio_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_solicitud_programa_comision_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_solicitud_programa_dictamen_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_solicitud_programa_institucion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_solicitud_programa_presencialidad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_solicitud_programa_votacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE informatizacion.tbr_telefono_celular_responsable_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE postgrado.tbr_tipo_solicitud_clasificacion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.temp_categoria_acreditacion_ies_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.temp_categoria_acreditacion_posgrado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evaluacion.temp_categoria_acreditacion_pregrado_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE informe_personalizado (
          id INT NOT NULL,
          usuario_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          contenido TEXT DEFAULT NULL,
          privado BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_69E8944EDB38439E ON informe_personalizado (usuario_id)');
        $this->addSql('CREATE TABLE notificaciones_usuario (
          id INT NOT NULL,
          usuario_envia_id INT DEFAULT NULL,
          usuario_recive_id INT DEFAULT NULL,
          texto TEXT NOT NULL,
          url TEXT DEFAULT NULL,
          leido BOOLEAN NOT NULL,
          fecha_creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C66FA6F59DA2C333 ON notificaciones_usuario (usuario_envia_id)');
        $this->addSql('CREATE INDEX IDX_C66FA6F55FA3BDA6 ON notificaciones_usuario (usuario_recive_id)');
        $this->addSql('CREATE TABLE rrhh.tbd_ae2 (
          id INT NOT NULL,
          entidad_id INT DEFAULT NULL,
          mes INT DEFAULT NULL,
          anno INT DEFAULT NULL,
          total_plantilla_aprobada INT DEFAULT NULL,
          total_plantilla_cubierta INT DEFAULT NULL,
          total_general_contratos INT DEFAULT NULL,
          total_contratos_profesores_tiempo_determinado INT DEFAULT NULL,
          profesores_tiempo_completo INT DEFAULT NULL,
          total_contratos_no_docentes INT DEFAULT NULL,
          contratos_no_docentes_con_respaldo INT DEFAULT NULL,
          contratos_por_sustitucion INT DEFAULT NULL,
          periodo_prueba INT DEFAULT NULL,
          serenos_auxiliares_limpieza INT DEFAULT NULL,
          labores_agricolas INT DEFAULT NULL,
          jubilados INT DEFAULT NULL,
          otros_con_respaldo INT DEFAULT NULL,
          contratos_no_docentes_sin_respaldo INT DEFAULT NULL,
          serenos_auxiliares_limpieza_sin_respaldo INT DEFAULT NULL,
          labores_agricolas_sin_respaldo INT DEFAULT NULL,
          jubilados_sin_respaldo INT DEFAULT NULL,
          ejecucion_obra INT DEFAULT NULL,
          otros_sin_respaldo INT DEFAULT NULL,
          reserva_cientifica_preparacion INT DEFAULT NULL,
          recien_graduados_preparacion INT DEFAULT NULL,
          reserva_direccion_provincial_trabajo INT DEFAULT NULL,
          tecnicos_medios_preparacion INT DEFAULT NULL,
          total_estudiantes_universidad_contratados INT DEFAULT NULL,
          estudiantes_auxiliares_tecnicos_docencia INT DEFAULT NULL,
          estudiantes_cargos_no_docentes INT DEFAULT NULL,
          documento VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_E712DE0F6CA204EF ON rrhh.tbd_ae2 (entidad_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_mes_anio ON rrhh.tbd_ae2 (mes, anno, entidad_id)');
        $this->addSql('CREATE TABLE rrhh.tbd_ae3 (
          id INT NOT NULL,
          entidad_id INT DEFAULT NULL,
          mes INT NOT NULL,
          anno INT NOT NULL,
          documento VARCHAR(255) DEFAULT NULL,
          total_cuadros_total_cubierta INT DEFAULT NULL,
          total_cuadros_de_ellos_fem INT DEFAULT NULL,
          total_cuadros_jovenes_total INT DEFAULT NULL,
          total_cuadros_jovenes_fem INT DEFAULT NULL,
          total_cuadros_fem INT DEFAULT NULL,
          total_cuadros_pt INT DEFAULT NULL,
          total_cuadros_pa INT DEFAULT NULL,
          total_cuadros_as INT DEFAULT NULL,
          total_cuadros_i INT DEFAULT NULL,
          total_cuadros_it INT DEFAULT NULL,
          total_cuadros_ia INT DEFAULT NULL,
          total_cuadros_iag INT DEFAULT NULL,
          total_cuadros_ai INT DEFAULT NULL,
          total_cuadros_aux_tec_doc INT DEFAULT NULL,
          total_cuadros_msc INT DEFAULT NULL,
          total_cuadros_dr INT DEFAULT NULL,
          cuadros_docentes_total_cubierta INT DEFAULT NULL,
          cuadros_docentes_de_ellos_fem INT DEFAULT NULL,
          cuadros_docentes_jovenes_total INT DEFAULT NULL,
          cuadros_docentes_jovenes_fem INT DEFAULT NULL,
          cuadros_docentes_fem INT DEFAULT NULL,
          cuadros_docentes_pt INT DEFAULT NULL,
          cuadros_docentes_pa INT DEFAULT NULL,
          cuadros_docentes_as INT DEFAULT NULL,
          cuadros_docentes_i INT DEFAULT NULL,
          cuadros_docentes_it INT DEFAULT NULL,
          cuadros_docentes_ia INT DEFAULT NULL,
          cuadros_docentes_iag INT DEFAULT NULL,
          cuadros_docentes_ai INT DEFAULT NULL,
          cuadros_docentes_aux_tec_doc INT DEFAULT NULL,
          cuadros_docentes_msc INT DEFAULT NULL,
          cuadros_docentes_dr INT DEFAULT NULL,
          cuadros_administrativos_total_cubierta INT DEFAULT NULL,
          cuadros_administrativos_de_ellos_fem INT DEFAULT NULL,
          cuadros_administrativos_jovenes_total INT DEFAULT NULL,
          cuadros_administrativos_jovenes_fem INT DEFAULT NULL,
          cuadros_administrativos_fem INT DEFAULT NULL,
          cuadros_administrativos_pt INT DEFAULT NULL,
          cuadros_administrativos_pa INT DEFAULT NULL,
          cuadros_administrativos_as INT DEFAULT NULL,
          cuadros_administrativos_i INT DEFAULT NULL,
          cuadros_administrativos_it INT DEFAULT NULL,
          cuadros_administrativos_ia INT DEFAULT NULL,
          cuadros_administrativos_iag INT DEFAULT NULL,
          cuadros_administrativos_ai INT DEFAULT NULL,
          cuadros_administrativos_aux_tec_doc INT DEFAULT NULL,
          cuadros_administrativos_msc INT DEFAULT NULL,
          cuadros_administrativos_dr INT DEFAULT NULL,
          cuadros_investigacion_total_cubierta INT DEFAULT NULL,
          cuadros_investigacion_de_ellos_fem INT DEFAULT NULL,
          cuadros_investigacion_jovenes_total INT DEFAULT NULL,
          cuadros_investigacion_jovenes_fem INT DEFAULT NULL,
          cuadros_investigacion_fem INT DEFAULT NULL,
          cuadros_investigacion_pt INT DEFAULT NULL,
          cuadros_investigacion_pa INT DEFAULT NULL,
          cuadros_investigacion_as INT DEFAULT NULL,
          cuadros_investigacion_i INT DEFAULT NULL,
          cuadros_investigacion_it INT DEFAULT NULL,
          cuadros_investigacion_ia INT DEFAULT NULL,
          cuadros_investigacion_iag INT DEFAULT NULL,
          cuadros_investigacion_ai INT DEFAULT NULL,
          cuadros_investigacion_aux_tec_doc INT DEFAULT NULL,
          cuadros_investigacion_msc INT DEFAULT NULL,
          cuadros_investigacion_dr INT DEFAULT NULL,
          otros_cuadros_total_cubierta INT DEFAULT NULL,
          otros_cuadros_de_ellos_fem INT DEFAULT NULL,
          otros_cuadros_jovenes_total INT DEFAULT NULL,
          otros_cuadros_jovenes_fem INT DEFAULT NULL,
          otros_cuadros_fem INT DEFAULT NULL,
          otros_cuadros_pt INT DEFAULT NULL,
          otros_cuadros_pa INT DEFAULT NULL,
          otros_cuadros_as INT DEFAULT NULL,
          otros_cuadros_i INT DEFAULT NULL,
          otros_cuadros_it INT DEFAULT NULL,
          otros_cuadros_ia INT DEFAULT NULL,
          otros_cuadros_iag INT DEFAULT NULL,
          otros_cuadros_ai INT DEFAULT NULL,
          otros_cuadros_aux_tec_doc INT DEFAULT NULL,
          otros_cuadros_msc INT DEFAULT NULL,
          otros_cuadros_dr INT DEFAULT NULL,
          total_tecnicos_total_cubierta INT DEFAULT NULL,
          total_tecnicos_de_ellos_fem INT DEFAULT NULL,
          total_tecnicos_jovenes_total INT DEFAULT NULL,
          total_tecnicos_jovenes_fem INT DEFAULT NULL,
          total_tecnicos_fem INT DEFAULT NULL,
          total_tecnicos_pt INT DEFAULT NULL,
          total_tecnicos_pa INT DEFAULT NULL,
          total_tecnicos_as INT DEFAULT NULL,
          total_tecnicos_i INT DEFAULT NULL,
          total_tecnicos_it INT DEFAULT NULL,
          total_tecnicos_ia INT DEFAULT NULL,
          total_tecnicos_iag INT DEFAULT NULL,
          total_tecnicos_ai INT DEFAULT NULL,
          total_tecnicos_aux_tec_doc INT DEFAULT NULL,
          total_tecnicos_msc INT DEFAULT NULL,
          total_tecnicos_dr INT DEFAULT NULL,
          profesores_tiempo_completo_total_cubierta INT DEFAULT NULL,
          profesores_tiempo_completo_de_ellos_fem INT DEFAULT NULL,
          profesores_tiempo_completo_jovenes_total INT DEFAULT NULL,
          profesores_tiempo_completo_jovenes_fem INT DEFAULT NULL,
          profesores_tiempo_completo_fem INT DEFAULT NULL,
          profesores_tiempo_completo_pt INT DEFAULT NULL,
          profesores_tiempo_completo_pa INT DEFAULT NULL,
          profesores_tiempo_completo_as INT DEFAULT NULL,
          profesores_tiempo_completo_i INT DEFAULT NULL,
          profesores_tiempo_completo_it INT DEFAULT NULL,
          profesores_tiempo_completo_ia INT DEFAULT NULL,
          profesores_tiempo_completo_iag INT DEFAULT NULL,
          profesores_tiempo_completo_ai INT DEFAULT NULL,
          profesores_tiempo_completo_aux_tec_doc INT DEFAULT NULL,
          profesores_tiempo_completo_msc INT DEFAULT NULL,
          profesores_tiempo_completo_dr INT DEFAULT NULL,
          asesores_metodologos_total_cubierta INT DEFAULT NULL,
          asesores_metodologos_de_ellos_fem INT DEFAULT NULL,
          asesores_metodologos_jovenes_total INT DEFAULT NULL,
          asesores_metodologos_jovenes_fem INT DEFAULT NULL,
          asesores_metodologos_fem INT DEFAULT NULL,
          asesores_metodologos_pt INT DEFAULT NULL,
          asesores_metodologos_pa INT DEFAULT NULL,
          asesores_metodologos_as INT DEFAULT NULL,
          asesores_metodologos_i INT DEFAULT NULL,
          asesores_metodologos_it INT DEFAULT NULL,
          asesores_metodologos_ia INT DEFAULT NULL,
          asesores_metodologos_iag INT DEFAULT NULL,
          asesores_metodologos_ai INT DEFAULT NULL,
          asesores_metodologos_aux_tec_doc INT DEFAULT NULL,
          asesores_metodologos_msc INT DEFAULT NULL,
          asesores_metodologos_dr INT DEFAULT NULL,
          investigadores_total_cubierta INT DEFAULT NULL,
          investigadores_de_ellos_fem INT DEFAULT NULL,
          investigadores_jovenes_total INT DEFAULT NULL,
          investigadores_jovenes_fem INT DEFAULT NULL,
          investigadores_fem INT DEFAULT NULL,
          investigadores_pt INT DEFAULT NULL,
          investigadores_pa INT DEFAULT NULL,
          investigadores_as INT DEFAULT NULL,
          investigadores_i INT DEFAULT NULL,
          investigadores_it INT DEFAULT NULL,
          investigadores_ia INT DEFAULT NULL,
          investigadores_iag INT DEFAULT NULL,
          investigadores_ai INT DEFAULT NULL,
          investigadores_aux_tec_doc INT DEFAULT NULL,
          investigadores_msc INT DEFAULT NULL,
          investigadores_dr INT DEFAULT NULL,
          otros_tecnicos_total_cubierta INT DEFAULT NULL,
          otros_tecnicos_de_ellos_fem INT DEFAULT NULL,
          otros_tecnicos_jovenes_total INT DEFAULT NULL,
          otros_tecnicos_jovenes_fem INT DEFAULT NULL,
          otros_tecnicos_fem INT DEFAULT NULL,
          otros_tecnicos_pt INT DEFAULT NULL,
          otros_tecnicos_pa INT DEFAULT NULL,
          otros_tecnicos_as INT DEFAULT NULL,
          otros_tecnicos_i INT DEFAULT NULL,
          otros_tecnicos_it INT DEFAULT NULL,
          otros_tecnicos_ia INT DEFAULT NULL,
          otros_tecnicos_iag INT DEFAULT NULL,
          otros_tecnicos_ai INT DEFAULT NULL,
          otros_tecnicos_aux_tec_doc INT DEFAULT NULL,
          otros_tecnicos_msc INT DEFAULT NULL,
          otros_tecnicos_dr INT DEFAULT NULL,
          administrativos_total_cubierta INT DEFAULT NULL,
          administrativos_de_ellos_fem INT DEFAULT NULL,
          administrativos_jovenes_total INT DEFAULT NULL,
          administrativos_jovenes_fem INT DEFAULT NULL,
          administrativos_fem INT DEFAULT NULL,
          administrativos_pt INT DEFAULT NULL,
          administrativos_pa INT DEFAULT NULL,
          administrativos_as INT DEFAULT NULL,
          administrativos_i INT DEFAULT NULL,
          administrativos_it INT DEFAULT NULL,
          administrativos_ia INT DEFAULT NULL,
          administrativos_iag INT DEFAULT NULL,
          administrativos_ai INT DEFAULT NULL,
          administrativos_aux_tec_doc INT DEFAULT NULL,
          administrativos_msc INT DEFAULT NULL,
          administrativos_dr INT DEFAULT NULL,
          servicio_total_cubierta INT DEFAULT NULL,
          servicio_de_ellos_fem INT DEFAULT NULL,
          servicio_jovenes_total INT DEFAULT NULL,
          servicio_jovenes_fem INT DEFAULT NULL,
          servicio_fem INT DEFAULT NULL,
          servicio_pt INT DEFAULT NULL,
          servicio_pa INT DEFAULT NULL,
          servicio_as INT DEFAULT NULL,
          servicio_i INT DEFAULT NULL,
          servicio_it INT DEFAULT NULL,
          servicio_ia INT DEFAULT NULL,
          servicio_iag INT DEFAULT NULL,
          servicio_ai INT DEFAULT NULL,
          servicio_aux_tec_doc INT DEFAULT NULL,
          servicio_msc INT DEFAULT NULL,
          servicio_dr INT DEFAULT NULL,
          operarios_total_cubierta INT DEFAULT NULL,
          operarios_de_ellos_fem INT DEFAULT NULL,
          operarios_jovenes_total INT DEFAULT NULL,
          operarios_jovenes_fem INT DEFAULT NULL,
          operarios_fem INT DEFAULT NULL,
          operarios_pt INT DEFAULT NULL,
          operarios_pa INT DEFAULT NULL,
          operarios_as INT DEFAULT NULL,
          operarios_i INT DEFAULT NULL,
          operarios_it INT DEFAULT NULL,
          operarios_ia INT DEFAULT NULL,
          operarios_iag INT DEFAULT NULL,
          operarios_ai INT DEFAULT NULL,
          operarios_aux_tec_doc INT DEFAULT NULL,
          operarios_msc INT DEFAULT NULL,
          operarios_dr INT DEFAULT NULL,
          total_total_cubierta INT DEFAULT NULL,
          total_de_ellos_fem INT DEFAULT NULL,
          total_jovenes_total INT DEFAULT NULL,
          total_jovenes_fem INT DEFAULT NULL,
          total_fem INT DEFAULT NULL,
          total_pt INT DEFAULT NULL,
          total_pa INT DEFAULT NULL,
          total_as INT DEFAULT NULL,
          total_i INT DEFAULT NULL,
          total_it INT DEFAULT NULL,
          total_ia INT DEFAULT NULL,
          total_iag INT DEFAULT NULL,
          total_ai INT DEFAULT NULL,
          total_aux_tec_doc INT DEFAULT NULL,
          total_msc INT DEFAULT NULL,
          total_dr INT DEFAULT NULL,
          profesores_tipo_parcial_total_cubierta INT DEFAULT NULL,
          profesores_tipo_parcial_de_ellos_fem INT DEFAULT NULL,
          profesores_tipo_parcial_jovenes_total INT DEFAULT NULL,
          profesores_tipo_parcial_jovenes_fem INT DEFAULT NULL,
          profesores_tipo_parcial_fem INT DEFAULT NULL,
          profesores_tipo_parcial_pt INT DEFAULT NULL,
          profesores_tipo_parcial_pa INT DEFAULT NULL,
          profesores_tipo_parcial_as INT DEFAULT NULL,
          profesores_tipo_parcial_i INT DEFAULT NULL,
          profesores_tipo_parcial_it INT DEFAULT NULL,
          profesores_tipo_parcial_ia INT DEFAULT NULL,
          profesores_tipo_parcial_iag INT DEFAULT NULL,
          profesores_tipo_parcial_ai INT DEFAULT NULL,
          profesores_tipo_parcial_aux_tec_doc INT DEFAULT NULL,
          profesores_tipo_parcial_msc INT DEFAULT NULL,
          profesores_tipo_parcial_dr INT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_9015EE996CA204EF ON rrhh.tbd_ae3 (entidad_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_mes_anio_ae3 ON rrhh.tbd_ae3 (mes, anno, entidad_id)');
        $this->addSql('CREATE TABLE evaluacion.tbd_aplazamiento_solicitud (
          id INT NOT NULL,
          solicitud_id INT NOT NULL,
          estado_aplazamiento_id INT NOT NULL,
          fecha_propuesta_aplazamiento TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_460B9DEF1CB9D6E4 ON evaluacion.tbd_aplazamiento_solicitud (solicitud_id)');
        $this->addSql('CREATE INDEX IDX_460B9DEFBB071712 ON evaluacion.tbd_aplazamiento_solicitud (estado_aplazamiento_id)');
        $this->addSql('CREATE TABLE informatizacion.tbd_centro_dato_virtual (
          id INT NOT NULL,
          estructura_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          ram VARCHAR(255) DEFAULT NULL,
          cpu VARCHAR(255) DEFAULT NULL,
          hdd VARCHAR(255) DEFAULT NULL,
          hdd_salva VARCHAR(255) DEFAULT NULL,
          cantidad_ip_reales VARCHAR(255) DEFAULT NULL,
          precio VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4F1FC251CD2B7E7C ON informatizacion.tbd_centro_dato_virtual (estructura_id)');
        $this->addSql('CREATE TABLE evaluacion.tbd_comision (
          id INT NOT NULL,
          solicitud_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_CA64D28A1CB9D6E4 ON evaluacion.tbd_comision (solicitud_id)');
        $this->addSql('CREATE TABLE postgrado.tbd_comision (
          id INT NOT NULL,
          tipo_comision_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C80B22D17E110519 ON postgrado.tbd_comision (tipo_comision_id)');
        $this->addSql('CREATE TABLE pregrado.tbd_comision_nacional (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE traza.tbd_configuracion_traza (
          id INT NOT NULL,
          persona_id INT NOT NULL,
          activo BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_447D6EE4F5F88DB9 ON traza.tbd_configuracion_traza (persona_id)');
        $this->addSql('CREATE TABLE convenio.tbd_convenio (
          id INT NOT NULL,
          modalidad_id INT DEFAULT NULL,
          region_id INT DEFAULT NULL,
          tipo_id INT DEFAULT NULL,
          institucion_cubana_id INT DEFAULT NULL,
          institucion_extranjera_id INT DEFAULT NULL,
          pais_id INT DEFAULT NULL,
          creado_por_id INT DEFAULT NULL,
          modificado_por_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          fecha_suscribe TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_caducidad TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          cantidad_acciones INT DEFAULT NULL,
          documento VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_113EF7501E092B9F ON convenio.tbd_convenio (modalidad_id)');
        $this->addSql('CREATE INDEX IDX_113EF75098260155 ON convenio.tbd_convenio (region_id)');
        $this->addSql('CREATE INDEX IDX_113EF750A9276E6C ON convenio.tbd_convenio (tipo_id)');
        $this->addSql('CREATE INDEX IDX_113EF7509098BF83 ON convenio.tbd_convenio (institucion_cubana_id)');
        $this->addSql('CREATE INDEX IDX_113EF75093977F77 ON convenio.tbd_convenio (institucion_extranjera_id)');
        $this->addSql('CREATE INDEX IDX_113EF750C604D5C6 ON convenio.tbd_convenio (pais_id)');
        $this->addSql('CREATE INDEX IDX_113EF750FE35D8C4 ON convenio.tbd_convenio (creado_por_id)');
        $this->addSql('CREATE INDEX IDX_113EF7508C871111 ON convenio.tbd_convenio (modificado_por_id)');
        $this->addSql('CREATE TABLE evaluacion.tbd_convocatoria (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          fecha_inicio TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_fin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          carta VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbd_copep (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE tramite.tbd_documento_salida (
          id INT NOT NULL,
          ficha_salida_id INT DEFAULT NULL,
          persona_id INT DEFAULT NULL,
          institucion_cubana_id INT DEFAULT NULL,
          pais_id INT DEFAULT NULL,
          institucion_extranjera_id INT DEFAULT NULL,
          concepto_salida_id INT DEFAULT NULL,
          tipo_pasaporte_id INT DEFAULT NULL,
          estado_documento_salida_id INT DEFAULT NULL,
          directivo_firma_id INT DEFAULT NULL,
          provincia_extranjera TEXT DEFAULT NULL,
          objetivo TEXT DEFAULT NULL,
          fecha_salida_prevista TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_regreso_prevista TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_salida_real TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_regreso_real TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          tiempo_estancia INT DEFAULT NULL,
          numero_pasaporte VARCHAR(50) DEFAULT NULL,
          fecha_emision_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_caducidad_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          requiere_visa BOOLEAN DEFAULT NULL,
          aprobado_factores_ies BOOLEAN DEFAULT NULL,
          carta_invitacion VARCHAR(255) DEFAULT NULL,
          fecha_finalizado TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_firma_directivo TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_entrega_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_recogida_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          mandatos_mision TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_117159B31114A03B ON tramite.tbd_documento_salida (ficha_salida_id)');
        $this->addSql('CREATE INDEX IDX_117159B3F5F88DB9 ON tramite.tbd_documento_salida (persona_id)');
        $this->addSql('CREATE INDEX IDX_117159B39098BF83 ON tramite.tbd_documento_salida (institucion_cubana_id)');
        $this->addSql('CREATE INDEX IDX_117159B3C604D5C6 ON tramite.tbd_documento_salida (pais_id)');
        $this->addSql('CREATE INDEX IDX_117159B393977F77 ON tramite.tbd_documento_salida (institucion_extranjera_id)');
        $this->addSql('CREATE INDEX IDX_117159B34BEE089D ON tramite.tbd_documento_salida (concepto_salida_id)');
        $this->addSql('CREATE INDEX IDX_117159B3251EA755 ON tramite.tbd_documento_salida (tipo_pasaporte_id)');
        $this->addSql('CREATE INDEX IDX_117159B3BDDB5F58 ON tramite.tbd_documento_salida (estado_documento_salida_id)');
        $this->addSql('CREATE INDEX IDX_117159B321B6FE31 ON tramite.tbd_documento_salida (directivo_firma_id)');
        $this->addSql('CREATE TABLE tramite.tbd_documento_salida_historial_documento (
          id INT NOT NULL,
          documento_salida_id INT NOT NULL,
          usuario_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          archivo VARCHAR(255) NOT NULL,
          fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_8A01F346E2962F06 ON tramite.tbd_documento_salida_historial_documento (documento_salida_id)');
        $this->addSql('CREATE INDEX IDX_8A01F346DB38439E ON tramite.tbd_documento_salida_historial_documento (usuario_id)');
        $this->addSql('CREATE TABLE informatizacion.tbd_enlace_conectividad (
          id INT NOT NULL,
          tipo_conectividad_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          ed VARCHAR(255) DEFAULT NULL,
          ancho_banda VARCHAR(255) DEFAULT NULL,
          precio VARCHAR(255) DEFAULT NULL,
          unidad_medida VARCHAR(255) DEFAULT NULL,
          tipo_conexion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_CA8531856451B6B1 ON informatizacion.tbd_enlace_conectividad (tipo_conectividad_id)');
        $this->addSql('CREATE INDEX IDX_CA853185CD2B7E7C ON informatizacion.tbd_enlace_conectividad (estructura_id)');
        $this->addSql('CREATE TABLE estructura.tbd_entidad (
          id INT NOT NULL,
          estructura_id INT DEFAULT NULL,
          tipo_entidad_id INT NOT NULL,
          entidad_id INT DEFAULT NULL,
          municipio_id INT NOT NULL,
          provincia_id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          email VARCHAR(180) NOT NULL,
          telefono VARCHAR(15) NOT NULL,
          direccion TEXT NOT NULL,
          codigo TEXT NOT NULL,
          siglas TEXT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB79ADA6CD2B7E7C ON estructura.tbd_entidad (estructura_id)');
        $this->addSql('CREATE INDEX IDX_AB79ADA6FD2406C7 ON estructura.tbd_entidad (tipo_entidad_id)');
        $this->addSql('CREATE INDEX IDX_AB79ADA66CA204EF ON estructura.tbd_entidad (entidad_id)');
        $this->addSql('CREATE INDEX IDX_AB79ADA658BC1BE0 ON estructura.tbd_entidad (municipio_id)');
        $this->addSql('CREATE INDEX IDX_AB79ADA64E7121AF ON estructura.tbd_entidad (provincia_id)');
        $this->addSql('CREATE TABLE estructura.tbd_estructura (
          id INT NOT NULL,
          tipo_estructura_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          categoria_estructura_id INT DEFAULT NULL,
          nivel_id INT DEFAULT NULL,
          municipio_id INT NOT NULL,
          provincia_id INT NOT NULL,
          grupo_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          clasificacion_presupuestaria VARCHAR(5) DEFAULT NULL,
          siglas VARCHAR(15) DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          es_entidad BOOLEAN DEFAULT NULL,
          centro_autorizado_posgrado BOOLEAN DEFAULT NULL,
          iafd BOOLEAN DEFAULT NULL,
          fecha_activacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          ubicacion VARCHAR(255) DEFAULT NULL,
          email VARCHAR(180) DEFAULT NULL,
          telefono VARCHAR(15) DEFAULT NULL,
          direccion TEXT NOT NULL,
          orden INT DEFAULT 0 NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_BF47AB85CD982D22 ON estructura.tbd_estructura (tipo_estructura_id)');
        $this->addSql('CREATE INDEX IDX_BF47AB85CD2B7E7C ON estructura.tbd_estructura (estructura_id)');
        $this->addSql('CREATE INDEX IDX_BF47AB854498FA55 ON estructura.tbd_estructura (categoria_estructura_id)');
        $this->addSql('CREATE INDEX IDX_BF47AB85DA3426AE ON estructura.tbd_estructura (nivel_id)');
        $this->addSql('CREATE INDEX IDX_BF47AB8558BC1BE0 ON estructura.tbd_estructura (municipio_id)');
        $this->addSql('CREATE INDEX IDX_BF47AB854E7121AF ON estructura.tbd_estructura (provincia_id)');
        $this->addSql('CREATE INDEX IDX_BF47AB859C833003 ON estructura.tbd_estructura (grupo_id)');
        $this->addSql('CREATE TABLE tramite.tbd_ficha_salida (
          id INT NOT NULL,
          persona_id INT DEFAULT NULL,
          institucion_cubana_id INT DEFAULT NULL,
          pais_id INT DEFAULT NULL,
          institucion_extranjera_id INT DEFAULT NULL,
          concepto_salida_id INT DEFAULT NULL,
          tipo_pasaporte_id INT DEFAULT NULL,
          estado_ficha_salida_id INT DEFAULT NULL,
          directivo_firma_id INT DEFAULT NULL,
          provincia_extranjera TEXT DEFAULT NULL,
          objetivo TEXT DEFAULT NULL,
          fecha_salida_prevista TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_regreso_prevista TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_salida_real TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_regreso_real TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          tiempo_estancia INT DEFAULT NULL,
          numero_pasaporte VARCHAR(50) DEFAULT NULL,
          fecha_emision_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_caducidad_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          requiere_visa BOOLEAN DEFAULT NULL,
          aprobado_factores_ies BOOLEAN DEFAULT NULL,
          carta_invitacion VARCHAR(255) DEFAULT NULL,
          fecha_firma_directivo TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          ficha_salida VARCHAR(255) DEFAULT NULL,
          mandatos_mision TEXT DEFAULT NULL,
          clasificacion VARCHAR(20) DEFAULT NULL,
          consulta_embajada BOOLEAN DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B4D7C548F5F88DB9 ON tramite.tbd_ficha_salida (persona_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C5489098BF83 ON tramite.tbd_ficha_salida (institucion_cubana_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C548C604D5C6 ON tramite.tbd_ficha_salida (pais_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C54893977F77 ON tramite.tbd_ficha_salida (institucion_extranjera_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C5484BEE089D ON tramite.tbd_ficha_salida (concepto_salida_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C548251EA755 ON tramite.tbd_ficha_salida (tipo_pasaporte_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C5481693D208 ON tramite.tbd_ficha_salida (estado_ficha_salida_id)');
        $this->addSql('CREATE INDEX IDX_B4D7C54821B6FE31 ON tramite.tbd_ficha_salida (directivo_firma_id)');
        $this->addSql('CREATE TABLE tramite.tbd_ficha_salida_historial_documento (
          id INT NOT NULL,
          ficha_salida_id INT NOT NULL,
          usuario_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          archivo VARCHAR(255) NOT NULL,
          fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_E80A77BD1114A03B ON tramite.tbd_ficha_salida_historial_documento (ficha_salida_id)');
        $this->addSql('CREATE INDEX IDX_E80A77BDDB38439E ON tramite.tbd_ficha_salida_historial_documento (usuario_id)');
        $this->addSql('CREATE TABLE seguridad.tbd_funcionalidad (
          id INT NOT NULL,
          modulo_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          role_key VARCHAR(250) NOT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A72671893EF22FDB ON seguridad.tbd_funcionalidad (role_key)');
        $this->addSql('CREATE INDEX IDX_A7267189C07F55F5 ON seguridad.tbd_funcionalidad (modulo_id)');
        $this->addSql('CREATE TABLE institucion.tbd_institucion (
          id INT NOT NULL,
          grado_academico_rector_id INT DEFAULT NULL,
          tipo_institucion_id INT DEFAULT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          logo VARCHAR(255) NOT NULL,
          siglas VARCHAR(100) NOT NULL,
          codigo VARCHAR(20) DEFAULT NULL,
          rector VARCHAR(255) NOT NULL,
          organigrama VARCHAR(255) DEFAULT NULL,
          fecha_fundacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          lema TEXT DEFAULT NULL,
          mision TEXT NOT NULL,
          vision TEXT NOT NULL,
          sitio_web VARCHAR(255) DEFAULT NULL,
          telefono VARCHAR(50) DEFAULT NULL,
          correo VARCHAR(50) DEFAULT NULL,
          direccion_sede_principal TEXT NOT NULL,
          coordenadas_sede_principal VARCHAR(255) DEFAULT NULL,
          campus_universitario TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_739EFE83C6879E6C ON institucion.tbd_institucion (grado_academico_rector_id)');
        $this->addSql('CREATE INDEX IDX_739EFE833EEA3466 ON institucion.tbd_institucion (tipo_institucion_id)');
        $this->addSql('CREATE INDEX IDX_739EFE83A8AF3830 ON institucion.tbd_institucion (categoria_acreditacion_id)');
        $this->addSql('CREATE INDEX IDX_739EFE83CD2B7E7C ON institucion.tbd_institucion (estructura_id)');
        $this->addSql('CREATE TABLE tramite.tbd_institucion_extranjera (
          id INT NOT NULL,
          pais_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          provincia VARCHAR(100) DEFAULT NULL,
          siglas VARCHAR(100) DEFAULT NULL,
          codigo VARCHAR(20) DEFAULT NULL,
          rector VARCHAR(255) DEFAULT NULL,
          sitio_web VARCHAR(255) DEFAULT NULL,
          telefono VARCHAR(50) DEFAULT NULL,
          correo VARCHAR(50) DEFAULT NULL,
          direccion_sede_principal TEXT DEFAULT NULL,
          coordenadas_sede_principal VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_D6AEBBC5C604D5C6 ON tramite.tbd_institucion_extranjera (pais_id)');
        $this->addSql('CREATE TABLE informatizacion.tbd_linea_celular (
          id INT NOT NULL,
          estructura_id INT DEFAULT NULL,
          numero_telefono TEXT DEFAULT NULL,
          pin TEXT DEFAULT NULL,
          puk TEXT DEFAULT NULL,
          plan_voz TEXT DEFAULT NULL,
          plan_sms TEXT DEFAULT NULL,
          plan_datos TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_76D64B5ECD2B7E7C ON informatizacion.tbd_linea_celular (estructura_id)');
        $this->addSql('CREATE TABLE seguridad.tbd_modulo (
          id INT NOT NULL,
          nombre TEXT NOT NULL,
          descripcion TEXT DEFAULT NULL,
          module_key VARCHAR(255) DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_285F9BC13A909126 ON seguridad.tbd_modulo (nombre)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_285F9BC166E3A25D ON seguridad.tbd_modulo (module_key)');
        $this->addSql('CREATE TABLE informatizacion.tbd_nauta_hogar (
          id INT NOT NULL,
          responsable_id INT DEFAULT NULL,
          servicio_contratado_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          numero_telefono TEXT DEFAULT NULL,
          direccion_instalacion TEXT DEFAULT NULL,
          precio TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_7A25B70E53C59D72 ON informatizacion.tbd_nauta_hogar (responsable_id)');
        $this->addSql('CREATE INDEX IDX_7A25B70E5354BD7D ON informatizacion.tbd_nauta_hogar (servicio_contratado_id)');
        $this->addSql('CREATE INDEX IDX_7A25B70ECD2B7E7C ON informatizacion.tbd_nauta_hogar (estructura_id)');
        $this->addSql('CREATE TABLE tramite.tbd_pasaporte (
          id INT NOT NULL,
          persona_id INT NOT NULL,
          tipo_pasaporte_id INT DEFAULT NULL,
          numero_pasaporte VARCHAR(50) DEFAULT NULL,
          fecha_emision_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_caducidad_pasaporte TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          activo BOOLEAN DEFAULT NULL,
          cara1 VARCHAR(255) DEFAULT NULL,
          cara2 VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_E7E54A21F5F88DB9 ON tramite.tbd_pasaporte (persona_id)');
        $this->addSql('CREATE INDEX IDX_E7E54A21251EA755 ON tramite.tbd_pasaporte (tipo_pasaporte_id)');
        $this->addSql('CREATE TABLE personal.tbd_persona (
          id INT NOT NULL,
          provincia_id INT DEFAULT NULL,
          municipio_id INT DEFAULT NULL,
          clasificacion_persona_id INT DEFAULT NULL,
          nivel_escolar_id INT DEFAULT NULL,
          categoria_docente_id INT DEFAULT NULL,
          profesion_id INT DEFAULT NULL,
          grado_academico_id INT DEFAULT NULL,
          categoria_investigativa_id INT DEFAULT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          sexo_id INT DEFAULT NULL,
          categoria_estructura_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          entidad_id INT DEFAULT NULL,
          responsabilidad_id INT DEFAULT NULL,
          usuario_id INT NOT NULL,
          activo BOOLEAN NOT NULL,
          carnet_identidad VARCHAR(11) NOT NULL,
          numero_serie_carnet_identidad VARCHAR(11) DEFAULT NULL,
          primer_nombre VARCHAR(50) NOT NULL,
          segundo_nombre VARCHAR(50) DEFAULT NULL,
          primer_apellido VARCHAR(50) DEFAULT NULL,
          segundo_apellido VARCHAR(50) DEFAULT NULL,
          fecha_nacimiento TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          email VARCHAR(180) DEFAULT NULL,
          celular VARCHAR(15) DEFAULT NULL,
          telefono VARCHAR(15) DEFAULT NULL,
          direccion TEXT DEFAULT NULL,
          foto VARCHAR(255) DEFAULT NULL,
          twitter VARCHAR(180) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D817A223327C5C8A ON personal.tbd_persona (carnet_identidad)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D817A223D1E70FBE ON personal.tbd_persona (numero_serie_carnet_identidad)');
        $this->addSql('CREATE INDEX IDX_D817A2234E7121AF ON personal.tbd_persona (provincia_id)');
        $this->addSql('CREATE INDEX IDX_D817A22358BC1BE0 ON personal.tbd_persona (municipio_id)');
        $this->addSql('CREATE INDEX IDX_D817A223CF5FD27C ON personal.tbd_persona (clasificacion_persona_id)');
        $this->addSql('CREATE INDEX IDX_D817A2235873B5B9 ON personal.tbd_persona (nivel_escolar_id)');
        $this->addSql('CREATE INDEX IDX_D817A22392D84F6 ON personal.tbd_persona (categoria_docente_id)');
        $this->addSql('CREATE INDEX IDX_D817A223C5AF4D0F ON personal.tbd_persona (profesion_id)');
        $this->addSql('CREATE INDEX IDX_D817A2234683F2FA ON personal.tbd_persona (grado_academico_id)');
        $this->addSql('CREATE INDEX IDX_D817A223D3ECE4BD ON personal.tbd_persona (categoria_investigativa_id)');
        $this->addSql('CREATE INDEX IDX_D817A22374422427 ON personal.tbd_persona (solicitud_programa_academico_id)');
        $this->addSql('CREATE INDEX IDX_D817A2232B32DB58 ON personal.tbd_persona (sexo_id)');
        $this->addSql('CREATE INDEX IDX_D817A2234498FA55 ON personal.tbd_persona (categoria_estructura_id)');
        $this->addSql('CREATE INDEX IDX_D817A223CD2B7E7C ON personal.tbd_persona (estructura_id)');
        $this->addSql('CREATE INDEX IDX_D817A2236CA204EF ON personal.tbd_persona (entidad_id)');
        $this->addSql('CREATE INDEX IDX_D817A22396079139 ON personal.tbd_persona (responsabilidad_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D817A223DB38439E ON personal.tbd_persona (usuario_id)');
        $this->addSql('CREATE TABLE pregrado.tbd_plan_estudio (
          id INT NOT NULL,
          curso_academico_id INT NOT NULL,
          carrera_id INT DEFAULT NULL,
          rama_ciencia_id INT DEFAULT NULL,
          organismo_demandante_id INT DEFAULT NULL,
          organismo_formador_id INT DEFAULT NULL,
          tipo_programa_academico_id INT DEFAULT NULL,
          oace_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          anno_aprobacion INT NOT NULL,
          fecha_aprobacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          plan_estudio VARCHAR(255) DEFAULT NULL,
          duracion_curso_diurno INT DEFAULT NULL,
          duracion_curso_por_encuentro INT DEFAULT NULL,
          duracion_curso_distancia VARCHAR(255) DEFAULT NULL,
          descripcion_plan_estudio VARCHAR(255) DEFAULT NULL,
          documento_ejecutivo VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2531662F7D35438E ON pregrado.tbd_plan_estudio (curso_academico_id)');
        $this->addSql('CREATE INDEX IDX_2531662FC671B40F ON pregrado.tbd_plan_estudio (carrera_id)');
        $this->addSql('CREATE INDEX IDX_2531662FF0FF4047 ON pregrado.tbd_plan_estudio (rama_ciencia_id)');
        $this->addSql('CREATE INDEX IDX_2531662F9B2A9D11 ON pregrado.tbd_plan_estudio (organismo_demandante_id)');
        $this->addSql('CREATE INDEX IDX_2531662F2F480A85 ON pregrado.tbd_plan_estudio (organismo_formador_id)');
        $this->addSql('CREATE INDEX IDX_2531662F16689255 ON pregrado.tbd_plan_estudio (tipo_programa_academico_id)');
        $this->addSql('CREATE INDEX IDX_2531662F68502903 ON pregrado.tbd_plan_estudio (oace_id)');
        $this->addSql('CREATE TABLE tramite.tbd_plan_mision (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbd_plantilla (
          id INT NOT NULL,
          persona_id INT NOT NULL,
          estructura_id INT NOT NULL,
          responsabilidad_id INT DEFAULT NULL,
          id_expediente VARCHAR(36) NOT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_405B8B6CF5F88DB9 ON personal.tbd_plantilla (persona_id)');
        $this->addSql('CREATE INDEX IDX_405B8B6CCD2B7E7C ON personal.tbd_plantilla (estructura_id)');
        $this->addSql('CREATE INDEX IDX_405B8B6C96079139 ON personal.tbd_plantilla (responsabilidad_id)');
        $this->addSql('CREATE TABLE pregrado.tbd_programa_academico_desactivado (
          id INT NOT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          curso_academico_id INT DEFAULT NULL,
          solicitud_centro_rector VARCHAR(255) DEFAULT NULL,
          dictamen_aprobacion VARCHAR(255) DEFAULT NULL,
          resolucion_desactivacion VARCHAR(255) DEFAULT NULL,
          descripcion VARCHAR(255) DEFAULT NULL,
          fecha_eliminacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_44A10FF974422427 ON pregrado.tbd_programa_academico_desactivado (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE INDEX IDX_44A10FF97D35438E ON pregrado.tbd_programa_academico_desactivado (curso_academico_id)');
        $this->addSql('CREATE TABLE pregrado.tbd_programa_academico_reabierto (
          id INT NOT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          curso_academico_id INT DEFAULT NULL,
          fundamentacion_reapertura VARCHAR(255) DEFAULT NULL,
          dictamen_dgp VARCHAR(255) DEFAULT NULL,
          descripcion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_833BE5D174422427 ON pregrado.tbd_programa_academico_reabierto (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE INDEX IDX_833BE5D17D35438E ON pregrado.tbd_programa_academico_reabierto (curso_academico_id)');
        $this->addSql('CREATE TABLE personal.tbd_responsable (
          id INT NOT NULL,
          persona_id INT NOT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2355D865F5F88DB9 ON personal.tbd_responsable (persona_id)');
        $this->addSql('CREATE TABLE seguridad.tbd_rol (
          id INT NOT NULL,
          nombre TEXT NOT NULL,
          descripcion TEXT DEFAULT NULL,
          role_key VARCHAR(255) DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F164513A909126 ON seguridad.tbd_rol (nombre)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F164513EF22FDB ON seguridad.tbd_rol (role_key)');
        $this->addSql('CREATE TABLE seguridad.tbr_rol_funcionalidad (
          rol_id INT NOT NULL,
          funcionalidad_id INT NOT NULL,
          PRIMARY KEY(rol_id, funcionalidad_id)
        )');
        $this->addSql('CREATE INDEX IDX_5C2783B44BAB96C ON seguridad.tbr_rol_funcionalidad (rol_id)');
        $this->addSql('CREATE INDEX IDX_5C2783B43637E16E ON seguridad.tbr_rol_funcionalidad (funcionalidad_id)');
        $this->addSql('CREATE TABLE informatizacion.tbd_servicio (
          id INT NOT NULL,
          visibilidad_id INT DEFAULT NULL,
          publico_objetivo_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          clasificacion VARCHAR(255) DEFAULT NULL,
          direccion_web VARCHAR(255) DEFAULT NULL,
          integracion_pasarela_pago BOOLEAN DEFAULT NULL,
          integracion_firma_digital BOOLEAN DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_55FC9E5E94F69A8C ON informatizacion.tbd_servicio (visibilidad_id)');
        $this->addSql('CREATE INDEX IDX_55FC9E5EE90161FC ON informatizacion.tbd_servicio (publico_objetivo_id)');
        $this->addSql('CREATE INDEX IDX_55FC9E5ECD2B7E7C ON informatizacion.tbd_servicio (estructura_id)');
        $this->addSql('CREATE TABLE informatizacion.tbd_sistema_informatico (
          id INT NOT NULL,
          visibilidad_id INT DEFAULT NULL,
          tipo_sistema_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          direccion_web VARCHAR(255) DEFAULT NULL,
          desarrollador VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B6DE6A6A94F69A8C ON informatizacion.tbd_sistema_informatico (visibilidad_id)');
        $this->addSql('CREATE INDEX IDX_B6DE6A6A864BA020 ON informatizacion.tbd_sistema_informatico (tipo_sistema_id)');
        $this->addSql('CREATE INDEX IDX_B6DE6A6ACD2B7E7C ON informatizacion.tbd_sistema_informatico (estructura_id)');
        $this->addSql('CREATE TABLE evaluacion.tbd_solicitud (
          id INT NOT NULL,
          convocatoria_id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          programa_pregrado_id INT DEFAULT NULL,
          programa_posgrado_id INT DEFAULT NULL,
          estado_solicitud_id INT NOT NULL,
          categoria_acreditacion_alcanzada_id INT DEFAULT NULL,
          tipo_solicitud VARCHAR(255) NOT NULL,
          fecha_propuesta TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          fecha_aprobada TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          carta_solicitud VARCHAR(255) NOT NULL,
          informe_autoevaluacion VARCHAR(255) DEFAULT NULL,
          plan_trabajo VARCHAR(255) DEFAULT NULL,
          estado_informe_autoevaluacion VARCHAR(255) DEFAULT NULL,
          estado_plan_trabajo VARCHAR(255) DEFAULT NULL,
          categoria_acreditacion_actual VARCHAR(255) DEFAULT NULL,
          motivo_rechazo TEXT DEFAULT NULL,
          dictamen_cte VARCHAR(255) DEFAULT NULL,
          dictamen_jan VARCHAR(255) DEFAULT NULL,
          fecha_emision TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          numero_pleno VARCHAR(255) DEFAULT NULL,
          numero_acuerdo_pleno VARCHAR(255) DEFAULT NULL,
          annos_vigencia_categoria_acreditacion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_46681D6C4EE93BE6 ON evaluacion.tbd_solicitud (convocatoria_id)');
        $this->addSql('CREATE INDEX IDX_46681D6CB239FBC6 ON evaluacion.tbd_solicitud (institucion_id)');
        $this->addSql('CREATE INDEX IDX_46681D6C65EC4CB2 ON evaluacion.tbd_solicitud (programa_pregrado_id)');
        $this->addSql('CREATE INDEX IDX_46681D6C68667D11 ON evaluacion.tbd_solicitud (programa_posgrado_id)');
        $this->addSql('CREATE INDEX IDX_46681D6C77E88BF9 ON evaluacion.tbd_solicitud (estado_solicitud_id)');
        $this->addSql('CREATE INDEX IDX_46681D6CEC47E158 ON evaluacion.tbd_solicitud (
          categoria_acreditacion_alcanzada_id
        )');
        $this->addSql('CREATE TABLE postgrado.tbd_solicitud_programa (
          id INT NOT NULL,
          tipo_solicitud_id INT DEFAULT NULL,
          tipo_solicitud_clasificacion_id INT DEFAULT NULL,
          estado_programa_id INT DEFAULT NULL,
          universidad_id INT DEFAULT NULL,
          original_de_id INT DEFAULT NULL,
          tipo_programa_id INT NOT NULL,
          rama_ciencia_id INT NOT NULL,
          modalidad_programa_id INT NOT NULL,
          presencialidad_programa_id INT DEFAULT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          nombre TEXT NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          nombre_coordinador VARCHAR(255) NOT NULL,
          correo_coordinador VARCHAR(255) NOT NULL,
          telefono_coordinador VARCHAR(255) DEFAULT NULL,
          codigo_programa VARCHAR(50) DEFAULT NULL,
          fecha_proxima_acreditacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          fecha_aprobacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          duracion_programa INT DEFAULT NULL,
          cantidad_creditos INT DEFAULT NULL,
          doc_programa VARCHAR(255) DEFAULT NULL,
          resolucion_programa VARCHAR(255) DEFAULT NULL,
          dictamen_general VARCHAR(255) DEFAULT NULL,
          dictamen_final VARCHAR(255) DEFAULT NULL,
          anno_acreditacion VARCHAR(4) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_59D7F1CFAFEA88E4 ON postgrado.tbd_solicitud_programa (tipo_solicitud_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CF45D881AC ON postgrado.tbd_solicitud_programa (
          tipo_solicitud_clasificacion_id
        )');
        $this->addSql('CREATE INDEX IDX_59D7F1CFC7869A78 ON postgrado.tbd_solicitud_programa (estado_programa_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CF271768CD ON postgrado.tbd_solicitud_programa (universidad_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CFF0D8DD71 ON postgrado.tbd_solicitud_programa (original_de_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CFC8AE5DD0 ON postgrado.tbd_solicitud_programa (tipo_programa_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CFF0FF4047 ON postgrado.tbd_solicitud_programa (rama_ciencia_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CF9EE50F0A ON postgrado.tbd_solicitud_programa (modalidad_programa_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CF84E810D2 ON postgrado.tbd_solicitud_programa (presencialidad_programa_id)');
        $this->addSql('CREATE INDEX IDX_59D7F1CFA8AF3830 ON postgrado.tbd_solicitud_programa (categoria_acreditacion_id)');
        $this->addSql('CREATE TABLE pregrado.tbd_solicitud_programa_academico (
          id INT NOT NULL,
          estado_programa_academico_id INT DEFAULT NULL,
          tipo_programa_academico_id INT NOT NULL,
          tipo_organismo_id INT DEFAULT NULL,
          organismo_demandante_id INT NOT NULL,
          centro_rector_id INT DEFAULT NULL,
          organismo_formador_id INT DEFAULT NULL,
          rama_ciencia_id INT DEFAULT NULL,
          centro_solicitante_id INT DEFAULT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          fundamentacion TEXT DEFAULT NULL,
          solicitud VARCHAR(255) DEFAULT NULL,
          resolucion VARCHAR(255) DEFAULT NULL,
          carta_aprobacion VARCHAR(255) DEFAULT NULL,
          fecha_aprobacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          descripcion_aprobacion TEXT DEFAULT NULL,
          descripcion_no_aprobacion TEXT DEFAULT NULL,
          duracion_curso_diurno INT DEFAULT NULL,
          duracion_curso_por_encuentro INT DEFAULT NULL,
          duracion_curso_adistancia INT DEFAULT NULL,
          modalidad_diurno BOOLEAN DEFAULT NULL,
          modalidad_por_encuentro BOOLEAN DEFAULT NULL,
          modalidad_adistancia BOOLEAN DEFAULT NULL,
          dictamen TEXT DEFAULT NULL,
          nombre_solicitante VARCHAR(255) DEFAULT NULL,
          correo_solicitante VARCHAR(255) DEFAULT NULL,
          telefono_solicitante VARCHAR(255) DEFAULT NULL,
          codigo_programa VARCHAR(100) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_5DC26277AC9ABFFB ON pregrado.tbd_solicitud_programa_academico (estado_programa_academico_id)');
        $this->addSql('CREATE INDEX IDX_5DC2627716689255 ON pregrado.tbd_solicitud_programa_academico (tipo_programa_academico_id)');
        $this->addSql('CREATE INDEX IDX_5DC2627781338691 ON pregrado.tbd_solicitud_programa_academico (tipo_organismo_id)');
        $this->addSql('CREATE INDEX IDX_5DC262779B2A9D11 ON pregrado.tbd_solicitud_programa_academico (organismo_demandante_id)');
        $this->addSql('CREATE INDEX IDX_5DC26277DC252D29 ON pregrado.tbd_solicitud_programa_academico (centro_rector_id)');
        $this->addSql('CREATE INDEX IDX_5DC262772F480A85 ON pregrado.tbd_solicitud_programa_academico (organismo_formador_id)');
        $this->addSql('CREATE INDEX IDX_5DC26277F0FF4047 ON pregrado.tbd_solicitud_programa_academico (rama_ciencia_id)');
        $this->addSql('CREATE INDEX IDX_5DC26277606D2041 ON pregrado.tbd_solicitud_programa_academico (centro_solicitante_id)');
        $this->addSql('CREATE INDEX IDX_5DC26277A8AF3830 ON pregrado.tbd_solicitud_programa_academico (categoria_acreditacion_id)');
        $this->addSql('CREATE TABLE informatizacion.tbd_telefono_celular (
          id INT NOT NULL,
          marca_id INT DEFAULT NULL,
          modelo_id INT DEFAULT NULL,
          sistema_operativo_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_26F77CCC81EF0041 ON informatizacion.tbd_telefono_celular (marca_id)');
        $this->addSql('CREATE INDEX IDX_26F77CCCC3A9576E ON informatizacion.tbd_telefono_celular (modelo_id)');
        $this->addSql('CREATE INDEX IDX_26F77CCCC616A017 ON informatizacion.tbd_telefono_celular (sistema_operativo_id)');
        $this->addSql('CREATE INDEX IDX_26F77CCCCD2B7E7C ON informatizacion.tbd_telefono_celular (estructura_id)');
        $this->addSql('CREATE TABLE traza.tbd_traza (
          id INT NOT NULL,
          tipo_traza_id INT NOT NULL,
          accion_id INT NOT NULL,
          objeto_id INT NOT NULL,
          persona_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          ip VARCHAR(255) NOT NULL,
          navegador VARCHAR(255) NOT NULL,
          sistema_operativo VARCHAR(255) NOT NULL,
          data TEXT NOT NULL,
          data_anterior TEXT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_5B9073638A5A731D ON traza.tbd_traza (tipo_traza_id)');
        $this->addSql('CREATE INDEX IDX_5B9073633F4B5275 ON traza.tbd_traza (accion_id)');
        $this->addSql('CREATE INDEX IDX_5B90736376F5CD27 ON traza.tbd_traza (objeto_id)');
        $this->addSql('CREATE INDEX IDX_5B907363F5F88DB9 ON traza.tbd_traza (persona_id)');
        $this->addSql('CREATE TABLE postgrado.tbd_universidad (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(3) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE seguridad.tbd_usuario (
          id INT NOT NULL,
          email VARCHAR(180) NOT NULL,
          PASSWORD VARCHAR(255) NOT NULL,
          role VARCHAR(255) NOT NULL,
          activo BOOLEAN NOT NULL,
          password_change_first_time BOOLEAN DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17879B6E7927C74 ON seguridad.tbd_usuario (email)');
        $this->addSql('CREATE TABLE seguridad.tbr_user_rol (
          user_id INT NOT NULL,
          rol_id INT NOT NULL,
          PRIMARY KEY(user_id, rol_id)
        )');
        $this->addSql('CREATE INDEX IDX_F808892AA76ED395 ON seguridad.tbr_user_rol (user_id)');
        $this->addSql('CREATE INDEX IDX_F808892A4BAB96C ON seguridad.tbr_user_rol (rol_id)');
        $this->addSql('CREATE TABLE traza.tbn_accion (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_carrera (
          id INT NOT NULL,
          nivel_escolar_id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_D3C253165873B5B9 ON personal.tbn_carrera (nivel_escolar_id)');
        $this->addSql('CREATE TABLE institucion.tbn_categoria_acreditacion (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          duracion INT DEFAULT NULL,
          color VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_categoria_categorizacion (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(3) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_categoria_docente (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE rrhh.tbn_categoria_docente (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE estructura.tbn_categoria_estructura (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          color VARCHAR(50) DEFAULT NULL,
          tributa_credencial BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_categoria_investigativa (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE estructura.tbn_categoria_responsabilidad (
          id INT NOT NULL,
          categoria_responsabilidad_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          color VARCHAR(50) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_39E2253E15B49E6B ON estructura.tbn_categoria_responsabilidad (categoria_responsabilidad_id)');
        $this->addSql('CREATE TABLE personal.tbn_clasificacion_persona (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE convenio.tbn_concepto_gasto (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE tramite.tbn_concepto_salida (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_curso_academico (
          id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion VARCHAR(255) DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_documento (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE rrhh.tbn_especialidad (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE evaluacion.tbn_estado_aplazamiento (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE tramite.tbn_estado_ficha_salida (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          documento_salida BOOLEAN DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_estado_programa (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_estado_programa_academico (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE evaluacion.tbn_estado_solicitud (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_grado_academico (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(8) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE rrhh.tbn_grupo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE configuracion.tbn_idioma (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(2) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_marca (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE convenio.tbn_modalida (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_modalidad_programa (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_modelo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE estructura.tbn_municipio (
          id INT NOT NULL,
          provincia_id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          codigo INT NOT NULL,
          ext_id VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_AF623AA94E7121AF ON estructura.tbn_municipio (provincia_id)');
        $this->addSql('CREATE TABLE estructura.tbn_nivel (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_nivel_escolar (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_oace (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE traza.tbn_objeto (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE convenio.tbn_obsequio (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          marca VARCHAR(50) DEFAULT NULL,
          modelo VARCHAR(50) DEFAULT NULL,
          color VARCHAR(50) DEFAULT NULL,
          talla VARCHAR(50) DEFAULT NULL,
          presentacion VARCHAR(50) DEFAULT NULL,
          tipo VARCHAR(50) DEFAULT NULL,
          genero VARCHAR(50) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_organismo_demandante (
          id INT NOT NULL,
          tipo_organismo_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(20) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4063FC281338691 ON pregrado.tbn_organismo_demandante (tipo_organismo_id)');
        $this->addSql('CREATE TABLE pregrado.tbn_organismo_formador (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_organizacion (
          id INT NOT NULL,
          tipo_organizacion_id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(10) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_7DDAD16046EB71B9 ON personal.tbn_organizacion (tipo_organizacion_id)');
        $this->addSql('CREATE TABLE estructura.tbn_pais (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          iso2 VARCHAR(255) NOT NULL,
          iso3 VARCHAR(255) NOT NULL,
          codigo_telefonico VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_presencialidad_programa (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_proceso (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_profesion (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE estructura.tbn_provincia (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          codigo INT NOT NULL,
          siglas VARCHAR(8) NOT NULL,
          ext_id VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_publico_objetivo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_rama_ciencia (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(3) DEFAULT NULL,
          color VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE institucion.tbn_recursos_humanos (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE institucion.tbn_red_social (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          logo VARCHAR(10) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE convenio.tbn_region (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE estructura.tbn_responsabilidad (
          id INT NOT NULL,
          categoria_responsabilidad_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_1C003A0215B49E6B ON estructura.tbn_responsabilidad (categoria_responsabilidad_id)');
        $this->addSql('CREATE TABLE evaluacion.tbn_rol_comision (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_rol_comision (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          copep BOOLEAN DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE institucion.tbn_rol_redes (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_servicio_contratado (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_sexo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(3) NOT NULL,
          ext_id VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_sistema_operativo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE convenio.tbn_tipo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_tipo_comision (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(9) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_tipo_conectividad (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE estructura.tbn_tipo_entidad (
          id INT NOT NULL,
          tipo_entidad_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          color VARCHAR(50) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_383FF7F6FD2406C7 ON estructura.tbn_tipo_entidad (tipo_entidad_id)');
        $this->addSql('CREATE TABLE estructura.tbn_tipo_estructura (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          color VARCHAR(50) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE institucion.tbn_tipo_institucion (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(10) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_tipo_organismo (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(5) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE personal.tbn_tipo_organizacion (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE tramite.tbn_tipo_pasaporte (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(10) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_tipo_programa (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          codigo VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE pregrado.tbn_tipo_programa_academico (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_tipo_sistema (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE postgrado.tbn_tipo_solicitud (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          siglas VARCHAR(3) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE traza.tbn_tipo_traza (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE tramite.tbn_tramite (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE informatizacion.tbn_visibilidad (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE institucion.tbn_visibilidad (
          id INT NOT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE convenio.tbr_convenio_accion (
          id INT NOT NULL,
          convenio_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_1EA2DC49F9D43F2A ON convenio.tbr_convenio_accion (convenio_id)');
        $this->addSql('CREATE TABLE tramite.tbr_documento_salida_tramite (
          id INT NOT NULL,
          documento_salida_id INT NOT NULL,
          tramite_id INT NOT NULL,
          listo BOOLEAN DEFAULT NULL,
          descripcion TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_D3C55E7E2962F06 ON tramite.tbr_documento_salida_tramite (documento_salida_id)');
        $this->addSql('CREATE INDEX IDX_D3C55E7820C2849 ON tramite.tbr_documento_salida_tramite (tramite_id)');
        $this->addSql('CREATE TABLE tramite.tbr_ficha_salida_concepto_gasto (
          id INT NOT NULL,
          ficha_salida_id INT NOT NULL,
          concepto_gasto_id INT NOT NULL,
          asumido_por VARCHAR(20) DEFAULT NULL,
          monto DOUBLE PRECISION DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_2C511CEF1114A03B ON tramite.tbr_ficha_salida_concepto_gasto (ficha_salida_id)');
        $this->addSql('CREATE INDEX IDX_2C511CEF79EED498 ON tramite.tbr_ficha_salida_concepto_gasto (concepto_gasto_id)');
        $this->addSql('CREATE TABLE tramite.tbr_ficha_salida_estado (
          id INT NOT NULL,
          ficha_salida_id INT NOT NULL,
          estado_ficha_salida_id INT NOT NULL,
          descripcion TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_FDB3DFD71114A03B ON tramite.tbr_ficha_salida_estado (ficha_salida_id)');
        $this->addSql('CREATE INDEX IDX_FDB3DFD71693D208 ON tramite.tbr_ficha_salida_estado (estado_ficha_salida_id)');
        $this->addSql('CREATE TABLE pregrado.tbr_historico_estado_programa_academico (
          id INT NOT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          estado_programa_academico_id INT DEFAULT NULL,
          curso_academico_id INT DEFAULT NULL,
          dictamen_aprobacion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F56AC5CA74422427 ON pregrado.tbr_historico_estado_programa_academico (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE INDEX IDX_F56AC5CAAC9ABFFB ON pregrado.tbr_historico_estado_programa_academico (estado_programa_academico_id)');
        $this->addSql('CREATE INDEX IDX_F56AC5CA7D35438E ON pregrado.tbr_historico_estado_programa_academico (curso_academico_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_centros_estudios (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          nombre_centro_estudio VARCHAR(100) NOT NULL,
          descripcion_centro_estudio VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_5C91D1FDB239FBC6 ON institucion.tbr_institucion_centros_estudios (institucion_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_cum (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre_facultad VARCHAR(100) NOT NULL,
          descripcion_facultad VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4FEDCBAFB239FBC6 ON institucion.tbr_institucion_cum (institucion_id)');
        $this->addSql('CREATE INDEX IDX_4FEDCBAFCD2B7E7C ON institucion.tbr_institucion_cum (estructura_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_editorial (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          nombre_editorial VARCHAR(100) NOT NULL,
          direccion_electronica_editorial VARCHAR(255) NOT NULL,
          descripcion_editorial VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_9AD9A400B239FBC6 ON institucion.tbr_institucion_editorial (institucion_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_facultades (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre_facultad VARCHAR(100) NOT NULL,
          descripcion_facultad VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_96EA29DCB239FBC6 ON institucion.tbr_institucion_facultades (institucion_id)');
        $this->addSql('CREATE INDEX IDX_96EA29DCCD2B7E7C ON institucion.tbr_institucion_facultades (estructura_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_fum (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          estructura_id INT DEFAULT NULL,
          nombre_facultad VARCHAR(100) NOT NULL,
          descripcion_facultad VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_49260944B239FBC6 ON institucion.tbr_institucion_fum (institucion_id)');
        $this->addSql('CREATE INDEX IDX_49260944CD2B7E7C ON institucion.tbr_institucion_fum (estructura_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_mecanismo_colaboracion (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          id_mecanismo INT NOT NULL,
          nombre_mecanismo VARCHAR(255) NOT NULL,
          id_tipo_mecanismo INT NOT NULL,
          nombre_tipo_mecanismo VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F5793233B239FBC6 ON institucion.tbr_institucion_mecanismo_colaboracion (institucion_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_membresia (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          id_membresia INT NOT NULL,
          nombre_membresia VARCHAR(255) NOT NULL,
          id_tipo_membresia INT NOT NULL,
          nombre_tipo_membresia VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_3E0C2F15B239FBC6 ON institucion.tbr_institucion_membresia (institucion_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_programa_colaboracion (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          id_programa INT NOT NULL,
          nombre_programa VARCHAR(255) NOT NULL,
          id_patrocinador INT NOT NULL,
          nombre_patrocinador VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F08B49C0B239FBC6 ON institucion.tbr_institucion_programa_colaboracion (institucion_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_proyecto (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          id_proyecto INT NOT NULL,
          nombre_proyecto VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_1D6C1FE4B239FBC6 ON institucion.tbr_institucion_proyecto (institucion_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_recurso_humano (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          recurso_humano_id INT DEFAULT NULL,
          cantidad INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F1C3DFBB239FBC6 ON institucion.tbr_institucion_recurso_humano (institucion_id)');
        $this->addSql('CREATE INDEX IDX_F1C3DFBF33A5713 ON institucion.tbr_institucion_recurso_humano (recurso_humano_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_redes (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          rol_redes_id INT DEFAULT NULL,
          nombre_red VARCHAR(100) NOT NULL,
          descripcion_red VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B73E8B4AB239FBC6 ON institucion.tbr_institucion_redes (institucion_id)');
        $this->addSql('CREATE INDEX IDX_B73E8B4A37E822D ON institucion.tbr_institucion_redes (rol_redes_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_redes_sociales (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          red_social_id INT DEFAULT NULL,
          perfil VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_A72EA82FB239FBC6 ON institucion.tbr_institucion_redes_sociales (institucion_id)');
        $this->addSql('CREATE INDEX IDX_A72EA82FD4DDE3A3 ON institucion.tbr_institucion_redes_sociales (red_social_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_revista_cientifica (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          visibilidad_id INT DEFAULT NULL,
          nombre_revista VARCHAR(100) NOT NULL,
          indexada_en VARCHAR(100) NOT NULL,
          direccion_electronica_revista VARCHAR(255) NOT NULL,
          descripcion_revista VARCHAR(255) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_29665DEFB239FBC6 ON institucion.tbr_institucion_revista_cientifica (institucion_id)');
        $this->addSql('CREATE INDEX IDX_29665DEF94F69A8C ON institucion.tbr_institucion_revista_cientifica (visibilidad_id)');
        $this->addSql('CREATE TABLE institucion.tbr_institucion_sedes (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          nombre_sede VARCHAR(100) NOT NULL,
          direccion_sede VARCHAR(255) NOT NULL,
          coordenadas_sede VARCHAR(100) NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_8A5EA2FAB239FBC6 ON institucion.tbr_institucion_sedes (institucion_id)');
        $this->addSql('CREATE TABLE informatizacion.tbr_linea_celular_recargas (
          id INT NOT NULL,
          linea_celular_id INT DEFAULT NULL,
          plan_voz TEXT DEFAULT NULL,
          plan_sms TEXT DEFAULT NULL,
          plan_datos TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_7B3632ECDCDD29FE ON informatizacion.tbr_linea_celular_recargas (linea_celular_id)');
        $this->addSql('CREATE TABLE informatizacion.tbr_linea_celular_responsable (
          id INT NOT NULL,
          linea_celular_id INT DEFAULT NULL,
          responsable_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_1F571B83DCDD29FE ON informatizacion.tbr_linea_celular_responsable (linea_celular_id)');
        $this->addSql('CREATE INDEX IDX_1F571B8353C59D72 ON informatizacion.tbr_linea_celular_responsable (responsable_id)');
        $this->addSql('CREATE TABLE evaluacion.tbr_miembros_comision (
          id INT NOT NULL,
          comision_id INT DEFAULT NULL,
          miembro_id INT NOT NULL,
          rol_comision_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_CA5FBC994B352BE1 ON evaluacion.tbr_miembros_comision (comision_id)');
        $this->addSql('CREATE INDEX IDX_CA5FBC997952B53A ON evaluacion.tbr_miembros_comision (miembro_id)');
        $this->addSql('CREATE INDEX IDX_CA5FBC9959546FD0 ON evaluacion.tbr_miembros_comision (rol_comision_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_miembros_comision (
          id INT NOT NULL,
          comision_id INT DEFAULT NULL,
          miembro_id INT NOT NULL,
          rol_comision_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F505EA7F4B352BE1 ON postgrado.tbr_miembros_comision (comision_id)');
        $this->addSql('CREATE INDEX IDX_F505EA7F7952B53A ON postgrado.tbr_miembros_comision (miembro_id)');
        $this->addSql('CREATE INDEX IDX_F505EA7F59546FD0 ON postgrado.tbr_miembros_comision (rol_comision_id)');
        $this->addSql('CREATE TABLE pregrado.tbr_miembros_comision_nacional (
          id INT NOT NULL,
          comision_id INT DEFAULT NULL,
          miembro_id INT NOT NULL,
          rol_comision_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_375C51384B352BE1 ON pregrado.tbr_miembros_comision_nacional (comision_id)');
        $this->addSql('CREATE INDEX IDX_375C51387952B53A ON pregrado.tbr_miembros_comision_nacional (miembro_id)');
        $this->addSql('CREATE INDEX IDX_375C513859546FD0 ON pregrado.tbr_miembros_comision_nacional (rol_comision_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_miembros_copep (
          id INT NOT NULL,
          copep_id INT DEFAULT NULL,
          miembro_id INT NOT NULL,
          rol_comision_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4F8097FFEE770D7F ON postgrado.tbr_miembros_copep (copep_id)');
        $this->addSql('CREATE INDEX IDX_4F8097FF7952B53A ON postgrado.tbr_miembros_copep (miembro_id)');
        $this->addSql('CREATE INDEX IDX_4F8097FF59546FD0 ON postgrado.tbr_miembros_copep (rol_comision_id)');
        $this->addSql('CREATE TABLE pregrado.tbr_modificacion_plan_estudio (
          id INT NOT NULL,
          plan_estudio_id INT NOT NULL,
          curso_academico_id INT NOT NULL,
          fecha_aprobacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          plan_estudio_doc VARCHAR(255) DEFAULT NULL,
          dictamen VARCHAR(255) DEFAULT NULL,
          duracion_curso_diurno INT NOT NULL,
          duracion_curso_por_encuentro INT NOT NULL,
          duracion_curso_distancia VARCHAR(255) NOT NULL,
          descripcion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          documento_ejecutivo VARCHAR(255) DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_4C02A0131B4FA1A5 ON pregrado.tbr_modificacion_plan_estudio (plan_estudio_id)');
        $this->addSql('CREATE INDEX IDX_4C02A0137D35438E ON pregrado.tbr_modificacion_plan_estudio (curso_academico_id)');
        $this->addSql('CREATE TABLE personal.tbr_persona_organizacion (
          id INT NOT NULL,
          persona_id INT NOT NULL,
          organizacion_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_DC0F6D7BF5F88DB9 ON personal.tbr_persona_organizacion (persona_id)');
        $this->addSql('CREATE INDEX IDX_DC0F6D7B90B1019E ON personal.tbr_persona_organizacion (organizacion_id)');
        $this->addSql('CREATE TABLE pregrado.tbr_plan_estudio_documento (
          id INT NOT NULL,
          plan_estudio_id INT DEFAULT NULL,
          documento_id INT DEFAULT NULL,
          nombre VARCHAR(255) NOT NULL,
          descripcion TEXT DEFAULT NULL,
          activo BOOLEAN NOT NULL,
          documento_fisico VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_71B6471C1B4FA1A5 ON pregrado.tbr_plan_estudio_documento (plan_estudio_id)');
        $this->addSql('CREATE INDEX IDX_71B6471C45C0CF75 ON pregrado.tbr_plan_estudio_documento (documento_id)');
        $this->addSql('CREATE TABLE tramite.tbr_plan_mision_detalles (
          id INT NOT NULL,
          plan_mision_id INT DEFAULT NULL,
          persona_id INT DEFAULT NULL,
          pais_id INT DEFAULT NULL,
          objetivo TEXT DEFAULT NULL,
          fecha TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          duracion INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C45D3A589FBFDC72 ON tramite.tbr_plan_mision_detalles (plan_mision_id)');
        $this->addSql('CREATE INDEX IDX_C45D3A58F5F88DB9 ON tramite.tbr_plan_mision_detalles (persona_id)');
        $this->addSql('CREATE INDEX IDX_C45D3A58C604D5C6 ON tramite.tbr_plan_mision_detalles (pais_id)');
        $this->addSql('CREATE TABLE estructura.tbr_plaza (
          id INT NOT NULL,
          estructura_id INT NOT NULL,
          responsabilidad_id INT NOT NULL,
          cantidad INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_9FACD1A8CD2B7E7C ON estructura.tbr_plaza (estructura_id)');
        $this->addSql('CREATE INDEX IDX_9FACD1A896079139 ON estructura.tbr_plaza (responsabilidad_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_estructura_plaza_estructura_responsabilidad ON estructura.tbr_plaza (
          estructura_id, responsabilidad_id
        )');
        $this->addSql('CREATE TABLE pregrado.tbr_programa_academico_reabierto_institucion (
          id INT NOT NULL,
          programa_academico_reabierto_id INT DEFAULT NULL,
          institucion_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_941D09961914411F ON pregrado.tbr_programa_academico_reabierto_institucion (
          programa_academico_reabierto_id
        )');
        $this->addSql('CREATE INDEX IDX_941D0996B239FBC6 ON pregrado.tbr_programa_academico_reabierto_institucion (institucion_id)');
        $this->addSql('CREATE TABLE seguridad.tbr_rol_estructura (
          id INT NOT NULL,
          rol_id INT NOT NULL,
          estructura_id INT NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_248D984E4BAB96C ON seguridad.tbr_rol_estructura (rol_id)');
        $this->addSql('CREATE INDEX IDX_248D984ECD2B7E7C ON seguridad.tbr_rol_estructura (estructura_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_248D984E4BAB96CCD2B7E7C ON seguridad.tbr_rol_estructura (rol_id, estructura_id)');
        $this->addSql('CREATE TABLE informatizacion.tbr_sistema_informatico_proceso (
          id INT NOT NULL,
          sistema_informatico_id INT DEFAULT NULL,
          proceso_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_28A3D70BFFEE1BB2 ON informatizacion.tbr_sistema_informatico_proceso (sistema_informatico_id)');
        $this->addSql('CREATE INDEX IDX_28A3D70B640D1D53 ON informatizacion.tbr_sistema_informatico_proceso (proceso_id)');
        $this->addSql('CREATE TABLE evaluacion.tbr_solicitud_dictamen_comision (
          id INT NOT NULL,
          solicitud_id INT NOT NULL,
          dictamen_comision VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_59D574CA1CB9D6E4 ON evaluacion.tbr_solicitud_dictamen_comision (solicitud_id)');
        $this->addSql('CREATE TABLE pregrado.tbr_solicitud_programa_academico_comision_nacional (
          id INT NOT NULL,
          comision_nacional_id INT DEFAULT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_1E76C96396660F28 ON pregrado.tbr_solicitud_programa_academico_comision_nacional (comision_nacional_id)');
        $this->addSql('CREATE INDEX IDX_1E76C96374422427 ON pregrado.tbr_solicitud_programa_academico_comision_nacional (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE TABLE pregrado.tbr_solicitud_programa_academico_institucion (
          id INT NOT NULL,
          institucion_id INT NOT NULL,
          solicitud_programa_academico_id INT NOT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          duracion_curso_diurno INT DEFAULT NULL,
          duracion_curso_por_encuentro INT DEFAULT NULL,
          duracion_curso_adistancia INT DEFAULT NULL,
          modalidad_diurno BOOLEAN DEFAULT NULL,
          modalidad_por_encuentro BOOLEAN DEFAULT NULL,
          modalidad_adistancia BOOLEAN DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_6D90058AB239FBC6 ON pregrado.tbr_solicitud_programa_academico_institucion (institucion_id)');
        $this->addSql('CREATE INDEX IDX_6D90058A74422427 ON pregrado.tbr_solicitud_programa_academico_institucion (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE INDEX IDX_6D90058AA8AF3830 ON pregrado.tbr_solicitud_programa_academico_institucion (categoria_acreditacion_id)');
        $this->addSql('CREATE TABLE pregrado.tbr_solicitud_programa_academico_institucion_cum (
          id INT NOT NULL,
          institucion_cum_id INT NOT NULL,
          solicitud_programa_academico_institucion_id INT NOT NULL,
          modalidad TEXT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_5F9D268C4C4F0345 ON pregrado.tbr_solicitud_programa_academico_institucion_cum (institucion_cum_id)');
        $this->addSql('CREATE INDEX IDX_5F9D268C997A0FA9 ON pregrado.tbr_solicitud_programa_academico_institucion_cum (
          solicitud_programa_academico_institucion_id
        )');
        $this->addSql('CREATE TABLE pregrado.tbr_solicitud_programa_academico_plan_estudio (
          id INT NOT NULL,
          plan_estudio_id INT DEFAULT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_E2E6AE71B4FA1A5 ON pregrado.tbr_solicitud_programa_academico_plan_estudio (plan_estudio_id)');
        $this->addSql('CREATE INDEX IDX_E2E6AE774422427 ON pregrado.tbr_solicitud_programa_academico_plan_estudio (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE TABLE postgrado.tbr_solicitud_programa_comision (
          id INT NOT NULL,
          comision_id INT DEFAULT NULL,
          solicitud_programa_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_38202D2A4B352BE1 ON postgrado.tbr_solicitud_programa_comision (comision_id)');
        $this->addSql('CREATE INDEX IDX_38202D2AE5C0BD32 ON postgrado.tbr_solicitud_programa_comision (solicitud_programa_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_solicitud_programa_dictamen (
          id INT NOT NULL,
          comision_id INT DEFAULT NULL,
          solicitud_programa_id INT DEFAULT NULL,
          propietario_dictamen_id INT DEFAULT NULL,
          rol_comision_id INT NOT NULL,
          dictamen VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F4B9FC084B352BE1 ON postgrado.tbr_solicitud_programa_dictamen (comision_id)');
        $this->addSql('CREATE INDEX IDX_F4B9FC08E5C0BD32 ON postgrado.tbr_solicitud_programa_dictamen (solicitud_programa_id)');
        $this->addSql('CREATE INDEX IDX_F4B9FC0886A86415 ON postgrado.tbr_solicitud_programa_dictamen (propietario_dictamen_id)');
        $this->addSql('CREATE INDEX IDX_F4B9FC0859546FD0 ON postgrado.tbr_solicitud_programa_dictamen (rol_comision_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_solicitud_programa_institucion (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          solicitud_programa_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F4BBA03BB239FBC6 ON postgrado.tbr_solicitud_programa_institucion (institucion_id)');
        $this->addSql('CREATE INDEX IDX_F4BBA03BE5C0BD32 ON postgrado.tbr_solicitud_programa_institucion (solicitud_programa_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_solicitud_programa_presencialidad (
          id INT NOT NULL,
          presencialidad_programa_id INT NOT NULL,
          solicitud_programa_id INT DEFAULT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_7B39973484E810D2 ON postgrado.tbr_solicitud_programa_presencialidad (presencialidad_programa_id)');
        $this->addSql('CREATE INDEX IDX_7B399734E5C0BD32 ON postgrado.tbr_solicitud_programa_presencialidad (solicitud_programa_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_solicitud_programa_votacion (
          id INT NOT NULL,
          miembros_copep_id INT DEFAULT NULL,
          solicitud_programa_id INT DEFAULT NULL,
          voto BOOLEAN NOT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_48CB5D685822F248 ON postgrado.tbr_solicitud_programa_votacion (miembros_copep_id)');
        $this->addSql('CREATE INDEX IDX_48CB5D68E5C0BD32 ON postgrado.tbr_solicitud_programa_votacion (solicitud_programa_id)');
        $this->addSql('CREATE TABLE informatizacion.tbr_telefono_celular_responsable (
          id INT NOT NULL,
          telefono_celular_id INT DEFAULT NULL,
          responsable_id INT DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_57ACF0A78400D170 ON informatizacion.tbr_telefono_celular_responsable (telefono_celular_id)');
        $this->addSql('CREATE INDEX IDX_57ACF0A753C59D72 ON informatizacion.tbr_telefono_celular_responsable (responsable_id)');
        $this->addSql('CREATE TABLE postgrado.tbr_tipo_solicitud_clasificacion (
          id INT NOT NULL,
          tipo_solicitud_id INT DEFAULT NULL,
          clasificacion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_D99FAF8FAFEA88E4 ON postgrado.tbr_tipo_solicitud_clasificacion (tipo_solicitud_id)');
        $this->addSql('CREATE TABLE evaluacion.temp_categoria_acreditacion_ies (
          id INT NOT NULL,
          institucion_id INT DEFAULT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          fecha_emision TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          numero_pleno VARCHAR(255) DEFAULT NULL,
          numero_acuerdo_pleno VARCHAR(255) DEFAULT NULL,
          annos_vigencia_categoria_acreditacion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_B84DADF2B239FBC6 ON evaluacion.temp_categoria_acreditacion_ies (institucion_id)');
        $this->addSql('CREATE INDEX IDX_B84DADF2A8AF3830 ON evaluacion.temp_categoria_acreditacion_ies (categoria_acreditacion_id)');
        $this->addSql('CREATE TABLE evaluacion.temp_categoria_acreditacion_posgrado (
          id INT NOT NULL,
          solicitud_programa_id INT DEFAULT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          fecha_emision TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          numero_pleno VARCHAR(255) DEFAULT NULL,
          numero_acuerdo_pleno VARCHAR(255) DEFAULT NULL,
          annos_vigencia_categoria_acreditacion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_A7A0CAA2E5C0BD32 ON evaluacion.temp_categoria_acreditacion_posgrado (solicitud_programa_id)');
        $this->addSql('CREATE INDEX IDX_A7A0CAA2A8AF3830 ON evaluacion.temp_categoria_acreditacion_posgrado (categoria_acreditacion_id)');
        $this->addSql('CREATE TABLE evaluacion.temp_categoria_acreditacion_pregrado (
          id INT NOT NULL,
          solicitud_programa_academico_id INT DEFAULT NULL,
          institucion_id INT NOT NULL,
          categoria_acreditacion_id INT DEFAULT NULL,
          fecha_emision TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
          numero_pleno VARCHAR(255) DEFAULT NULL,
          numero_acuerdo_pleno VARCHAR(255) DEFAULT NULL,
          annos_vigencia_categoria_acreditacion VARCHAR(255) DEFAULT NULL,
          creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_7426D62A74422427 ON evaluacion.temp_categoria_acreditacion_pregrado (
          solicitud_programa_academico_id
        )');
        $this->addSql('CREATE INDEX IDX_7426D62AB239FBC6 ON evaluacion.temp_categoria_acreditacion_pregrado (institucion_id)');
        $this->addSql('CREATE INDEX IDX_7426D62AA8AF3830 ON evaluacion.temp_categoria_acreditacion_pregrado (categoria_acreditacion_id)');
        $this->addSql('ALTER TABLE
          informe_personalizado
        ADD
          CONSTRAINT FK_69E8944EDB38439E FOREIGN KEY (usuario_id) REFERENCES seguridad.tbd_usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          notificaciones_usuario
        ADD
          CONSTRAINT FK_C66FA6F59DA2C333 FOREIGN KEY (usuario_envia_id) REFERENCES seguridad.tbd_usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          notificaciones_usuario
        ADD
          CONSTRAINT FK_C66FA6F55FA3BDA6 FOREIGN KEY (usuario_recive_id) REFERENCES seguridad.tbd_usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          rrhh.tbd_ae2
        ADD
          CONSTRAINT FK_E712DE0F6CA204EF FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          rrhh.tbd_ae3
        ADD
          CONSTRAINT FK_9015EE996CA204EF FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_aplazamiento_solicitud
        ADD
          CONSTRAINT FK_460B9DEF1CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES evaluacion.tbd_solicitud (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_aplazamiento_solicitud
        ADD
          CONSTRAINT FK_460B9DEFBB071712 FOREIGN KEY (estado_aplazamiento_id) REFERENCES evaluacion.tbn_estado_aplazamiento (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_centro_dato_virtual
        ADD
          CONSTRAINT FK_4F1FC251CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_comision
        ADD
          CONSTRAINT FK_CA64D28A1CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES evaluacion.tbd_solicitud (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_comision
        ADD
          CONSTRAINT FK_C80B22D17E110519 FOREIGN KEY (tipo_comision_id) REFERENCES postgrado.tbn_tipo_comision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          traza.tbd_configuracion_traza
        ADD
          CONSTRAINT FK_447D6EE4F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF7501E092B9F FOREIGN KEY (modalidad_id) REFERENCES convenio.tbn_modalida (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF75098260155 FOREIGN KEY (region_id) REFERENCES convenio.tbn_region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF750A9276E6C FOREIGN KEY (tipo_id) REFERENCES convenio.tbn_tipo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF7509098BF83 FOREIGN KEY (institucion_cubana_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF75093977F77 FOREIGN KEY (institucion_extranjera_id) REFERENCES tramite.tbd_institucion_extranjera (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF750C604D5C6 FOREIGN KEY (pais_id) REFERENCES estructura.tbn_pais (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF750FE35D8C4 FOREIGN KEY (creado_por_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbd_convenio
        ADD
          CONSTRAINT FK_113EF7508C871111 FOREIGN KEY (modificado_por_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B31114A03B FOREIGN KEY (ficha_salida_id) REFERENCES tramite.tbd_ficha_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B3F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B39098BF83 FOREIGN KEY (institucion_cubana_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B3C604D5C6 FOREIGN KEY (pais_id) REFERENCES estructura.tbn_pais (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B393977F77 FOREIGN KEY (institucion_extranjera_id) REFERENCES tramite.tbd_institucion_extranjera (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B34BEE089D FOREIGN KEY (concepto_salida_id) REFERENCES tramite.tbn_concepto_salida (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B3251EA755 FOREIGN KEY (tipo_pasaporte_id) REFERENCES tramite.tbn_tipo_pasaporte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B3BDDB5F58 FOREIGN KEY (estado_documento_salida_id) REFERENCES tramite.tbn_estado_ficha_salida (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida
        ADD
          CONSTRAINT FK_117159B321B6FE31 FOREIGN KEY (directivo_firma_id) REFERENCES seguridad.tbd_usuario (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida_historial_documento
        ADD
          CONSTRAINT FK_8A01F346E2962F06 FOREIGN KEY (documento_salida_id) REFERENCES tramite.tbd_documento_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida_historial_documento
        ADD
          CONSTRAINT FK_8A01F346DB38439E FOREIGN KEY (usuario_id) REFERENCES seguridad.tbd_usuario (id) ON DELETE
        SET
          NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_enlace_conectividad
        ADD
          CONSTRAINT FK_CA8531856451B6B1 FOREIGN KEY (tipo_conectividad_id) REFERENCES informatizacion.tbn_tipo_conectividad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_enlace_conectividad
        ADD
          CONSTRAINT FK_CA853185CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_entidad
        ADD
          CONSTRAINT FK_AB79ADA6CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_entidad
        ADD
          CONSTRAINT FK_AB79ADA6FD2406C7 FOREIGN KEY (tipo_entidad_id) REFERENCES estructura.tbn_tipo_entidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_entidad
        ADD
          CONSTRAINT FK_AB79ADA66CA204EF FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_entidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_entidad
        ADD
          CONSTRAINT FK_AB79ADA658BC1BE0 FOREIGN KEY (municipio_id) REFERENCES estructura.tbn_municipio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_entidad
        ADD
          CONSTRAINT FK_AB79ADA64E7121AF FOREIGN KEY (provincia_id) REFERENCES estructura.tbn_provincia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB85CD982D22 FOREIGN KEY (tipo_estructura_id) REFERENCES estructura.tbn_tipo_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB85CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB854498FA55 FOREIGN KEY (categoria_estructura_id) REFERENCES estructura.tbn_categoria_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB85DA3426AE FOREIGN KEY (nivel_id) REFERENCES estructura.tbn_nivel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB8558BC1BE0 FOREIGN KEY (municipio_id) REFERENCES estructura.tbn_municipio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB854E7121AF FOREIGN KEY (provincia_id) REFERENCES estructura.tbn_provincia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbd_estructura
        ADD
          CONSTRAINT FK_BF47AB859C833003 FOREIGN KEY (grupo_id) REFERENCES rrhh.tbn_grupo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C548F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C5489098BF83 FOREIGN KEY (institucion_cubana_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C548C604D5C6 FOREIGN KEY (pais_id) REFERENCES estructura.tbn_pais (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C54893977F77 FOREIGN KEY (institucion_extranjera_id) REFERENCES tramite.tbd_institucion_extranjera (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C5484BEE089D FOREIGN KEY (concepto_salida_id) REFERENCES tramite.tbn_concepto_salida (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C548251EA755 FOREIGN KEY (tipo_pasaporte_id) REFERENCES tramite.tbn_tipo_pasaporte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C5481693D208 FOREIGN KEY (estado_ficha_salida_id) REFERENCES tramite.tbn_estado_ficha_salida (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida
        ADD
          CONSTRAINT FK_B4D7C54821B6FE31 FOREIGN KEY (directivo_firma_id) REFERENCES seguridad.tbd_usuario (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida_historial_documento
        ADD
          CONSTRAINT FK_E80A77BD1114A03B FOREIGN KEY (ficha_salida_id) REFERENCES tramite.tbd_ficha_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_ficha_salida_historial_documento
        ADD
          CONSTRAINT FK_E80A77BDDB38439E FOREIGN KEY (usuario_id) REFERENCES seguridad.tbd_usuario (id) ON DELETE
        SET
          NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbd_funcionalidad
        ADD
          CONSTRAINT FK_A7267189C07F55F5 FOREIGN KEY (modulo_id) REFERENCES seguridad.tbd_modulo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbd_institucion
        ADD
          CONSTRAINT FK_739EFE83C6879E6C FOREIGN KEY (grado_academico_rector_id) REFERENCES personal.tbn_grado_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbd_institucion
        ADD
          CONSTRAINT FK_739EFE833EEA3466 FOREIGN KEY (tipo_institucion_id) REFERENCES institucion.tbn_tipo_institucion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbd_institucion
        ADD
          CONSTRAINT FK_739EFE83A8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbd_institucion
        ADD
          CONSTRAINT FK_739EFE83CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_institucion_extranjera
        ADD
          CONSTRAINT FK_D6AEBBC5C604D5C6 FOREIGN KEY (pais_id) REFERENCES estructura.tbn_pais (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_linea_celular
        ADD
          CONSTRAINT FK_76D64B5ECD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_nauta_hogar
        ADD
          CONSTRAINT FK_7A25B70E53C59D72 FOREIGN KEY (responsable_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_nauta_hogar
        ADD
          CONSTRAINT FK_7A25B70E5354BD7D FOREIGN KEY (servicio_contratado_id) REFERENCES informatizacion.tbn_servicio_contratado (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_nauta_hogar
        ADD
          CONSTRAINT FK_7A25B70ECD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_pasaporte
        ADD
          CONSTRAINT FK_E7E54A21F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbd_pasaporte
        ADD
          CONSTRAINT FK_E7E54A21251EA755 FOREIGN KEY (tipo_pasaporte_id) REFERENCES tramite.tbn_tipo_pasaporte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A2234E7121AF FOREIGN KEY (provincia_id) REFERENCES estructura.tbn_provincia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A22358BC1BE0 FOREIGN KEY (municipio_id) REFERENCES estructura.tbn_municipio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A223CF5FD27C FOREIGN KEY (clasificacion_persona_id) REFERENCES personal.tbn_clasificacion_persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A2235873B5B9 FOREIGN KEY (nivel_escolar_id) REFERENCES personal.tbn_nivel_escolar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A22392D84F6 FOREIGN KEY (categoria_docente_id) REFERENCES personal.tbn_categoria_docente (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A223C5AF4D0F FOREIGN KEY (profesion_id) REFERENCES personal.tbn_profesion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A2234683F2FA FOREIGN KEY (grado_academico_id) REFERENCES personal.tbn_grado_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A223D3ECE4BD FOREIGN KEY (categoria_investigativa_id) REFERENCES personal.tbn_categoria_investigativa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A22374422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A2232B32DB58 FOREIGN KEY (sexo_id) REFERENCES personal.tbn_sexo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A2234498FA55 FOREIGN KEY (categoria_estructura_id) REFERENCES estructura.tbn_categoria_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A223CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A2236CA204EF FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A22396079139 FOREIGN KEY (responsabilidad_id) REFERENCES estructura.tbn_responsabilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_persona
        ADD
          CONSTRAINT FK_D817A223DB38439E FOREIGN KEY (usuario_id) REFERENCES seguridad.tbd_usuario (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662F7D35438E FOREIGN KEY (curso_academico_id) REFERENCES pregrado.tbn_curso_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662FC671B40F FOREIGN KEY (carrera_id) REFERENCES pregrado.tbd_solicitud_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662FF0FF4047 FOREIGN KEY (rama_ciencia_id) REFERENCES postgrado.tbn_rama_ciencia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662F9B2A9D11 FOREIGN KEY (organismo_demandante_id) REFERENCES pregrado.tbn_organismo_demandante (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662F2F480A85 FOREIGN KEY (organismo_formador_id) REFERENCES pregrado.tbn_organismo_formador (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662F16689255 FOREIGN KEY (tipo_programa_academico_id) REFERENCES pregrado.tbn_tipo_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_plan_estudio
        ADD
          CONSTRAINT FK_2531662F68502903 FOREIGN KEY (oace_id) REFERENCES pregrado.tbn_oace (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_plantilla
        ADD
          CONSTRAINT FK_405B8B6CF5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_plantilla
        ADD
          CONSTRAINT FK_405B8B6CCD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_plantilla
        ADD
          CONSTRAINT FK_405B8B6C96079139 FOREIGN KEY (responsabilidad_id) REFERENCES estructura.tbn_responsabilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_programa_academico_desactivado
        ADD
          CONSTRAINT FK_44A10FF974422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_programa_academico_desactivado
        ADD
          CONSTRAINT FK_44A10FF97D35438E FOREIGN KEY (curso_academico_id) REFERENCES pregrado.tbn_curso_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_programa_academico_reabierto
        ADD
          CONSTRAINT FK_833BE5D174422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_programa_academico_reabierto
        ADD
          CONSTRAINT FK_833BE5D17D35438E FOREIGN KEY (curso_academico_id) REFERENCES pregrado.tbn_curso_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbd_responsable
        ADD
          CONSTRAINT FK_2355D865F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbr_rol_funcionalidad
        ADD
          CONSTRAINT FK_5C2783B44BAB96C FOREIGN KEY (rol_id) REFERENCES seguridad.tbd_rol (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbr_rol_funcionalidad
        ADD
          CONSTRAINT FK_5C2783B43637E16E FOREIGN KEY (funcionalidad_id) REFERENCES seguridad.tbd_funcionalidad (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_servicio
        ADD
          CONSTRAINT FK_55FC9E5E94F69A8C FOREIGN KEY (visibilidad_id) REFERENCES informatizacion.tbn_visibilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_servicio
        ADD
          CONSTRAINT FK_55FC9E5EE90161FC FOREIGN KEY (publico_objetivo_id) REFERENCES informatizacion.tbn_publico_objetivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_servicio
        ADD
          CONSTRAINT FK_55FC9E5ECD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_sistema_informatico
        ADD
          CONSTRAINT FK_B6DE6A6A94F69A8C FOREIGN KEY (visibilidad_id) REFERENCES informatizacion.tbn_visibilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_sistema_informatico
        ADD
          CONSTRAINT FK_B6DE6A6A864BA020 FOREIGN KEY (tipo_sistema_id) REFERENCES informatizacion.tbn_tipo_sistema (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_sistema_informatico
        ADD
          CONSTRAINT FK_B6DE6A6ACD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_solicitud
        ADD
          CONSTRAINT FK_46681D6C4EE93BE6 FOREIGN KEY (convocatoria_id) REFERENCES evaluacion.tbd_convocatoria (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_solicitud
        ADD
          CONSTRAINT FK_46681D6CB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_solicitud
        ADD
          CONSTRAINT FK_46681D6C65EC4CB2 FOREIGN KEY (programa_pregrado_id) REFERENCES pregrado.tbd_solicitud_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_solicitud
        ADD
          CONSTRAINT FK_46681D6C68667D11 FOREIGN KEY (programa_posgrado_id) REFERENCES postgrado.tbd_solicitud_programa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_solicitud
        ADD
          CONSTRAINT FK_46681D6C77E88BF9 FOREIGN KEY (estado_solicitud_id) REFERENCES evaluacion.tbn_estado_solicitud (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbd_solicitud
        ADD
          CONSTRAINT FK_46681D6CEC47E158 FOREIGN KEY (
            categoria_acreditacion_alcanzada_id
          ) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CFAFEA88E4 FOREIGN KEY (tipo_solicitud_id) REFERENCES postgrado.tbn_tipo_solicitud (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CF45D881AC FOREIGN KEY (
            tipo_solicitud_clasificacion_id
          ) REFERENCES postgrado.tbr_tipo_solicitud_clasificacion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CFC7869A78 FOREIGN KEY (estado_programa_id) REFERENCES postgrado.tbn_estado_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CF271768CD FOREIGN KEY (universidad_id) REFERENCES estructura.tbd_estructura (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CFF0D8DD71 FOREIGN KEY (original_de_id) REFERENCES estructura.tbd_estructura (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CFC8AE5DD0 FOREIGN KEY (tipo_programa_id) REFERENCES postgrado.tbn_tipo_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CFF0FF4047 FOREIGN KEY (rama_ciencia_id) REFERENCES postgrado.tbn_rama_ciencia (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CF9EE50F0A FOREIGN KEY (modalidad_programa_id) REFERENCES postgrado.tbn_modalidad_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CF84E810D2 FOREIGN KEY (presencialidad_programa_id) REFERENCES postgrado.tbn_presencialidad_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbd_solicitud_programa
        ADD
          CONSTRAINT FK_59D7F1CFA8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC26277AC9ABFFB FOREIGN KEY (estado_programa_academico_id) REFERENCES pregrado.tbn_estado_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC2627716689255 FOREIGN KEY (tipo_programa_academico_id) REFERENCES pregrado.tbn_tipo_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC2627781338691 FOREIGN KEY (tipo_organismo_id) REFERENCES pregrado.tbn_tipo_organismo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC262779B2A9D11 FOREIGN KEY (organismo_demandante_id) REFERENCES pregrado.tbn_organismo_demandante (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC26277DC252D29 FOREIGN KEY (centro_rector_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC262772F480A85 FOREIGN KEY (organismo_formador_id) REFERENCES pregrado.tbn_organismo_formador (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC26277F0FF4047 FOREIGN KEY (rama_ciencia_id) REFERENCES postgrado.tbn_rama_ciencia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC26277606D2041 FOREIGN KEY (centro_solicitante_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbd_solicitud_programa_academico
        ADD
          CONSTRAINT FK_5DC26277A8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_telefono_celular
        ADD
          CONSTRAINT FK_26F77CCC81EF0041 FOREIGN KEY (marca_id) REFERENCES informatizacion.tbn_marca (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_telefono_celular
        ADD
          CONSTRAINT FK_26F77CCCC3A9576E FOREIGN KEY (modelo_id) REFERENCES informatizacion.tbn_modelo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_telefono_celular
        ADD
          CONSTRAINT FK_26F77CCCC616A017 FOREIGN KEY (sistema_operativo_id) REFERENCES informatizacion.tbn_sistema_operativo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbd_telefono_celular
        ADD
          CONSTRAINT FK_26F77CCCCD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          traza.tbd_traza
        ADD
          CONSTRAINT FK_5B9073638A5A731D FOREIGN KEY (tipo_traza_id) REFERENCES traza.tbn_tipo_traza (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          traza.tbd_traza
        ADD
          CONSTRAINT FK_5B9073633F4B5275 FOREIGN KEY (accion_id) REFERENCES traza.tbn_accion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          traza.tbd_traza
        ADD
          CONSTRAINT FK_5B90736376F5CD27 FOREIGN KEY (objeto_id) REFERENCES traza.tbn_objeto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          traza.tbd_traza
        ADD
          CONSTRAINT FK_5B907363F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbr_user_rol
        ADD
          CONSTRAINT FK_F808892AA76ED395 FOREIGN KEY (user_id) REFERENCES seguridad.tbd_usuario (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbr_user_rol
        ADD
          CONSTRAINT FK_F808892A4BAB96C FOREIGN KEY (rol_id) REFERENCES seguridad.tbd_rol (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbn_carrera
        ADD
          CONSTRAINT FK_D3C253165873B5B9 FOREIGN KEY (nivel_escolar_id) REFERENCES personal.tbn_nivel_escolar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbn_categoria_responsabilidad
        ADD
          CONSTRAINT FK_39E2253E15B49E6B FOREIGN KEY (categoria_responsabilidad_id) REFERENCES estructura.tbn_categoria_responsabilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbn_municipio
        ADD
          CONSTRAINT FK_AF623AA94E7121AF FOREIGN KEY (provincia_id) REFERENCES estructura.tbn_provincia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbn_organismo_demandante
        ADD
          CONSTRAINT FK_4063FC281338691 FOREIGN KEY (tipo_organismo_id) REFERENCES pregrado.tbn_tipo_organismo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbn_organizacion
        ADD
          CONSTRAINT FK_7DDAD16046EB71B9 FOREIGN KEY (tipo_organizacion_id) REFERENCES personal.tbn_tipo_organizacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbn_responsabilidad
        ADD
          CONSTRAINT FK_1C003A0215B49E6B FOREIGN KEY (categoria_responsabilidad_id) REFERENCES estructura.tbn_categoria_responsabilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbn_tipo_entidad
        ADD
          CONSTRAINT FK_383FF7F6FD2406C7 FOREIGN KEY (tipo_entidad_id) REFERENCES estructura.tbn_tipo_entidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          convenio.tbr_convenio_accion
        ADD
          CONSTRAINT FK_1EA2DC49F9D43F2A FOREIGN KEY (convenio_id) REFERENCES convenio.tbd_convenio (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_documento_salida_tramite
        ADD
          CONSTRAINT FK_D3C55E7E2962F06 FOREIGN KEY (documento_salida_id) REFERENCES tramite.tbd_documento_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_documento_salida_tramite
        ADD
          CONSTRAINT FK_D3C55E7820C2849 FOREIGN KEY (tramite_id) REFERENCES tramite.tbn_tramite (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_ficha_salida_concepto_gasto
        ADD
          CONSTRAINT FK_2C511CEF1114A03B FOREIGN KEY (ficha_salida_id) REFERENCES tramite.tbd_ficha_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_ficha_salida_concepto_gasto
        ADD
          CONSTRAINT FK_2C511CEF79EED498 FOREIGN KEY (concepto_gasto_id) REFERENCES convenio.tbn_concepto_gasto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_ficha_salida_estado
        ADD
          CONSTRAINT FK_FDB3DFD71114A03B FOREIGN KEY (ficha_salida_id) REFERENCES tramite.tbd_ficha_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_ficha_salida_estado
        ADD
          CONSTRAINT FK_FDB3DFD71693D208 FOREIGN KEY (estado_ficha_salida_id) REFERENCES tramite.tbn_estado_ficha_salida (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_historico_estado_programa_academico
        ADD
          CONSTRAINT FK_F56AC5CA74422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_historico_estado_programa_academico
        ADD
          CONSTRAINT FK_F56AC5CAAC9ABFFB FOREIGN KEY (estado_programa_academico_id) REFERENCES pregrado.tbn_estado_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_historico_estado_programa_academico
        ADD
          CONSTRAINT FK_F56AC5CA7D35438E FOREIGN KEY (curso_academico_id) REFERENCES pregrado.tbn_curso_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_centros_estudios
        ADD
          CONSTRAINT FK_5C91D1FDB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_cum
        ADD
          CONSTRAINT FK_4FEDCBAFB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_cum
        ADD
          CONSTRAINT FK_4FEDCBAFCD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_editorial
        ADD
          CONSTRAINT FK_9AD9A400B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_facultades
        ADD
          CONSTRAINT FK_96EA29DCB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_facultades
        ADD
          CONSTRAINT FK_96EA29DCCD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_fum
        ADD
          CONSTRAINT FK_49260944B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_fum
        ADD
          CONSTRAINT FK_49260944CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_mecanismo_colaboracion
        ADD
          CONSTRAINT FK_F5793233B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_membresia
        ADD
          CONSTRAINT FK_3E0C2F15B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_programa_colaboracion
        ADD
          CONSTRAINT FK_F08B49C0B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_proyecto
        ADD
          CONSTRAINT FK_1D6C1FE4B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_recurso_humano
        ADD
          CONSTRAINT FK_F1C3DFBB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_recurso_humano
        ADD
          CONSTRAINT FK_F1C3DFBF33A5713 FOREIGN KEY (recurso_humano_id) REFERENCES institucion.tbn_recursos_humanos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_redes
        ADD
          CONSTRAINT FK_B73E8B4AB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_redes
        ADD
          CONSTRAINT FK_B73E8B4A37E822D FOREIGN KEY (rol_redes_id) REFERENCES institucion.tbn_rol_redes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_redes_sociales
        ADD
          CONSTRAINT FK_A72EA82FB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_redes_sociales
        ADD
          CONSTRAINT FK_A72EA82FD4DDE3A3 FOREIGN KEY (red_social_id) REFERENCES institucion.tbn_red_social (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_revista_cientifica
        ADD
          CONSTRAINT FK_29665DEFB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_revista_cientifica
        ADD
          CONSTRAINT FK_29665DEF94F69A8C FOREIGN KEY (visibilidad_id) REFERENCES institucion.tbn_visibilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_sedes
        ADD
          CONSTRAINT FK_8A5EA2FAB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_linea_celular_recargas
        ADD
          CONSTRAINT FK_7B3632ECDCDD29FE FOREIGN KEY (linea_celular_id) REFERENCES informatizacion.tbd_linea_celular (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_linea_celular_responsable
        ADD
          CONSTRAINT FK_1F571B83DCDD29FE FOREIGN KEY (linea_celular_id) REFERENCES informatizacion.tbd_linea_celular (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_linea_celular_responsable
        ADD
          CONSTRAINT FK_1F571B8353C59D72 FOREIGN KEY (responsable_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbr_miembros_comision
        ADD
          CONSTRAINT FK_CA5FBC994B352BE1 FOREIGN KEY (comision_id) REFERENCES evaluacion.tbd_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbr_miembros_comision
        ADD
          CONSTRAINT FK_CA5FBC997952B53A FOREIGN KEY (miembro_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbr_miembros_comision
        ADD
          CONSTRAINT FK_CA5FBC9959546FD0 FOREIGN KEY (rol_comision_id) REFERENCES evaluacion.tbn_rol_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_miembros_comision
        ADD
          CONSTRAINT FK_F505EA7F4B352BE1 FOREIGN KEY (comision_id) REFERENCES postgrado.tbd_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_miembros_comision
        ADD
          CONSTRAINT FK_F505EA7F7952B53A FOREIGN KEY (miembro_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_miembros_comision
        ADD
          CONSTRAINT FK_F505EA7F59546FD0 FOREIGN KEY (rol_comision_id) REFERENCES postgrado.tbn_rol_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_miembros_comision_nacional
        ADD
          CONSTRAINT FK_375C51384B352BE1 FOREIGN KEY (comision_id) REFERENCES pregrado.tbd_comision_nacional (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_miembros_comision_nacional
        ADD
          CONSTRAINT FK_375C51387952B53A FOREIGN KEY (miembro_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_miembros_comision_nacional
        ADD
          CONSTRAINT FK_375C513859546FD0 FOREIGN KEY (rol_comision_id) REFERENCES postgrado.tbn_rol_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_miembros_copep
        ADD
          CONSTRAINT FK_4F8097FFEE770D7F FOREIGN KEY (copep_id) REFERENCES postgrado.tbd_copep (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_miembros_copep
        ADD
          CONSTRAINT FK_4F8097FF7952B53A FOREIGN KEY (miembro_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_miembros_copep
        ADD
          CONSTRAINT FK_4F8097FF59546FD0 FOREIGN KEY (rol_comision_id) REFERENCES postgrado.tbn_rol_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_modificacion_plan_estudio
        ADD
          CONSTRAINT FK_4C02A0131B4FA1A5 FOREIGN KEY (plan_estudio_id) REFERENCES pregrado.tbd_plan_estudio (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_modificacion_plan_estudio
        ADD
          CONSTRAINT FK_4C02A0137D35438E FOREIGN KEY (curso_academico_id) REFERENCES pregrado.tbn_curso_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbr_persona_organizacion
        ADD
          CONSTRAINT FK_DC0F6D7BF5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          personal.tbr_persona_organizacion
        ADD
          CONSTRAINT FK_DC0F6D7B90B1019E FOREIGN KEY (organizacion_id) REFERENCES personal.tbn_organizacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_plan_estudio_documento
        ADD
          CONSTRAINT FK_71B6471C1B4FA1A5 FOREIGN KEY (plan_estudio_id) REFERENCES pregrado.tbd_plan_estudio (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_plan_estudio_documento
        ADD
          CONSTRAINT FK_71B6471C45C0CF75 FOREIGN KEY (documento_id) REFERENCES pregrado.tbn_documento (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_plan_mision_detalles
        ADD
          CONSTRAINT FK_C45D3A589FBFDC72 FOREIGN KEY (plan_mision_id) REFERENCES tramite.tbd_plan_mision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_plan_mision_detalles
        ADD
          CONSTRAINT FK_C45D3A58F5F88DB9 FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          tramite.tbr_plan_mision_detalles
        ADD
          CONSTRAINT FK_C45D3A58C604D5C6 FOREIGN KEY (pais_id) REFERENCES estructura.tbn_pais (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbr_plaza
        ADD
          CONSTRAINT FK_9FACD1A8CD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          estructura.tbr_plaza
        ADD
          CONSTRAINT FK_9FACD1A896079139 FOREIGN KEY (responsabilidad_id) REFERENCES estructura.tbn_responsabilidad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_programa_academico_reabierto_institucion
        ADD
          CONSTRAINT FK_941D09961914411F FOREIGN KEY (
            programa_academico_reabierto_id
          ) REFERENCES pregrado.tbd_programa_academico_reabierto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_programa_academico_reabierto_institucion
        ADD
          CONSTRAINT FK_941D0996B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbr_rol_estructura
        ADD
          CONSTRAINT FK_248D984E4BAB96C FOREIGN KEY (rol_id) REFERENCES seguridad.tbd_rol (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          seguridad.tbr_rol_estructura
        ADD
          CONSTRAINT FK_248D984ECD2B7E7C FOREIGN KEY (estructura_id) REFERENCES estructura.tbd_estructura (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_sistema_informatico_proceso
        ADD
          CONSTRAINT FK_28A3D70BFFEE1BB2 FOREIGN KEY (sistema_informatico_id) REFERENCES informatizacion.tbd_sistema_informatico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_sistema_informatico_proceso
        ADD
          CONSTRAINT FK_28A3D70B640D1D53 FOREIGN KEY (proceso_id) REFERENCES informatizacion.tbn_proceso (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.tbr_solicitud_dictamen_comision
        ADD
          CONSTRAINT FK_59D574CA1CB9D6E4 FOREIGN KEY (solicitud_id) REFERENCES evaluacion.tbd_solicitud (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_comision_nacional
        ADD
          CONSTRAINT FK_1E76C96396660F28 FOREIGN KEY (comision_nacional_id) REFERENCES pregrado.tbd_comision_nacional (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_comision_nacional
        ADD
          CONSTRAINT FK_1E76C96374422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion
        ADD
          CONSTRAINT FK_6D90058AB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion
        ADD
          CONSTRAINT FK_6D90058A74422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion
        ADD
          CONSTRAINT FK_6D90058AA8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion_cum
        ADD
          CONSTRAINT FK_5F9D268C4C4F0345 FOREIGN KEY (institucion_cum_id) REFERENCES institucion.tbr_institucion_cum (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion_cum
        ADD
          CONSTRAINT FK_5F9D268C997A0FA9 FOREIGN KEY (
            solicitud_programa_academico_institucion_id
          ) REFERENCES pregrado.tbr_solicitud_programa_academico_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_plan_estudio
        ADD
          CONSTRAINT FK_E2E6AE71B4FA1A5 FOREIGN KEY (plan_estudio_id) REFERENCES pregrado.tbd_plan_estudio (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_plan_estudio
        ADD
          CONSTRAINT FK_E2E6AE774422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_comision
        ADD
          CONSTRAINT FK_38202D2A4B352BE1 FOREIGN KEY (comision_id) REFERENCES postgrado.tbd_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_comision
        ADD
          CONSTRAINT FK_38202D2AE5C0BD32 FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_dictamen
        ADD
          CONSTRAINT FK_F4B9FC084B352BE1 FOREIGN KEY (comision_id) REFERENCES postgrado.tbd_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_dictamen
        ADD
          CONSTRAINT FK_F4B9FC08E5C0BD32 FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_dictamen
        ADD
          CONSTRAINT FK_F4B9FC0886A86415 FOREIGN KEY (propietario_dictamen_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_dictamen
        ADD
          CONSTRAINT FK_F4B9FC0859546FD0 FOREIGN KEY (rol_comision_id) REFERENCES postgrado.tbn_rol_comision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_institucion
        ADD
          CONSTRAINT FK_F4BBA03BB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_institucion
        ADD
          CONSTRAINT FK_F4BBA03BE5C0BD32 FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_presencialidad
        ADD
          CONSTRAINT FK_7B39973484E810D2 FOREIGN KEY (presencialidad_programa_id) REFERENCES postgrado.tbn_presencialidad_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_presencialidad
        ADD
          CONSTRAINT FK_7B399734E5C0BD32 FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_votacion
        ADD
          CONSTRAINT FK_48CB5D685822F248 FOREIGN KEY (miembros_copep_id) REFERENCES postgrado.tbr_miembros_copep (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_votacion
        ADD
          CONSTRAINT FK_48CB5D68E5C0BD32 FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_telefono_celular_responsable
        ADD
          CONSTRAINT FK_57ACF0A78400D170 FOREIGN KEY (telefono_celular_id) REFERENCES informatizacion.tbd_telefono_celular (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_telefono_celular_responsable
        ADD
          CONSTRAINT FK_57ACF0A753C59D72 FOREIGN KEY (responsable_id) REFERENCES personal.tbd_persona (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          postgrado.tbr_tipo_solicitud_clasificacion
        ADD
          CONSTRAINT FK_D99FAF8FAFEA88E4 FOREIGN KEY (tipo_solicitud_id) REFERENCES postgrado.tbn_tipo_solicitud (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_ies
        ADD
          CONSTRAINT FK_B84DADF2B239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_ies
        ADD
          CONSTRAINT FK_B84DADF2A8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_posgrado
        ADD
          CONSTRAINT FK_A7A0CAA2E5C0BD32 FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_posgrado
        ADD
          CONSTRAINT FK_A7A0CAA2A8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_pregrado
        ADD
          CONSTRAINT FK_7426D62A74422427 FOREIGN KEY (
            solicitud_programa_academico_id
          ) REFERENCES pregrado.tbd_solicitud_programa_academico (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_pregrado
        ADD
          CONSTRAINT FK_7426D62AB239FBC6 FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_pregrado
        ADD
          CONSTRAINT FK_7426D62AA8AF3830 FOREIGN KEY (categoria_acreditacion_id) REFERENCES institucion.tbn_categoria_acreditacion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'This migration can only be executed safely on PostgreSQL.'
        );

        $this->addSql('DROP SEQUENCE informe_personalizado_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notificaciones_usuario_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rrhh.tbd_ae2_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rrhh.tbd_ae3_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbd_aplazamiento_solicitud_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_centro_dato_virtual_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbd_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbd_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbd_comision_nacional_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE traza.tbd_configuracion_traza_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbd_convenio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbd_convocatoria_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbd_copep_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_documento_salida_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_documento_salida_historial_documento_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_enlace_conectividad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbd_entidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbd_estructura_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_ficha_salida_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_ficha_salida_historial_documento_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE seguridad.tbd_funcionalidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbd_institucion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_institucion_extranjera_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_linea_celular_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE seguridad.tbd_modulo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_nauta_hogar_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_pasaporte_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbd_persona_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbd_plan_estudio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbd_plan_mision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbd_plantilla_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbd_programa_academico_desactivado_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbd_programa_academico_reabierto_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbd_responsable_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE seguridad.tbd_rol_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_servicio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_sistema_informatico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbd_solicitud_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbd_solicitud_programa_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbd_solicitud_programa_academico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbd_telefono_celular_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE traza.tbd_traza_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbd_universidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE seguridad.tbd_usuario_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE traza.tbn_accion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_carrera_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbn_categoria_acreditacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_categoria_categorizacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_categoria_docente_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rrhh.tbn_categoria_docente_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_categoria_estructura_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_categoria_investigativa_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_categoria_responsabilidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_clasificacion_persona_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbn_concepto_gasto_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbn_concepto_salida_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_curso_academico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_documento_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rrhh.tbn_especialidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbn_estado_aplazamiento_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbn_estado_ficha_salida_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_estado_programa_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_estado_programa_academico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbn_estado_solicitud_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_grado_academico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rrhh.tbn_grupo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE configuracion.tbn_idioma_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_marca_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbn_modalida_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_modalidad_programa_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_modelo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_municipio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_nivel_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_nivel_escolar_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_oace_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE traza.tbn_objeto_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbn_obsequio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_organismo_demandante_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_organismo_formador_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_organizacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_pais_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_presencialidad_programa_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_proceso_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_profesion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_provincia_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_publico_objetivo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_rama_ciencia_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbn_recursos_humanos_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbn_red_social_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbn_region_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_responsabilidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbn_rol_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_rol_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbn_rol_redes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_servicio_contratado_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_sexo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_sistema_operativo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbn_tipo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_tipo_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_tipo_conectividad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_tipo_entidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbn_tipo_estructura_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbn_tipo_institucion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_tipo_organismo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbn_tipo_organizacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbn_tipo_pasaporte_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_tipo_programa_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbn_tipo_programa_academico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_tipo_sistema_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbn_tipo_solicitud_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE traza.tbn_tipo_traza_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbn_tramite_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbn_visibilidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbn_visibilidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE convenio.tbr_convenio_accion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbr_documento_salida_tramite_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbr_ficha_salida_concepto_gasto_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbr_ficha_salida_estado_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_historico_estado_programa_academico_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_centros_estudios_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_cum_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_editorial_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_facultades_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_fum_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_mecanismo_colaboracion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_membresia_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_programa_colaboracion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_proyecto_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_recurso_humano_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_redes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_redes_sociales_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_revista_cientifica_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE institucion.tbr_institucion_sedes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbr_linea_celular_recargas_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbr_linea_celular_responsable_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbr_miembros_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_miembros_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_miembros_comision_nacional_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_miembros_copep_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_modificacion_plan_estudio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE personal.tbr_persona_organizacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_plan_estudio_documento_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tramite.tbr_plan_mision_detalles_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE estructura.tbr_plaza_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_programa_academico_reabierto_institucion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE seguridad.tbr_rol_estructura_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbr_sistema_informatico_proceso_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.tbr_solicitud_dictamen_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_solicitud_programa_academico_comision_nacional_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_solicitud_programa_academico_institucion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_solicitud_programa_academico_institucion_cum_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pregrado.tbr_solicitud_programa_academico_plan_estudio_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_solicitud_programa_comision_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_solicitud_programa_dictamen_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_solicitud_programa_institucion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_solicitud_programa_presencialidad_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_solicitud_programa_votacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE informatizacion.tbr_telefono_celular_responsable_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE postgrado.tbr_tipo_solicitud_clasificacion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.temp_categoria_acreditacion_ies_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.temp_categoria_acreditacion_posgrado_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evaluacion.temp_categoria_acreditacion_pregrado_id_seq CASCADE');
        $this->addSql('ALTER TABLE informe_personalizado DROP CONSTRAINT FK_69E8944EDB38439E');
        $this->addSql('ALTER TABLE notificaciones_usuario DROP CONSTRAINT FK_C66FA6F59DA2C333');
        $this->addSql('ALTER TABLE notificaciones_usuario DROP CONSTRAINT FK_C66FA6F55FA3BDA6');
        $this->addSql('ALTER TABLE rrhh.tbd_ae2 DROP CONSTRAINT FK_E712DE0F6CA204EF');
        $this->addSql('ALTER TABLE rrhh.tbd_ae3 DROP CONSTRAINT FK_9015EE996CA204EF');
        $this->addSql('ALTER TABLE evaluacion.tbd_aplazamiento_solicitud DROP CONSTRAINT FK_460B9DEF1CB9D6E4');
        $this->addSql('ALTER TABLE evaluacion.tbd_aplazamiento_solicitud DROP CONSTRAINT FK_460B9DEFBB071712');
        $this->addSql('ALTER TABLE informatizacion.tbd_centro_dato_virtual DROP CONSTRAINT FK_4F1FC251CD2B7E7C');
        $this->addSql('ALTER TABLE evaluacion.tbd_comision DROP CONSTRAINT FK_CA64D28A1CB9D6E4');
        $this->addSql('ALTER TABLE postgrado.tbd_comision DROP CONSTRAINT FK_C80B22D17E110519');
        $this->addSql('ALTER TABLE traza.tbd_configuracion_traza DROP CONSTRAINT FK_447D6EE4F5F88DB9');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF7501E092B9F');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF75098260155');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF750A9276E6C');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF7509098BF83');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF75093977F77');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF750C604D5C6');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF750FE35D8C4');
        $this->addSql('ALTER TABLE convenio.tbd_convenio DROP CONSTRAINT FK_113EF7508C871111');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B31114A03B');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B3F5F88DB9');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B39098BF83');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B3C604D5C6');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B393977F77');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B34BEE089D');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B3251EA755');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B3BDDB5F58');
        $this->addSql('ALTER TABLE tramite.tbd_documento_salida DROP CONSTRAINT FK_117159B321B6FE31');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida_historial_documento
        DROP
          CONSTRAINT FK_8A01F346E2962F06');
        $this->addSql('ALTER TABLE
          tramite.tbd_documento_salida_historial_documento
        DROP
          CONSTRAINT FK_8A01F346DB38439E');
        $this->addSql('ALTER TABLE informatizacion.tbd_enlace_conectividad DROP CONSTRAINT FK_CA8531856451B6B1');
        $this->addSql('ALTER TABLE informatizacion.tbd_enlace_conectividad DROP CONSTRAINT FK_CA853185CD2B7E7C');
        $this->addSql('ALTER TABLE estructura.tbd_entidad DROP CONSTRAINT FK_AB79ADA6CD2B7E7C');
        $this->addSql('ALTER TABLE estructura.tbd_entidad DROP CONSTRAINT FK_AB79ADA6FD2406C7');
        $this->addSql('ALTER TABLE estructura.tbd_entidad DROP CONSTRAINT FK_AB79ADA66CA204EF');
        $this->addSql('ALTER TABLE estructura.tbd_entidad DROP CONSTRAINT FK_AB79ADA658BC1BE0');
        $this->addSql('ALTER TABLE estructura.tbd_entidad DROP CONSTRAINT FK_AB79ADA64E7121AF');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB85CD982D22');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB85CD2B7E7C');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB854498FA55');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB85DA3426AE');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB8558BC1BE0');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB854E7121AF');
        $this->addSql('ALTER TABLE estructura.tbd_estructura DROP CONSTRAINT FK_BF47AB859C833003');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C548F5F88DB9');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C5489098BF83');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C548C604D5C6');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C54893977F77');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C5484BEE089D');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C548251EA755');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C5481693D208');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida DROP CONSTRAINT FK_B4D7C54821B6FE31');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida_historial_documento DROP CONSTRAINT FK_E80A77BD1114A03B');
        $this->addSql('ALTER TABLE tramite.tbd_ficha_salida_historial_documento DROP CONSTRAINT FK_E80A77BDDB38439E');
        $this->addSql('ALTER TABLE seguridad.tbd_funcionalidad DROP CONSTRAINT FK_A7267189C07F55F5');
        $this->addSql('ALTER TABLE institucion.tbd_institucion DROP CONSTRAINT FK_739EFE83C6879E6C');
        $this->addSql('ALTER TABLE institucion.tbd_institucion DROP CONSTRAINT FK_739EFE833EEA3466');
        $this->addSql('ALTER TABLE institucion.tbd_institucion DROP CONSTRAINT FK_739EFE83A8AF3830');
        $this->addSql('ALTER TABLE institucion.tbd_institucion DROP CONSTRAINT FK_739EFE83CD2B7E7C');
        $this->addSql('ALTER TABLE tramite.tbd_institucion_extranjera DROP CONSTRAINT FK_D6AEBBC5C604D5C6');
        $this->addSql('ALTER TABLE informatizacion.tbd_linea_celular DROP CONSTRAINT FK_76D64B5ECD2B7E7C');
        $this->addSql('ALTER TABLE informatizacion.tbd_nauta_hogar DROP CONSTRAINT FK_7A25B70E53C59D72');
        $this->addSql('ALTER TABLE informatizacion.tbd_nauta_hogar DROP CONSTRAINT FK_7A25B70E5354BD7D');
        $this->addSql('ALTER TABLE informatizacion.tbd_nauta_hogar DROP CONSTRAINT FK_7A25B70ECD2B7E7C');
        $this->addSql('ALTER TABLE tramite.tbd_pasaporte DROP CONSTRAINT FK_E7E54A21F5F88DB9');
        $this->addSql('ALTER TABLE tramite.tbd_pasaporte DROP CONSTRAINT FK_E7E54A21251EA755');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A2234E7121AF');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A22358BC1BE0');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A223CF5FD27C');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A2235873B5B9');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A22392D84F6');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A223C5AF4D0F');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A2234683F2FA');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A223D3ECE4BD');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A22374422427');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A2232B32DB58');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A2234498FA55');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A223CD2B7E7C');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A2236CA204EF');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A22396079139');
        $this->addSql('ALTER TABLE personal.tbd_persona DROP CONSTRAINT FK_D817A223DB38439E');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662F7D35438E');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662FC671B40F');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662FF0FF4047');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662F9B2A9D11');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662F2F480A85');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662F16689255');
        $this->addSql('ALTER TABLE pregrado.tbd_plan_estudio DROP CONSTRAINT FK_2531662F68502903');
        $this->addSql('ALTER TABLE personal.tbd_plantilla DROP CONSTRAINT FK_405B8B6CF5F88DB9');
        $this->addSql('ALTER TABLE personal.tbd_plantilla DROP CONSTRAINT FK_405B8B6CCD2B7E7C');
        $this->addSql('ALTER TABLE personal.tbd_plantilla DROP CONSTRAINT FK_405B8B6C96079139');
        $this->addSql('ALTER TABLE pregrado.tbd_programa_academico_desactivado DROP CONSTRAINT FK_44A10FF974422427');
        $this->addSql('ALTER TABLE pregrado.tbd_programa_academico_desactivado DROP CONSTRAINT FK_44A10FF97D35438E');
        $this->addSql('ALTER TABLE pregrado.tbd_programa_academico_reabierto DROP CONSTRAINT FK_833BE5D174422427');
        $this->addSql('ALTER TABLE pregrado.tbd_programa_academico_reabierto DROP CONSTRAINT FK_833BE5D17D35438E');
        $this->addSql('ALTER TABLE personal.tbd_responsable DROP CONSTRAINT FK_2355D865F5F88DB9');
        $this->addSql('ALTER TABLE seguridad.tbr_rol_funcionalidad DROP CONSTRAINT FK_5C2783B44BAB96C');
        $this->addSql('ALTER TABLE seguridad.tbr_rol_funcionalidad DROP CONSTRAINT FK_5C2783B43637E16E');
        $this->addSql('ALTER TABLE informatizacion.tbd_servicio DROP CONSTRAINT FK_55FC9E5E94F69A8C');
        $this->addSql('ALTER TABLE informatizacion.tbd_servicio DROP CONSTRAINT FK_55FC9E5EE90161FC');
        $this->addSql('ALTER TABLE informatizacion.tbd_servicio DROP CONSTRAINT FK_55FC9E5ECD2B7E7C');
        $this->addSql('ALTER TABLE informatizacion.tbd_sistema_informatico DROP CONSTRAINT FK_B6DE6A6A94F69A8C');
        $this->addSql('ALTER TABLE informatizacion.tbd_sistema_informatico DROP CONSTRAINT FK_B6DE6A6A864BA020');
        $this->addSql('ALTER TABLE informatizacion.tbd_sistema_informatico DROP CONSTRAINT FK_B6DE6A6ACD2B7E7C');
        $this->addSql('ALTER TABLE evaluacion.tbd_solicitud DROP CONSTRAINT FK_46681D6C4EE93BE6');
        $this->addSql('ALTER TABLE evaluacion.tbd_solicitud DROP CONSTRAINT FK_46681D6CB239FBC6');
        $this->addSql('ALTER TABLE evaluacion.tbd_solicitud DROP CONSTRAINT FK_46681D6C65EC4CB2');
        $this->addSql('ALTER TABLE evaluacion.tbd_solicitud DROP CONSTRAINT FK_46681D6C68667D11');
        $this->addSql('ALTER TABLE evaluacion.tbd_solicitud DROP CONSTRAINT FK_46681D6C77E88BF9');
        $this->addSql('ALTER TABLE evaluacion.tbd_solicitud DROP CONSTRAINT FK_46681D6CEC47E158');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CFAFEA88E4');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CF45D881AC');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CFC7869A78');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CF271768CD');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CFF0D8DD71');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CFC8AE5DD0');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CFF0FF4047');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CF9EE50F0A');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CF84E810D2');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_59D7F1CFA8AF3830');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC26277AC9ABFFB');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC2627716689255');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC2627781338691');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC262779B2A9D11');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC26277DC252D29');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC262772F480A85');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC26277F0FF4047');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC26277606D2041');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_programa_academico DROP CONSTRAINT FK_5DC26277A8AF3830');
        $this->addSql('ALTER TABLE informatizacion.tbd_telefono_celular DROP CONSTRAINT FK_26F77CCC81EF0041');
        $this->addSql('ALTER TABLE informatizacion.tbd_telefono_celular DROP CONSTRAINT FK_26F77CCCC3A9576E');
        $this->addSql('ALTER TABLE informatizacion.tbd_telefono_celular DROP CONSTRAINT FK_26F77CCCC616A017');
        $this->addSql('ALTER TABLE informatizacion.tbd_telefono_celular DROP CONSTRAINT FK_26F77CCCCD2B7E7C');
        $this->addSql('ALTER TABLE traza.tbd_traza DROP CONSTRAINT FK_5B9073638A5A731D');
        $this->addSql('ALTER TABLE traza.tbd_traza DROP CONSTRAINT FK_5B9073633F4B5275');
        $this->addSql('ALTER TABLE traza.tbd_traza DROP CONSTRAINT FK_5B90736376F5CD27');
        $this->addSql('ALTER TABLE traza.tbd_traza DROP CONSTRAINT FK_5B907363F5F88DB9');
        $this->addSql('ALTER TABLE seguridad.tbr_user_rol DROP CONSTRAINT FK_F808892AA76ED395');
        $this->addSql('ALTER TABLE seguridad.tbr_user_rol DROP CONSTRAINT FK_F808892A4BAB96C');
        $this->addSql('ALTER TABLE personal.tbn_carrera DROP CONSTRAINT FK_D3C253165873B5B9');
        $this->addSql('ALTER TABLE estructura.tbn_categoria_responsabilidad DROP CONSTRAINT FK_39E2253E15B49E6B');
        $this->addSql('ALTER TABLE estructura.tbn_municipio DROP CONSTRAINT FK_AF623AA94E7121AF');
        $this->addSql('ALTER TABLE pregrado.tbn_organismo_demandante DROP CONSTRAINT FK_4063FC281338691');
        $this->addSql('ALTER TABLE personal.tbn_organizacion DROP CONSTRAINT FK_7DDAD16046EB71B9');
        $this->addSql('ALTER TABLE estructura.tbn_responsabilidad DROP CONSTRAINT FK_1C003A0215B49E6B');
        $this->addSql('ALTER TABLE estructura.tbn_tipo_entidad DROP CONSTRAINT FK_383FF7F6FD2406C7');
        $this->addSql('ALTER TABLE convenio.tbr_convenio_accion DROP CONSTRAINT FK_1EA2DC49F9D43F2A');
        $this->addSql('ALTER TABLE tramite.tbr_documento_salida_tramite DROP CONSTRAINT FK_D3C55E7E2962F06');
        $this->addSql('ALTER TABLE tramite.tbr_documento_salida_tramite DROP CONSTRAINT FK_D3C55E7820C2849');
        $this->addSql('ALTER TABLE tramite.tbr_ficha_salida_concepto_gasto DROP CONSTRAINT FK_2C511CEF1114A03B');
        $this->addSql('ALTER TABLE tramite.tbr_ficha_salida_concepto_gasto DROP CONSTRAINT FK_2C511CEF79EED498');
        $this->addSql('ALTER TABLE tramite.tbr_ficha_salida_estado DROP CONSTRAINT FK_FDB3DFD71114A03B');
        $this->addSql('ALTER TABLE tramite.tbr_ficha_salida_estado DROP CONSTRAINT FK_FDB3DFD71693D208');
        $this->addSql('ALTER TABLE
          pregrado.tbr_historico_estado_programa_academico
        DROP
          CONSTRAINT FK_F56AC5CA74422427');
        $this->addSql('ALTER TABLE
          pregrado.tbr_historico_estado_programa_academico
        DROP
          CONSTRAINT FK_F56AC5CAAC9ABFFB');
        $this->addSql('ALTER TABLE
          pregrado.tbr_historico_estado_programa_academico
        DROP
          CONSTRAINT FK_F56AC5CA7D35438E');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_centros_estudios DROP CONSTRAINT FK_5C91D1FDB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_cum DROP CONSTRAINT FK_4FEDCBAFB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_cum DROP CONSTRAINT FK_4FEDCBAFCD2B7E7C');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_editorial DROP CONSTRAINT FK_9AD9A400B239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_facultades DROP CONSTRAINT FK_96EA29DCB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_facultades DROP CONSTRAINT FK_96EA29DCCD2B7E7C');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_fum DROP CONSTRAINT FK_49260944B239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_fum DROP CONSTRAINT FK_49260944CD2B7E7C');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_mecanismo_colaboracion
        DROP
          CONSTRAINT FK_F5793233B239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_membresia DROP CONSTRAINT FK_3E0C2F15B239FBC6');
        $this->addSql('ALTER TABLE
          institucion.tbr_institucion_programa_colaboracion
        DROP
          CONSTRAINT FK_F08B49C0B239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_proyecto DROP CONSTRAINT FK_1D6C1FE4B239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_recurso_humano DROP CONSTRAINT FK_F1C3DFBB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_recurso_humano DROP CONSTRAINT FK_F1C3DFBF33A5713');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_redes DROP CONSTRAINT FK_B73E8B4AB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_redes DROP CONSTRAINT FK_B73E8B4A37E822D');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_redes_sociales DROP CONSTRAINT FK_A72EA82FB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_redes_sociales DROP CONSTRAINT FK_A72EA82FD4DDE3A3');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_revista_cientifica DROP CONSTRAINT FK_29665DEFB239FBC6');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_revista_cientifica DROP CONSTRAINT FK_29665DEF94F69A8C');
        $this->addSql('ALTER TABLE institucion.tbr_institucion_sedes DROP CONSTRAINT FK_8A5EA2FAB239FBC6');
        $this->addSql('ALTER TABLE informatizacion.tbr_linea_celular_recargas DROP CONSTRAINT FK_7B3632ECDCDD29FE');
        $this->addSql('ALTER TABLE informatizacion.tbr_linea_celular_responsable DROP CONSTRAINT FK_1F571B83DCDD29FE');
        $this->addSql('ALTER TABLE informatizacion.tbr_linea_celular_responsable DROP CONSTRAINT FK_1F571B8353C59D72');
        $this->addSql('ALTER TABLE evaluacion.tbr_miembros_comision DROP CONSTRAINT FK_CA5FBC994B352BE1');
        $this->addSql('ALTER TABLE evaluacion.tbr_miembros_comision DROP CONSTRAINT FK_CA5FBC997952B53A');
        $this->addSql('ALTER TABLE evaluacion.tbr_miembros_comision DROP CONSTRAINT FK_CA5FBC9959546FD0');
        $this->addSql('ALTER TABLE postgrado.tbr_miembros_comision DROP CONSTRAINT FK_F505EA7F4B352BE1');
        $this->addSql('ALTER TABLE postgrado.tbr_miembros_comision DROP CONSTRAINT FK_F505EA7F7952B53A');
        $this->addSql('ALTER TABLE postgrado.tbr_miembros_comision DROP CONSTRAINT FK_F505EA7F59546FD0');
        $this->addSql('ALTER TABLE pregrado.tbr_miembros_comision_nacional DROP CONSTRAINT FK_375C51384B352BE1');
        $this->addSql('ALTER TABLE pregrado.tbr_miembros_comision_nacional DROP CONSTRAINT FK_375C51387952B53A');
        $this->addSql('ALTER TABLE pregrado.tbr_miembros_comision_nacional DROP CONSTRAINT FK_375C513859546FD0');
        $this->addSql('ALTER TABLE postgrado.tbr_miembros_copep DROP CONSTRAINT FK_4F8097FFEE770D7F');
        $this->addSql('ALTER TABLE postgrado.tbr_miembros_copep DROP CONSTRAINT FK_4F8097FF7952B53A');
        $this->addSql('ALTER TABLE postgrado.tbr_miembros_copep DROP CONSTRAINT FK_4F8097FF59546FD0');
        $this->addSql('ALTER TABLE pregrado.tbr_modificacion_plan_estudio DROP CONSTRAINT FK_4C02A0131B4FA1A5');
        $this->addSql('ALTER TABLE pregrado.tbr_modificacion_plan_estudio DROP CONSTRAINT FK_4C02A0137D35438E');
        $this->addSql('ALTER TABLE personal.tbr_persona_organizacion DROP CONSTRAINT FK_DC0F6D7BF5F88DB9');
        $this->addSql('ALTER TABLE personal.tbr_persona_organizacion DROP CONSTRAINT FK_DC0F6D7B90B1019E');
        $this->addSql('ALTER TABLE pregrado.tbr_plan_estudio_documento DROP CONSTRAINT FK_71B6471C1B4FA1A5');
        $this->addSql('ALTER TABLE pregrado.tbr_plan_estudio_documento DROP CONSTRAINT FK_71B6471C45C0CF75');
        $this->addSql('ALTER TABLE tramite.tbr_plan_mision_detalles DROP CONSTRAINT FK_C45D3A589FBFDC72');
        $this->addSql('ALTER TABLE tramite.tbr_plan_mision_detalles DROP CONSTRAINT FK_C45D3A58F5F88DB9');
        $this->addSql('ALTER TABLE tramite.tbr_plan_mision_detalles DROP CONSTRAINT FK_C45D3A58C604D5C6');
        $this->addSql('ALTER TABLE estructura.tbr_plaza DROP CONSTRAINT FK_9FACD1A8CD2B7E7C');
        $this->addSql('ALTER TABLE estructura.tbr_plaza DROP CONSTRAINT FK_9FACD1A896079139');
        $this->addSql('ALTER TABLE
          pregrado.tbr_programa_academico_reabierto_institucion
        DROP
          CONSTRAINT FK_941D09961914411F');
        $this->addSql('ALTER TABLE
          pregrado.tbr_programa_academico_reabierto_institucion
        DROP
          CONSTRAINT FK_941D0996B239FBC6');
        $this->addSql('ALTER TABLE seguridad.tbr_rol_estructura DROP CONSTRAINT FK_248D984E4BAB96C');
        $this->addSql('ALTER TABLE seguridad.tbr_rol_estructura DROP CONSTRAINT FK_248D984ECD2B7E7C');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_sistema_informatico_proceso
        DROP
          CONSTRAINT FK_28A3D70BFFEE1BB2');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_sistema_informatico_proceso
        DROP
          CONSTRAINT FK_28A3D70B640D1D53');
        $this->addSql('ALTER TABLE evaluacion.tbr_solicitud_dictamen_comision DROP CONSTRAINT FK_59D574CA1CB9D6E4');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_comision_nacional
        DROP
          CONSTRAINT FK_1E76C96396660F28');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_comision_nacional
        DROP
          CONSTRAINT FK_1E76C96374422427');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion
        DROP
          CONSTRAINT FK_6D90058AB239FBC6');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion
        DROP
          CONSTRAINT FK_6D90058A74422427');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion
        DROP
          CONSTRAINT FK_6D90058AA8AF3830');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion_cum
        DROP
          CONSTRAINT FK_5F9D268C4C4F0345');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_institucion_cum
        DROP
          CONSTRAINT FK_5F9D268C997A0FA9');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_plan_estudio
        DROP
          CONSTRAINT FK_E2E6AE71B4FA1A5');
        $this->addSql('ALTER TABLE
          pregrado.tbr_solicitud_programa_academico_plan_estudio
        DROP
          CONSTRAINT FK_E2E6AE774422427');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_comision DROP CONSTRAINT FK_38202D2A4B352BE1');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_comision DROP CONSTRAINT FK_38202D2AE5C0BD32');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_dictamen DROP CONSTRAINT FK_F4B9FC084B352BE1');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_dictamen DROP CONSTRAINT FK_F4B9FC08E5C0BD32');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_dictamen DROP CONSTRAINT FK_F4B9FC0886A86415');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_dictamen DROP CONSTRAINT FK_F4B9FC0859546FD0');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_institucion DROP CONSTRAINT FK_F4BBA03BB239FBC6');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_institucion DROP CONSTRAINT FK_F4BBA03BE5C0BD32');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_presencialidad
        DROP
          CONSTRAINT FK_7B39973484E810D2');
        $this->addSql('ALTER TABLE
          postgrado.tbr_solicitud_programa_presencialidad
        DROP
          CONSTRAINT FK_7B399734E5C0BD32');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_votacion DROP CONSTRAINT FK_48CB5D685822F248');
        $this->addSql('ALTER TABLE postgrado.tbr_solicitud_programa_votacion DROP CONSTRAINT FK_48CB5D68E5C0BD32');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_telefono_celular_responsable
        DROP
          CONSTRAINT FK_57ACF0A78400D170');
        $this->addSql('ALTER TABLE
          informatizacion.tbr_telefono_celular_responsable
        DROP
          CONSTRAINT FK_57ACF0A753C59D72');
        $this->addSql('ALTER TABLE postgrado.tbr_tipo_solicitud_clasificacion DROP CONSTRAINT FK_D99FAF8FAFEA88E4');
        $this->addSql('ALTER TABLE evaluacion.temp_categoria_acreditacion_ies DROP CONSTRAINT FK_B84DADF2B239FBC6');
        $this->addSql('ALTER TABLE evaluacion.temp_categoria_acreditacion_ies DROP CONSTRAINT FK_B84DADF2A8AF3830');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_posgrado
        DROP
          CONSTRAINT FK_A7A0CAA2E5C0BD32');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_posgrado
        DROP
          CONSTRAINT FK_A7A0CAA2A8AF3830');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_pregrado
        DROP
          CONSTRAINT FK_7426D62A74422427');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_pregrado
        DROP
          CONSTRAINT FK_7426D62AB239FBC6');
        $this->addSql('ALTER TABLE
          evaluacion.temp_categoria_acreditacion_pregrado
        DROP
          CONSTRAINT FK_7426D62AA8AF3830');
        $this->addSql('DROP TABLE informe_personalizado');
        $this->addSql('DROP TABLE notificaciones_usuario');
        $this->addSql('DROP TABLE rrhh.tbd_ae2');
        $this->addSql('DROP TABLE rrhh.tbd_ae3');
        $this->addSql('DROP TABLE evaluacion.tbd_aplazamiento_solicitud');
        $this->addSql('DROP TABLE informatizacion.tbd_centro_dato_virtual');
        $this->addSql('DROP TABLE evaluacion.tbd_comision');
        $this->addSql('DROP TABLE postgrado.tbd_comision');
        $this->addSql('DROP TABLE pregrado.tbd_comision_nacional');
        $this->addSql('DROP TABLE traza.tbd_configuracion_traza');
        $this->addSql('DROP TABLE convenio.tbd_convenio');
        $this->addSql('DROP TABLE evaluacion.tbd_convocatoria');
        $this->addSql('DROP TABLE postgrado.tbd_copep');
        $this->addSql('DROP TABLE tramite.tbd_documento_salida');
        $this->addSql('DROP TABLE tramite.tbd_documento_salida_historial_documento');
        $this->addSql('DROP TABLE informatizacion.tbd_enlace_conectividad');
        $this->addSql('DROP TABLE estructura.tbd_entidad');
        $this->addSql('DROP TABLE estructura.tbd_estructura');
        $this->addSql('DROP TABLE tramite.tbd_ficha_salida');
        $this->addSql('DROP TABLE tramite.tbd_ficha_salida_historial_documento');
        $this->addSql('DROP TABLE seguridad.tbd_funcionalidad');
        $this->addSql('DROP TABLE institucion.tbd_institucion');
        $this->addSql('DROP TABLE tramite.tbd_institucion_extranjera');
        $this->addSql('DROP TABLE informatizacion.tbd_linea_celular');
        $this->addSql('DROP TABLE seguridad.tbd_modulo');
        $this->addSql('DROP TABLE informatizacion.tbd_nauta_hogar');
        $this->addSql('DROP TABLE tramite.tbd_pasaporte');
        $this->addSql('DROP TABLE personal.tbd_persona');
        $this->addSql('DROP TABLE pregrado.tbd_plan_estudio');
        $this->addSql('DROP TABLE tramite.tbd_plan_mision');
        $this->addSql('DROP TABLE personal.tbd_plantilla');
        $this->addSql('DROP TABLE pregrado.tbd_programa_academico_desactivado');
        $this->addSql('DROP TABLE pregrado.tbd_programa_academico_reabierto');
        $this->addSql('DROP TABLE personal.tbd_responsable');
        $this->addSql('DROP TABLE seguridad.tbd_rol');
        $this->addSql('DROP TABLE seguridad.tbr_rol_funcionalidad');
        $this->addSql('DROP TABLE informatizacion.tbd_servicio');
        $this->addSql('DROP TABLE informatizacion.tbd_sistema_informatico');
        $this->addSql('DROP TABLE evaluacion.tbd_solicitud');
        $this->addSql('DROP TABLE postgrado.tbd_solicitud_programa');
        $this->addSql('DROP TABLE pregrado.tbd_solicitud_programa_academico');
        $this->addSql('DROP TABLE informatizacion.tbd_telefono_celular');
        $this->addSql('DROP TABLE traza.tbd_traza');
        $this->addSql('DROP TABLE postgrado.tbd_universidad');
        $this->addSql('DROP TABLE seguridad.tbd_usuario');
        $this->addSql('DROP TABLE seguridad.tbr_user_rol');
        $this->addSql('DROP TABLE traza.tbn_accion');
        $this->addSql('DROP TABLE personal.tbn_carrera');
        $this->addSql('DROP TABLE institucion.tbn_categoria_acreditacion');
        $this->addSql('DROP TABLE postgrado.tbn_categoria_categorizacion');
        $this->addSql('DROP TABLE personal.tbn_categoria_docente');
        $this->addSql('DROP TABLE rrhh.tbn_categoria_docente');
        $this->addSql('DROP TABLE estructura.tbn_categoria_estructura');
        $this->addSql('DROP TABLE personal.tbn_categoria_investigativa');
        $this->addSql('DROP TABLE estructura.tbn_categoria_responsabilidad');
        $this->addSql('DROP TABLE personal.tbn_clasificacion_persona');
        $this->addSql('DROP TABLE convenio.tbn_concepto_gasto');
        $this->addSql('DROP TABLE tramite.tbn_concepto_salida');
        $this->addSql('DROP TABLE pregrado.tbn_curso_academico');
        $this->addSql('DROP TABLE pregrado.tbn_documento');
        $this->addSql('DROP TABLE rrhh.tbn_especialidad');
        $this->addSql('DROP TABLE evaluacion.tbn_estado_aplazamiento');
        $this->addSql('DROP TABLE tramite.tbn_estado_ficha_salida');
        $this->addSql('DROP TABLE postgrado.tbn_estado_programa');
        $this->addSql('DROP TABLE pregrado.tbn_estado_programa_academico');
        $this->addSql('DROP TABLE evaluacion.tbn_estado_solicitud');
        $this->addSql('DROP TABLE personal.tbn_grado_academico');
        $this->addSql('DROP TABLE rrhh.tbn_grupo');
        $this->addSql('DROP TABLE configuracion.tbn_idioma');
        $this->addSql('DROP TABLE informatizacion.tbn_marca');
        $this->addSql('DROP TABLE convenio.tbn_modalida');
        $this->addSql('DROP TABLE postgrado.tbn_modalidad_programa');
        $this->addSql('DROP TABLE informatizacion.tbn_modelo');
        $this->addSql('DROP TABLE estructura.tbn_municipio');
        $this->addSql('DROP TABLE estructura.tbn_nivel');
        $this->addSql('DROP TABLE personal.tbn_nivel_escolar');
        $this->addSql('DROP TABLE pregrado.tbn_oace');
        $this->addSql('DROP TABLE traza.tbn_objeto');
        $this->addSql('DROP TABLE convenio.tbn_obsequio');
        $this->addSql('DROP TABLE pregrado.tbn_organismo_demandante');
        $this->addSql('DROP TABLE pregrado.tbn_organismo_formador');
        $this->addSql('DROP TABLE personal.tbn_organizacion');
        $this->addSql('DROP TABLE estructura.tbn_pais');
        $this->addSql('DROP TABLE postgrado.tbn_presencialidad_programa');
        $this->addSql('DROP TABLE informatizacion.tbn_proceso');
        $this->addSql('DROP TABLE personal.tbn_profesion');
        $this->addSql('DROP TABLE estructura.tbn_provincia');
        $this->addSql('DROP TABLE informatizacion.tbn_publico_objetivo');
        $this->addSql('DROP TABLE postgrado.tbn_rama_ciencia');
        $this->addSql('DROP TABLE institucion.tbn_recursos_humanos');
        $this->addSql('DROP TABLE institucion.tbn_red_social');
        $this->addSql('DROP TABLE convenio.tbn_region');
        $this->addSql('DROP TABLE estructura.tbn_responsabilidad');
        $this->addSql('DROP TABLE evaluacion.tbn_rol_comision');
        $this->addSql('DROP TABLE postgrado.tbn_rol_comision');
        $this->addSql('DROP TABLE institucion.tbn_rol_redes');
        $this->addSql('DROP TABLE informatizacion.tbn_servicio_contratado');
        $this->addSql('DROP TABLE personal.tbn_sexo');
        $this->addSql('DROP TABLE informatizacion.tbn_sistema_operativo');
        $this->addSql('DROP TABLE convenio.tbn_tipo');
        $this->addSql('DROP TABLE postgrado.tbn_tipo_comision');
        $this->addSql('DROP TABLE informatizacion.tbn_tipo_conectividad');
        $this->addSql('DROP TABLE estructura.tbn_tipo_entidad');
        $this->addSql('DROP TABLE estructura.tbn_tipo_estructura');
        $this->addSql('DROP TABLE institucion.tbn_tipo_institucion');
        $this->addSql('DROP TABLE pregrado.tbn_tipo_organismo');
        $this->addSql('DROP TABLE personal.tbn_tipo_organizacion');
        $this->addSql('DROP TABLE tramite.tbn_tipo_pasaporte');
        $this->addSql('DROP TABLE postgrado.tbn_tipo_programa');
        $this->addSql('DROP TABLE pregrado.tbn_tipo_programa_academico');
        $this->addSql('DROP TABLE informatizacion.tbn_tipo_sistema');
        $this->addSql('DROP TABLE postgrado.tbn_tipo_solicitud');
        $this->addSql('DROP TABLE traza.tbn_tipo_traza');
        $this->addSql('DROP TABLE tramite.tbn_tramite');
        $this->addSql('DROP TABLE informatizacion.tbn_visibilidad');
        $this->addSql('DROP TABLE institucion.tbn_visibilidad');
        $this->addSql('DROP TABLE convenio.tbr_convenio_accion');
        $this->addSql('DROP TABLE tramite.tbr_documento_salida_tramite');
        $this->addSql('DROP TABLE tramite.tbr_ficha_salida_concepto_gasto');
        $this->addSql('DROP TABLE tramite.tbr_ficha_salida_estado');
        $this->addSql('DROP TABLE pregrado.tbr_historico_estado_programa_academico');
        $this->addSql('DROP TABLE institucion.tbr_institucion_centros_estudios');
        $this->addSql('DROP TABLE institucion.tbr_institucion_cum');
        $this->addSql('DROP TABLE institucion.tbr_institucion_editorial');
        $this->addSql('DROP TABLE institucion.tbr_institucion_facultades');
        $this->addSql('DROP TABLE institucion.tbr_institucion_fum');
        $this->addSql('DROP TABLE institucion.tbr_institucion_mecanismo_colaboracion');
        $this->addSql('DROP TABLE institucion.tbr_institucion_membresia');
        $this->addSql('DROP TABLE institucion.tbr_institucion_programa_colaboracion');
        $this->addSql('DROP TABLE institucion.tbr_institucion_proyecto');
        $this->addSql('DROP TABLE institucion.tbr_institucion_recurso_humano');
        $this->addSql('DROP TABLE institucion.tbr_institucion_redes');
        $this->addSql('DROP TABLE institucion.tbr_institucion_redes_sociales');
        $this->addSql('DROP TABLE institucion.tbr_institucion_revista_cientifica');
        $this->addSql('DROP TABLE institucion.tbr_institucion_sedes');
        $this->addSql('DROP TABLE informatizacion.tbr_linea_celular_recargas');
        $this->addSql('DROP TABLE informatizacion.tbr_linea_celular_responsable');
        $this->addSql('DROP TABLE evaluacion.tbr_miembros_comision');
        $this->addSql('DROP TABLE postgrado.tbr_miembros_comision');
        $this->addSql('DROP TABLE pregrado.tbr_miembros_comision_nacional');
        $this->addSql('DROP TABLE postgrado.tbr_miembros_copep');
        $this->addSql('DROP TABLE pregrado.tbr_modificacion_plan_estudio');
        $this->addSql('DROP TABLE personal.tbr_persona_organizacion');
        $this->addSql('DROP TABLE pregrado.tbr_plan_estudio_documento');
        $this->addSql('DROP TABLE tramite.tbr_plan_mision_detalles');
        $this->addSql('DROP TABLE estructura.tbr_plaza');
        $this->addSql('DROP TABLE pregrado.tbr_programa_academico_reabierto_institucion');
        $this->addSql('DROP TABLE seguridad.tbr_rol_estructura');
        $this->addSql('DROP TABLE informatizacion.tbr_sistema_informatico_proceso');
        $this->addSql('DROP TABLE evaluacion.tbr_solicitud_dictamen_comision');
        $this->addSql('DROP TABLE pregrado.tbr_solicitud_programa_academico_comision_nacional');
        $this->addSql('DROP TABLE pregrado.tbr_solicitud_programa_academico_institucion');
        $this->addSql('DROP TABLE pregrado.tbr_solicitud_programa_academico_institucion_cum');
        $this->addSql('DROP TABLE pregrado.tbr_solicitud_programa_academico_plan_estudio');
        $this->addSql('DROP TABLE postgrado.tbr_solicitud_programa_comision');
        $this->addSql('DROP TABLE postgrado.tbr_solicitud_programa_dictamen');
        $this->addSql('DROP TABLE postgrado.tbr_solicitud_programa_institucion');
        $this->addSql('DROP TABLE postgrado.tbr_solicitud_programa_presencialidad');
        $this->addSql('DROP TABLE postgrado.tbr_solicitud_programa_votacion');
        $this->addSql('DROP TABLE informatizacion.tbr_telefono_celular_responsable');
        $this->addSql('DROP TABLE postgrado.tbr_tipo_solicitud_clasificacion');
        $this->addSql('DROP TABLE evaluacion.temp_categoria_acreditacion_ies');
        $this->addSql('DROP TABLE evaluacion.temp_categoria_acreditacion_posgrado');
        $this->addSql('DROP TABLE evaluacion.temp_categoria_acreditacion_pregrado');
        $this->addSql('DROP SCHEMA configuracion');
        $this->addSql('DROP SCHEMA convenio');
        $this->addSql('DROP SCHEMA estructura');
        $this->addSql('DROP SCHEMA evaluacion');
        $this->addSql('DROP SCHEMA informatizacion');
        $this->addSql('DROP SCHEMA institucion');
        $this->addSql('DROP SCHEMA personal');
        $this->addSql('DROP SCHEMA postgrado');
        $this->addSql('DROP SCHEMA pregrado');
        $this->addSql('DROP SCHEMA rrhh');
        $this->addSql('DROP SCHEMA seguridad');
        $this->addSql('DROP SCHEMA tramite');
        $this->addSql('DROP SCHEMA traza');
    }
}
