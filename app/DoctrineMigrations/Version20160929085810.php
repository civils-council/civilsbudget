<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160929085810 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE paper_vote (id INT NOT NULL, user_id INT DEFAULT NULL, location_id INT DEFAULT NULL, date DATETIME DEFAULT NULL, last VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, middle VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, liked_projects INT DEFAULT NULL, INDEX IDX_ACF82473A76ED395 (user_id), INDEX IDX_ACF8247364D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, source VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, charge INT DEFAULT NULL, confirmedAt DATETIME DEFAULT NULL, approved TINYINT(1) DEFAULT \'1\' NOT NULL, external_id VARCHAR(20) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, last_date_of_votes DATETIME DEFAULT NULL, create_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, confirmBy_id INT DEFAULT NULL, INDEX IDX_2FB3D0EEAA5F488F (confirmBy_id), INDEX IDX_2FB3D0EE7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, city_region VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, sex VARCHAR(255) DEFAULT NULL, birthday VARCHAR(255) DEFAULT NULL, last_login_at DATETIME DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, clid VARCHAR(255) NOT NULL, inn VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, count_votes INT DEFAULT 0, is_subscribe TINYINT(1) DEFAULT \'1\', create_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64964D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, create_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paper_vote ADD CONSTRAINT FK_ACF82473A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paper_vote ADD CONSTRAINT FK_ACF8247364D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEAA5F488F FOREIGN KEY (confirmBy_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64964D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT FK_77BECEE4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT FK_77BECEE4166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_project DROP FOREIGN KEY FK_77BECEE4166D1F9C');
        $this->addSql('ALTER TABLE paper_vote DROP FOREIGN KEY FK_ACF8247364D218E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64964D218E');
        $this->addSql('ALTER TABLE paper_vote DROP FOREIGN KEY FK_ACF82473A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE7E3C61F9');
        $this->addSql('ALTER TABLE user_project DROP FOREIGN KEY FK_77BECEE4A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEAA5F488F');
        $this->addSql('DROP TABLE paper_vote');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE admin');
    }
}
