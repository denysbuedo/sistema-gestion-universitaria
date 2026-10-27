<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Registra instrumentos ministeriales reutilizables y habilita Profesor Invitado en expedientes MES';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE rrhh.tbd_instrumento_aprobacion_categoria_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<'SQL'
            CREATE TABLE rrhh.tbd_instrumento_aprobacion_categoria (
                id INT NOT NULL DEFAULT nextval('rrhh.tbd_instrumento_aprobacion_categoria_id_seq'),
                tipo VARCHAR(20) NOT NULL,
                numero VARCHAR(100) DEFAULT NULL,
                fecha DATE NOT NULL,
                documento VARCHAR(255) NOT NULL,
                nombre_original VARCHAR(255) NOT NULL,
                observaciones TEXT DEFAULT NULL,
                creado_por_id INT NOT NULL,
                creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(id),
                CONSTRAINT chk_instrumento_categoria_tipo CHECK (tipo IN ('RESOLUCION', 'CARTA')),
                CONSTRAINT chk_instrumento_categoria_numero CHECK (tipo <> 'RESOLUCION' OR (numero IS NOT NULL AND btrim(numero) <> ''))
            )
            SQL);
        $this->addSql('CREATE UNIQUE INDEX uniq_instrumento_categoria_resolucion ON rrhh.tbd_instrumento_aprobacion_categoria (tipo, numero, fecha) WHERE tipo = \'RESOLUCION\'');
        $this->addSql('ALTER TABLE rrhh.tbd_instrumento_aprobacion_categoria ADD CONSTRAINT fk_instrumento_categoria_usuario FOREIGN KEY (creado_por_id) REFERENCES seguridad.tbd_usuario (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD instrumento_aprobacion_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_proceso_categoria_instrumento ON rrhh.tbd_proceso_categoria_docente (instrumento_aprobacion_id)');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD CONSTRAINT fk_proceso_categoria_instrumento FOREIGN KEY (instrumento_aprobacion_id) REFERENCES rrhh.tbd_instrumento_aprobacion_categoria (id)');
        $this->addSql("UPDATE personal.tbn_categoria_docente SET requiere_aprobacion_mes=TRUE, autoridad_otorgamiento='Ministro de Educación Superior', actualizado=CURRENT_TIMESTAMP WHERE codigo='PI'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente DROP CONSTRAINT fk_proceso_categoria_instrumento');
        $this->addSql('DROP INDEX rrhh.idx_proceso_categoria_instrumento');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente DROP instrumento_aprobacion_id');
        $this->addSql('DROP TABLE rrhh.tbd_instrumento_aprobacion_categoria');
        $this->addSql('DROP SEQUENCE rrhh.tbd_instrumento_aprobacion_categoria_id_seq');
        $this->addSql("UPDATE personal.tbn_categoria_docente SET autoridad_otorgamiento='Rector' WHERE codigo='PI'");
    }
}
