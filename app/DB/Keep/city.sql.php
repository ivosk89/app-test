<?php
$db = \App::_('mysqlkeep');
if (!$db->CheckField("region","id")) {
    $db->retry_update = true;
    return;
}
if (!$db->CheckTable("city")) {
    $db->QueryEcho("CREATE TABLE `city` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'CityID',
        `name` varchar(128) NOT NULL,
        `region_id` int(11) DEFAULT NULL COMMENT 'RegionID',
        PRIMARY KEY (`id`) USING BTREE,
        KEY `fk_region_id` (`region_id`) USING BTREE,
        CONSTRAINT `fk_region_id` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
}