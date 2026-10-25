<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025173000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Concede al administrador de RRHH la gestión nacional de entidades y catálogos de estructura, excepto provincias y municipios';
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
                'ROLE_GEST_ENTITY',
                'ROLE_ESTRUCTURA_EMPRESA',
                'ROLE_ESTRUCTURA_UNIDAD_PRESUPUESTADA_TRATAMIENTO_ESPECIAL',
                'ROLE_ESTRUCTURA_UNIDAD_PRESUPUESTADA',
                'ROLE_ESTRUCTURA_INTERFACES_MYPIMES',
                'ROLE_GEST_CATGEST',
                'ROLE_GEST_TYPEESTR',
                'ROLE_GEST_CATRESP',
                'ROLE_GEST_RESPON'
              )
            ON CONFLICT DO NOTHING
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
              AND f.role_key IN (
                'ROLE_GEST_ENTITY',
                'ROLE_ESTRUCTURA_EMPRESA',
                'ROLE_ESTRUCTURA_UNIDAD_PRESUPUESTADA_TRATAMIENTO_ESPECIAL',
                'ROLE_ESTRUCTURA_UNIDAD_PRESUPUESTADA',
                'ROLE_ESTRUCTURA_INTERFACES_MYPIMES',
                'ROLE_GEST_CATGEST',
                'ROLE_GEST_TYPEESTR',
                'ROLE_GEST_CATRESP',
                'ROLE_GEST_RESPON'
              )
            SQL);
    }
}
