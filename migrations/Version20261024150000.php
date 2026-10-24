<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261024150000 extends AbstractMigration
{
    public function getDescription(): string { return 'Add the formal document to university program incorporation requests'; }
    public function up(Schema $schema): void { $this->addSql('ALTER TABLE pregrado.tbd_solicitud_incorporacion_programa ADD documento_solicitud VARCHAR(255) DEFAULT NULL'); }
    public function down(Schema $schema): void { $this->addSql('ALTER TABLE pregrado.tbd_solicitud_incorporacion_programa DROP documento_solicitud'); }
}
