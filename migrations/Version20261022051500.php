<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261022051500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add indexes for dashboard trends and notification polling';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_sgu_traza_creado ON traza.tbd_traza (creado)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_sgu_ficha_salida_creado ON tramite.tbd_ficha_salida (creado)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_sgu_notificacion_usuario_reciente ON public.notificaciones_usuario (usuario_recive_id, id DESC)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS traza.idx_sgu_traza_creado');
        $this->addSql('DROP INDEX IF EXISTS tramite.idx_sgu_ficha_salida_creado');
        $this->addSql('DROP INDEX IF EXISTS public.idx_sgu_notificacion_usuario_reciente');
    }
}
