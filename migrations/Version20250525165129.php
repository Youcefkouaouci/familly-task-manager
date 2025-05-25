<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525165129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE task_assignment ADD child_id INT NOT NULL, DROP user_id, CHANGE task_id task_id INT NOT NULL, CHANGE status status VARCHAR(20) NOT NULL, CHANGE notes notes LONGTEXT DEFAULT NULL, CHANGE completed_at completed_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE last_status_changed_at last_status_changed_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE notified is_notified TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task_assignment ADD CONSTRAINT FK_2CD60F158DB60186 FOREIGN KEY (task_id) REFERENCES task (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task_assignment ADD CONSTRAINT FK_2CD60F15DD62C21B FOREIGN KEY (child_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2CD60F15DD62C21B ON task_assignment (child_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE task_assignment DROP FOREIGN KEY FK_2CD60F158DB60186
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task_assignment DROP FOREIGN KEY FK_2CD60F15DD62C21B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2CD60F15DD62C21B ON task_assignment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task_assignment ADD user_id INT DEFAULT NULL, DROP child_id, CHANGE task_id task_id INT DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE completed_at completed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE notes notes VARCHAR(255) NOT NULL, CHANGE last_status_changed_at last_status_changed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE is_notified notified TINYINT(1) NOT NULL
        SQL);
    }
}
