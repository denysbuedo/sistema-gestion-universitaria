<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025211000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Limita los reportes del Director de RRHH a los envíos de su entidad';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE FROM seguridad.tbr_rol_funcionalidad rf
            USING seguridad.tbd_rol r, seguridad.tbd_funcionalidad f
            WHERE rf.rol_id = r.id
              AND rf.funcionalidad_id = f.id
              AND r.nombre = 'Director de RRHH'
              AND f.role_key IN (
                  'ROLE_RRHH_REPORTE_AE2_CONSOLIDADO',
                  'ROLE_RRHH_REPORTE_AE3_CONSOLIDADO',
                  'ROLE_RRHH_REPORTE_AE2_MES',
                  'ROLE_RRHH_REPORTE_1_4',
                  'ROLE_RRHH_CLAUSTRO_DRC_PT'
              )
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO seguridad.tbr_rol_funcionalidad (rol_id, funcionalidad_id)
            SELECT r.id, f.id
            FROM seguridad.tbd_rol r
            CROSS JOIN seguridad.tbd_funcionalidad f
            WHERE r.nombre = 'Director de RRHH'
              AND f.role_key = 'ROLE_RRHH_REPORTE_AE2_CONSOLIDADO'
            ON CONFLICT DO NOTHING
            SQL);
    }
}
