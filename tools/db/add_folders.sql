-- -----------------------------------------------------
-- Table `testtaggingdb`.`folders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testtaggingdb`.`folders` ;

CREATE TABLE IF NOT EXISTS `testtaggingdb`.`folders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- ALTER TABLE `testtaggingdb`.`documents` ADD COLUMN `folder_id` INT NULL;
ALTER TABLE `testtaggingdb`.`documents` ADD INDEX `fk_documents_folders1_idx` (`folder_id` ASC),
ALTER TABLE `testtaggingdb`.`documents` ADD FOREIGN KEY (`folder_id`)  REFERENCES `testtaggingdb`.`folders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

