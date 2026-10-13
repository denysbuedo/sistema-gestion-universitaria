<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedDocumentStorage
{
    public const MAX_BYTES = 33_554_432;

    private const EXTENSIONS_BY_MIME = [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.oasis.opendocument.text' => 'odt',
    ];

    private string $publicDirectory;

    public function __construct(string $projectDir)
    {
        $this->publicDirectory = rtrim($projectDir, '/\\') . '/public';
    }

    /** @return array{path: string, originalName: string} */
    public function store(UploadedFile $file, string $relativeDirectory): array
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('La carga del archivo no es válida.');
        }

        if (($file->getSize() ?: 0) > self::MAX_BYTES) {
            throw new \InvalidArgumentException('El archivo excede el límite de 32 MB.');
        }

        $mime = $file->getMimeType();
        $extension = self::EXTENSIONS_BY_MIME[$mime] ?? null;
        if ($extension === null) {
            throw new \InvalidArgumentException('El tipo de archivo no está permitido.');
        }

        $relativeDirectory = trim(str_replace('\\', '/', $relativeDirectory), '/');
        if ($relativeDirectory === '' || str_contains($relativeDirectory, '..')) {
            throw new \InvalidArgumentException('El destino del archivo no es válido.');
        }

        $targetDirectory = $this->publicDirectory . '/' . $relativeDirectory;
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0770, true) && !is_dir($targetDirectory)) {
            throw new \RuntimeException('No se pudo preparar el directorio de carga.');
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $file->move($targetDirectory, $storedName);

        return [
            'path' => $relativeDirectory . '/' . $storedName,
            'originalName' => basename($file->getClientOriginalName()),
        ];
    }
}
