CREATE TABLE IF NOT EXISTS `Vendedor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Nome` VARCHAR(255) NOT NULL,
  `Email` VARCHAR(255) NOT NULL UNIQUE,
  `Senha` VARCHAR(255) NOT NULL,
  `Foto_perfil` VARCHAR(255) DEFAULT '',
  `Produtos_cadastrados` VARCHAR(255) DEFAULT '',
  `Chat` VARCHAR(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Nome` VARCHAR(255) NOT NULL,
  `Sobrenome` VARCHAR(255) NOT NULL DEFAULT '',
  `Dtnasc` DATE DEFAULT NULL,
  `Telefone` VARCHAR(20) DEFAULT '',
  `CPF` VARCHAR(14) DEFAULT '',
  `Email` VARCHAR(255) NOT NULL UNIQUE,
  `Senha` VARCHAR(255) NOT NULL,
  `Foto_perfil` VARCHAR(255) DEFAULT '',
  `Produtos_comprados` VARCHAR(255) DEFAULT '',
  `Produtos_carrinho` VARCHAR(255) DEFAULT '',
  `produtos_desejo` VARCHAR(255) DEFAULT '',
  `Chat` VARCHAR(255) DEFAULT '',
  `Tipo` VARCHAR(50) NOT NULL DEFAULT 'usuario',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Categoria` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Nome` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Produto` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_vendedor` INT UNSIGNED NOT NULL,
  `Nome` VARCHAR(255) NOT NULL,
  `Imagem` VARCHAR(255) NOT NULL,
  `Preco` DECIMAL(10,2) NOT NULL,
  `Frete` DECIMAL(10,2) NOT NULL,
  `Estoque` INT NOT NULL DEFAULT 0,
  `Descricao` TEXT NOT NULL,
  `id_categoria` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_produto_vendedor` (`id_vendedor`),
  KEY `idx_produto_categoria` (`id_categoria`),
  CONSTRAINT `fk_produto_vendedor`
    FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_produto_categoria`
    FOREIGN KEY (`id_categoria`) REFERENCES `Categoria` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

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

CREATE TABLE IF NOT EXISTS `Produtos_comprados` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_compra` INT UNSIGNED DEFAULT NULL,
  `id_produto` INT UNSIGNED NOT NULL,
  `Imagem` VARCHAR(255) NOT NULL,
  `Nome` VARCHAR(255) NOT NULL,
  `Preco` DECIMAL(10,2) NOT NULL,
  `Frete` DECIMAL(10,2) NOT NULL,
  `Quantidade` INT NOT NULL DEFAULT 1,
  `id_usuario` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comprados_compra` (`id_compra`),
  KEY `idx_comprados_produto` (`id_produto`),
  KEY `idx_comprados_usuario` (`id_usuario`),
  CONSTRAINT `fk_comprados_compra`
    FOREIGN KEY (`id_compra`) REFERENCES `Compra` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_comprados_produto`
    FOREIGN KEY (`id_produto`) REFERENCES `Produto` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_comprados_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Produtos_carrinho` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_produto` INT UNSIGNED NOT NULL,
  `Imagem` VARCHAR(255) NOT NULL,
  `Nome` VARCHAR(255) NOT NULL,
  `Preco` DECIMAL(10,2) NOT NULL,
  `Frete` DECIMAL(10,2) NOT NULL,
  `Quantidade` INT NOT NULL DEFAULT 1,
  `id_usuario` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_carrinho_produto` (`id_produto`),
  KEY `idx_carrinho_usuario` (`id_usuario`),
  CONSTRAINT `fk_carrinho_produto`
    FOREIGN KEY (`id_produto`) REFERENCES `Produto` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_carrinho_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Produto_desejo` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `id_produto` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_desejo_usuario` (`id_usuario`),
  KEY `idx_desejo_produto` (`id_produto`),
  CONSTRAINT `fk_desejo_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_desejo_produto`
    FOREIGN KEY (`id_produto`) REFERENCES `Produto` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Chat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_vendedor` INT UNSIGNED NOT NULL,
  `id_usuario` INT UNSIGNED NOT NULL,
  `data_inicio` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_chat_vendedor` (`id_vendedor`),
  KEY `idx_chat_usuario` (`id_usuario`),
  CONSTRAINT `fk_chat_vendedor`
    FOREIGN KEY (`id_vendedor`) REFERENCES `Vendedor` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_chat_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Mensagem` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_conversa` INT UNSIGNED NOT NULL,
  `id_remetente` INT UNSIGNED NOT NULL,
  `Mensagem` VARCHAR(255) NOT NULL,
  `data_hora` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mensagem_conversa` (`id_conversa`),
  KEY `idx_mensagem_remetente` (`id_remetente`),
  CONSTRAINT `fk_mensagem_conversa`
    FOREIGN KEY (`id_conversa`) REFERENCES `Chat` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_mensagem_remetente`
    FOREIGN KEY (`id_remetente`) REFERENCES `Usuario` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `Categoria` (`Nome`) VALUES
  ('Monitores'),
  ('TV'),
  ('Periféricos'),
  ('Ofertas'),
  ('Cadeiras')
ON DUPLICATE KEY UPDATE `Nome` = VALUES(`Nome`);

INSERT INTO `Usuario` (`id`, `Nome`, `Email`, `Senha`, `Tipo`) VALUES ('1', 'admin', 'admin@admin', '$2y$10$8sHx8xKvp27NRmBkw52xmuQeuTgtHMrPMbsUSZi2YlfSuM7YgiTKq', 'admin');

INSERT INTO Vendedor (id, Nome, Email, Senha) VALUES (1, 'Loja', 'loja@loja.com', '123456') ON DUPLICATE KEY UPDATE Nome = VALUES(Nome);

INSERT INTO Produto (id_vendedor, Nome, Imagem, Preco, Frete, Estoque, Descricao, id_categoria)
VALUES
(1, 'RTX 5090', 'http://localhost/ecommerce/assets/img/RTX5090.png', 24999.99, 30.00, 5, 'Placa de Vídeo ASUS RTX5090 ROG Astral Gaming OC NVIDIA GeForce, 32GB, GDDR7, ARGB, G-Sync, Ray Tracing, DLSS 4, HDR - 90YV0LW0-M0NA00', 3),
(1, 'Monitor LG Ultragear', 'http://localhost/ecommerce/assets/img/monitorlg.png', 999.90, 20.00, 10, 'Monitor Gamer LG Ultragear 24” LED, 180Hz, 1ms, HDR10, AMD Freesync, DisplayPort, HDMI - 24gs60f-b', 1),
(1, 'Mouse Gamer', 'http://localhost/ecommerce/assets/img/mousegamersemfio.webp', 549.90, 15.00, 20, 'Mouse Gamer Sem Fio Logitech G305 LIGHTSPEED, 12000 DPI, 6 Botões, Preto - 910-005281', 3),
(1, 'Ryzen 7 5800X3D', 'http://localhost/ecommerce/assets/img/ryzen75800x3d.webp', 2599.90, 30.00, 5, 'Processador AMD Ryzen 7 5800X3D, 3.4GHZ, 8-CORE, Cache 100MB, AM4 - 100-100000651POF', 3),
(1, 'SSD NVMe', 'http://localhost/ecommerce/assets/img/ssd.webp', 1094.99, 30.00, 5, 'ssd kingston nv3 1 tb m.2 2280 pcie 4.0 x4 nvme leitura 6000 mb/s gravação 4000 mb/s', 3),
(1, 'Smart TV TCL', 'http://localhost/ecommerce/assets/img/smarttv.webp', 3234.67, 10.00, 10, 'Smart TV TCL 55 Polegadas QLED Mini LED 4K C6K WiFi Bluetooth Google TV 4 HDMI 144Hz HDR10+ 55C6K', 2),
(1, 'PC Gamer', 'http://localhost/ecommerce/assets/img/pcgamer.webp', 5899.99, 15.00, 6, 'PC GAMER CAPTAIN AMD Ryzen 5 5500 / AMD Radeon RX 9060 XT / 16GB DDR4 / SSD 480GB', 4),
(1, 'Alexa', 'http://localhost/ecommerce/assets/img/alexa.webp', 399.90, 20.00, 9, 'Echo Dot 5ª geração Amazon, com Alexa, Smart Speaker, Preto - B09B8VGCR8', 4),
(1, 'Cadeira Gamer', 'http://localhost/ecommerce/assets/img/cadeiragamer.webp', 899.99, 25.00, 8, 'Cadeira Gamer XPG NEXUS, Até 125kg, Ajuste de Altura, Encosto Reclinável, Preto e Vermelho - NEXUS-RDCWW', 5);
