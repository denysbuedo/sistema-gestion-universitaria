<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025213000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Incorpora documentos, evidencias y trazabilidad a los expedientes docentes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE rrhh.tbd_documento_proceso_categoria_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_documento_proceso_categoria (id INT NOT NULL DEFAULT nextval('rrhh.tbd_documento_proceso_categoria_id_seq'), proceso_id INT NOT NULL, tipo VARCHAR(40) NOT NULL, nombre_original VARCHAR(255) NOT NULL, archivo VARCHAR(255) NOT NULL, descripcion TEXT DEFAULT NULL, creado_por_id INT NOT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id))");
        $this->addSql('CREATE INDEX idx_documento_proceso_categoria ON rrhh.tbd_documento_proceso_categoria (proceso_id, tipo)');
        $this->addSql('ALTER TABLE rrhh.tbd_documento_proceso_categoria ADD CONSTRAINT fk_documento_categoria_proceso FOREIGN KEY (proceso_id) REFERENCES rrhh.tbd_proceso_categoria_docente (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rrhh.tbd_documento_proceso_categoria ADD CONSTRAINT fk_documento_categoria_usuario FOREIGN KEY (creado_por_id) REFERENCES seguridad.tbd_usuario (id)');

        $this->addSql('CREATE SEQUENCE rrhh.tbd_movimiento_proceso_categoria_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_movimiento_proceso_categoria (id INT NOT NULL DEFAULT nextval('rrhh.tbd_movimiento_proceso_categoria_id_seq'), proceso_id INT NOT NULL, estado_origen VARCHAR(30) DEFAULT NULL, estado_destino VARCHAR(30) NOT NULL, accion VARCHAR(80) NOT NULL, observaciones TEXT DEFAULT NULL, realizado_por_id INT NOT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id))");
        $this->addSql('CREATE INDEX idx_movimiento_proceso_categoria ON rrhh.tbd_movimiento_proceso_categoria (proceso_id, creado)');
        $this->addSql('ALTER TABLE rrhh.tbd_movimiento_proceso_categoria ADD CONSTRAINT fk_movimiento_categoria_proceso FOREIGN KEY (proceso_id) REFERENCES rrhh.tbd_proceso_categoria_docente (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rrhh.tbd_movimiento_proceso_categoria ADD CONSTRAINT fk_movimiento_categoria_usuario FOREIGN KEY (realizado_por_id) REFERENCES seguridad.tbd_usuario (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE rrhh.tbd_movimiento_proceso_categoria');
        $this->addSql('DROP SEQUENCE rrhh.tbd_movimiento_proceso_categoria_id_seq');
        $this->addSql('DROP TABLE rrhh.tbd_documento_proceso_categoria');
        $this->addSql('DROP SEQUENCE rrhh.tbd_documento_proceso_categoria_id_seq');
    }
}
