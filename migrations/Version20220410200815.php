<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Post;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220410200815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds state to posts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE posts ADD state INT DEFAULT '. Post::STATE_PUBLISHED .' NOT NULL');
        $this->addSql('ALTER TABLE posts ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE posts ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ALTER published_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN posts.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN posts.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE posts DROP state');
        $this->addSql('ALTER TABLE posts DROP created_at');
        $this->addSql('ALTER TABLE posts DROP updated_at');
        $this->addSql('ALTER TABLE posts ALTER published_at SET NOT NULL');
    }
}
