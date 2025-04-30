<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430120433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movement (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_reception_id INT DEFAULT NULL, stock_modification_id INT DEFAULT NULL, stock_transfert_id INT DEFAULT NULL, inventory_id INT DEFAULT NULL, last_qty INT NOT NULL, new_qty INT NOT NULL, movement_type VARCHAR(20) NOT NULL, INDEX IDX_F4DD95F74584665A (product_id), INDEX IDX_F4DD95F7F1B0FD39 (product_reception_id), INDEX IDX_F4DD95F7ABC92A86 (stock_modification_id), INDEX IDX_F4DD95F71DF771BE (stock_transfert_id), INDEX IDX_F4DD95F79EEA759 (inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F7F1B0FD39 FOREIGN KEY (product_reception_id) REFERENCES product_reception (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F7ABC92A86 FOREIGN KEY (stock_modification_id) REFERENCES stock_modification (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F71DF771BE FOREIGN KEY (stock_transfert_id) REFERENCES stock_transfert (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F79EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF609EEA759');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF601DF771BE');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF60ABC92A86');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF604584665A');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF60F1B0FD39');
        $this->addSql('DROP TABLE product_movement');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_movement (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_reception_id INT DEFAULT NULL, stock_modification_id INT DEFAULT NULL, stock_transfert_id INT DEFAULT NULL, inventory_id INT DEFAULT NULL, last_qty INT NOT NULL, new_qty INT NOT NULL, product_movement_type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_3F6DFF609EEA759 (inventory_id), INDEX IDX_3F6DFF60F1B0FD39 (product_reception_id), INDEX IDX_3F6DFF60ABC92A86 (stock_modification_id), INDEX IDX_3F6DFF601DF771BE (stock_transfert_id), INDEX IDX_3F6DFF604584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF609EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF601DF771BE FOREIGN KEY (stock_transfert_id) REFERENCES stock_transfert (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF60ABC92A86 FOREIGN KEY (stock_modification_id) REFERENCES stock_modification (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF604584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF60F1B0FD39 FOREIGN KEY (product_reception_id) REFERENCES product_reception (id)');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F74584665A');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F7F1B0FD39');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F7ABC92A86');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F71DF771BE');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F79EEA759');
        $this->addSql('DROP TABLE movement');
    }
}
