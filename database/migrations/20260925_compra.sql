-- Migração do checkout para bancos já existentes.
-- A alteração é idempotente para MariaDB/MySQL e não remove compras antigas.

CREATE TABLE IF NOT EXISTS `Compra` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `Data_compra` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` VARCHAR(50) NOT NULL DEFAULT 'Em análise de pagamento',
  `Valor_total` DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_compra_usuario` (`id_usuario`),
  CONSTRAINT `fk_compra_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

SET @migration_sql = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'Produtos_comprados'
     AND COLUMN_NAME = 'id_compra') = 0,
  'ALTER TABLE `Produtos_comprados` ADD COLUMN `id_compra` INT UNSIGNED DEFAULT NULL AFTER `id`, ADD KEY `idx_comprados_compra` (`id_compra`)',
  'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;

SET @migration_sql = IF(
  (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
   WHERE CONSTRAINT_SCHEMA = DATABASE()
     AND TABLE_NAME = 'Produtos_comprados'
     AND CONSTRAINT_NAME = 'fk_comprados_compra'
     AND CONSTRAINT_TYPE = 'FOREIGN KEY') = 0,
  'ALTER TABLE `Produtos_comprados` ADD CONSTRAINT `fk_comprados_compra` FOREIGN KEY (`id_compra`) REFERENCES `Compra` (`id`) ON UPDATE CASCADE ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE migration_stmt FROM @migration_sql;
EXECUTE migration_stmt;
DEALLOCATE PREPARE migration_stmt;
