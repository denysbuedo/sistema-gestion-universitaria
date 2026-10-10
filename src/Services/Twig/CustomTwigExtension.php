<?php

namespace App\Services\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CustomTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [           
            
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasRoles', [SecurityTwigExtension::class, 'hasRoles']),
        ];
    }
}
