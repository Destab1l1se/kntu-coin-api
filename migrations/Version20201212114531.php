<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201212114531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE SEQUENCE block_id_seq INCREMENT BY 1 MINVALUE 1 START 1'
        );
        $this->addSql(
            'CREATE SEQUENCE block_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1'
        );
        $this->addSql(
            'CREATE TABLE block (id INT NOT NULL, prev_block_id INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, nonce INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_831B97228601390A ON block (prev_block_id)'
        );
        $this->addSql(
            'CREATE TABLE block_transaction (id INT NOT NULL, block_id INT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT NOT NULL, coin_quantity INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_A83A500AE9ED820C ON block_transaction (block_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_A83A500AF624B39D ON block_transaction (sender_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_A83A500ACD53EDB6 ON block_transaction (receiver_id)'
        );
        $this->addSql(
            'ALTER TABLE block ADD CONSTRAINT FK_831B97228601390A FOREIGN KEY (prev_block_id) REFERENCES block (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE block_transaction ADD CONSTRAINT FK_A83A500AE9ED820C FOREIGN KEY (block_id) REFERENCES block (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE block_transaction ADD CONSTRAINT FK_A83A500AF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE block_transaction ADD CONSTRAINT FK_A83A500ACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE committed_transaction ADD approved_for_next_block BOOLEAN NOT NULL'
        );
        $this->addSql(
            "INSERT INTO block(id, prev_block_id, hash, nonce) VALUES (nextval('block_id_seq'), null, 'GENESIS_BLOCK', 0) "
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE block DROP CONSTRAINT FK_831B97228601390A');
        $this->addSql(
            'ALTER TABLE block_transaction DROP CONSTRAINT FK_A83A500AE9ED820C'
        );
        $this->addSql('DROP SEQUENCE block_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE block_transaction_id_seq CASCADE');
        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE block_transaction');
        $this->addSql(
            'ALTER TABLE committed_transaction DROP approved_for_next_block'
        );
    }
}
