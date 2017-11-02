<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171102223251 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CBA76ED395 ON location (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBA76ED395');
        $this->addSql('DROP INDEX IDX_5E9E89CBA76ED395 ON location');
        $this->addSql('ALTER TABLE location DROP user_id');
    }
}
