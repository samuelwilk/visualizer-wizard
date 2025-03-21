<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316195606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE builder_data_source (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, selected_columns JSON DEFAULT NULL, base_data_source_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_641A1F61A8A6EC4A ON builder_data_source (base_data_source_id)');
        $this->addSql('CREATE TABLE chart_configuration (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, chart_type VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, labels JSON DEFAULT NULL, series JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, visualization_configuration_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_19E49A60D210691 ON chart_configuration (visualization_configuration_id)');
        $this->addSql('CREATE TABLE data_source (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, ingestion_mode VARCHAR(255) NOT NULL, api_endpoint VARCHAR(255) DEFAULT NULL, api_credentials JSON DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, columns JSON DEFAULT NULL, row_filters JSON DEFAULT NULL, api_response_content_type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE pre_processed_data (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, data JSON DEFAULT NULL, scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, data_source_id INT NOT NULL, visualization_configuration_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_84E5F7791A935C57 ON pre_processed_data (data_source_id)');
        $this->addSql('CREATE INDEX IDX_84E5F779D210691 ON pre_processed_data (visualization_configuration_id)');
        $this->addSql('CREATE TABLE "user" (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE visualization_builder_progress (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, builder_data_source_id INT DEFAULT NULL, last_modified_by_id INT NOT NULL, visualization_configuration_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BD3224331F6C93A7 ON visualization_builder_progress (builder_data_source_id)');
        $this->addSql('CREATE INDEX IDX_BD322433F703974A ON visualization_builder_progress (last_modified_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BD322433D210691 ON visualization_builder_progress (visualization_configuration_id)');
        $this->addSql('CREATE TABLE visualization_configuration (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, configuration JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, data_source_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E33682E61A935C57 ON visualization_configuration (data_source_id)');
        $this->addSql('ALTER TABLE builder_data_source ADD CONSTRAINT FK_641A1F61A8A6EC4A FOREIGN KEY (base_data_source_id) REFERENCES data_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chart_configuration ADD CONSTRAINT FK_19E49A60D210691 FOREIGN KEY (visualization_configuration_id) REFERENCES visualization_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pre_processed_data ADD CONSTRAINT FK_84E5F7791A935C57 FOREIGN KEY (data_source_id) REFERENCES data_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pre_processed_data ADD CONSTRAINT FK_84E5F779D210691 FOREIGN KEY (visualization_configuration_id) REFERENCES visualization_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visualization_builder_progress ADD CONSTRAINT FK_BD3224331F6C93A7 FOREIGN KEY (builder_data_source_id) REFERENCES builder_data_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visualization_builder_progress ADD CONSTRAINT FK_BD322433F703974A FOREIGN KEY (last_modified_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visualization_builder_progress ADD CONSTRAINT FK_BD322433D210691 FOREIGN KEY (visualization_configuration_id) REFERENCES visualization_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE visualization_configuration ADD CONSTRAINT FK_E33682E61A935C57 FOREIGN KEY (data_source_id) REFERENCES data_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE builder_data_source DROP CONSTRAINT FK_641A1F61A8A6EC4A');
        $this->addSql('ALTER TABLE chart_configuration DROP CONSTRAINT FK_19E49A60D210691');
        $this->addSql('ALTER TABLE pre_processed_data DROP CONSTRAINT FK_84E5F7791A935C57');
        $this->addSql('ALTER TABLE pre_processed_data DROP CONSTRAINT FK_84E5F779D210691');
        $this->addSql('ALTER TABLE visualization_builder_progress DROP CONSTRAINT FK_BD3224331F6C93A7');
        $this->addSql('ALTER TABLE visualization_builder_progress DROP CONSTRAINT FK_BD322433F703974A');
        $this->addSql('ALTER TABLE visualization_builder_progress DROP CONSTRAINT FK_BD322433D210691');
        $this->addSql('ALTER TABLE visualization_configuration DROP CONSTRAINT FK_E33682E61A935C57');
        $this->addSql('DROP TABLE builder_data_source');
        $this->addSql('DROP TABLE chart_configuration');
        $this->addSql('DROP TABLE data_source');
        $this->addSql('DROP TABLE pre_processed_data');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE visualization_builder_progress');
        $this->addSql('DROP TABLE visualization_configuration');
    }
}
