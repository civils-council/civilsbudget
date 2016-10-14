<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161014191209 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vote_settings DROP FOREIGN KEY FK_1B1A09EC64D218E');
        $this->addSql('ALTER TABLE vote_settings ADD CONSTRAINT FK_1B1A09EC64D218E FOREIGN KEY (location_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vote_settings DROP FOREIGN KEY FK_1B1A09EC64D218E');
        $this->addSql('ALTER TABLE vote_settings ADD CONSTRAINT FK_1B1A09EC64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
    }
}
