<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Doctrine\EventListener\TimestampableListener;
use App\Entity\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\TestCase;

final class TimestampableListenerTest extends TestCase
{
    public function testPrePersistInitializesInheritedCreateAndUpdateTimestamps(): void
    {
        $entity = new class extends BaseEntity {
        };
        $objectManager = $this->createMock(EntityManagerInterface::class);

        (new TimestampableListener())->prePersist(new PrePersistEventArgs($entity, $objectManager));

        self::assertInstanceOf(\DateTime::class, $entity->getCreado());
        self::assertInstanceOf(\DateTime::class, $entity->getActualizado());
        self::assertNotSame($entity->getCreado(), $entity->getActualizado());
    }
}
