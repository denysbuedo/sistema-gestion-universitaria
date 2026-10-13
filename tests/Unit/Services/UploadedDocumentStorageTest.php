<?php

namespace App\Tests\Unit\Services;

use App\Services\UploadedDocumentStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedDocumentStorageTest extends TestCase
{
    private string $projectDirectory;

    protected function setUp(): void
    {
        $this->projectDirectory = sys_get_temp_dir() . '/sgu-upload-' . bin2hex(random_bytes(6));
        mkdir($this->projectDirectory . '/public', 0770, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->projectDirectory);
    }

    public function testStoresAnAllowedDocumentWithAnUnpredictableName(): void
    {
        $source = $this->temporaryFile('%PDF-1.4 test');
        $file = new UploadedFile($source, '../../informe.pdf', 'application/pdf', null, true);

        $stored = (new UploadedDocumentStorage($this->projectDirectory))
            ->store($file, 'uploads/documents');

        self::assertSame('informe.pdf', $stored['originalName']);
        self::assertMatchesRegularExpression('#^uploads/documents/[a-f0-9]{32}\.pdf$#', $stored['path']);
        self::assertFileExists($this->projectDirectory . '/public/' . $stored['path']);
    }

    public function testRejectsAnExecutablePayload(): void
    {
        $source = $this->temporaryFile('<?php echo "unsafe";');
        $file = new UploadedFile($source, 'informe.pdf', 'application/pdf', null, true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('tipo de archivo');

        (new UploadedDocumentStorage($this->projectDirectory))->store($file, 'uploads/documents');
    }

    public function testRejectsAnUnsafeDestination(): void
    {
        $source = $this->temporaryFile('%PDF-1.4 test');
        $file = new UploadedFile($source, 'informe.pdf', 'application/pdf', null, true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('destino');

        (new UploadedDocumentStorage($this->projectDirectory))->store($file, '../outside');
    }

    public function testRejectsAFileLargerThanTheConfiguredLimit(): void
    {
        $source = $this->temporaryFile('');
        $handle = fopen($source, 'c');
        self::assertIsResource($handle);
        ftruncate($handle, UploadedDocumentStorage::MAX_BYTES + 1);
        fclose($handle);
        $file = new UploadedFile($source, 'informe.pdf', 'application/pdf', null, true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('32 MB');

        (new UploadedDocumentStorage($this->projectDirectory))->store($file, 'uploads/documents');
    }

    private function temporaryFile(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'sgu-file-');
        self::assertNotFalse($path);
        file_put_contents($path, $contents);

        return $path;
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (array_diff(scandir($directory) ?: [], ['.', '..']) as $entry) {
            $path = $directory . '/' . $entry;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($directory);
    }
}
