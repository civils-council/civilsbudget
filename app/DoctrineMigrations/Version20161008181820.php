<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add title_h1 and address to entity vote_settings
 */
class Version20161008181820 extends AbstractMigration
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
        if (!$schemaTable->hasColumn('title_h1')) {
            $columns[] = 'ADD `title_h1` VARCHAR(255) DEFAULT NULL';
        }

        if (!$schemaTable->hasColumn('address')) {
            $columns[] = 'ADD `address` VARCHAR(255) DEFAULT NULL';
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
        if ($schemaTable->hasColumn('title_h1')) {
            $columns[] = 'DROP `title_h1`';
        }

        if ($schemaTable->hasColumn('address')) {
            $columns[] = 'DROP `address`';
        }

        if (count($columns)) {
            $query = sprintf('ALTER TABLE `vote_settings` %s', implode(', ', $columns));
            $this->addSql($query);
        }
    }
}
