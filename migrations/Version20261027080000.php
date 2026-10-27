<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027080000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Limita el piloto a expedientes MES e incorpora Honoris Causa y extranjería';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD es_extranjero BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql(<<<'SQL'
            INSERT INTO personal.tbn_categoria_docente
                (id, nombre, descripcion, activo, creado, actualizado, codigo, clasificacion, requiere_aprobacion_mes, autoridad_otorgamiento, duracion_maxima_annos, orden)
            SELECT nextval('personal.tbn_categoria_docente_id_seq'), 'Doctor Honoris Causa', 'Distinción honorífica aprobada por el MES', TRUE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'DHC', 'Honorífica', TRUE, 'Ministro de Educación Superior', NULL, 95
            WHERE NOT EXISTS (SELECT 1 FROM personal.tbn_categoria_docente WHERE codigo='DHC')
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM personal.tbn_categoria_docente WHERE codigo='DHC'");
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente DROP es_extranjero');
    }
}
