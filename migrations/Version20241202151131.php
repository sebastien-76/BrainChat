<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202151131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_message ADD room_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC1654177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC16A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_FAB3FC1654177093 ON chat_message (room_id)');
        $this->addSql('CREATE INDEX IDX_FAB3FC16A76ED395 ON chat_message (user_id)');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B948B568F');
        $this->addSql('DROP INDEX IDX_729F519B948B568F ON room');
        $this->addSql('ALTER TABLE room DROP chat_message_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649948B568F');
        $this->addSql('DROP INDEX IDX_8D93D649948B568F ON user');
        $this->addSql('ALTER TABLE user DROP chat_message_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room ADD chat_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B948B568F FOREIGN KEY (chat_message_id) REFERENCES chat_message (id)');
        $this->addSql('CREATE INDEX IDX_729F519B948B568F ON room (chat_message_id)');
        $this->addSql('ALTER TABLE `user` ADD chat_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649948B568F FOREIGN KEY (chat_message_id) REFERENCES chat_message (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649948B568F ON `user` (chat_message_id)');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_FAB3FC1654177093');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_FAB3FC16A76ED395');
        $this->addSql('DROP INDEX IDX_FAB3FC1654177093 ON chat_message');
        $this->addSql('DROP INDEX IDX_FAB3FC16A76ED395 ON chat_message');
        $this->addSql('ALTER TABLE chat_message DROP room_id, DROP user_id');
    }
}