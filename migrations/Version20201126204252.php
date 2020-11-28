<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126204252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE SEQUENCE committed_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1'
        );
        $this->addSql(
            'CREATE TABLE committed_transaction (id INT NOT NULL, receiver_id INT NOT NULL, sender_id INT DEFAULT NULL, coin_quantity INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE INDEX IDX_4620118BCD53EDB6 ON committed_transaction (receiver_id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_4620118BF624B39D ON committed_transaction (sender_id)'
        );
        $this->addSql(
            'ALTER TABLE committed_transaction ADD CONSTRAINT FK_4620118BCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE committed_transaction ADD CONSTRAINT FK_4620118BF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE "user" ADD coin_balance INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE committed_transaction_id_seq CASCADE');
        $this->addSql('DROP TABLE committed_transaction');
        $this->addSql('ALTER TABLE "user" DROP coin_balance');
    }
}
