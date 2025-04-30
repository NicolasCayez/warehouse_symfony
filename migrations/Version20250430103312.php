<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430103312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory ADD warehouse_id INT NOT NULL');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A365080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_B12D4A365080ECDE ON inventory (warehouse_id)');
        $this->addSql('ALTER TABLE product_reception ADD warehouse_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_reception ADD CONSTRAINT FK_3E7633A25080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_3E7633A25080ECDE ON product_reception (warehouse_id)');
        $this->addSql('ALTER TABLE stock_modification ADD warehouse_id INT NOT NULL');
        $this->addSql('ALTER TABLE stock_modification ADD CONSTRAINT FK_CE0F1FC15080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_CE0F1FC15080ECDE ON stock_modification (warehouse_id)');
        $this->addSql('ALTER TABLE stock_transfert ADD warehouse_id INT NOT NULL');
        $this->addSql('ALTER TABLE stock_transfert ADD CONSTRAINT FK_86D34A535080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_86D34A535080ECDE ON stock_transfert (warehouse_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A365080ECDE');
        $this->addSql('DROP INDEX IDX_B12D4A365080ECDE ON inventory');
        $this->addSql('ALTER TABLE inventory DROP warehouse_id');
        $this->addSql('ALTER TABLE product_reception DROP FOREIGN KEY FK_3E7633A25080ECDE');
        $this->addSql('DROP INDEX IDX_3E7633A25080ECDE ON product_reception');
        $this->addSql('ALTER TABLE product_reception DROP warehouse_id');
        $this->addSql('ALTER TABLE stock_modification DROP FOREIGN KEY FK_CE0F1FC15080ECDE');
        $this->addSql('DROP INDEX IDX_CE0F1FC15080ECDE ON stock_modification');
        $this->addSql('ALTER TABLE stock_modification DROP warehouse_id');
        $this->addSql('ALTER TABLE stock_transfert DROP FOREIGN KEY FK_86D34A535080ECDE');
        $this->addSql('DROP INDEX IDX_86D34A535080ECDE ON stock_transfert');
        $this->addSql('ALTER TABLE stock_transfert DROP warehouse_id');
    }
}
