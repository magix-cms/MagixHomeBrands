-- Création de la table principale
CREATE TABLE IF NOT EXISTS `mc_plug_homebrands` (
    `id_brand` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `img_brand` varchar(255) DEFAULT NULL,
    `order_brand` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
    `date_register` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_brand`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table des traductions
CREATE TABLE IF NOT EXISTS `mc_plug_homebrands_content` (
    `id_brand_content` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_brand` int(10) UNSIGNED NOT NULL,
    `id_lang` smallint(3) UNSIGNED NOT NULL,
    `url_brand` varchar(255) DEFAULT NULL,
    `blank_brand` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    `title_brand` varchar(255) DEFAULT NULL,
    `desc_brand` text,
    `published_brand` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_brand_content`),
    KEY `id_lang` (`id_lang`),
    KEY `id_brand` (`id_brand`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajout des clés étrangères (Intégrité référentielle)
ALTER TABLE `mc_plug_homebrands_content`
    ADD CONSTRAINT `mc_plug_homebrands_content_ibfk_1` FOREIGN KEY (`id_brand`) REFERENCES `mc_plug_homebrands` (`id_brand`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mc_plug_homebrands_content_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Injection des formats d'images liés au plugin
INSERT INTO `mc_config_img` (`module_img`, `attribute_img`, `width_img`, `height_img`, `type_img`, `prefix_img`, `resize_img`) VALUES
('magixhomebrands', 'magixhomebrands', '150', '150', 'small', 's', 'basic'),
('magixhomebrands', 'magixhomebrands', '300', '300', 'medium','m', 'basic'),
('magixhomebrands', 'magixhomebrands', '600', '600', 'large','l', 'basic');