<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add new fields to vote setting
 */
class Version20161008113945 extends AbstractMigration
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
        if (!$schemaTable->hasColumn('date_from')) {
            $columns[] = 'ADD `date_from` DATETIME NOT NULL';
        }

        if (!$schemaTable->hasColumn('date_to')) {
            $columns[] = 'ADD `date_to` DATETIME NOT NULL';
        }

        if (!$schemaTable->hasColumn('description')) {
            $columns[] = 'ADD `description` LONGTEXT DEFAULT NULL';
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
        if ($schemaTable->hasColumn('date_from')) {
            $columns[] = 'DROP `date_from`';
        }

        if ($schemaTable->hasColumn('date_to')) {
            $columns[] = 'DROP `date_to`';
        }

        if ($schemaTable->hasColumn('description')) {
            $columns[] = 'DROP `description`';
        }

        if (count($columns)) {
            $query = sprintf('ALTER TABLE `vote_settings` %s', implode(', ', $columns));
            $this->addSql($query);
        }
    }
}
