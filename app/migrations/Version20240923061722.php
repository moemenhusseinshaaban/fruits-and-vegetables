<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240923061722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food ADD external_id INT NOT NULL');
        $this->addSql('UPDATE food SET external_id = id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D43829F79F75D7B0 ON food (external_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D43829F79F75D7B0 ON food');
        $this->addSql('ALTER TABLE food DROP external_id');
    }
}
