<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261025193000 extends AbstractMigration
{
    public function getDescription(): string { return 'Crea expedientes e historial de categorías docentes'; }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE rrhh.tbd_proceso_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_proceso_categoria_docente (id INT NOT NULL DEFAULT nextval('rrhh.tbd_proceso_categoria_docente_id_seq'), persona_id INT NOT NULL, categoria_docente_id INT NOT NULL, entidad_id INT NOT NULL, numero_expediente VARCHAR(30) DEFAULT NULL, fecha_solicitud DATE NOT NULL, estado VARCHAR(30) NOT NULL, nivel_actual VARCHAR(20) NOT NULL, requiere_aprobacion_mes BOOLEAN NOT NULL DEFAULT FALSE, fecha_aprobacion_mes DATE DEFAULT NULL, observaciones TEXT DEFAULT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql('CREATE UNIQUE INDEX uniq_proceso_categoria_numero ON rrhh.tbd_proceso_categoria_docente (numero_expediente)');
        $this->addSql('CREATE INDEX idx_proceso_categoria_entidad_estado ON rrhh.tbd_proceso_categoria_docente (entidad_id, estado)');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD CONSTRAINT fk_proceso_categoria_persona FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD CONSTRAINT fk_proceso_categoria_categoria FOREIGN KEY (categoria_docente_id) REFERENCES personal.tbn_categoria_docente (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_proceso_categoria_docente ADD CONSTRAINT fk_proceso_categoria_entidad FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id)');

        $this->addSql('CREATE SEQUENCE rrhh.tbd_historial_categoria_docente_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql("CREATE TABLE rrhh.tbd_historial_categoria_docente (id INT NOT NULL DEFAULT nextval('rrhh.tbd_historial_categoria_docente_id_seq'), proceso_id INT DEFAULT NULL, persona_id INT NOT NULL, categoria_docente_id INT NOT NULL, entidad_id INT NOT NULL, fecha_otorgamiento DATE NOT NULL, fecha_fin DATE DEFAULT NULL, numero_resolucion VARCHAR(100) NOT NULL, autoridad VARCHAR(100) NOT NULL, activo BOOLEAN NOT NULL DEFAULT TRUE, documento VARCHAR(255) DEFAULT NULL, observaciones TEXT DEFAULT NULL, creado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, actualizado TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql('CREATE INDEX idx_historial_categoria_persona ON rrhh.tbd_historial_categoria_docente (persona_id, activo)');
        $this->addSql('ALTER TABLE rrhh.tbd_historial_categoria_docente ADD CONSTRAINT fk_historial_categoria_proceso FOREIGN KEY (proceso_id) REFERENCES rrhh.tbd_proceso_categoria_docente (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_historial_categoria_docente ADD CONSTRAINT fk_historial_categoria_persona FOREIGN KEY (persona_id) REFERENCES personal.tbd_persona (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_historial_categoria_docente ADD CONSTRAINT fk_historial_categoria_categoria FOREIGN KEY (categoria_docente_id) REFERENCES personal.tbn_categoria_docente (id)');
        $this->addSql('ALTER TABLE rrhh.tbd_historial_categoria_docente ADD CONSTRAINT fk_historial_categoria_entidad FOREIGN KEY (entidad_id) REFERENCES estructura.tbd_estructura (id)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE rrhh.tbd_historial_categoria_docente');
        $this->addSql('DROP SEQUENCE rrhh.tbd_historial_categoria_docente_id_seq');
        $this->addSql('DROP TABLE rrhh.tbd_proceso_categoria_docente');
        $this->addSql('DROP SEQUENCE rrhh.tbd_proceso_categoria_docente_id_seq');
    }
}
