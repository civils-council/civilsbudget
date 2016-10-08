<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * change vote_setting_id nullable false
 */
class Version20161008113944 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('project'),
            'Table project doesn\'t exist'
        );

        $schemaTable = $schema->getTable('project');

        if ($schemaTable->hasColumn('vote_setting_id')) {
            $this->addSql('
                ALTER TABLE `project` CHANGE `vote_setting_id` `vote_setting_id` INT NOT NULL
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('project'),
            'Table project doesn\'t exist'
        );

        $schemaTable = $schema->getTable('project');

        if ($schemaTable->hasColumn('vote_setting_id')) {
            $this->addSql('ALTER TABLE `project` CHANGE `vote_setting_id` `vote_setting_id` INT DEFAULT NULL');
        }
    }
}
