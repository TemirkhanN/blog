<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519130118 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE posts_tags (post_id VARCHAR(255) NOT NULL, tag_id VARCHAR(50) NOT NULL, PRIMARY KEY(post_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_D5ECAD9F4B89032C ON posts_tags (post_id)');
        $this->addSql('CREATE INDEX IDX_D5ECAD9FBAD26311 ON posts_tags (tag_id)');
        $this->addSql('CREATE TABLE tags (name VARCHAR(50) NOT NULL, PRIMARY KEY(name))');
        $this->addSql('ALTER TABLE posts_tags ADD CONSTRAINT FK_D5ECAD9F4B89032C FOREIGN KEY (post_id) REFERENCES posts (slug) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts_tags ADD CONSTRAINT FK_D5ECAD9FBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (name) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE posts_tags DROP CONSTRAINT FK_D5ECAD9FBAD26311');
        $this->addSql('DROP TABLE posts_tags');
        $this->addSql('DROP TABLE tags');
    }
}
