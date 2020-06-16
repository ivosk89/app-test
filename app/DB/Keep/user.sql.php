<?php
$db = \App::_('mysqlkeep');

if (!$db->CheckTable("user")) {
    $db->QueryEcho("CREATE TABLE `user` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(127) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `login` (`login`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
}