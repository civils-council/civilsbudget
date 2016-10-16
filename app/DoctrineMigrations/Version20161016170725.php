<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * change type address in instance vote_settings
 */
class Version20161016170725 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('vote_settings'),
            'Table vote_settings doesn\'t exist'
        );

        $schemaTable = $schema->getTable('vote_settings');

        if ($schemaTable->hasColumn('address')) {
            $this->addSql('
                ALTER TABLE `vote_settings` 
                CHANGE `address` `address` LONGTEXT DEFAULT NULL
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('vote_settings'),
            'Table vote_settings doesn\'t exist'
        );

        $schemaTable = $schema->getTable('vote_settings');

        if ($schemaTable->hasColumn('address')) {
            $this->addSql('
                ALTER TABLE `vote_settings` 
                CHANGE `address` `address` 
                VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci
            ');
        }
    }
}
