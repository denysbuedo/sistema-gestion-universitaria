<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Filesystem\Filesystem;

class PdfService
{
    public function generateFichaSalidaPdf(array $data, string $outputPath): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);

        $html = $this->renderHtml($data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filesystem = new Filesystem();
        $filesystem->mkdir(dirname($outputPath));

        file_put_contents($outputPath, $dompdf->output());

        return $outputPath;
    }

    private function renderHtml(array $data): string
    {
        $escape = function ($value) {
            return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $fSolicitud = $escape($data['fechaSolicitud'] ?? '');
        $persona = $escape($data['persona'] ?? '');
        $cargo = $escape($data['cargo'] ?? '');
        $centro = $escape($data['institucionCubana'] ?? '');
        $facultad = $escape($data['facultad'] ?? '');
        $pais = $escape($data['pais'] ?? '');
        $lugares = $escape($data['lugares'] ?? '');
        $personaInvita = $escape($data['personaInvita'] ?? '');
        $tiempoDuracion = $escape($data['tiempoDuracion'] ?? '');
        $desde = $escape($data['fechaSalida'] ?? '');
        $hasta = $escape($data['fechaRegreso'] ?? '');
        $fSalida = $escape($data['fechaSalida'] ?? '');
        $objetivo = $escape($data['objetivo'] ?? '');
        $mandatos = $escape($data['mandatosMision'] ?? '');

        $fuente = $data['fuenteFinanciamiento'] ?? [];
        $estanciaF = $escape($fuente['estancia'] ?? '');

        $gastosHtml = '';
        $totalUsd = 0;
        if (!empty($data['gastos']) && is_array($data['gastos'])) {
            foreach ($data['gastos'] as $g) {
                $nombre = $escape($g['nombre'] ?? '');
                $asumido = $g['asumidoPor'] ?? '';
                $monto = $g['monto'] ?? 0;

                $asInst = $asumido === 'institucion' ? ($monto > 0 ? number_format($monto, 2) : '&#10003;') : '';
                $asExtranj = $asumido === 'extranjera' ? '&#10003;' : '';

                if ($asumido === 'institucion') {
                    $totalUsd += (float) $monto;
                }

                $gastosHtml .= "<tr>
                    <td style='text-align:left; padding-left:6px;'>{$nombre}</td>
                    <td>{$asInst}</td>
                    <td>{$asExtranj}</td>
                </tr>";
            }
            $totalUsdFmt = number_format($totalUsd, 2);
            $gastosHtml .= "<tr>
                <td style='text-align:right; padding-right:10px; font-weight:bold;'>TOTAL (USD)</td>
                <td style='font-weight:bold;'>{$totalUsdFmt}</td>
                <td></td>
            </tr>";
        } else {
            $gastosHtml = '<tr><td colspan="3" style="height:18px;"></td></tr>';
        }

        $salidasHtml = '';
        if (!empty($data['salidasAnteriores']) && is_array($data['salidasAnteriores'])) {
            foreach ($data['salidasAnteriores'] as $s) {
                $salidasHtml .= "<tr>
                    <td>" . $escape($s['pais'] ?? '') . "</td>
                    <td>" . $escape($s['estancia'] ?? '') . "</td>
                    <td>" . $escape($s['desdeHasta'] ?? '') . "</td>
                    <td>" . nl2br($escape($s['resultados'] ?? '')) . "</td>
                </tr>";
            }
        } else {
            for ($i = 0; $i < 3; $i++) {
                $salidasHtml .= '<tr><td style="height:20px;"></td><td></td><td></td><td></td></tr>';
            }
        }

        return '
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            <style>
                @page { margin: 1cm 1.5cm; }
                body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; line-height: 1.6; color: #000; }
                .text-center { text-align: center; }
                .text-bold { font-weight: bold; }
                .header { margin-bottom: 25px; }
                .header h1 { font-size: 14px; margin: 0; }
                .header h2 { font-size: 12px; margin: 0; font-weight: normal; }
                .header h3 { font-size: 12px; margin: 10px 0; text-decoration: underline; font-weight: bold; }
                
                .form-table { width: 100%; border-collapse: collapse; border: none; }
                .form-table td { border: none; padding: 2px 0; vertical-align: top; }
                .label { font-weight: bold; white-space: nowrap; }
                .underline { border-bottom: 1px solid #000; display: inline-block; padding: 0 5px; min-height: 15px; }
                
                .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                .data-table th, .data-table td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 10px; }
                .data-table th { font-weight: bold; }
                
                .signatures-table { width: 100%; margin-top: 40px; border: none; }
                .signatures-table td { border: none; width: 50%; font-weight: bold; }
                
                .footer { margin-top: 40px; border-top: 4px solid #000; padding-top: 8px; font-size: 9px; line-height: 1.3; }
            </style>
        </head>
        <body>
            <div class="header text-center">
                <h1 class="text-bold">Ministerio de Educación Superior</h1>
                <h2>Dirección de Relaciones Internacionales</h2>
                <h3>Ficha de Propuesta de Misiones al Exterior para Reunión de la Dirección del Mes</h3>
            </div>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 140px;">Fecha de la solicitud:</td>
                    <td><div class="underline" style="width: 250px;">' . $fSolicitud . '</div></td>
                </tr>
            </table>

            <table class="form-table" style="margin-top: 10px;">
                <tr>
                    <td class="label" style="width: 130px;">Nombre y Apellidos<br/>(viajero):</td>
                    <td><div class="underline" style="width: 100%; margin-top: 8px;">' . $persona . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 50px;">Cargo:</td>
                    <td><div class="underline" style="width: 100%;">' . $cargo . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 55px;">Entidad:</td>
                    <td><div class="underline" style="width: 320px;">' . $centro . '</div></td>
                    <td class="label" style="width: 65px; text-align: center;">Unidad Organizativa:</td>
                    <td><div class="underline" style="width: 150px;">' . $facultad . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 135px;">País o países a visitar:</td>
                    <td><div class="underline" style="width: 100%;">' . $pais . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 300px;">Lugar de ubicación del viajero en el exterior (ciudad e <br/>institución):</td>
                    <td><div class="underline" style="width: 100%; margin-top: 8px;">' . $lugares . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 175px;">Persona que invita y cargo:</td>
                    <td><div class="underline" style="width: 100%;">. ' . $personaInvita . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 115px;">Tiempo de duración<br/>(en días):</td>
                    <td style="width: 50px;"><div class="underline" style="width: 50px; margin-top: 8px;">' . $tiempoDuracion . '</div></td>
                    <td class="label" style="width: 45px; text-align: right; padding-right: 5px; padding-top: 8px;">desde:</td>
                    <td style="width: 70px;"><div class="underline" style="width: 70px; margin-top: 8px;">' . $desde . '</div></td>
                    <td class="label" style="width: 45px; text-align: right; padding-right: 5px; padding-top: 8px;">hasta:</td>
                    <td style="width: 70px;"><div class="underline" style="width: 70px; margin-top: 8px;">' . $hasta . '</div></td>
                    <td class="label" style="width: 110px; text-align: right; padding-right: 5px; padding-top: 8px;">Fecha de salida:</td>
                    <td><div class="underline" style="width: 80px; margin-top: 8px;">' . $fSalida . '</div></td>
                </tr>
            </table>

            <table class="form-table" style="margin-top: 8px;">
                <tr>
                    <td class="label" style="width: 115px;">Objetivos de la misión:</td>
                    <td><div class="underline" style="width: 100%;">' . $objetivo . '</div></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <td class="label" style="width: 135px;">Mandatos de la Misión:</td>
                    <td><div class="underline" style="width: 100%;">' . $mandatos . '</div></td>
                </tr>
            </table>

            <div class="text-center text-bold" style="font-size: 11px; margin-top: 14px; margin-bottom: 4px;">FUENTE DE FINANCIAMIENTO</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align:left; padding-left:6px;">CONCEPTO DE GASTO</th>
                        <th style="width: 22%;">INSTITUCIÓN</th>
                        <th style="width: 22%;">PARTE EXTRANJERA</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $gastosHtml . '
                </tbody>
            </table>

            <div class="text-center text-bold" style="font-size: 12px; margin-top: 5px;">SALIDAS ANTERIORES EN EL AÑO:</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">PAIS</th>
                        <th style="width: 15%;">ESTANCIA</th>
                        <th style="width: 20%;">DESDE Y HASTA</th>
                        <th>RESULTADOS ALCANZADOS EN ELLAS</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $salidasHtml . '
                </tbody>
            </table>

            <table class="signatures-table">
                <tr>
                    <td>DIRECTIVO CORRESPONDIENTE: __________________</td>
                    <td style="text-align: right;">FIRMA DRI __________________</td>
                </tr>
            </table>

            <div class="footer">
                Calle 23 No. 565 entre F y G, Vedado, La Habana, Cuba. C.P. 10400<br/>
                Teléfono: (53 + 7) 8382363<br/>
                E-mail: dri@mes.gob.cu
            </div>
        </body>
        </html>';
    }
}
