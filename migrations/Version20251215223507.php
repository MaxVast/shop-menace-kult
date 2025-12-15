<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215223507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products_image ALTER name DROP NOT NULL');
        $this->addSql('ALTER TABLE products_image ALTER original_name DROP NOT NULL');
        $this->addSql('ALTER TABLE products_image ALTER mime_type DROP NOT NULL');
        $this->addSql('ALTER TABLE products_image ALTER size DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE products_image ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE products_image ALTER original_name SET NOT NULL');
        $this->addSql('ALTER TABLE products_image ALTER mime_type SET NOT NULL');
        $this->addSql('ALTER TABLE products_image ALTER size SET NOT NULL');
    }
}
