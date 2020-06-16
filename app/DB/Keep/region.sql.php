<?php
$db = \App::_('mysqlkeep');
if (!$db->CheckField("country","id")) {
    $db->retry_update = true;
    return;
}
if (!$db->CheckTable("region")) {
    $db->QueryEcho("CREATE TABLE `region` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'RegionID',
        `name` varchar(128) CHARACTER SET utf8 NOT NULL,
        `country_id` int(11) DEFAULT NULL COMMENT 'CountryID',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `fk_country_id` (`country_id`) USING BTREE,
        CONSTRAINT `fk_country_id` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
      ) ENGINE=InnoDB  DEFAULT CHARSET=latin1");
}