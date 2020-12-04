<?php

namespace AppBundle\Enum;

use MyCLabs\Enum\Enum;

class ProjectType extends Enum
{
    const SMALL_BUDGETARY = 'SB';
    const LARGE_BUDGETARY = 'LB';
    const SMALL_MUNICIPAL = 'SM';
    const LARGE_MUNICIPAL = 'LM';

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