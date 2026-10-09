<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Estructura\Estructura;
use App\Entity\Institucion\CategoriaAcreditacion;
use App\Entity\Institucion\InstitucionMecanismoColaboracion;
use App\Entity\Institucion\InstitucionMembresia;
use App\Entity\Institucion\InstitucionProgramaColaboracion;
use App\Entity\Institucion\InstitucionProyecto;
use App\Entity\Institucion\InstitucionRecursoHumano;
use App\Entity\RRHH\Grupo;
use PHPUnit\Framework\TestCase;

class MappingConsistencyTest extends TestCase
{
    public function testIntegerFieldsKeepIntegerValues(): void
    {
        $categoria = new CategoriaAcreditacion();
        $categoria->setDuracion(24);
        self::assertSame(24, $categoria->getDuracion());

        $mecanismo = new InstitucionMecanismoColaboracion();
        $mecanismo->setIdMecanismo(10);
        $mecanismo->setIdTipoMecanismo(11);
        self::assertSame(10, $mecanismo->getIdMecanismo());
        self::assertSame(11, $mecanismo->getIdTipoMecanismo());

        $membresia = new InstitucionMembresia();
        $membresia->setIdMembresia(20);
        $membresia->setIdTipoMembresia(21);
        self::assertSame(20, $membresia->getIdMembresia());
        self::assertSame(21, $membresia->getIdTipoMembresia());

        $programa = new InstitucionProgramaColaboracion();
        $programa->setIdPrograma(30);
        $programa->setIdPatrocinador(31);
        self::assertSame(30, $programa->getIdPrograma());
        self::assertSame(31, $programa->getIdPatrocinador());

        $proyecto = new InstitucionProyecto();
        $proyecto->setIdProyecto(40);
        self::assertSame(40, $proyecto->getIdProyecto());

        $recurso = new InstitucionRecursoHumano();
        $recurso->setCantidad(50);
        self::assertSame(50, $recurso->getCantidad());
    }

    public function testGrupoSynchronizesTheOwningSideWhenAddingAndRemovingEstructura(): void
    {
        $grupo = new Grupo();
        $estructura = new Estructura();

        $grupo->addEstructura($estructura);

        self::assertSame($grupo, $estructura->getGrupo());
        self::assertTrue($grupo->getEstructuras()->contains($estructura));

        $grupo->removeEstructura($estructura);

        self::assertNull($estructura->getGrupo());
        self::assertFalse($grupo->getEstructuras()->contains($estructura));
    }
}
