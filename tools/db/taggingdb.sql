SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `taggingdb` ;
CREATE SCHEMA IF NOT EXISTS `taggingdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `taggingdb` ;

-- -----------------------------------------------------
-- Table `taggingdb`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`roles` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`users` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`users` (
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
    REFERENCES `taggingdb`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`languages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`languages` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`languages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(10) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`documents`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`documents` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`documents` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(70) NULL,
  `user_id` INT NULL,
  `language_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_documents_users1_idx` (`user_id` ASC),
  INDEX `fk_documents_languages1_idx` (`language_id` ASC),
  CONSTRAINT `fk_documents_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `taggingdb`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documents_languages1`
    FOREIGN KEY (`language_id`)
    REFERENCES `taggingdb`.`languages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`sentences`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`sentences` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`sentences` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `document_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sentences_documents1_idx` (`document_id` ASC),
  CONSTRAINT `fk_sentences_documents1`
    FOREIGN KEY (`document_id`)
    REFERENCES `taggingdb`.`documents` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`words`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`words` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`words` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sentence_id` INT NULL,
  `text` VARCHAR(255) NULL,
  `stem` VARCHAR(255) NULL,
  `suffix` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_words_sentences1_idx` (`sentence_id` ASC),
  CONSTRAINT `fk_words_sentences1`
    FOREIGN KEY (`sentence_id`)
    REFERENCES `taggingdb`.`sentences` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`word_annotation_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`word_annotation_types` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`word_annotation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `strict_choices` TINYINT(1) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`word_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`word_annotations` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`word_annotations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text_value` VARCHAR(255) NULL,
  `type_id` INT NULL,
  `word_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_word_annotations_word_annotation_types1_idx` (`type_id` ASC),
  INDEX `fk_word_annotations_words1_idx` (`word_id` ASC),
  CONSTRAINT `fk_word_annotations_word_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `taggingdb`.`word_annotation_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_word_annotations_words1`
    FOREIGN KEY (`word_id`)
    REFERENCES `taggingdb`.`words` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`word_annotation_type_choices`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`word_annotation_type_choices` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`word_annotation_type_choices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(255) NULL,
  `type_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_word_annotation_type_choices_word_annotation_types1_idx` (`type_id` ASC),
  CONSTRAINT `fk_word_annotation_type_choices_word_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `taggingdb`.`word_annotation_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`sentence_annotation_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`sentence_annotation_types` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`sentence_annotation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`sentence_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`sentence_annotations` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`sentence_annotations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text` TEXT NULL,
  `type_id` INT NULL,
  `sentence_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sentence_annotations_sentence_annotation_types1_idx` (`type_id` ASC),
  INDEX `fk_sentence_annotations_sentences1_idx` (`sentence_id` ASC),
  CONSTRAINT `fk_sentence_annotations_sentence_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `taggingdb`.`sentence_annotation_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sentence_annotations_sentences1`
    FOREIGN KEY (`sentence_id`)
    REFERENCES `taggingdb`.`sentences` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `taggingdb`.`word_annotation_type_choices_word_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `taggingdb`.`word_annotation_type_choices_word_annotations` ;

CREATE TABLE IF NOT EXISTS `taggingdb`.`word_annotation_type_choices_word_annotations` (
  `word_annotation_id` INT NOT NULL,
  `word_annotation_type_choice_id` INT NOT NULL,
  PRIMARY KEY (`word_annotation_id`, `word_annotation_type_choice_id`),
  INDEX `fk_word_annotations_has_word_annotation_type_choices_word_a_idx` (`word_annotation_type_choice_id` ASC),
  INDEX `fk_word_annotations_has_word_annotation_type_choices_word_a_idx1` (`word_annotation_id` ASC),
  CONSTRAINT `fk_word_annotations_has_word_annotation_type_choices_word_ann1`
    FOREIGN KEY (`word_annotation_id`)
    REFERENCES `taggingdb`.`word_annotations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_word_annotations_has_word_annotation_type_choices_word_ann2`
    FOREIGN KEY (`word_annotation_type_choice_id`)
    REFERENCES `taggingdb`.`word_annotation_type_choices` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;