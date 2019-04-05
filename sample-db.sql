-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mpesa_test
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mpesa_test
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mpesa_test` ;
USE `mpesa_test` ;

-- -----------------------------------------------------
-- Table `mpesa_test`.`callback`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mpesa_test`.`callback` (
  `callback_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `response_body` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`callback_id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
