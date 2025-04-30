<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430104101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_movement ADD product_reception_id INT DEFAULT NULL, ADD stock_modification_id INT DEFAULT NULL, ADD stock_transfert_id INT DEFAULT NULL, ADD inventory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF60F1B0FD39 FOREIGN KEY (product_reception_id) REFERENCES product_reception (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF60ABC92A86 FOREIGN KEY (stock_modification_id) REFERENCES stock_modification (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF601DF771BE FOREIGN KEY (stock_transfert_id) REFERENCES stock_transfert (id)');
        $this->addSql('ALTER TABLE product_movement ADD CONSTRAINT FK_3F6DFF609EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('CREATE INDEX IDX_3F6DFF60F1B0FD39 ON product_movement (product_reception_id)');
        $this->addSql('CREATE INDEX IDX_3F6DFF60ABC92A86 ON product_movement (stock_modification_id)');
        $this->addSql('CREATE INDEX IDX_3F6DFF601DF771BE ON product_movement (stock_transfert_id)');
        $this->addSql('CREATE INDEX IDX_3F6DFF609EEA759 ON product_movement (inventory_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF60F1B0FD39');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF60ABC92A86');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF601DF771BE');
        $this->addSql('ALTER TABLE product_movement DROP FOREIGN KEY FK_3F6DFF609EEA759');
        $this->addSql('DROP INDEX IDX_3F6DFF60F1B0FD39 ON product_movement');
        $this->addSql('DROP INDEX IDX_3F6DFF60ABC92A86 ON product_movement');
        $this->addSql('DROP INDEX IDX_3F6DFF601DF771BE ON product_movement');
        $this->addSql('DROP INDEX IDX_3F6DFF609EEA759 ON product_movement');
        $this->addSql('ALTER TABLE product_movement DROP product_reception_id, DROP stock_modification_id, DROP stock_transfert_id, DROP inventory_id');
    }
}
