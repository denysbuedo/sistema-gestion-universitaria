<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261027210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Corrige compatibilidad de categorías especiales y conserva un documento vigente por requisito';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE FROM rrhh.tbd_documento_proceso_categoria anterior
            USING rrhh.tbd_documento_proceso_categoria posterior
            WHERE anterior.proceso_id=posterior.proceso_id
              AND anterior.tipo=posterior.tipo
              AND anterior.tipo<>'OTRA_EVIDENCIA'
              AND (anterior.creado, anterior.id)<(posterior.creado, posterior.id)
            SQL);
        $this->addSql("CREATE UNIQUE INDEX uniq_documento_categoria_requisito ON rrhh.tbd_documento_proceso_categoria (proceso_id, tipo) WHERE tipo<>'OTRA_EVIDENCIA'");
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION rrhh.validar_proceso_categoria_docente() RETURNS trigger AS $$
            DECLARE
                codigo_solicitado VARCHAR(20);
                codigo_actual VARCHAR(20);
                rango_solicitado INT;
                rango_actual INT;
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM rrhh.tbd_proceso_categoria_docente p
                    WHERE p.persona_id=NEW.persona_id
                      AND p.categoria_docente_id=NEW.categoria_docente_id
                ) THEN
                    RAISE EXCEPTION 'Ya existe un expediente para esta persona y la misma categoría.' USING ERRCODE='23505';
                END IF;

                SELECT codigo,
                       CASE codigo WHEN 'PT' THEN 1 WHEN 'PA' THEN 2 WHEN 'AS' THEN 3 WHEN 'I' THEN 4 END
                  INTO codigo_solicitado, rango_solicitado
                  FROM personal.tbn_categoria_docente
                 WHERE id=NEW.categoria_docente_id;

                SELECT actual.codigo,
                       CASE actual.codigo WHEN 'PT' THEN 1 WHEN 'PA' THEN 2 WHEN 'AS' THEN 3 WHEN 'I' THEN 4 END
                  INTO codigo_actual, rango_actual
                  FROM personal.tbd_persona persona
                  LEFT JOIN personal.tbn_categoria_docente actual ON actual.id=persona.categoria_docente_id
                 WHERE persona.id=NEW.persona_id;

                IF rango_solicitado IS NOT NULL AND rango_actual IS NOT NULL AND rango_solicitado>rango_actual THEN
                    RAISE EXCEPTION 'No puede solicitarse una categoría docente principal inferior a la que ya posee la persona.' USING ERRCODE='23514';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX rrhh.uniq_documento_categoria_requisito');
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
}
