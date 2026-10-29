<?php

namespace App\Services;

use App\Entity\Postgrado\SolicitudPrograma;
use App\Entity\Postgrado\SolicitudProgramaDocumento;
use App\Entity\Postgrado\SolicitudProgramaHistorial;
use App\Repository\Postgrado\SolicitudProgramaDocumentoRepository;
use App\Repository\Postgrado\SolicitudProgramaHistorialRepository;

class PostgradoSolicitudWorkflow
{
    public function __construct(
        private readonly SolicitudProgramaDocumentoRepository $documentos,
        private readonly SolicitudProgramaHistorialRepository $historial,
    ) {
    }

    public function registrarDocumento(SolicitudPrograma $solicitud, string $tipo, ?string $ruta, ?string $usuario = null): void
    {
        if (!$ruta) {
            return;
        }

        $documento = (new SolicitudProgramaDocumento())
            ->setSolicitudPrograma($solicitud)
            ->setTipo($tipo)
            ->setRuta($ruta)
            ->setNombreOriginal(basename($ruta))
            ->setSubidoPor($usuario)
            ->setVigente(true);
        $this->documentos->reemplazar($documento);
    }

    public function registrarEvento(
        SolicitudPrograma $solicitud,
        string $accion,
        ?string $estadoAnterior,
        ?string $estadoNuevo,
        ?string $observacion,
        ?string $usuario,
    ): void {
        $evento = (new SolicitudProgramaHistorial())
            ->setSolicitudPrograma($solicitud)
            ->setAccion($accion)
            ->setEstadoAnterior($estadoAnterior)
            ->setEstadoNuevo($estadoNuevo)
            ->setObservacion($observacion)
            ->setRealizadoPor($usuario);
        $this->historial->add($evento);
    }
}
