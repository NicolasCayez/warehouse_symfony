<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513133638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_transfert DROP FOREIGN KEY FK_86D34A5392A50EF7');
        $this->addSql('DROP INDEX UNIQ_86D34A5392A50EF7 ON stock_transfert');
        $this->addSql('ALTER TABLE stock_transfert ADD linked_stock_transfert_id INT NOT NULL, DROP linked_transfert_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock_transfert ADD linked_transfert_id INT DEFAULT NULL, DROP linked_stock_transfert_id');
        $this->addSql('ALTER TABLE stock_transfert ADD CONSTRAINT FK_86D34A5392A50EF7 FOREIGN KEY (linked_transfert_id) REFERENCES stock_transfert (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_86D34A5392A50EF7 ON stock_transfert (linked_transfert_id)');
    }
}
