<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217133753 extends AbstractMigration {
    /**
     * @return string
     */
    public function getDescription(): string {
        return '';
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE error (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT NULL, user_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, error_message LONGTEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NUll, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NUll, INDEX IDX_5DDDBC71537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, message_id VARCHAR(255) NOT NULL, status INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NUll, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NUll, UNIQUE INDEX UNIQ_B6BD307F537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tblProductData (intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL, strProductName VARCHAR(50) NOT NULL, strProductDesc VARCHAR(255) NOT NULL, strProductCode VARCHAR(10) NOT NULL, stock INT DEFAULT NULL, costInGbp INT NOT NULL, dtmAdded DATETIME DEFAULT NULL, dtmDiscontinued DATETIME DEFAULT NULL, stmTimestamp DATETIME NOT NULL, UNIQUE INDEX strProductCode (strProductCode), PRIMARY KEY(intProductDataId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE error ADD CONSTRAINT FK_5DDDBC71537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE error DROP FOREIGN KEY FK_5DDDBC71537A1329');
        $this->addSql('DROP TABLE error');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE tblProductData');
    }
}
