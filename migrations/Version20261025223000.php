<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025223000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convierte los tribunales docentes en recursos reutilizables por universidad';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rrhh.tbd_tribunal_categoria_docente ADD entidad_id INT DEFAULT NULL, ADD nombre VARCHAR(255) DEFAULT NULL');
        $this->addSql("UPDATE rrhh.tbd_tribunal_categoria_docente t SET entidad_id=p.entidad_id, nombre=concat('Tribunal ', p.numero_expediente), estado='ACTIVO' FROM rrhh.tbd_proceso_categoria_docente p WHERE p.id=t.proceso_id");
        $this->addSql('ALTER TABLE rrhh.tbd_tribunal_categoria_docente ALTER entidad_id SET NOT NULL, ALTER nombre SET NOT NULL, ALTER proceso_id DROP NOT NULL');
        $this->addSql('DROP INDEX rrhh.uniq_tribunal_categoria_proceso');
        $this->addSql('CREATE INDEX idx_tribunal_categoria_entidad ON rrhh.tbd_tribunal_categoria_docente (entidad_id, estado)');
        $this->addSql('ALTER TABLE rrhh.tbd_tribunal_categoria_docente ADD CONSTRAINT fk_tribunal_categoria_entidad FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD tribunal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD CONSTRAINT fk_proceso_categoria_tribunal FOREIGN KEY (tribunal_id) REFERENCES rrhh.tbd_tribunal_categoria_docente (id)');
        $this->addSql('UPDATE rrhh.tbd_proceso_categoria_docente p SET tribunal_id=t.id FROM rrhh.tbd_tribunal_categoria_docente t WHERE t.proceso_id=p.id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente DROP CONSTRAINT fk_proceso_categoria_tribunal, DROP tribunal_id');
        $this->addSql('ALTER TABLE rrhh.tbd_tribunal_categoria_docente DROP CONSTRAINT fk_tribunal_categoria_entidad');
        $this->addSql('DELETE FROM rrhh.tbd_tribunal_categoria_docente WHERE proceso_id IS NULL');
        $this->addSql('ALTER TABLE rrhh.tbd_tribunal_categoria_docente ALTER proceso_id SET NOT NULL, DROP entidad_id, DROP nombre');
        $this->addSql('CREATE UNIQUE INDEX uniq_tribunal_categoria_proceso ON rrhh.tbd_tribunal_categoria_docente (proceso_id)');
    }
}
