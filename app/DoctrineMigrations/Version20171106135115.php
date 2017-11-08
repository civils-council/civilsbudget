<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171106135115 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            UPDATE `user` u, `user_project` up
            SET 
                up.added_by_id = u.added_by_admin_id, 
                up.blank_number = u.number_blank,
                up.create_at = u.create_at, 
                up.update_at = u.update_at
            WHERE 
                up.user_id = u.id 
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
