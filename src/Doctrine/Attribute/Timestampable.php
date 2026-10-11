<?php

declare(strict_types=1);

namespace App\Doctrine\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Timestampable
{
    public function __construct(public readonly string $on)
    {
        if (!in_array($on, ['create', 'update'], true)) {
            throw new \InvalidArgumentException('Timestampable only supports "create" or "update".');
        }
    }
}
