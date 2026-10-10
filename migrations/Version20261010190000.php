<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261010190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the stable Keycloak issuer + subject identity to SGU users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seguridad.tbd_usuario ADD oidc_issuer VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE seguridad.tbd_usuario ADD oidc_subject VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_usuario_oidc_identity ON seguridad.tbd_usuario (oidc_issuer, oidc_subject)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX seguridad.uniq_usuario_oidc_identity');
        $this->addSql('ALTER TABLE seguridad.tbd_usuario DROP oidc_issuer');
        $this->addSql('ALTER TABLE seguridad.tbd_usuario DROP oidc_subject');
    }
}
