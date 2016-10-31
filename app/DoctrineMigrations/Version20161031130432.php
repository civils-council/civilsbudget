<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * change type to string
 */
class Version20161031130432 extends AbstractMigration
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
            $this->addSql('ALTER TABLE `user` CHANGE `number_blank` `number_blank` VARCHAR(255) DEFAULT NULL');
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
            $this->addSql('ALTER TABLE `user` CHANGE `number_blank` `number_blank` INT DEFAULT NULL');
        }
    }
}
