<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * create number blank
 */
class Version20161027180728 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('user'),
            'Table user doesn\'t exist'
        );

        $schemaTable = $schema->getTable('user');

        if (!$schemaTable->hasColumn('number_blank')) {
            $this->addSql('
                ALTER TABLE `user` 
                ADD `number_blank` INT DEFAULT NULL
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('user'),
            'Table user doesn\'t exist'
        );

        $schemaTable = $schema->getTable('user');

        if ($schemaTable->hasColumn('number_blank')) {
            $this->addSql('ALTER TABLE `user` DROP `number_blank`');
        }
    }
}
