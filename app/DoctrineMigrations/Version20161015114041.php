<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * change relation and create new instance country 
 */
class Version20161015114041 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('country')) {
            $this->addSql('
                  CREATE TABLE `country` (
                    `id` INT AUTO_INCREMENT NOT NULL, 
                    `country` VARCHAR(255) DEFAULT NULL, 
                    PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            ');
        }

        if ($schema->hasTable('location')) {
            $schemaTable = $schema->getTable('location');
            
            if (!$schemaTable->hasColumn('location_id')) {
                $this->addSql('
                    ALTER TABLE `location` 
                    ADD `location_id` INT DEFAULT NULL, 
                    ADD `country_id` INT DEFAULT NULL, 
                    DROP `country`
                ');                
            }

            if (!$schemaTable->hasForeignKey('FK_5E9E89CB64D218E')) {
                $this->addSql('
                    ALTER TABLE `location` 
                    ADD CONSTRAINT `FK_5E9E89CB64D218E` 
                    FOREIGN KEY (`location_id`) REFERENCES `city` (`id`)
                ');    
            }

            if (!$schemaTable->hasForeignKey('FK_5E9E89CB64D218E')) {
                $this->addSql('
                    ALTER TABLE `location` 
                    ADD CONSTRAINT `FK_5E9E89CBF92F3E70` 
                    FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)
                ');                
            }

            if (!$schemaTable->hasIndex('IDX_5E9E89CB64D218E')) {
                $this->addSql('
                    CREATE INDEX `IDX_5E9E89CB64D218E` ON `location` (`location_id`)
                ');    
            }

            if (!$schemaTable->hasIndex('IDX_5E9E89CBF92F3E70')) {
                $this->addSql('
                    CREATE INDEX `IDX_5E9E89CBF92F3E70` ON `location` (`country_id`)
                ');
            }
        }
        
        if ($schema->hasTable('vote_settings')) {
            $schemaTable = $schema->getTable('vote_settings');
            if (!$schemaTable->hasForeignKey('FK_1B1A09EC64D218E')) {
                $this->addSql('
                    ALTER TABLE `vote_settings` 
                    ADD CONSTRAINT `FK_1B1A09EC64D218E` 
                    FOREIGN KEY (`location_id`) REFERENCES `city` (`id`)
                ');   
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if ($schema->hasTable('location')) {
            $schemaTable = $schema->getTable('location');
            if ($schemaTable->hasForeignKey('FK_5E9E89CBF92F3E70')) {
                $this->addSql('
                    ALTER TABLE `location` 
                    DROP FOREIGN KEY `FK_5E9E89CBF92F3E70`
                ');   
            }
        }

        if ($schema->hasTable('country')) {
            $this->addSql('DROP TABLE `country`');            
        }

        if ($schema->hasTable('location')) {
            $schemaTable = $schema->getTable('location');
            if ($schemaTable->hasIndex('IDX_5E9E89CB64D218E')) {
                $this->addSql('DROP INDEX `IDX_5E9E89CB64D218E` ON `location`');    
            }

            if ($schemaTable->hasIndex('IDX_5E9E89CBF92F3E70')) {
                $this->addSql('DROP INDEX `IDX_5E9E89CBF92F3E70` ON `location`');
            }
            
            if (!$schemaTable->hasColumn('country')) {
                $this->addSql('ALTER TABLE `location` ADD `country` VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');    
            }

            if (!$schemaTable->hasColumn('location_id')) {
                $this->addSql('ALTER TABLE `location` DROP `location_id`');
            }

            if (!$schemaTable->hasColumn('country_id')) {
                $this->addSql('ALTER TABLE `location` DROP `country_id`');
            }
        }

        if ($schema->hasTable('vote_settings')) {
            $schemaTable = $schema->getTable('vote_settings');
            if ($schemaTable->hasForeignKey('FK_1B1A09EC64D218E')) {
                $this->addSql('
                    ALTER TABLE `vote_settings` 
                    DROP FOREIGN KEY `FK_1B1A09EC64D218E`
                ');
            }
        }
    }
}
