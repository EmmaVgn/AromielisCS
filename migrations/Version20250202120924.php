<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250202120924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_advise (tag_id INT NOT NULL, advise_id INT NOT NULL, INDEX IDX_C70155F0BAD26311 (tag_id), INDEX IDX_C70155F072791587 (advise_id), PRIMARY KEY(tag_id, advise_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_advise ADD CONSTRAINT FK_C70155F0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_advise ADD CONSTRAINT FK_C70155F072791587 FOREIGN KEY (advise_id) REFERENCES advise (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_advise DROP FOREIGN KEY FK_C70155F0BAD26311');
        $this->addSql('ALTER TABLE tag_advise DROP FOREIGN KEY FK_C70155F072791587');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_advise');
    }
}
