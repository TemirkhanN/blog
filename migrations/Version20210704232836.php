<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210704232836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create integer identifier for posts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE posts ADD COLUMN id SERIAL NOT NULL");

        $this->addSql('ALTER TABLE posts DROP CONSTRAINT posts_pkey CASCADE');
        $this->addSql('ALTER TABLE posts ADD PRIMARY KEY(id)');

        $this->addSql('ALTER TABLE posts_tags RENAME post_id TO post_id_old');
        $this->addSql('ALTER TABLE posts_tags ADD post_id INT');
        $this->addSql('UPDATE posts_tags pt SET post_id=(SELECT p.id FROM posts p WHERE slug=pt.post_id_old)');
        $this->addSql('ALTER TABLE posts_tags DROP post_id_old');
        $this->addSql('ALTER TABLE posts_tags ALTER post_id SET NOT NULL');
        $this->addSql('ALTER TABLE posts_tags
                            ADD CONSTRAINT FK_D5ECAD9F4B89032C
                                FOREIGN KEY (post_id)
                                    REFERENCES posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('ALTER TABLE posts_tags ADD PRIMARY KEY (post_id, tag_id)');

        $this->addSql('DROP INDEX idx_5f9e962a51c8fc69');
        $this->addSql('ALTER TABLE comments ADD post_id INT DEFAULT NULL');
        $this->addSql('UPDATE comments c SET post_id=(SELECT p.id FROM posts p WHERE p.slug=c.post_slug)');
        $this->addSql('ALTER TABLE comments ALTER post_id SET NOT NULL');
        $this->addSql('ALTER TABLE comments DROP post_slug');
        $this->addSql('ALTER TABLE comments
                                ADD CONSTRAINT FK_5F9E962A4B89032C
                                    FOREIGN KEY (post_id)
                                        REFERENCES posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
        $this->addSql('CREATE INDEX IDX_5F9E962A4B89032C ON comments (post_id)');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA989D9B62 ON posts (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('There is no reason to move backwards at this point');
    }
}
