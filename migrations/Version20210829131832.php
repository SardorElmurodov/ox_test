<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210829131832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE color_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE input_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE output_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE size_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE color (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE input (id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, amount DOUBLE PRECISION NOT NULL, input_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D82832D74584665A ON input (product_id)');
        $this->addSql('CREATE TABLE output (id INT NOT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, amount DOUBLE PRECISION NOT NULL, output_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CCDE149E4584665A ON output (product_id)');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, size_id INT NOT NULL, color_id INT NOT NULL, name VARCHAR(255) NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD498DA827 ON product (size_id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD7ADA1FB5 ON product (color_id)');
        $this->addSql('CREATE TABLE size (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE input ADD CONSTRAINT FK_D82832D74584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE output ADD CONSTRAINT FK_CCDE149E4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD498DA827 FOREIGN KEY (size_id) REFERENCES size (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD7ADA1FB5');
        $this->addSql('ALTER TABLE input DROP CONSTRAINT FK_D82832D74584665A');
        $this->addSql('ALTER TABLE output DROP CONSTRAINT FK_CCDE149E4584665A');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD498DA827');
        $this->addSql('DROP SEQUENCE color_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE input_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE output_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE size_id_seq CASCADE');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE input');
        $this->addSql('DROP TABLE output');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE size');
    }
}
