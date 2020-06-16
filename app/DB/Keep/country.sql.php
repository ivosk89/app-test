<?php
$db = \App::_('mysqlkeep');

if (!$db->CheckTable("country")) {
    $db->QueryEcho("CREATE TABLE `country` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'CountryID',
        `name` varchar(128) NOT NULL,
        PRIMARY KEY (`id`) USING BTREE
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8");
}