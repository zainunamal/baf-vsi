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

define("ONPAYS_SW_DBHOST", "172.20.172.105");   // database host
define("ONPAYS_SW_DBNAME", "VSI_SWITCHER");   // database name
define("ONPAYS_SW_DBUSER", "sw_user"); // database username to connect to database
define("ONPAYS_SW_DBPWD", "sw_pwd");   // database password to connect to database for supplied username

?>
