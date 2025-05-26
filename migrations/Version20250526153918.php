<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250526153918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, brand_name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE family (id INT AUTO_INCREMENT NOT NULL, family_name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventory (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT DEFAULT NULL, inventory_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', inventory_closed TINYINT(1) NOT NULL, INDEX IDX_B12D4A365080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_reception_id INT DEFAULT NULL, stock_modification_id INT DEFAULT NULL, stock_transfert_id INT DEFAULT NULL, inventory_id INT DEFAULT NULL, last_qty INT NOT NULL, movement_qty INT NOT NULL, movement_type VARCHAR(20) NOT NULL, INDEX IDX_F4DD95F74584665A (product_id), INDEX IDX_F4DD95F7F1B0FD39 (product_reception_id), INDEX IDX_F4DD95F7ABC92A86 (stock_modification_id), INDEX IDX_F4DD95F71DF771BE (stock_transfert_id), INDEX IDX_F4DD95F79EEA759 (inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, supplier_id INT NOT NULL, product_serial_number VARCHAR(50) DEFAULT NULL, product_name VARCHAR(50) NOT NULL, product_ref VARCHAR(50) DEFAULT NULL, product_ref2 VARCHAR(50) DEFAULT NULL, product_value NUMERIC(8, 2) DEFAULT NULL, INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04AD2ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_family (product_id INT NOT NULL, family_id INT NOT NULL, INDEX IDX_C79A60FF4584665A (product_id), INDEX IDX_C79A60FFC35E566A (family_id), PRIMARY KEY(product_id, family_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_color (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_color_name VARCHAR(50) NOT NULL, product_color_label VARCHAR(50) NOT NULL, INDEX IDX_C70A33B54584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_info (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_info_name VARCHAR(50) NOT NULL, product_info_content LONGTEXT NOT NULL, INDEX IDX_466113F64584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_reception (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, product_reception_invoice_ref VARCHAR(50) NOT NULL, product_reception_parcel_ref VARCHAR(50) NOT NULL, product_reception_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3E7633A25080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_size (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_size_height DOUBLE PRECISION DEFAULT NULL, product_size_width DOUBLE PRECISION DEFAULT NULL, product_size_depth DOUBLE PRECISION DEFAULT NULL, product_size_weight DOUBLE PRECISION DEFAULT NULL, INDEX IDX_7A2806CB4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_modification (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, stock_modification_message LONGTEXT NOT NULL, stock_modification_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CE0F1FC15080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_transfert (id INT AUTO_INCREMENT NOT NULL, linked_transfert_id INT DEFAULT NULL, warehouse_id INT NOT NULL, stock_transfert_message LONGTEXT NOT NULL, stock_transfert_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', transfert_origin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_86D34A5392A50EF7 (linked_transfert_id), INDEX IDX_86D34A535080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, supplier_name VARCHAR(50) NOT NULL, supplier_phone VARCHAR(25) DEFAULT NULL, supplier_address_number INT DEFAULT NULL, supplier_address_road VARCHAR(50) DEFAULT NULL, supplier_address_label VARCHAR(50) DEFAULT NULL, supplier_address_postal_code VARCHAR(8) DEFAULT NULL, supplier_address_city VARCHAR(50) DEFAULT NULL, supplier_address_state VARCHAR(50) DEFAULT NULL, supplier_address_country VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, user_last_name VARCHAR(50) NOT NULL, user_first_name VARCHAR(50) NOT NULL, user_phone VARCHAR(25) DEFAULT NULL, user_address_number INT DEFAULT NULL, user_address_road VARCHAR(50) DEFAULT NULL, user_address_label VARCHAR(50) DEFAULT NULL, user_address_postal_code VARCHAR(8) DEFAULT NULL, user_address_city VARCHAR(50) DEFAULT NULL, user_address_state VARCHAR(50) DEFAULT NULL, user_address_country VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_warehouse (user_id INT NOT NULL, warehouse_id INT NOT NULL, INDEX IDX_EC530618A76ED395 (user_id), INDEX IDX_EC5306185080ECDE (warehouse_id), PRIMARY KEY(user_id, warehouse_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, warehouse_name VARCHAR(50) NOT NULL, warehouse_phone VARCHAR(25) DEFAULT NULL, warehouse_address_number INT DEFAULT NULL, warehouse_address_road VARCHAR(50) DEFAULT NULL, warehouse_address_label VARCHAR(50) DEFAULT NULL, warehouse_address_postal_code VARCHAR(8) DEFAULT NULL, warehouse_address_city VARCHAR(50) DEFAULT NULL, warehouse_address_state VARCHAR(50) DEFAULT NULL, warehouse_address_country VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_product (warehouse_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_F4AD11D85080ECDE (warehouse_id), INDEX IDX_F4AD11D84584665A (product_id), PRIMARY KEY(warehouse_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A365080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F74584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F7F1B0FD39 FOREIGN KEY (product_reception_id) REFERENCES product_reception (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F7ABC92A86 FOREIGN KEY (stock_modification_id) REFERENCES stock_modification (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F71DF771BE FOREIGN KEY (stock_transfert_id) REFERENCES stock_transfert (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F79EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE product_family ADD CONSTRAINT FK_C79A60FF4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_family ADD CONSTRAINT FK_C79A60FFC35E566A FOREIGN KEY (family_id) REFERENCES family (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_color ADD CONSTRAINT FK_C70A33B54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_info ADD CONSTRAINT FK_466113F64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_reception ADD CONSTRAINT FK_3E7633A25080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE product_size ADD CONSTRAINT FK_7A2806CB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock_modification ADD CONSTRAINT FK_CE0F1FC15080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE stock_transfert ADD CONSTRAINT FK_86D34A5392A50EF7 FOREIGN KEY (linked_transfert_id) REFERENCES stock_transfert (id)');
        $this->addSql('ALTER TABLE stock_transfert ADD CONSTRAINT FK_86D34A535080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE user_warehouse ADD CONSTRAINT FK_EC530618A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_warehouse ADD CONSTRAINT FK_EC5306185080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_product ADD CONSTRAINT FK_F4AD11D85080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_product ADD CONSTRAINT FK_F4AD11D84584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A365080ECDE');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F74584665A');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F7F1B0FD39');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F7ABC92A86');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F71DF771BE');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F79EEA759');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD2ADD6D8C');
        $this->addSql('ALTER TABLE product_family DROP FOREIGN KEY FK_C79A60FF4584665A');
        $this->addSql('ALTER TABLE product_family DROP FOREIGN KEY FK_C79A60FFC35E566A');
        $this->addSql('ALTER TABLE product_color DROP FOREIGN KEY FK_C70A33B54584665A');
        $this->addSql('ALTER TABLE product_info DROP FOREIGN KEY FK_466113F64584665A');
        $this->addSql('ALTER TABLE product_reception DROP FOREIGN KEY FK_3E7633A25080ECDE');
        $this->addSql('ALTER TABLE product_size DROP FOREIGN KEY FK_7A2806CB4584665A');
        $this->addSql('ALTER TABLE stock_modification DROP FOREIGN KEY FK_CE0F1FC15080ECDE');
        $this->addSql('ALTER TABLE stock_transfert DROP FOREIGN KEY FK_86D34A5392A50EF7');
        $this->addSql('ALTER TABLE stock_transfert DROP FOREIGN KEY FK_86D34A535080ECDE');
        $this->addSql('ALTER TABLE user_warehouse DROP FOREIGN KEY FK_EC530618A76ED395');
        $this->addSql('ALTER TABLE user_warehouse DROP FOREIGN KEY FK_EC5306185080ECDE');
        $this->addSql('ALTER TABLE warehouse_product DROP FOREIGN KEY FK_F4AD11D85080ECDE');
        $this->addSql('ALTER TABLE warehouse_product DROP FOREIGN KEY FK_F4AD11D84584665A');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE family');
        $this->addSql('DROP TABLE inventory');
        $this->addSql('DROP TABLE movement');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_family');
        $this->addSql('DROP TABLE product_color');
        $this->addSql('DROP TABLE product_info');
        $this->addSql('DROP TABLE product_reception');
        $this->addSql('DROP TABLE product_size');
        $this->addSql('DROP TABLE stock_modification');
        $this->addSql('DROP TABLE stock_transfert');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_warehouse');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE warehouse_product');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
