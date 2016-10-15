<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161015114041 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location ADD location_id INT DEFAULT NULL, ADD country_id INT DEFAULT NULL, DROP country');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB64D218E FOREIGN KEY (location_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB64D218E ON location (location_id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CBF92F3E70 ON location (country_id)');
//        $this->addSql('ALTER TABLE project CHANGE vote_setting_id vote_setting_id INT NOT NULL');
        $this->addSql('ALTER TABLE vote_settings ADD CONSTRAINT FK_1B1A09EC64D218E FOREIGN KEY (location_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBF92F3E70');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP INDEX IDX_5E9E89CB64D218E ON location');
        $this->addSql('DROP INDEX IDX_5E9E89CBF92F3E70 ON location');
        $this->addSql('ALTER TABLE location ADD country VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP location_id, DROP country_id');
//        $this->addSql('ALTER TABLE project CHANGE vote_setting_id vote_setting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote_settings DROP FOREIGN KEY FK_1B1A09EC64D218E');
    }
}
