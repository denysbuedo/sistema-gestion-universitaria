<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Grant the HR administrator access to structures and every HR report';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT INTO seguridad.tbr_rol_funcionalidad (rol_id, funcionalidad_id)
            SELECT r.id, f.id
            FROM seguridad.tbd_rol r
            CROSS JOIN seguridad.tbd_funcionalidad f
            WHERE r.role_key = 'ROL_RRHH'
              AND f.role_key IN (
                'ROLE_GEST_ESTRUCT',
                'ROLE_RRHH_REPORTE_AE2',
                'ROLE_RRHH_REPORTE_AE3',
                'ROLE_RRHH_REPORTE_AE2_CONSOLIDADO',
                'ROLE_RRHH_REPORTE_AE3_CONSOLIDADO',
                'ROLE_RRHH_REPORTE_AE2_MES',
                'ROLE_RRHH_REPORTE_1_4',
                'ROLE_RRHH_CLAUSTRO_DRC_PT'
              )
            ON CONFLICT (rol_id, funcionalidad_id) DO NOTHING
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE FROM seguridad.tbr_rol_funcionalidad rf
            USING seguridad.tbd_rol r, seguridad.tbd_funcionalidad f
            WHERE rf.rol_id = r.id
              AND rf.funcionalidad_id = f.id
              AND r.role_key = 'ROL_RRHH'
              AND f.role_key = 'ROLE_GEST_ESTRUCT'
            SQL);
    }
}
