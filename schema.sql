CREATE DATABASE IF NOT EXISTS db_pubman;

USE db_pubman;

CREATE TABLE IF NOT EXISTS `tbl_editora` ( 
  `id` INTEGER NOT NULL AUTO_INCREMENT, 
  `nome` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
  `cidade` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
  PRIMARY KEY (`id`), 
  UNIQUE KEY `id` (`id`) 
);

CREATE TABLE IF NOT EXISTS `tbl_veiculo` ( 
  `id` INTEGER NOT NULL AUTO_INCREMENT, 
  `nome_completo` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
  `acronimo` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci , 
  `id_qualis` INTEGER, 
  PRIMARY KEY (`id`), 
  UNIQUE KEY `id`(`id`) 
);

CREATE TABLE IF NOT EXISTS `tbl_author` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name_as_cited` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);

CREATE TABLE IF NOT EXISTS `tbl_referencia` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `id_tipo` INTEGER NOT NULL,
  `id_editora` INTEGER NOT NULL,
  `id_veiculo` INTEGER NOT NULL,
  `ano` int(4) NOT NULL,
  `pagina_i` int(5),
  `pagina_f` int(5),
  INDEX (`id_tipo`), 
  INDEX (`id_editora`), 
  INDEX (`id_veiculo`),
  FOREIGN KEY (`id_editora`) REFERENCES `tbl_editora` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  FOREIGN KEY (`id_veiculo`) REFERENCES `tbl_veiculo` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  PRIMARY KEY (`id`), 
  UNIQUE KEY `id` (`id`)
) ;

CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(228) CHARACTER SET utf8 COLLATE utf8_unicode_ci
); 

