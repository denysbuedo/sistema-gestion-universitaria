<?php

namespace App\Tests\Unit\Services\RRHH;

use App\Entity\RRHH\AE2;
use App\Entity\RRHH\AE3;
use App\Services\RRHH\PartesEstadisticosValidator;
use PHPUnit\Framework\TestCase;

final class PartesEstadisticosValidatorTest extends TestCase
{
    public function testAE3RequiresCorrespondingAE2(): void
    {
        $errores = (new PartesEstadisticosValidator())->validarAE3(new AE3(), null);

        self::assertSame(['Debe registrar primero el AE2 de la misma entidad, mes y año.'], $errores);
    }

    public function testEmptyAE3MatchesEmptyAE2(): void
    {
        $ae2 = new AE2();
        $ae2->setTotalPlantillaCubierta(0);
        $ae2->setProfesoresTiempoCompleto(0);

        self::assertSame([], (new PartesEstadisticosValidator())->validarAE3(new AE3(), $ae2));
    }

    public function testEmptyConsistentAE2IsAccepted(): void
    {
        $ae2 = new AE2();
        $ae2->setTotalPlantillaAprobada(0);
        $ae2->setTotalPlantillaCubierta(0);

        self::assertSame([], (new PartesEstadisticosValidator())->validarAE2($ae2));
    }

    public function testAE2RejectsCoveredStaffAboveApprovedStaff(): void
    {
        $ae2 = new AE2();
        $ae2->setTotalPlantillaAprobada(10);
        $ae2->setTotalPlantillaCubierta(11);

        self::assertContains(
            'La plantilla cubierta no puede superar la plantilla aprobada.',
            (new PartesEstadisticosValidator())->validarAE2($ae2)
        );
    }
}
