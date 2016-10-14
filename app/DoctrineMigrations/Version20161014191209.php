<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * change relation
 */
class Version20161014191209 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($schema->hasTable('vote_settings')) {
            $schemaTable = $schema->getTable('vote_settings');

            if ($schemaTable->hasForeignKey('FK_1B1A09EC64D218E')) {
                $this->addSql('
                    ALTER TABLE `vote_settings` DROP FOREIGN KEY `FK_1B1A09EC64D218E`
                ');
            }
            
            if (!$schemaTable->hasForeignKey('FK_1B1A09EC64D218E')) {
                $this->addSql('
                    ALTER TABLE `vote_settings` 
                    ADD CONSTRAINT `FK_1B1A09EC64D218E` 
                    FOREIGN KEY (`location_id`) 
                    REFERENCES `city` (`id`)
                ');
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if ($schema->hasTable('vote_settings')) {
            $schemaTable = $schema->getTable('vote_settings');

            if ($schemaTable->hasForeignKey('FK_1B1A09EC64D218E')) {
                $this->addSql('
                    ALTER TABLE `vote_settings` 
                    DROP FOREIGN KEY `FK_1B1A09EC64D218E`
                ');
            }

            if (!$schemaTable->hasForeignKey('FK_1B1A09EC64D218E')) {
                $this->addSql('
                    ALTER TABLE `vote_settings` 
                    ADD CONSTRAINT `FK_1B1A09EC64D218E` 
                    FOREIGN KEY (`location_id`) 
                    REFERENCES `location` (`id`)
                ');
            }
        }
    }
}
