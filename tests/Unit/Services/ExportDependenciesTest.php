<?php

namespace App\Tests\Unit\Services;

use App\Services\ExportListHeader;
use App\Services\ExportListMultiple;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class ExportDependenciesTest extends TestCase
{
    public function testLegacyExportBaseClassesAreAutoloadable(): void
    {
        self::assertTrue(class_exists(ExportListHeader::class));
        self::assertTrue(class_exists(ExportListMultiple::class));
    }

    public function testPhpSpreadsheetGeneratesAValidXlsxFile(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->fromArray([
                ['Programa', 'Estado'],
                ['Maestría de prueba', 'Aprobado'],
            ]);

        $path = tempnam(sys_get_temp_dir(), 'sgu-xlsx-');
        self::assertNotFalse($path);

        try {
            (new Xlsx($spreadsheet))->save($path);

            self::assertGreaterThan(0, filesize($path));
            self::assertSame('PK', file_get_contents($path, false, null, 0, 2));
        } finally {
            $spreadsheet->disconnectWorksheets();
            if (is_string($path) && is_file($path)) {
                unlink($path);
            }
        }
    }

    public function testDompdfGeneratesAValidPdfDocument(): void
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml('<h1>Reporte de prueba</h1><p>Akademos</p>');
        $dompdf->render();

        $pdf = $dompdf->output();

        self::assertStringStartsWith('%PDF-', $pdf);
        self::assertGreaterThan(100, strlen($pdf));
    }
}
