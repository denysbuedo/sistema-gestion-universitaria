<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Completa tribunal, ejercicios, dictámenes y resolución de categorías docentes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD numero_resolucion VARCHAR(100) DEFAULT NULL, ADD fecha_resolucion DATE DEFAULT NULL, ADD resultado_final VARCHAR(30) DEFAULT NULL");
        $this->addSql(<<<'SQL'
            CREATE FUNCTION rrhh.validar_proceso_categoria_docente() RETURNS trigger AS $$
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM rrhh.tbd_proceso_categoria_docente p
                    JOIN personal.tbn_categoria_docente existente ON existente.id=p.categoria_docente_id
                    JOIN personal.tbn_categoria_docente solicitada ON solicitada.id=NEW.categoria_docente_id
                    WHERE p.persona_id=NEW.persona_id
                      AND p.estado NOT IN ('RECHAZADO', 'NO_APROBADO', 'OTORGADO')
                      AND existente.orden <= solicitada.orden
                ) THEN
                    RAISE EXCEPTION 'Ya existe un expediente activo para la misma categoría o una superior.' USING ERRCODE='23505';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
            SQL);
        $this->addSql('CREATE TRIGGER trg_validar_proceso_categoria BEFORE INSERT ON rrhh.tbd_proceso_categoria_docente FOR EACH ROW EXECUTE FUNCTION rrhh.validar_proceso_categoria_docente()');

        $this->addSql('CREATE SEQUENCE rrhh.tbd_tribunal_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_tribunal_categoria_docente (id INT NOT NULL DEFAULT nextval('rrhh.tbd_tribunal_categoria_docente_id_seq'), proceso_id INT NOT NULL, fecha_constitucion DATE NOT NULL, presidente VARCHAR(255) NOT NULL, secretario VARCHAR(255) NOT NULL, vocales TEXT NOT NULL, estado VARCHAR(30) NOT NULL DEFAULT 'PROPUESTO', acta VARCHAR(255) DEFAULT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id))");
        $this->addSql('CREATE UNIQUE INDEX uniq_tribunal_categoria_proceso ON rrhh.tbd_tribunal_categoria_docente (proceso_id)');
        $this->addSql('ALTER TABLE rrhh.tbd_tribunal_categoria_docente ADD CONSTRAINT fk_tribunal_categoria_proceso FOREIGN KEY (proceso_id) REFERENCES rrhh.tbd_proceso_categoria_docente (id) ON DELETE CASCADE');

        $this->addSql('CREATE SEQUENCE rrhh.tbd_ejercicio_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_ejercicio_categoria_docente (id INT NOT NULL DEFAULT nextval('rrhh.tbd_ejercicio_categoria_docente_id_seq'), proceso_id INT NOT NULL, tipo VARCHAR(80) NOT NULL, fecha DATE NOT NULL, resultado VARCHAR(30) NOT NULL, calificacion VARCHAR(100) DEFAULT NULL, acta VARCHAR(255) DEFAULT NULL, observaciones TEXT DEFAULT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id))");
        $this->addSql('CREATE INDEX idx_ejercicio_categoria_proceso ON rrhh.tbd_ejercicio_categoria_docente (proceso_id, fecha)');
        $this->addSql('ALTER TABLE rrhh.tbd_ejercicio_categoria_docente ADD CONSTRAINT fk_ejercicio_categoria_proceso FOREIGN KEY (proceso_id) REFERENCES rrhh.tbd_proceso_categoria_docente (id) ON DELETE CASCADE');

        $this->addSql('CREATE SEQUENCE rrhh.tbd_dictamen_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_dictamen_categoria_docente (id INT NOT NULL DEFAULT nextval('rrhh.tbd_dictamen_categoria_docente_id_seq'), proceso_id INT NOT NULL, area VARCHAR(80) NOT NULL, fecha DATE NOT NULL, resultado VARCHAR(30) NOT NULL, documento VARCHAR(255) DEFAULT NULL, observaciones TEXT DEFAULT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id))");
        $this->addSql('CREATE INDEX idx_dictamen_categoria_proceso ON rrhh.tbd_dictamen_categoria_docente (proceso_id, area)');
        $this->addSql('ALTER TABLE rrhh.tbd_dictamen_categoria_docente ADD CONSTRAINT fk_dictamen_categoria_proceso FOREIGN KEY (proceso_id) REFERENCES rrhh.tbd_proceso_categoria_docente (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE rrhh.tbd_dictamen_categoria_docente');
        $this->addSql('DROP SEQUENCE rrhh.tbd_dictamen_categoria_docente_id_seq');
        $this->addSql('DROP TABLE rrhh.tbd_ejercicio_categoria_docente');
        $this->addSql('DROP SEQUENCE rrhh.tbd_ejercicio_categoria_docente_id_seq');
        $this->addSql('DROP TABLE rrhh.tbd_tribunal_categoria_docente');
        $this->addSql('DROP SEQUENCE rrhh.tbd_tribunal_categoria_docente_id_seq');
        $this->addSql('DROP TRIGGER trg_validar_proceso_categoria ON rrhh.tbd_proceso_categoria_docente');
        $this->addSql('DROP FUNCTION rrhh.validar_proceso_categoria_docente()');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente DROP numero_resolucion, DROP fecha_resolucion, DROP resultado_final');
    }
}
