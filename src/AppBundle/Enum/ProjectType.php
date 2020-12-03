<?php

namespace AppBundle\Enum;

use MyCLabs\Enum\Enum;

class ProjectType extends Enum
{
    const NATIONAL_SMALL = 'NS';
    const NATIONAL_BIG = 'NB';
    const PERSONAL_SMALL = 'PS';
    const PERSONAL_BIG = 'PB';

    const ENUM_NAME = [
        self::NATIONAL_SMALL => 'Державні / Малі',
        self::NATIONAL_BIG => 'Державні / Великі',
        self::PERSONAL_SMALL => 'Особисті / Малі',
        self::PERSONAL_BIG => 'Особисті / Великі',
    ];

    public function description() {
        return self::ENUM_NAME[$this->getValue()];
    }
}