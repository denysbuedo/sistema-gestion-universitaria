<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261028231500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Evoluciona solicitudes de maestrías y especialidades sin eliminar datos heredados';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE postgrado.tbd_solicitud_programa ADD naturaleza_solicitud VARCHAR(40) DEFAULT 'LEGADO' NOT NULL");
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD justificacion_solicitud TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD organismo_demandante VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD programa_referencia_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD porcentaje_modificacion INT DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD cambia_denominacion BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD cambia_objetivos BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD numero_resolucion_ministerial VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD fecha_resolucion_ministerial DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD fecha_presentacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD fecha_decision TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_POSTGRADO_PROGRAMA_REFERENCIA ON postgrado.tbd_solicitud_programa (programa_referencia_id)');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa ADD CONSTRAINT FK_POSTGRADO_PROGRAMA_REFERENCIA FOREIGN KEY (programa_referencia_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE SEQUENCE postgrado.tbd_solicitud_programa_documento_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<'SQL'
            CREATE TABLE postgrado.tbd_solicitud_programa_documento (
                id INT NOT NULL,
                solicitud_programa_id INT NOT NULL,
                tipo VARCHAR(50) NOT NULL,
                ruta VARCHAR(255) NOT NULL,
                nombre_original VARCHAR(255) DEFAULT NULL,
                vigente BOOLEAN DEFAULT TRUE NOT NULL,
                subido_por VARCHAR(180) DEFAULT NULL,
                creado TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                actualizado TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY(id)
            )
            SQL);
        $this->addSql('CREATE INDEX IDX_POSTGRADO_SOL_DOCUMENTO ON postgrado.tbd_solicitud_programa_documento (solicitud_programa_id)');
        $this->addSql("CREATE UNIQUE INDEX UNIQ_POSTGRADO_DOCUMENTO_VIGENTE ON postgrado.tbd_solicitud_programa_documento (solicitud_programa_id, tipo) WHERE vigente = TRUE");
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa_documento ADD CONSTRAINT FK_POSTGRADO_SOL_DOCUMENTO FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE SEQUENCE postgrado.tbd_solicitud_programa_historial_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<'SQL'
            CREATE TABLE postgrado.tbd_solicitud_programa_historial (
                id INT NOT NULL,
                solicitud_programa_id INT NOT NULL,
                accion VARCHAR(50) NOT NULL,
                estado_anterior VARCHAR(100) DEFAULT NULL,
                estado_nuevo VARCHAR(100) DEFAULT NULL,
                observacion TEXT DEFAULT NULL,
                realizado_por VARCHAR(180) DEFAULT NULL,
                creado TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                actualizado TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY(id)
            )
            SQL);
        $this->addSql('CREATE INDEX IDX_POSTGRADO_SOL_HISTORIAL ON postgrado.tbd_solicitud_programa_historial (solicitud_programa_id, creado)');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa_historial ADD CONSTRAINT FK_POSTGRADO_SOL_HISTORIAL FOREIGN KEY (solicitud_programa_id) REFERENCES postgrado.tbd_solicitud_programa (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql("UPDATE postgrado.tbd_solicitud_programa SET naturaleza_solicitud='NUEVO'");
        $this->addSql("UPDATE postgrado.tbd_solicitud_programa s SET naturaleza_solicitud='IMPARTIR_EXISTENTE' FROM postgrado.tbr_tipo_solicitud_clasificacion c WHERE c.id=s.tipo_solicitud_clasificacion_id AND c.clasificacion ILIKE '%exist%'");
        $this->addSql("UPDATE postgrado.tbd_solicitud_programa s SET naturaleza_solicitud='RED' FROM postgrado.tbn_tipo_solicitud t WHERE t.id=s.tipo_solicitud_id AND t.nombre ILIKE '%red%' AND s.naturaleza_solicitud<>'IMPARTIR_EXISTENTE'");
        $this->addSql("UPDATE postgrado.tbd_solicitud_programa s SET naturaleza_solicitud='NACIONAL' FROM postgrado.tbn_tipo_solicitud t WHERE t.id=s.tipo_solicitud_id AND t.nombre ILIKE '%nacional%' AND s.naturaleza_solicitud<>'IMPARTIR_EXISTENTE'");

        $this->addSql("INSERT INTO postgrado.tbd_solicitud_programa_documento (id, solicitud_programa_id, tipo, ruta, nombre_original, vigente, creado, actualizado) SELECT nextval('postgrado.tbd_solicitud_programa_documento_id_seq'), id, 'PROGRAMA', doc_programa, doc_programa, TRUE, COALESCE(creado, CURRENT_TIMESTAMP), COALESCE(actualizado, CURRENT_TIMESTAMP) FROM postgrado.tbd_solicitud_programa WHERE doc_programa IS NOT NULL AND doc_programa<>''");
        $this->addSql("INSERT INTO postgrado.tbd_solicitud_programa_documento (id, solicitud_programa_id, tipo, ruta, nombre_original, vigente, creado, actualizado) SELECT nextval('postgrado.tbd_solicitud_programa_documento_id_seq'), id, 'RESOLUCION_MINISTERIAL', resolucion_programa, resolucion_programa, TRUE, COALESCE(creado, CURRENT_TIMESTAMP), COALESCE(actualizado, CURRENT_TIMESTAMP) FROM postgrado.tbd_solicitud_programa WHERE resolucion_programa IS NOT NULL AND resolucion_programa<>''");
        $this->addSql("INSERT INTO postgrado.tbd_solicitud_programa_documento (id, solicitud_programa_id, tipo, ruta, nombre_original, vigente, creado, actualizado) SELECT nextval('postgrado.tbd_solicitud_programa_documento_id_seq'), id, 'DICTAMEN_GENERAL', dictamen_general, dictamen_general, TRUE, COALESCE(creado, CURRENT_TIMESTAMP), COALESCE(actualizado, CURRENT_TIMESTAMP) FROM postgrado.tbd_solicitud_programa WHERE dictamen_general IS NOT NULL AND dictamen_general<>''");
        $this->addSql("INSERT INTO postgrado.tbd_solicitud_programa_documento (id, solicitud_programa_id, tipo, ruta, nombre_original, vigente, creado, actualizado) SELECT nextval('postgrado.tbd_solicitud_programa_documento_id_seq'), id, 'DICTAMEN_FINAL', dictamen_final, dictamen_final, TRUE, COALESCE(creado, CURRENT_TIMESTAMP), COALESCE(actualizado, CURRENT_TIMESTAMP) FROM postgrado.tbd_solicitud_programa WHERE dictamen_final IS NOT NULL AND dictamen_final<>''");
        $this->addSql("INSERT INTO postgrado.tbd_solicitud_programa_historial (id, solicitud_programa_id, accion, estado_nuevo, observacion, realizado_por, creado, actualizado) SELECT nextval('postgrado.tbd_solicitud_programa_historial_id_seq'), s.id, 'MIGRACION', e.nombre, 'Expediente incorporado desde el flujo histórico sin alterar sus datos.', 'sistema', COALESCE(s.creado, CURRENT_TIMESTAMP), COALESCE(s.actualizado, CURRENT_TIMESTAMP) FROM postgrado.tbd_solicitud_programa s LEFT JOIN postgrado.tbn_estado_programa e ON e.id=s.estado_programa_id");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE postgrado.tbd_solicitud_programa_historial');
        $this->addSql('DROP SEQUENCE postgrado.tbd_solicitud_programa_historial_id_seq CASCADE');
        $this->addSql('DROP TABLE postgrado.tbd_solicitud_programa_documento');
        $this->addSql('DROP SEQUENCE postgrado.tbd_solicitud_programa_documento_id_seq CASCADE');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP CONSTRAINT FK_POSTGRADO_PROGRAMA_REFERENCIA');
        $this->addSql('DROP INDEX postgrado.IDX_POSTGRADO_PROGRAMA_REFERENCIA');
        $this->addSql('ALTER TABLE postgrado.tbd_solicitud_programa DROP naturaleza_solicitud, DROP justificacion_solicitud, DROP organismo_demandante, DROP programa_referencia_id, DROP porcentaje_modificacion, DROP cambia_denominacion, DROP cambia_objetivos, DROP numero_resolucion_ministerial, DROP fecha_resolucion_ministerial, DROP fecha_presentacion, DROP fecha_decision');
    }
}
