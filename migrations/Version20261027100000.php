<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Añade país de procedencia y desistimiento a expedientes MES';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD pais_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD CONSTRAINT fk_proceso_categoria_pais FOREIGN KEY (pais_id) REFERENCES estructura.tbn_pais (id)');
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION rrhh.validar_proceso_categoria_docente() RETURNS trigger AS $$
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM rrhh.tbd_proceso_categoria_docente p
                    JOIN personal.tbn_categoria_docente existente ON existente.id=p.categoria_docente_id
                    JOIN personal.tbn_categoria_docente solicitada ON solicitada.id=NEW.categoria_docente_id
                    WHERE p.persona_id=NEW.persona_id
                      AND p.estado NOT IN ('RECHAZADO', 'NO_APROBADO', 'DESISTIDO')
                      AND existente.orden <= solicitada.orden
                ) THEN
                    RAISE EXCEPTION 'Ya existe un expediente activo o aprobado para la misma categoría o una superior.' USING ERRCODE='23505';
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente DROP CONSTRAINT fk_proceso_categoria_pais, DROP pais_id');
    }
}
