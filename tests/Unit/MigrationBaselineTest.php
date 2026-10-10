<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MigrationBaselineTest extends TestCase
{
    private string $migration;

    protected function setUp(): void
    {
        $files = glob(dirname(__DIR__, 2) . '/migrations/Version*.php');

        self::assertCount(1, $files, 'La línea base debe ser la única migración inicial.');
        $this->migration = file_get_contents($files[0]);
    }

    public function testBaselineContainsTheValidatedSchema(): void
    {
        preg_match_all("/addSql\('CREATE TABLE /", $this->migration, $createdTables);
        preg_match_all("/addSql\('DROP TABLE /", $this->migration, $droppedTables);

        self::assertCount(164, $createdTables[0]);
        self::assertCount(164, $droppedTables[0]);

        foreach ([
            'configuracion', 'convenio', 'estructura', 'evaluacion',
            'informatizacion', 'institucion', 'personal', 'postgrado',
            'pregrado', 'rrhh', 'seguridad', 'tramite', 'traza',
        ] as $schema) {
            self::assertStringContainsString("CREATE SCHEMA {$schema}", $this->migration);
            self::assertStringContainsString("DROP SCHEMA {$schema}", $this->migration);
        }
    }

    public function testBaselineIsRestrictedToPostgresql(): void
    {
        self::assertStringContainsString("getName() !== 'postgresql'", $this->migration);
        self::assertStringContainsString('can only be executed safely on PostgreSQL', $this->migration);
    }

    public function testMigrationsRunAtomically(): void
    {
        $configuration = file_get_contents(dirname(__DIR__, 2) . '/config/packages/doctrine_migrations.yaml');

        self::assertStringContainsString('all_or_nothing: true', $configuration);
    }
}
