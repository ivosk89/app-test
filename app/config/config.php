<?php
define('ENV', 'dev');

define('DS', DIRECTORY_SEPARATOR);
define('DIR_ROOT', realpath(__DIR__.'/../../').DS);
define('DIR_APP', DIR_ROOT.'app'.DS);
define('DIR_CASHE', DIR_APP.'cache'.DS);
define('DIR_CONFIG', DIR_APP.'config'.DS);
define('DIR_MODELS', DIR_APP.'Models'.DS);
define('DIR_DB_KEEP', DIR_APP.'DB'.DS.'Keep'.DS);
define('DIR_DB_MAP', DIR_APP.'DB'.DS.'Map'.DS);
define('DIR_VIEW', DIR_APP.'Views'.DS);
define('DIR_PUBLIC', DIR_ROOT.'public'.DS);

chdir(DIR_APP);

$local = file_exists(DIR_CONFIG.'local.conf.php') ? require DIR_CONFIG.'local.conf.php' : array();
$options = ENV == 'prod' ? require DIR_CONFIG.'prod.conf.php' : require DIR_CONFIG.'dev.conf.php';
$global = require DIR_CONFIG.'global.conf.php';
$app['options'] = array_replace_recursive($global, $options, $local);