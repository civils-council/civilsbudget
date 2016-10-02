<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * create table votes settings and relation with users and project
 */
class Version20161002185028 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $isNow = false;
        if (!$schema->hasTable('vote_settings')) {
            $this->addSql('
                CREATE TABLE `vote_settings` 
                (
                  `id` INT AUTO_INCREMENT NOT NULL, 
                  `location_id` INT DEFAULT NULL, 
                  `title` VARCHAR(255) DEFAULT NULL, 
                  `vote_limits` INT NOT NULL, 
                  `create_at` DATETIME DEFAULT NULL, 
                  `update_at` DATETIME DEFAULT NULL, 
                  `deleted_at` DATETIME DEFAULT NULL, 
                  INDEX `IDX_1B1A09EC64D218E` (`location_id`), PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            ');
            $isNow = true;
        }

        if ($isNow || ($schema->hasTable('vote_settings') && !$schema->getTable('vote_settings')->hasForeignKey('FK_1B1A09EC64D218E'))) {
            $this->addSql('
                ALTER TABLE `vote_settings` 
                ADD CONSTRAINT `FK_1B1A09EC64D218E` 
                FOREIGN KEY (`location_id`) 
                REFERENCES `location` (`id`)
            ');
        }

        if ($schema->hasTable('project')) {
            $schemaTable = $schema->getTable('project');
            if (!$schemaTable->hasColumn('vote_setting_id')) {
                $this->addSql('
                    ALTER TABLE `project` ADD `vote_setting_id` INT DEFAULT NULL
                ');   
            }
            
            if (!$schemaTable->hasForeignKey('FK_2FB3D0EE579C78A')) {
                $this->addSql('
                    ALTER TABLE `project` 
                    ADD CONSTRAINT `FK_2FB3D0EE579C78A` 
                    FOREIGN KEY (`vote_setting_id`) REFERENCES `vote_settings` (`id`)
                ');                
            }
            
            if (!$schemaTable->hasIndex('IDX_2FB3D0EE579C78A')) {
                $this->addSql('CREATE INDEX `IDX_2FB3D0EE579C78A` ON `project` (`vote_setting_id`)');
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if ($schema->hasTable('project')) {
            $schemaTable = $schema->getTable('project');
            if ($schemaTable->hasForeignKey('FK_2FB3D0EE579C78A')) {
                $this->addSql('ALTER TABLE `project` DROP FOREIGN KEY `FK_2FB3D0EE579C78A`');
            }
        }

        if ($schema->hasTable('vote_settings')) {
            $this->addSql('DROP TABLE `vote_settings`');            
        }

        if ($schema->hasTable('project')) {
            $schemaTable = $schema->getTable('project');
            if ($schemaTable->hasIndex('IDX_2FB3D0EE579C78A')) {
                $this->addSql('DROP INDEX `IDX_2FB3D0EE579C78A` ON `project`');
            }
            
            if ($schemaTable->hasColumn('vote_setting_id')) {
                $this->addSql('ALTER TABLE `project` DROP `vote_setting_id`');       
            }
        }
    }
}
