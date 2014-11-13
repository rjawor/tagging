SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `testtaggingdb` ;
CREATE SCHEMA IF NOT EXISTS `testtaggingdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `testtaggingdb` ;

-- -----------------------------------------------------
-- Table `testtaggingdb`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`roles` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`users` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NULL,
  `password` VARCHAR(255) NULL,
  `created` DATETIME NULL,
  `role_id` INT NULL,
  `last_login` DATETIME NULL,
  `current_document_id` INT NULL,
  `current_document_offset` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_users_roles_idx` (`role_id` ASC),
  CONSTRAINT `fk_users_roles`
    FOREIGN KEY (`role_id`)
    REFERENCES `testtaggingdb`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`languages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`languages` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`languages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(10) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`documents`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`documents` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`documents` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(70) NULL,
  `user_id` INT NULL,
  `language_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_documents_users1_idx` (`user_id` ASC),
  INDEX `fk_documents_languages1_idx` (`language_id` ASC),
  CONSTRAINT `fk_documents_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `testtaggingdb`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documents_languages1`
    FOREIGN KEY (`language_id`)
    REFERENCES `testtaggingdb`.`languages` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`sentences`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`sentences` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`sentences` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `document_id` INT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sentences_documents1_idx` (`document_id` ASC),
  CONSTRAINT `fk_sentences_documents1`
    FOREIGN KEY (`document_id`)
    REFERENCES `testtaggingdb`.`documents` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`words`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`words` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`words` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sentence_id` INT NULL,
  `text` VARCHAR(255) NULL,
  `stem` VARCHAR(255) NULL,
  `suffix` VARCHAR(255) NULL,
  `split` TINYINT(1) NULL DEFAULT 0,
  `position` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_words_sentences1_idx` (`sentence_id` ASC),
  CONSTRAINT `fk_words_sentences1`
    FOREIGN KEY (`sentence_id`)
    REFERENCES `testtaggingdb`.`sentences` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`word_annotation_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`word_annotation_types` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`word_annotation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `strict_choices` TINYINT(1) NULL,
  `multiple_choices` TINYINT(1) NULL,
  `description` TEXT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`word_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`word_annotations` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`word_annotations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text_value` VARCHAR(255) NULL,
  `type_id` INT NULL,
  `word_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_word_annotations_word_annotation_types1_idx` (`type_id` ASC),
  INDEX `fk_word_annotations_words1_idx` (`word_id` ASC),
  CONSTRAINT `fk_word_annotations_word_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `testtaggingdb`.`word_annotation_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_word_annotations_words1`
    FOREIGN KEY (`word_id`)
    REFERENCES `testtaggingdb`.`words` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`word_annotation_type_choices`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`word_annotation_type_choices` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`word_annotation_type_choices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(255) NULL,
  `word_annotation_type_id` INT NULL,
  `description` TEXT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_word_annotation_type_choices_word_annotation_types1_idx` (`word_annotation_type_id` ASC),
  CONSTRAINT `fk_word_annotation_type_choices_word_annotation_types1`
    FOREIGN KEY (`word_annotation_type_id`)
    REFERENCES `testtaggingdb`.`word_annotation_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`sentence_annotation_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`sentence_annotation_types` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`sentence_annotation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` TEXT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`sentence_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`sentence_annotations` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`sentence_annotations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text` TEXT NULL,
  `type_id` INT NULL,
  `sentence_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sentence_annotations_sentence_annotation_types1_idx` (`type_id` ASC),
  INDEX `fk_sentence_annotations_sentences1_idx` (`sentence_id` ASC),
  CONSTRAINT `fk_sentence_annotations_sentence_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `testtaggingdb`.`sentence_annotation_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sentence_annotations_sentences1`
    FOREIGN KEY (`sentence_id`)
    REFERENCES `testtaggingdb`.`sentences` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `testtaggingdb`.`word_annotation_type_choices_word_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`word_annotation_type_choices_word_annotations` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`word_annotation_type_choices_word_annotations` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `word_annotation_id` INT NULL,
  `word_annotation_type_choice_id` INT NULL,
  INDEX `fk_word_annotations_has_word_annotation_type_choices_word_a_idx` (`word_annotation_type_choice_id` ASC),
  INDEX `fk_word_annotations_has_word_annotation_type_choices_word_a_idx1` (`word_annotation_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_word_annotations_has_word_annotation_type_choices_word_ann1`
    FOREIGN KEY (`word_annotation_id`)
    REFERENCES `testtaggingdb`.`word_annotations` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_word_annotations_has_word_annotation_type_choices_word_ann2`
    FOREIGN KEY (`word_annotation_type_choice_id`)
    REFERENCES `testtaggingdb`.`word_annotation_type_choices` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
