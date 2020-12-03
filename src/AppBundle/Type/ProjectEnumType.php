<?php

namespace AppBundle\Type;

use AppBundle\Enum\ProjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ProjectEnumType extends Type
{
    public function getName() {
        return 'php_enum_project_type';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProjectType {
        if ($value == '') {
            return null;
        }
        if (!ProjectType::isValid($value)) {
            throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not valid for the enum "%s". Expected one of ["%s"]',
                $value,
                ProjectType::class,
                implode('", "', ProjectType::keys())
            ));
        }
        return new ProjectType($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return (string) $value;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'VARCHAR(10) COMMENT "php_enum_project_type"';
    }
}