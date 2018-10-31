<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181031121711 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE car_mark (id_car_mark INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_create INT DEFAULT NULL, date_update INT DEFAULT NULL, id_car_type INT NOT NULL, name_rus VARCHAR(255) DEFAULT NULL, INDEX id_car_type (id_car_type), PRIMARY KEY(id_car_mark)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_limit (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, limit_id INT DEFAULT NULL, INDEX IDX_9D541338A76ED395 (user_id), INDEX IDX_9D541338A15D41FA (limit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_limit (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, limit_id INT DEFAULT NULL, INDEX IDX_97A5A72912469DE2 (category_id), INDEX IDX_97A5A729A15D41FA (limit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE limits (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, working_hours LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', limitation INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_56C338BBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, letter VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, letter_description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, car_mark_id INT DEFAULT NULL, car_model_id INT DEFAULT NULL, operator_id INT DEFAULT NULL, property_type SMALLINT NOT NULL, legal_name VARCHAR(255) DEFAULT NULL, owner_first_name VARCHAR(255) NOT NULL, owner_last_name VARCHAR(255) NOT NULL, owner_middle_name VARCHAR(255) DEFAULT NULL, card_is_secondary TINYINT(1) NOT NULL, card_is_duplicate_for INT DEFAULT NULL, is_archive TINYINT(1) NOT NULL, body_number VARCHAR(255) DEFAULT NULL, date_of_diagnosis INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, valid_till INT NOT NULL, note LONGTEXT DEFAULT NULL, registration_number VARCHAR(255) DEFAULT NULL, test_result TINYINT(1) NOT NULL, test_type SMALLINT NOT NULL, vin VARCHAR(255) DEFAULT NULL, frame_number VARCHAR(255) DEFAULT NULL, empty_mass INT NOT NULL, max_mass INT NOT NULL, fuel_type VARCHAR(255) DEFAULT NULL, braking_system VARCHAR(255) NOT NULL, tyres VARCHAR(255) NOT NULL, kilometres INT NOT NULL, date_of_retest INT DEFAULT NULL, car_year INT NOT NULL, document_type SMALLINT NOT NULL, document_series VARCHAR(255) NOT NULL, document_number VARCHAR(255) NOT NULL, document_organization VARCHAR(255) NOT NULL, document_date INT NOT NULL, document_is_foreign TINYINT(1) DEFAULT NULL, expert VARCHAR(255) NOT NULL, eaisto_number VARCHAR(255) DEFAULT NULL, eaisto_date INT DEFAULT NULL, status SMALLINT DEFAULT NULL, eaisto_id INT DEFAULT NULL, created_at INT NOT NULL, issued_at INT DEFAULT NULL, updated_at INT DEFAULT NULL, INDEX IDX_161498D3A76ED395 (user_id), INDEX IDX_161498D312469DE2 (category_id), INDEX IDX_161498D3113B0AF7 (car_mark_id), INDEX IDX_161498D3F64382E3 (car_model_id), INDEX IDX_161498D3584598A3 (operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(64) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, price_a INT NOT NULL, price_b INT NOT NULL, price_c INT NOT NULL, price_d INT NOT NULL, price_e INT NOT NULL, ips LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', created_at INT NOT NULL, updated_at INT DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), INDEX IDX_1483A5E98BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator_category (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_D26A6CFB584598A3 (operator_id), INDEX IDX_D26A6CFB12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator_limit (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, limit_id INT DEFAULT NULL, INDEX IDX_283E56B584598A3 (operator_id), INDEX IDX_283E56BA15D41FA (limit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fuel_type (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expert (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) DEFAULT NULL, short_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, soap_login VARCHAR(255) NOT NULL, soap_password VARCHAR(255) NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, short_name VARCHAR(255) NOT NULL, reg_number VARCHAR(255) NOT NULL, legal_address VARCHAR(255) NOT NULL, service_address VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, soap_login VARCHAR(255) NOT NULL, soap_password VARCHAR(255) NOT NULL, type INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, car_mark_id INT DEFAULT NULL, car_model_id INT DEFAULT NULL, operator_id INT DEFAULT NULL, property_type SMALLINT NOT NULL, legal_name VARCHAR(255) DEFAULT NULL, owner_first_name VARCHAR(255) DEFAULT NULL, owner_last_name VARCHAR(255) DEFAULT NULL, owner_middle_name VARCHAR(255) DEFAULT NULL, card_is_secondary TINYINT(1) DEFAULT NULL, card_is_duplicate_for INT DEFAULT NULL, is_archive TINYINT(1) DEFAULT NULL, body_number VARCHAR(255) DEFAULT NULL, date_of_diagnosis INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, valid_till INT DEFAULT NULL, note LONGTEXT DEFAULT NULL, registration_number VARCHAR(255) DEFAULT NULL, test_result TINYINT(1) DEFAULT NULL, test_type SMALLINT DEFAULT NULL, vin VARCHAR(255) DEFAULT NULL, frame_number VARCHAR(255) DEFAULT NULL, empty_mass INT DEFAULT NULL, max_mass INT DEFAULT NULL, fuel_type VARCHAR(255) DEFAULT NULL, braking_system VARCHAR(255) DEFAULT NULL, tyres VARCHAR(255) DEFAULT NULL, kilometres INT DEFAULT NULL, date_of_retest INT DEFAULT NULL, car_year INT DEFAULT NULL, document_type SMALLINT DEFAULT NULL, document_series VARCHAR(255) DEFAULT NULL, document_number VARCHAR(255) DEFAULT NULL, document_organization VARCHAR(255) DEFAULT NULL, document_date INT DEFAULT NULL, document_is_foreign TINYINT(1) DEFAULT NULL, expert VARCHAR(255) DEFAULT NULL, eaisto_number VARCHAR(255) DEFAULT NULL, eaisto_date INT DEFAULT NULL, status SMALLINT DEFAULT NULL, eaisto_id INT DEFAULT NULL, created_at INT DEFAULT NULL, issued_at INT DEFAULT NULL, updated_at INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, purchased TINYINT(1) DEFAULT NULL, car_mark_name VARCHAR(255) DEFAULT NULL, car_model_name VARCHAR(255) DEFAULT NULL, eaisto_status SMALLINT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, photo1 VARCHAR(255) DEFAULT NULL, photo2 VARCHAR(255) DEFAULT NULL, type SMALLINT DEFAULT NULL, INDEX IDX_A45BDDC1A76ED395 (user_id), INDEX IDX_A45BDDC112469DE2 (category_id), INDEX IDX_A45BDDC1113B0AF7 (car_mark_id), INDEX IDX_A45BDDC1F64382E3 (car_model_id), INDEX IDX_A45BDDC1584598A3 (operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brake_type (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, ru_type VARCHAR(50) NOT NULL, ru_path VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_model (id_car_model INT AUTO_INCREMENT NOT NULL, id_car_mark INT NOT NULL, name VARCHAR(255) NOT NULL, date_create INT DEFAULT NULL, date_update INT DEFAULT NULL, id_car_type INT NOT NULL, name_rus VARCHAR(255) DEFAULT NULL, INDEX name (name), INDEX id_car_mark (id_car_mark), INDEX id_car_type (id_car_type), PRIMARY KEY(id_car_model)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_limit ADD CONSTRAINT FK_9D541338A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_limit ADD CONSTRAINT FK_9D541338A15D41FA FOREIGN KEY (limit_id) REFERENCES limits (id)');
        $this->addSql('ALTER TABLE category_limit ADD CONSTRAINT FK_97A5A72912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category_limit ADD CONSTRAINT FK_97A5A729A15D41FA FOREIGN KEY (limit_id) REFERENCES limits (id)');
        $this->addSql('ALTER TABLE limits ADD CONSTRAINT FK_56C338BBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3113B0AF7 FOREIGN KEY (car_mark_id) REFERENCES car_mark (id_car_mark)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3F64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id_car_model)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E98BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE operator_category ADD CONSTRAINT FK_D26A6CFB584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_category ADD CONSTRAINT FK_D26A6CFB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE operator_limit ADD CONSTRAINT FK_283E56B584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_limit ADD CONSTRAINT FK_283E56BA15D41FA FOREIGN KEY (limit_id) REFERENCES limits (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1113B0AF7 FOREIGN KEY (car_mark_id) REFERENCES car_mark (id_car_mark)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1F64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id_car_model)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3113B0AF7');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1113B0AF7');
        $this->addSql('ALTER TABLE user_limit DROP FOREIGN KEY FK_9D541338A15D41FA');
        $this->addSql('ALTER TABLE category_limit DROP FOREIGN KEY FK_97A5A729A15D41FA');
        $this->addSql('ALTER TABLE operator_limit DROP FOREIGN KEY FK_283E56BA15D41FA');
        $this->addSql('ALTER TABLE category_limit DROP FOREIGN KEY FK_97A5A72912469DE2');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D312469DE2');
        $this->addSql('ALTER TABLE operator_category DROP FOREIGN KEY FK_D26A6CFB12469DE2');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC112469DE2');
        $this->addSql('ALTER TABLE user_limit DROP FOREIGN KEY FK_9D541338A76ED395');
        $this->addSql('ALTER TABLE limits DROP FOREIGN KEY FK_56C338BBA76ED395');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3A76ED395');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1A76ED395');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3584598A3');
        $this->addSql('ALTER TABLE operator_category DROP FOREIGN KEY FK_D26A6CFB584598A3');
        $this->addSql('ALTER TABLE operator_limit DROP FOREIGN KEY FK_283E56B584598A3');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1584598A3');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E98BAC62AF');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3F64382E3');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1F64382E3');
        $this->addSql('DROP TABLE car_mark');
        $this->addSql('DROP TABLE user_limit');
        $this->addSql('DROP TABLE category_limit');
        $this->addSql('DROP TABLE limits');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE operator_category');
        $this->addSql('DROP TABLE operator_limit');
        $this->addSql('DROP TABLE fuel_type');
        $this->addSql('DROP TABLE expert');
        $this->addSql('DROP TABLE operator');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE brake_type');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE car_model');
    }
}
