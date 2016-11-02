<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add city to admin profile
 */
class Version20161102193016 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('admin'),
            'Table admin doesn\'t exist'
        );

        $schemaTable = $schema->getTable('admin');

        if (!$schemaTable->hasColumn('city_id')) {
            $this->addSql('ALTER TABLE `admin` ADD `city_id` INT NOT NULL');            
        }

        if (!$schemaTable->hasForeignKey('FK_880E0D768BAC62AF')) {
            $this->addSql('
                ALTER TABLE `admin` 
                ADD CONSTRAINT `FK_880E0D768BAC62AF` 
                FOREIGN KEY (`city_id`) 
                REFERENCES `city` (`id`)
            ');
        }
        
        if (!$schemaTable->hasIndex('IDX_880E0D768BAC62AF')) {
            $this->addSql('CREATE INDEX `IDX_880E0D768BAC62AF` ON `admin` (`city_id`)');   
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('admin'),
            'Table admin doesn\'t exist'
        );

        $schemaTable = $schema->getTable('admin');
        
        if ($schemaTable->hasForeignKey('FK_880E0D768BAC62AF')) {
            $this->addSql('
                ALTER TABLE `admin` DROP FOREIGN KEY `FK_880E0D768BAC62AF`');            
        }

        if ($schemaTable->hasIndex('IDX_880E0D768BAC62AF')) {
            $this->addSql('DROP INDEX `IDX_880E0D768BAC62AF` ON `admin`');            
        }
        
        if ($schemaTable->hasColumn('city_id')) {
            $this->addSql('ALTER TABLE `admin` DROP `city_id`');   
        }
    }
}
