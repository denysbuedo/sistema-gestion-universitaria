<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261023120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Record explicitly the modalities taught at the university central campus';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE pregrado.tbr_solicitud_programa_academico_institucion ADD modalidades_sede_central JSON NOT NULL DEFAULT '[]'");
        $this->addSql('DELETE FROM pregrado.tbr_solicitud_programa_academico_institucion_cum repetido USING pregrado.tbr_solicitud_programa_academico_institucion_cum original WHERE repetido.id > original.id AND repetido.solicitud_programa_academico_institucion_id = original.solicitud_programa_academico_institucion_id AND repetido.institucion_cum_id = original.institucion_cum_id AND repetido.modalidad IS NOT DISTINCT FROM original.modalidad');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_programa_institucion_cum_modalidad ON pregrado.tbr_solicitud_programa_academico_institucion_cum (solicitud_programa_academico_institucion_id, institucion_cum_id, modalidad)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_programa_institucion_institucion ON pregrado.tbr_solicitud_programa_academico_institucion (institucion_id, solicitud_programa_academico_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS pregrado.uniq_programa_institucion_cum_modalidad');
        $this->addSql('DROP INDEX IF EXISTS pregrado.idx_programa_institucion_institucion');
        $this->addSql('ALTER TABLE pregrado.tbr_solicitud_programa_academico_institucion DROP modalidades_sede_central');
    }
}
