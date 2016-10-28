<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add added_by_admin_id to user
 */
class Version20161028101229 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('user'),
            'Table user doesn\'t exist'
        );

        $schemaTable = $schema->getTable('user');

        if (!$schemaTable->hasColumn('added_by_admin_id')) {
            $this->addSql('
                ALTER TABLE `user` ADD `added_by_admin_id` INT DEFAULT NULL
            ');
        }

        if (!$schemaTable->hasForeignKey('FK_8D93D6493E434AF4')) {
            $this->addSql('
                ALTER TABLE `user` 
                ADD CONSTRAINT `FK_8D93D6493E434AF4` 
                FOREIGN KEY (`added_by_admin_id`) 
                REFERENCES `admin` (`id`)
            ');    
        }

        if (!$schemaTable->hasIndex('IDX_8D93D6493E434AF4')) {
            $this->addSql('
                CREATE INDEX `IDX_8D93D6493E434AF4` 
                ON `user` (`added_by_admin_id`)
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->skipIf(
            !$schema->hasTable('user'),
            'Table user doesn\'t exist'
        );

        $schemaTable = $schema->getTable('user');

        if ($schemaTable->hasForeignKey('FK_8D93D6493E434AF4')) {
            $this->addSql('
                ALTER TABLE `user` 
                DROP FOREIGN KEY `FK_8D93D6493E434AF4`
            ');
        }

        if ($schemaTable->hasIndex('IDX_8D93D6493E434AF4')) {
            $this->addSql('
                DROP INDEX `IDX_8D93D6493E434AF4` 
                ON `user`
            ');
        }

        if ($schemaTable->hasColumn('added_by_admin_id')) {
            $this->addSql('ALTER TABLE `user` DROP `added_by_admin_id`');
        }
    }
}
