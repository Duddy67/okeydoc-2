-- -----------------------------------------------------
-- Table `#__okeydoc_document`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__okeydoc_document`;
CREATE TABLE `#__okeydoc_document` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(225) NOT NULL ,
  `alias` VARCHAR(255) NOT NULL ,
  `intro_text` MEDIUMTEXT NULL ,
  `full_text` MEDIUMTEXT NULL ,
  `file_name` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(30) NOT NULL,
  `file_size` VARCHAR(20) NOT NULL,
  `file_path` TINYTEXT NOT NULL,
  `file_location` VARCHAR(10) NOT NULL,
  `file_icon` VARCHAR(20) NOT NULL,
  `author` VARCHAR(80) NOT NULL,
  `downloads` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `email_required` TINYINT(1) NOT NULL DEFAULT 0 ,
  `published` TINYINT NOT NULL DEFAULT 0 ,
  `catid` INT UNSIGNED NOT NULL ,
  `checked_out` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `ordering` INT NOT NULL DEFAULT 0 ,
  `asset_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `access` TINYINT NOT NULL DEFAULT 0 ,
  `params` TEXT NOT NULL ,
  `metakey` TEXT NOT NULL ,
  `metadesc` TEXT NOT NULL ,
  `metadata` TEXT NOT NULL ,
  `xreference` VARCHAR(50) NOT NULL ,
  `hits` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` INT UNSIGNED NOT NULL ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` INT UNSIGNED NOT NULL ,
  `language` CHAR(7) NOT NULL,
  PRIMARY KEY (`id`) ,
  INDEX `idx_access` (`access` ASC) ,
  INDEX `idx_created_by` (`created_by` ASC) ,
  INDEX `idx_published` (`published` ASC) ,
  INDEX `idx_check_out` (`checked_out` ASC) )
ENGINE = MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `#__okeydoc_document_linking`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__okeydoc_document_linking`;
CREATE TABLE `#__okeydoc_document_linking` (
  `doc_id` INT UNSIGNED NOT NULL,
  `item_id` INT UNSIGNED NOT NULL,
  `item_type` CHAR(8) NOT NULL ,
  `linking_type` CHAR(8) NOT NULL )
ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__okeydoc_archive`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__okeydoc_archive`;
CREATE TABLE `#__okeydoc_archive` (
  `doc_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `file_name` VARCHAR(255) NOT NULL ,
  `file_type` VARCHAR(30) NOT NULL ,
  `file_size` VARCHAR(20) NOT NULL ,
  `file_path` TINYTEXT NOT NULL ,
  `file_icon` VARCHAR(20) NOT NULL ,
  `downloads` INT UNSIGNED NOT NULL ,
  `version` TINYINT UNSIGNED NOT NULL ,
  `archived` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  INDEX `idx_doc_id` (`doc_id` ASC) )
ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `#__okeydoc_email`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `#__okeydoc_email`;
CREATE TABLE `#__okeydoc_email` (
  `doc_id` INT UNSIGNED NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `downloaded` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  INDEX `idx_doc_id` (`doc_id` ASC) )
ENGINE = MyISAM DEFAULT CHARSET=utf8;

