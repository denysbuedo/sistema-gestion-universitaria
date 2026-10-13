<?php

namespace App\Form\Extension;

use App\Services\UploadedDocumentStorage;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

final class SecureFileTypeExtension extends AbstractTypeExtension
{
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.text',
        'image/jpeg',
        'image/png',
    ];

    public static function getExtendedTypes(): iterable
    {
        return [FileType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setNormalizer('constraints', static function (Options $options, $constraints): array {
            $constraints = $constraints instanceof \Traversable
                ? iterator_to_array($constraints)
                : (array) $constraints;

            $constraints[] = new File([
                'maxSize' => UploadedDocumentStorage::MAX_BYTES,
                'mimeTypes' => self::ALLOWED_MIME_TYPES,
                'maxSizeMessage' => 'El archivo no puede superar 32 MB.',
                'mimeTypesMessage' => 'El tipo de archivo no está permitido.',
            ]);

            return $constraints;
        });
    }
}
