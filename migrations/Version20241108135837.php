<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108135837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, binded_order_id INT NOT NULL, product VARCHAR(255) NOT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_845CA2C17C78A4E3 (binded_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C17C78A4E3 FOREIGN KEY (binded_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE `order` ADD carrier_name VARCHAR(255) NOT NULL, ADD carrier_price VARCHAR(255) NOT NULL, ADD delivery LONGTEXT NOT NULL, ADD reference VARCHAR(255) NOT NULL, ADD stripe_session VARCHAR(255) DEFAULT NULL, ADD state INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C17C78A4E3');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('ALTER TABLE `order` DROP carrier_name, DROP carrier_price, DROP delivery, DROP reference, DROP stripe_session, DROP state');
    }
}
