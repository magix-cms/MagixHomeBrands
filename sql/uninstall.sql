-- Nettoyage de la configuration des images
DELETE FROM `mc_config_img` WHERE `module_img` = 'magixhomebrands';

-- Suppression des tables (l'ordre est important à cause des clés étrangères)
DROP TABLE IF EXISTS `mc_plug_homebrands_content`;
DROP TABLE IF EXISTS `mc_plug_homebrands`;