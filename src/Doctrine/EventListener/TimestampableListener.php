<?php

declare(strict_types=1);

namespace App\Doctrine\EventListener;

use App\Doctrine\Attribute\Timestampable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final class TimestampableListener
{
    public function prePersist(PrePersistEventArgs $event): void
    {
        $this->updateTimestamps($event->getObject(), true);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();
        $this->updateTimestamps($entity, false);

        $entityManager = $event->getObjectManager();
        $metadata = $entityManager->getClassMetadata($entity::class);
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet($metadata, $entity);
    }

    private function updateTimestamps(object $entity, bool $isCreate): void
    {
        $now = new \DateTime();
        $class = new \ReflectionClass($entity);

        do {
            foreach ($class->getProperties() as $property) {
                if ($property->getDeclaringClass()->getName() !== $class->getName()) {
                    continue;
                }

                $attributes = $property->getAttributes(Timestampable::class);
                if ($attributes === []) {
                    continue;
                }

                $timestampable = $attributes[0]->newInstance();
                if ($timestampable->on === 'update' || ($timestampable->on === 'create' && $isCreate)) {
                    $property->setValue($entity, clone $now);
                }
            }
        } while ($class = $class->getParentClass());
    }
}
