<?php

namespace App\Services\RRHH;

use App\Entity\RRHH\AE2;
use App\Entity\RRHH\AE3;

final class PartesEstadisticosValidator
{
    private const AE3_INDICADORES = [
        'total_cuadros', 'cuadros_docentes', 'cuadros_administrativos',
        'cuadros_investigacion', 'otros_cuadros', 'total_tecnicos',
        'profesores_tiempo_completo', 'asesores_metodologos', 'investigadores',
        'otros_tecnicos', 'administrativos', 'servicio', 'operarios', 'total',
        'profesores_tipo_parcial',
    ];

    private const AE3_VARIABLES = [
        'total_cubierta', 'de_ellos_fem', 'jovenes_total', 'jovenes_fem',
        'pt', 'pa', 'as', 'i', 'it', 'ia', 'iag', 'ai', 'aux_tec_doc', 'msc', 'dr',
    ];

    public function validarAE2(AE2 $ae2): array
    {
        $errores = [];
        foreach ((new \ReflectionObject($ae2))->getProperties() as $propiedad) {
            $propiedad->setAccessible(true);
            if (!$propiedad->isInitialized($ae2)) {
                continue;
            }
            $valor = $propiedad->getValue($ae2);
            if (is_numeric($valor) && (int) $valor < 0) {
                $errores[] = 'Los valores del AE2 no pueden ser negativos.';
                break;
            }
        }
        $aprobada = $this->numero($ae2->getTotalPlantillaAprobada());
        $cubierta = $this->numero($ae2->getTotalPlantillaCubierta());

        if ($cubierta > $aprobada) {
            $errores[] = 'La plantilla cubierta no puede superar la plantilla aprobada.';
        }

        $conRespaldo = $this->numero($ae2->getContratosPorSustitucion())
            + $this->numero($ae2->getPeriodoPrueba())
            + $this->numero($ae2->getSerenosAuxiliaresLimpieza())
            + $this->numero($ae2->getLaboresAgricolas())
            + $this->numero($ae2->getJubilados())
            + $this->numero($ae2->getOtrosConRespaldo());
        if ($this->numero($ae2->getContratosNoDocentesConRespaldo()) !== $conRespaldo) {
            $errores[] = 'El total de contratos no docentes con respaldo no coincide con la suma de su desglose.';
        }

        $sinRespaldo = $this->numero($ae2->getSerenosAuxiliaresLimpiezaSinRespaldo())
            + $this->numero($ae2->getLaboresAgricolasSinRespaldo())
            + $this->numero($ae2->getJubiladosSinRespaldo())
            + $this->numero($ae2->getEjecucionObra())
            + $this->numero($ae2->getOtrosSinRespaldo());
        if ($this->numero($ae2->getContratosNoDocentesSinRespaldo()) !== $sinRespaldo) {
            $errores[] = 'El total de contratos no docentes sin respaldo no coincide con la suma de su desglose.';
        }

        $totalNoDocentes = $this->numero($ae2->getContratosNoDocentesConRespaldo())
            + $this->numero($ae2->getContratosNoDocentesSinRespaldo());
        if ($this->numero($ae2->getTotalContratosNoDocentes()) !== $totalNoDocentes) {
            $errores[] = 'El total de contratos no docentes debe ser la suma de los contratos con y sin respaldo.';
        }

        $totalContratos = $this->numero($ae2->getTotalContratosProfesoresTiempoDeterminado())
            + $this->numero($ae2->getTotalContratosNoDocentes());
        if ($this->numero($ae2->getTotalGeneralContratos()) !== $totalContratos) {
            $errores[] = 'El total general de contratos debe coincidir con profesores a tiempo determinado más contratos no docentes.';
        }

        $estudiantes = $this->numero($ae2->getTotalEstudiantesUniversidadContratados());
        if ($this->numero($ae2->getEstudiantesAuxiliaresTecnicosDocencia()) > $estudiantes
            || $this->numero($ae2->getEstudiantesCargosNoDocentes()) > $estudiantes) {
            $errores[] = 'Los desgloses de estudiantes contratados no pueden superar el total de estudiantes.';
        }

        return $errores;
    }

