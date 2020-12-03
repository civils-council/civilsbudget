<?php

namespace AppBundle\Enum;

use MyCLabs\Enum\Enum;

class ProjectType extends Enum
{
    const SMALL_BUDGETARY = 'NS';
    const LARGE_BUDGETARY = 'NB';
    const SMALL_MUNICIPAL = 'PS';
    const LARGE_MUNICIPAL = 'PB';

    const ENUM_NAME = [
        self::SMALL_BUDGETARY => 'Малий Бюджетний',
        self::LARGE_BUDGETARY => 'Великий Бюджетний',
        self::SMALL_MUNICIPAL => 'Малий Громадський',
        self::LARGE_MUNICIPAL => 'Великий Громадський',
    ];

    public function description() {
        return self::ENUM_NAME[$this->getValue()];
    }
}