-- sample sql
CREATE  TABLE IF NOT EXISTS `user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `password_hash` VARCHAR(255) NOT NULL ,
  `hash_method` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `birthday` DATE NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;
