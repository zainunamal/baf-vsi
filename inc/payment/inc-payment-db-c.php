<?php
// require_once 'Zend/Db.php';
// require_once 'Zend/Db/Table.php';
// require_once 'Zend/Db/Table/Row.php';

// conections with database Mysql 'Pdo_Mysql' / oracle 'Pdo_Oci'
// $db = Zend_Db::factory('Pdo_Mysql', array(
// 'host'     => 'localhost',
// 'username' => 'sw_user_devel',
// 'password' => 'sw_pwd_devel',
// 'dbname'   => 'VSI_SWITCHER_DEVEL'
// ));

// database connection
// EDIT: dipakai untuk GW POS
// define("ONPAYS_DBHOST", "localhost");   // database host
// define("ONPAYS_DBNAME", "VSI_SWITCHER_DEVEL");   // database name
// define("ONPAYS_DBUSER", "sw_user_devel"); // database username to connect to database
// define("ONPAYS_DBPWD", "sw_pwd_devel");   // database password to connect to database for supplied username

define("ONPAYS_DBHOST", "localhost");   // database host
define("ONPAYS_DBNAME", "gw_vpos");   // database name
define("ONPAYS_DBUSER", "root"); // database username to connect to database
define("ONPAYS_DBPWD", "");   // database password to connect to database for supplied username

?>