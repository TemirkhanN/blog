<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619154252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE posts_tags DROP CONSTRAINT fk_d5ecad9fbad26311');
        $this->addSql("ALTER TABLE posts_tags RENAME tag_id TO name");
        $this->addSql('ALTER TABLE posts_tags DROP CONSTRAINT fk_d5ecad9f4b89032c');
        $this->addSql('DROP INDEX idx_d5ecad9fbad26311');
        $this->addSql('ALTER TABLE posts_tags ADD CONSTRAINT FK_6FBC94264B89032C FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6FBC94264B89032C ON posts_tags (post_id)');
        $this->addSql('CREATE INDEX name_idx ON posts_tags (name)');
        $this->addSql('ALTER TABLE posts_tags ADD id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE posts_tags DROP CONSTRAINT posts_tags_pkey');
        $this->addSql('ALTER TABLE posts_tags ADD PRIMARY KEY (id)');

        $this->addSql('DROP TABLE tags');

        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962A4B89032C');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4B89032C FOREIGN KEY (post_id) REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
