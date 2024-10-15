<?php
// global variables
define("VERSION", "1.0.0.400");   // application version (DO NOT CHANGE THIS BY YOUR SELF OR AT YOUR OWN RISK!)

// identities
define("ONPAYS_ELECTRICITY_LOG_OWNER", "ONPAYS_CENTRAL_ELECTRICITY"); // DO NOT CHANGE THIS

// log purpose
//define("DEBUG", DEBUG_ERROR); // 0 = no debug, 2 = info, 4 = warning, 8 = error(can be OR'd)
define("DEBUG", 15|128); // 0 = no debug, 2 = info, 4 = warning, 8 = error(can be OR'd)

define("LOG_FILENAME", "C:\\laragon\\tmp\\".date('Ymd')."-DEVEL-scan-onpays-vsi-c.log");

// session
define("ONPAYS_SESSION_INTERVAL", 360000); // in miliseconds, default 100 hours = 360000 miliseconds

// authentication & authorization
define("ONPAYS_AUTH_ACTIVE", true);
define("ONPAYS_ACCESS_ACTIVE", true);
define("ONPAYS_BLOCKER_ACTIVE", true); // true: check if PP is blocked or not, continue check if subscriber is blocked at PP if PP was not blocked.

// development purpose (DO NOT CHANGE THIS VALUE)
//error_reporting(0);
?>
