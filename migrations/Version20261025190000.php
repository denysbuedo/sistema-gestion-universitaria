<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convierte las categorías docentes de Personal en catálogo normativo de la Resolución 117/2023';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE personal.tbn_categoria_docente ADD codigo VARCHAR(20) DEFAULT NULL");
        $this->addSql("ALTER TABLE personal.tbn_categoria_docente ADD clasificacion VARCHAR(30) DEFAULT NULL");
        $this->addSql("ALTER TABLE personal.tbn_categoria_docente ADD requiere_aprobacion_mes BOOLEAN NOT NULL DEFAULT FALSE");
        $this->addSql("ALTER TABLE personal.tbn_categoria_docente ADD autoridad_otorgamiento VARCHAR(30) DEFAULT NULL");
        $this->addSql("ALTER TABLE personal.tbn_categoria_docente ADD duracion_maxima_annos INT DEFAULT NULL");
        $this->addSql("ALTER TABLE personal.tbn_categoria_docente ADD orden INT NOT NULL DEFAULT 0");
        $this->addSql("CREATE UNIQUE INDEX uniq_categoria_docente_codigo ON personal.tbn_categoria_docente (codigo) WHERE codigo IS NOT NULL");

        $this->addSql(<<<'SQL'
            UPDATE personal.tbn_categoria_docente SET
                nombre = CASE lower(nombre)
                    WHEN 'titular' THEN 'Profesor Titular'
                    WHEN 'auxiliar' THEN 'Profesor Auxiliar'
                    WHEN 'asistente' THEN 'Profesor Asistente'
                    ELSE nombre END,
                codigo = CASE lower(nombre) WHEN 'titular' THEN 'PT' WHEN 'auxiliar' THEN 'PA' WHEN 'asistente' THEN 'AS' WHEN 'instructor' THEN 'I' END,
                clasificacion = CASE WHEN lower(nombre) = 'instructor' THEN 'Transitoria' ELSE 'Principal' END,
                requiere_aprobacion_mes = lower(nombre) IN ('titular', 'auxiliar'),
                autoridad_otorgamiento = 'Rector',
                duracion_maxima_annos = CASE WHEN lower(nombre) = 'instructor' THEN 5 ELSE NULL END,
                orden = CASE lower(nombre) WHEN 'titular' THEN 10 WHEN 'auxiliar' THEN 20 WHEN 'asistente' THEN 30 WHEN 'instructor' THEN 40 ELSE 99 END
            WHERE lower(nombre) IN ('titular', 'auxiliar', 'asistente', 'instructor')
            SQL);

        $categorias = [
            ['ATDNS', 'Auxiliar Técnico de la Docencia de Nivel Superior', 'Complementaria', false, 'Rector', null, 50],
            ['ATDNMS', 'Auxiliar Técnico de la Docencia de Nivel Medio Superior', 'Complementaria', false, 'Rector', null, 60],
            ['PC', 'Profesor Consultante', 'Condición especial', false, 'Rector', null, 70],
            ['PI', 'Profesor Invitado', 'Especial', true, 'Rector', null, 80],
            ['PE', 'Profesor Emérito', 'Especial', true, 'Ministro de Educación Superior', null, 90],
        ];
        foreach ($categorias as [$codigo, $nombre, $clasificacion, $mes, $autoridad, $duracion, $orden]) {
            $this->addSql(<<<'SQL'
                INSERT INTO personal.tbn_categoria_docente
                    (id, nombre, descripcion, activo, creado, actualizado, codigo, clasificacion, requiere_aprobacion_mes, autoridad_otorgamiento, duracion_maxima_annos, orden)
                SELECT nextval('personal.tbn_categoria_docente_id_seq'), :nombre, NULL, TRUE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :codigo, :clasificacion, CAST(:mes AS BOOLEAN), :autoridad, :duracion, :orden
                WHERE NOT EXISTS (SELECT 1 FROM personal.tbn_categoria_docente WHERE codigo = :codigo)
                SQL, ['nombre' => $nombre, 'codigo' => $codigo, 'clasificacion' => $clasificacion, 'mes' => $mes ? 'true' : 'false', 'autoridad' => $autoridad, 'duracion' => $duracion, 'orden' => $orden]);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM personal.tbn_categoria_docente WHERE codigo IN ('ATDNS','ATDNMS','PC','PI','PE')");
        $this->addSql('DROP INDEX personal.uniq_categoria_docente_codigo');
        $this->addSql('ALTER TABLE personal.tbn_categoria_docente DROP codigo, DROP clasificacion, DROP requiere_aprobacion_mes, DROP autoridad_otorgamiento, DROP duracion_maxima_annos, DROP orden');
    }
}
