<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430132008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_reception ADD product_reception_invoice_ref VARCHAR(50) NOT NULL, ADD product_reception_parcel_ref VARCHAR(50) NOT NULL, DROP invoice_ref, DROP parcel_ref');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_reception ADD invoice_ref VARCHAR(50) NOT NULL, ADD parcel_ref VARCHAR(50) NOT NULL, DROP product_reception_invoice_ref, DROP product_reception_parcel_ref');
    }
}
