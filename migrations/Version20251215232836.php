<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215232836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE products_categories (products_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(products_id, category_id))');
        $this->addSql('CREATE INDEX IDX_E8ACBE766C8A81A9 ON products_categories (products_id)');
        $this->addSql('CREATE INDEX IDX_E8ACBE7612469DE2 ON products_categories (category_id)');
        $this->addSql('COMMENT ON COLUMN products_categories.products_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN products_categories.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE products_categories ADD CONSTRAINT FK_E8ACBE766C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products_categories ADD CONSTRAINT FK_E8ACBE7612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT fk_b3ba5a5a12469de2');
        $this->addSql('DROP INDEX idx_b3ba5a5a12469de2');
        $this->addSql('ALTER TABLE products DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE products_categories DROP CONSTRAINT FK_E8ACBE766C8A81A9');
        $this->addSql('ALTER TABLE products_categories DROP CONSTRAINT FK_E8ACBE7612469DE2');
        $this->addSql('DROP TABLE products_categories');
        $this->addSql('ALTER TABLE products ADD category_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN products.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT fk_b3ba5a5a12469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b3ba5a5a12469de2 ON products (category_id)');
    }
}
