<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171113082411 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location ADD added_by_admin_id INT DEFAULT NULL, ADD create_at DATETIME DEFAULT NULL, ADD update_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB3E434AF4 FOREIGN KEY (added_by_admin_id) REFERENCES admin (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB3E434AF4 ON location (added_by_admin_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB3E434AF4');
        $this->addSql('DROP INDEX IDX_5E9E89CB3E434AF4 ON location');
        $this->addSql('ALTER TABLE location DROP added_by_admin_id, DROP create_at, DROP update_at, DROP deleted_at');
    }
}
