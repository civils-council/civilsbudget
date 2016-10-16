<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add logo and background_img to vote_settings
 */
class Version20161014144100 extends AbstractMigration
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

        $columns = [];
        if (!$schemaTable->hasColumn('logo')) {
            $columns[] = 'ADD `logo` VARCHAR(255) DEFAULT NULL';
        }

        if (!$schemaTable->hasColumn('background_img')) {
            $columns[] = 'ADD `background_img` VARCHAR(255) DEFAULT NULL';
        }

        if (count($columns)) {
            $query = sprintf('ALTER TABLE `vote_settings` %s', implode(', ', $columns));
            $this->addSql($query);
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

        $columns = [];
        if ($schemaTable->hasColumn('logo')) {
            $columns[] = 'DROP `logo`';
        }

        if ($schemaTable->hasColumn('background_img')) {
            $columns[] = 'DROP `background_img`';
        }

        if (count($columns)) {
            $query = sprintf('ALTER TABLE `vote_settings` %s', implode(', ', $columns));
            $this->addSql($query);
        }
    }
}