    public function validarAE3(AE3 $ae3, ?AE2 $ae2): array
    {
        if (!$ae2) {
            return ['Debe registrar primero el AE2 de la misma entidad, mes y año.'];
        }

        $errores = [];
        foreach (self::AE3_INDICADORES as $indicador) {
            foreach (self::AE3_VARIABLES as $variable) {
                if ($this->valorAE3($ae3, "{$indicador}_{$variable}") < 0) {
                    $errores[] = 'Los valores del AE3 no pueden ser negativos.';
                    break 2;
                }
            }
        }
        if ($this->valorAE3($ae3, 'total_total_cubierta') !== $this->numero($ae2->getTotalPlantillaCubierta())) {
            $errores[] = 'El total cubierto del AE3 debe coincidir con la plantilla cubierta del AE2 correspondiente.';
        }
        if ($this->valorAE3($ae3, 'profesores_tiempo_completo_total_cubierta') !== $this->numero($ae2->getProfesoresTiempoCompleto())) {
            $errores[] = 'Los profesores a tiempo completo del AE3 deben coincidir con el AE2 correspondiente.';
        }

        foreach (self::AE3_VARIABLES as $variable) {
            $cuadros = $this->valorAE3($ae3, "cuadros_docentes_{$variable}")
                + $this->valorAE3($ae3, "cuadros_administrativos_{$variable}")
                + $this->valorAE3($ae3, "cuadros_investigacion_{$variable}")
                + $this->valorAE3($ae3, "otros_cuadros_{$variable}");
            if ($this->valorAE3($ae3, "total_cuadros_{$variable}") !== $cuadros) {
                $errores[] = "El total de cuadros no coincide con su desglose en {$this->etiqueta($variable)}.";
            }

            $tecnicos = $this->valorAE3($ae3, "profesores_tiempo_completo_{$variable}")
                + $this->valorAE3($ae3, "asesores_metodologos_{$variable}")
                + $this->valorAE3($ae3, "investigadores_{$variable}")
                + $this->valorAE3($ae3, "otros_tecnicos_{$variable}");
            if ($this->valorAE3($ae3, "total_tecnicos_{$variable}") !== $tecnicos) {
                $errores[] = "El total de técnicos no coincide con su desglose en {$this->etiqueta($variable)}.";
            }

            $general = $this->valorAE3($ae3, "total_cuadros_{$variable}")
                + $this->valorAE3($ae3, "total_tecnicos_{$variable}")
                + $this->valorAE3($ae3, "administrativos_{$variable}")
                + $this->valorAE3($ae3, "servicio_{$variable}")
                + $this->valorAE3($ae3, "operarios_{$variable}");
            if ($this->valorAE3($ae3, "total_{$variable}") !== $general) {
                $errores[] = "El total general no coincide con su desglose en {$this->etiqueta($variable)}.";
            }
        }

        foreach (self::AE3_INDICADORES as $indicador) {
            $total = $this->valorAE3($ae3, "{$indicador}_total_cubierta");
            $mujeres = $this->valorAE3($ae3, "{$indicador}_de_ellos_fem");
            $jovenes = $this->valorAE3($ae3, "{$indicador}_jovenes_total");
            $jovenesMujeres = $this->valorAE3($ae3, "{$indicador}_jovenes_fem");
            if ($mujeres > $total || $jovenes > $total || $jovenesMujeres > $mujeres || $jovenesMujeres > $jovenes) {
                $errores[] = "Los subconjuntos de {$this->etiqueta($indicador)} superan sus totales.";
            }
            if ($this->valorAE3($ae3, "{$indicador}_msc") > $total || $this->valorAE3($ae3, "{$indicador}_dr") > $total) {
                $errores[] = "Los másteres o doctores de {$this->etiqueta($indicador)} superan el total cubierto.";
            }
        }

        return array_values(array_unique($errores));
    }

    private function valorAE3(AE3 $ae3, string $campo): int
    {
        $metodo = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $campo)));
        return method_exists($ae3, $metodo) ? $this->numero($ae3->{$metodo}()) : 0;
    }

    private function numero(mixed $valor): int
    {
        return $valor === null || $valor === '' ? 0 : (int) $valor;
    }

    private function etiqueta(string $valor): string
    {
        return str_replace('_', ' ', $valor);
    }
}
