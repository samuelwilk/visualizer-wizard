<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317024628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chart_configuration ADD visualization_builder_progress_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chart_configuration ADD CONSTRAINT FK_19E49A6079532E62 FOREIGN KEY (visualization_builder_progress_id) REFERENCES visualization_builder_progress (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_19E49A6079532E62 ON chart_configuration (visualization_builder_progress_id)');
        $this->addSql('ALTER TABLE table_configuration ADD visualization_builder_progress_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE table_configuration ADD CONSTRAINT FK_D03DE5DA79532E62 FOREIGN KEY (visualization_builder_progress_id) REFERENCES visualization_builder_progress (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D03DE5DA79532E62 ON table_configuration (visualization_builder_progress_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE table_configuration DROP CONSTRAINT FK_D03DE5DA79532E62');
        $this->addSql('DROP INDEX IDX_D03DE5DA79532E62');
        $this->addSql('ALTER TABLE table_configuration DROP visualization_builder_progress_id');
        $this->addSql('ALTER TABLE chart_configuration DROP CONSTRAINT FK_19E49A6079532E62');
        $this->addSql('DROP INDEX IDX_19E49A6079532E62');
        $this->addSql('ALTER TABLE chart_configuration DROP visualization_builder_progress_id');
    }
}
