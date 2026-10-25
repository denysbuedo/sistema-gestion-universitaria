<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261025170000 extends AbstractMigration
{
    public function getDescription(): string { return 'Create approved staffing master and grant HR administrator permission'; }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE rrhh.tbd_plantilla_aprobada (entidad_id INT NOT NULL, total INT NOT NULL DEFAULT 0, actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(entidad_id))');
        $this->addSql('ALTER TABLE rrhh.tbd_plantilla_aprobada ADD CONSTRAINT FK_PLANTILLA_ENTIDAD FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id) ON DELETE CASCADE');
        $this->addSql("INSERT INTO rrhh.tbd_plantilla_aprobada (entidad_id,total,actualizado) SELECT DISTINCT ON (entidad_id) entidad_id, COALESCE(total_plantilla_aprobada,0), CURRENT_TIMESTAMP FROM rrhh.tbd_ae2 ORDER BY entidad_id, anno DESC, mes DESC, id DESC ON CONFLICT DO NOTHING");
        $this->addSql("INSERT INTO seguridad.tbd_funcionalidad (id,modulo_id,nombre,descripcion,role_key,activo,creado,actualizado) SELECT nextval('seguridad.tbd_funcionalidad_id_seq'),m.id,'Gestionar plantilla aprobada','Gestionar plantilla aprobada por entidad','ROLE_RRHH_GEST_PLANTILLA',true,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP FROM seguridad.tbd_modulo m WHERE m.module_key='MODULE_RRHH' ON CONFLICT (role_key) DO NOTHING");
        $this->addSql("INSERT INTO seguridad.tbr_rol_funcionalidad (rol_id,funcionalidad_id) SELECT r.id,f.id FROM seguridad.tbd_rol r CROSS JOIN seguridad.tbd_funcionalidad f WHERE r.role_key='ROL_RRHH' AND f.role_key='ROLE_RRHH_GEST_PLANTILLA' ON CONFLICT DO NOTHING");
    }
    public function down(Schema $schema): void { $this->addSql("DELETE FROM seguridad.tbd_funcionalidad WHERE role_key='ROLE_RRHH_GEST_PLANTILLA'"); $this->addSql('DROP TABLE rrhh.tbd_plantilla_aprobada'); }
}
