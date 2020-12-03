<?php

namespace AppBundle\Twig;

use AppBundle\Enum\ProjectType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ProjectTypeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('projectTypeDescription', [$this, 'typeDescription']),
        ];
    }

    public function typeDescription(?ProjectType $value = null) {
        return $value ? $value->description() : null;
    }
}