SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `tagger_dbmodel` ;
CREATE SCHEMA IF NOT EXISTS `tagger_dbmodel` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `tagger_dbmodel` ;

-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`roles` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`users` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`users` (
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
    REFERENCES `tagger_dbmodel`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`languages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`languages` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`languages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(10) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`epoques`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`epoques` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`epoques` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`catalogues`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`catalogues` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`catalogues` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`documents`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`documents` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`documents` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(70) NULL,
  `user_id` INT NULL,
  `language_id` INT NULL,
  `epoque_id` INT NULL,
  `catalogue_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_documents_users1_idx` (`user_id` ASC),
  INDEX `fk_documents_languages1_idx` (`language_id` ASC),
  INDEX `fk_documents_catalogues1_idx` (`catalogue_id` ASC),
  CONSTRAINT `fk_documents_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `tagger_dbmodel`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documents_languages1`
    FOREIGN KEY (`language_id`)
    REFERENCES `tagger_dbmodel`.`languages` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documents_epoques1`
    FOREIGN KEY (`epoque_id`)
    REFERENCES `tagger_dbmodel`.`epoques` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_documents_catalogues1`
    FOREIGN KEY (`catalogue_id`)
    REFERENCES `tagger_dbmodel`.`catalogues` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`sentences`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`sentences` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`sentences` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `document_id` INT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sentences_documents1_idx` (`document_id` ASC),
  CONSTRAINT `fk_sentences_documents1`
    FOREIGN KEY (`document_id`)
    REFERENCES `tagger_dbmodel`.`documents` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`words`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`words` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`words` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sentence_id` INT NULL,
  `text` VARCHAR(255) NULL,
  `stem` VARCHAR(255) NULL,
  `suffix` VARCHAR(255) NULL,
  `split` TINYINT(1) NULL,
  `position` INT NULL,
  `is_postposition` TINYINT(1) NULL,
  `postposition_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_words_sentences1_idx` (`sentence_id` ASC),
  INDEX `fk_words_words1_idx` (`postposition_id` ASC),
  CONSTRAINT `fk_words_sentences1`
    FOREIGN KEY (`sentence_id`)
    REFERENCES `tagger_dbmodel`.`sentences` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_words_words1`
    FOREIGN KEY (`postposition_id`)
    REFERENCES `tagger_dbmodel`.`words` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`word_annotation_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`word_annotation_types` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`word_annotation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `strict_choices` TINYINT(1) NULL,
  `multiple_choices` TINYINT(1) NULL,
  `word_reference` TINYINT(1) NULL,
  `description` TEXT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`word_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`word_annotations` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`word_annotations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text_value` VARCHAR(255) NULL,
  `numeric_value` INT NULL,
  `type_id` INT NULL,
  `word_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_word_annotations_word_annotation_types1_idx` (`type_id` ASC),
  INDEX `fk_word_annotations_words1_idx` (`word_id` ASC),
  CONSTRAINT `fk_word_annotations_word_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `tagger_dbmodel`.`word_annotation_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_word_annotations_words1`
    FOREIGN KEY (`word_id`)
    REFERENCES `tagger_dbmodel`.`words` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`word_annotation_type_choices`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`word_annotation_type_choices` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`word_annotation_type_choices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(255) NULL,
  `word_annotation_type_id` INT NULL,
  `description` TEXT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_word_annotation_type_choices_word_annotation_types1_idx` (`word_annotation_type_id` ASC),
  CONSTRAINT `fk_word_annotation_type_choices_word_annotation_types1`
    FOREIGN KEY (`word_annotation_type_id`)
    REFERENCES `tagger_dbmodel`.`word_annotation_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`sentence_annotation_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`sentence_annotation_types` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`sentence_annotation_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `description` TEXT NULL,
  `position` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`sentence_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`sentence_annotations` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`sentence_annotations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `text` TEXT NULL,
  `type_id` INT NULL,
  `sentence_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sentence_annotations_sentence_annotation_types1_idx` (`type_id` ASC),
  INDEX `fk_sentence_annotations_sentences1_idx` (`sentence_id` ASC),
  CONSTRAINT `fk_sentence_annotations_sentence_annotation_types1`
    FOREIGN KEY (`type_id`)
    REFERENCES `tagger_dbmodel`.`sentence_annotation_types` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sentence_annotations_sentences1`
    FOREIGN KEY (`sentence_id`)
    REFERENCES `tagger_dbmodel`.`sentences` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`word_annotation_type_choices_word_annotations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`word_annotation_type_choices_word_annotations` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`word_annotation_type_choices_word_annotations` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `word_annotation_id` INT NULL,
  `word_annotation_type_choice_id` INT NULL,
  INDEX `fk_word_annotations_has_word_annotation_type_choices_word_a_idx` (`word_annotation_type_choice_id` ASC),
  INDEX `fk_word_annotations_has_word_annotation_type_choices_word_a_idx1` (`word_annotation_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_word_annotations_has_word_annotation_type_choices_word_ann1`
    FOREIGN KEY (`word_annotation_id`)
    REFERENCES `tagger_dbmodel`.`word_annotations` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_word_annotations_has_word_annotation_type_choices_word_ann2`
    FOREIGN KEY (`word_annotation_type_choice_id`)
    REFERENCES `tagger_dbmodel`.`word_annotation_type_choices` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagger_dbmodel`.`help_sections`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagger_dbmodel`.`help_sections` ;

CREATE TABLE IF NOT EXISTS `tagger_dbmodel`.`help_sections` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `position` INT NULL,
  `text` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

DROP FUNCTION IF EXISTS string_array_size;
DROP FUNCTION IF EXISTS string_array_at;
DROP FUNCTION IF EXISTS within_distance;

DELIMITER $$

CREATE FUNCTION `tagger_dbmodel`.`string_array_size` (input varchar(500)) RETURNS INT
    DETERMINISTIC
BEGIN
    RETURN LENGTH(input) - LENGTH(REPLACE(input, ',','')) + 1;
END$$

CREATE FUNCTION `tagger_dbmodel`.`string_array_at` (input varchar(500), i INT) RETURNS INT
    DETERMINISTIC
BEGIN
    RETURN substring_index(substring_index(input,',',i-string_array_size(input)), ',',1);
END$$


CREATE FUNCTION `tagger_dbmodel`.`within_distance` (positions1str varchar(500), positions2str varchar(500), distance INT, two_way BOOL) RETURNS BOOL
    DETERMINISTIC
BEGIN
    DECLARE index1, index2, size1, size2, pos1, pos2 INT;
    DECLARE found BOOL;

    IF positions1str is null or length(positions1str) = 0 or positions2str is null or length(positions2str) = 0 THEN
        RETURN false;
    ELSEIF distance = 0 THEN
        RETURN true;
    ELSE
        SET index1 = 0;
        SET size1 = string_array_size(positions1str);
        SET size2 = string_array_size(positions2str);

        WHILE index1 < size1 DO
            SET pos1 = string_array_at(positions1str, index1);
            SET index2 = 0;
            WHILE index2 < size2 DO
                SET pos2 = string_array_at(positions2str, index2);

                IF pos2 > pos1 AND pos2 <= pos1 + distance THEN
                    RETURN true;
                END IF;
                IF two_way AND pos2 >= pos1 - distance AND pos2 < pos1 THEN
                    RETURN true;
                END IF;
                SET index2 = index2 + 1;
            END WHILE;

            SET index1 = index1 + 1;
        END WHILE;
        RETURN false;
    END IF;
END$$

DELIMITER ;
