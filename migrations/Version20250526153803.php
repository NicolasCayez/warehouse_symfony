<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250526153803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE supplier CHANGE supplier_phone supplier_phone VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE user_phone user_phone VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse CHANGE warehouse_phone warehouse_phone VARCHAR(25) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE supplier CHANGE supplier_phone supplier_phone VARCHAR(13) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE user_phone user_phone VARCHAR(13) DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse CHANGE warehouse_phone warehouse_phone VARCHAR(13) DEFAULT NULL');
    }
}
