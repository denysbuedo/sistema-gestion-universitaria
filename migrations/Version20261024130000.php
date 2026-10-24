<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261024130000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create requests to incorporate an existing national program into a university'; }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE pregrado.tbd_solicitud_incorporacion_programa (id SERIAL NOT NULL, programa_id INT NOT NULL, institucion_id INT NOT NULL, fundamentacion TEXT NOT NULL, modalidad_diurno BOOLEAN NOT NULL, modalidad_por_encuentro BOOLEAN NOT NULL, modalidad_adistancia BOOLEAN NOT NULL, estado VARCHAR(20) NOT NULL, observaciones TEXT DEFAULT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql('CREATE INDEX idx_sol_incorp_programa ON pregrado.tbd_solicitud_incorporacion_programa (programa_id)');
        $this->addSql('CREATE INDEX idx_sol_incorp_institucion ON pregrado.tbd_solicitud_incorporacion_programa (institucion_id, estado)');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_incorporacion_programa ADD CONSTRAINT fk_sol_incorp_programa FOREIGN KEY (programa_id) REFERENCES pregrado.tbd_solicitud_programa_academico (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pregrado.tbd_solicitud_incorporacion_programa ADD CONSTRAINT fk_sol_incorp_institucion FOREIGN KEY (institucion_id) REFERENCES institucion.tbd_institucion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE pregrado.tbd_solicitud_incorporacion_programa');
    }
}
