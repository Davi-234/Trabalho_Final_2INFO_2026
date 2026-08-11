-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema dev_share
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema dev_share
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `dev_share` DEFAULT CHARACTER SET utf8mb3 ;
USE `dev_share` ;

-- -----------------------------------------------------
-- Table `dev_share`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `senha_hash` VARCHAR(255) NOT NULL,
  `link_github` VARCHAR(255) NULL,
  `link_linkedin` VARCHAR(255) NULL,
  `perfil_lattes` VARCHAR(300) NULL,
  `tipo` ENUM('adm', 'comum') NOT NULL,
  `foto_perfil` VARCHAR(300) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_unique` (`email` ASC) VISIBLE,
  UNIQUE INDEX `link_github_unique` (`link_github` ASC) VISIBLE,
  UNIQUE INDEX `link_linkedin_unique` (`link_linkedin` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`projeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`projeto` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `visibilidade` TINYINT NOT NULL,
  `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descricao` VARCHAR(500) NOT NULL,
  `data_atualizacao` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nome` VARCHAR(200) NOT NULL,
  `status` ENUM('em_desenvolvimento', 'completo', 'pausado') NOT NULL,
  `nivel` ENUM('iniciante', 'intermediario', 'avancado') NOT NULL,
  `usuario_id` INT NOT NULL,
  `imagem_projeto` VARCHAR(300) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_projeto_usuario1_idx` (`usuario_id` ASC) VISIBLE,
  CONSTRAINT `fk_projeto_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`avaliacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`avaliacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nota` INT UNSIGNED NOT NULL CHECK (nota <= 10),
  `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `projeto_id` INT NOT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uk_avaliacao_usuario_projeto` (`usuario_id` ASC, `projeto_id` ASC) VISIBLE,
  INDEX `fk_avaliacao_projeto1_idx` (`projeto_id` ASC) VISIBLE,
  INDEX `fk_avaliacao_usuario_idx` (`usuario_id` ASC) VISIBLE,
  CONSTRAINT `fk_avaliacao_projeto`
    FOREIGN KEY (`projeto_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_avaliacao_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`comentario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`comentario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(500) NOT NULL,
  `data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` INT NOT NULL,
  `projeto_id` INT NOT NULL,
  `comentario_pai_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comentario_usuario1_idx` (`usuario_id` ASC) VISIBLE,
  INDEX `fk_comentario_projeto1_idx` (`projeto_id` ASC) VISIBLE,
  INDEX `fk_comentario_comentario1_idx` (`comentario_pai_id` ASC) VISIBLE,
  CONSTRAINT `fk_comentario_comentario`
    FOREIGN KEY (`comentario_pai_id`)
    REFERENCES `dev_share`.`comentario` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_comentario_projeto`
    FOREIGN KEY (`projeto_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_comentario_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`denuncia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`denuncia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `motivo` VARCHAR(100) NOT NULL,
  `data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pendente', 'removida', 'analisando') NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`denuncia_comentario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`denuncia_comentario` (
  `denuncia_id` INT NOT NULL,
  `denunciado_id` INT NOT NULL,
  `denunciante_id` INT NOT NULL,
  PRIMARY KEY (`denuncia_id`),
  INDEX `fk_table3_denuncia1_idx` (`denuncia_id` ASC) VISIBLE,
  INDEX `fk_denuncia_comentario_comentario1_idx` (`denunciado_id` ASC) VISIBLE,
  INDEX `fk_denuncia_comentario_usuario1_idx` (`denunciante_id` ASC) VISIBLE,
  CONSTRAINT `fk_denuncia_comentario_comentario`
    FOREIGN KEY (`denunciado_id`)
    REFERENCES `dev_share`.`comentario` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_denuncia_comentario_usuario`
    FOREIGN KEY (`denunciante_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_denuncia_comentario_denuncia`
    FOREIGN KEY (`denuncia_id`)
    REFERENCES `dev_share`.`denuncia` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`denuncia_projeto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`denuncia_projeto` (
  `denuncia_id` INT NOT NULL,
  `denunciado_id` INT NOT NULL,
  `denunciante_id` INT NOT NULL,
  PRIMARY KEY (`denuncia_id`),
  INDEX `fk_table1_denuncia1_idx` (`denuncia_id` ASC) VISIBLE,
  INDEX `fk_denuncia_projeto_projeto1_idx` (`denunciado_id` ASC) VISIBLE,
  INDEX `fk_denuncia_projeto_usuario1_idx` (`denunciante_id` ASC) VISIBLE,
  CONSTRAINT `fk_denuncia_projeto_projeto`
    FOREIGN KEY (`denunciado_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_denuncia_projeto_usuario`
    FOREIGN KEY (`denunciante_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_denuncia_projeto_denuncia`
    FOREIGN KEY (`denuncia_id`)
    REFERENCES `dev_share`.`denuncia` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`denuncia_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`denuncia_usuario` (
  `denuncia_id` INT NOT NULL,
  `denunciado_id` INT NOT NULL,
  `denunciante_id` INT NOT NULL,
  PRIMARY KEY (`denuncia_id`),
  INDEX `fk_table2_denuncia1_idx` (`denuncia_id` ASC) VISIBLE,
  INDEX `fk_denuncia_usuario_usuario1_idx` (`denunciado_id` ASC) VISIBLE,
  INDEX `fk_denuncia_usuario_usuario2_idx` (`denunciante_id` ASC) VISIBLE,
  CONSTRAINT `fk_denuncia_usuario_usuario1`
    FOREIGN KEY (`denunciado_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_denuncia_usuario_usuario`
    FOREIGN KEY (`denunciante_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_denuncia_usuario_denuncia`
    FOREIGN KEY (`denuncia_id`)
    REFERENCES `dev_share`.`denuncia` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`tag` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_unique` (`nome` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`projeto_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`projeto_tag` (
  `projeto_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`projeto_id`, `tag_id`),
  INDEX `fk_projeto_has_tag_tag1_idx` (`tag_id` ASC) VISIBLE,
  INDEX `fk_projeto_has_tag_projeto1_idx` (`projeto_id` ASC) VISIBLE,
  CONSTRAINT `fk_projeto_tag_projeto`
    FOREIGN KEY (`projeto_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_projeto_tag_tag`
    FOREIGN KEY (`tag_id`)
    REFERENCES `dev_share`.`tag` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`tecnologia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`tecnologia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_unique` (`nome` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`projeto_tecnologia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`projeto_tecnologia` (
  `tecnologias_id` INT NOT NULL,
  `projeto_id` INT NOT NULL,
  PRIMARY KEY (`tecnologias_id`, `projeto_id`),
  INDEX `fk_tecnologias_has_projeto_projeto1_idx` (`projeto_id` ASC) VISIBLE,
  INDEX `fk_tecnologias_has_projeto_tecnologias1_idx` (`tecnologias_id` ASC) VISIBLE,
  CONSTRAINT `fk_tecnologias_projeto_projeto`
    FOREIGN KEY (`projeto_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tecnologias_projeto_tecnologias`
    FOREIGN KEY (`tecnologias_id`)
    REFERENCES `dev_share`.`tecnologia` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`url`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`url` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `link` VARCHAR(255) NOT NULL,
  `projeto_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `link_unique` (`link` ASC) VISIBLE,
  INDEX `fk_url_projeto_idx` (`projeto_id` ASC) VISIBLE,
  CONSTRAINT `fk_url_projeto`
    FOREIGN KEY (`projeto_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `dev_share`.`visualizacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dev_share`.`visualizacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `data` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` INT NOT NULL,
  `projeto_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_visualizacao_usuario1_idx` (`usuario_id` ASC) VISIBLE,
  INDEX `fk_visualizacao_projeto1_idx` (`projeto_id` ASC) VISIBLE,
  CONSTRAINT `fk_visualizacao_projeto`
    FOREIGN KEY (`projeto_id`)
    REFERENCES `dev_share`.`projeto` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_visualizacao_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `dev_share`.`usuario` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;