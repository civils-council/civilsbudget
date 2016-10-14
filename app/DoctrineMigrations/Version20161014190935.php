<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * create table city
 */
class Version20161014190935 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('city')) {
            $this->addSql('
                CREATE TABLE `city` (
                  `id` INT AUTO_INCREMENT NOT NULL, 
                  `city` VARCHAR(255) DEFAULT NULL, 
                  PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if ($schema->hasTable('city')) {
            $this->addSql('DROP TABLE city');   
        }
    }
}
