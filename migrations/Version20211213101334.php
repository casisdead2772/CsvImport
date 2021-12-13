<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211213101334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE error ADD import_result_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE error ADD CONSTRAINT FK_5DDDBC715A6602FF FOREIGN KEY (import_result_id) REFERENCES import_result (id)');
        $this->addSql('CREATE INDEX IDX_5DDDBC715A6602FF ON error (import_result_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE error DROP FOREIGN KEY FK_5DDDBC715A6602FF');
        $this->addSql('DROP INDEX IDX_5DDDBC715A6602FF ON error');
        $this->addSql('ALTER TABLE error DROP import_result_id');
    }
}
