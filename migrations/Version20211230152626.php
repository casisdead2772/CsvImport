<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211230152626 extends AbstractMigration {
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE error (id INT AUTO_INCREMENT NOT NULL, message_id VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, error_message LONGTEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX IDX_5DDDBC71537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id VARCHAR(255) NOT NULL, status INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tblProductData (intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL, strProductName VARCHAR(50) NOT NULL, strProductDesc VARCHAR(255) NOT NULL, strProductCode VARCHAR(10) NOT NULL, stock INT DEFAULT NULL, costInGbp INT NOT NULL, dtmAdded DATETIME DEFAULT NULL, dtmDiscontinued DATETIME DEFAULT NULL, stmTimestamp DATETIME NOT NULL, UNIQUE INDEX strProductCode (strProductCode), PRIMARY KEY(intProductDataId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE error ADD CONSTRAINT FK_5DDDBC71537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE error DROP FOREIGN KEY FK_5DDDBC71537A1329');
        $this->addSql('DROP TABLE error');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE tblProductData');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
