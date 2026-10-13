<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SecureFileTypeExtensionTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;
    /** @var list<string> */
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = self::getContainer()->get(FormFactoryInterface::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    public function testRejectsExecutableContentForEveryFileFormField(): void
    {
        $form = $this->formFactory->create(FileType::class);
        $form->submit(new UploadedFile(
            $this->temporaryFile('<?php echo "unsafe";'),
            'documento.pdf',
            'application/pdf',
            null,
            true
        ));

        self::assertFalse($form->isValid());
        self::assertSame('El tipo de archivo no está permitido.', $form->getErrors(true)->current()->getMessage());
    }

    public function testAcceptsAPdfWithinTheLimit(): void
    {
        $form = $this->formFactory->create(FileType::class);
        $form->submit(new UploadedFile(
            $this->temporaryFile('%PDF-1.4 test'),
            'documento.pdf',
            'application/pdf',
            null,
            true
        ));

        self::assertTrue($form->isValid());
    }

    private function temporaryFile(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'sgu-form-file-');
        self::assertNotFalse($path);
        file_put_contents($path, $contents);
        $this->temporaryFiles[] = $path;

        return $path;
    }
}
