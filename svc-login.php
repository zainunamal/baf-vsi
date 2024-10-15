<?php
// PHP environment settings
set_time_limit(0);
date_default_timezone_set("Asia/Jakarta");

ob_start();
error_reporting(E_ALL);
// includes
$sRootPath = str_replace('\\', '/', str_replace(DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR.'svr', '', dirname(__FILE__))).'/';
require_once("inc/payment/constant.php");
require_once("inc/payment/db-payment.php");
require_once("inc/payment/ctools.php");
// require_once("inc/payment/json.php");
require_once("inc/payment/inc-payment-c.php");
require_once("inc/payment/inc-payment-db-c.php");
require_once("inc/central/session-central.php");
require_once("inc/central/user-central.php");

// start stopwatch
if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG)) {
	$iStart = microtime(true);
}

// global variables
$iCentralTS = time();
$iErrCode = 0;
$sErrMsg = '';
$sResponse = '';
$aRequest='';
$DBLink = NULL;
$DBConn = NULL;
$aCentralPrefs = NULL;
$json = new Services_JSON(SERVICES_JSON_SUPPRESS_ERRORS);
if(isset($_REQUEST["q"])){
	//echo base64_decode($_REQUEST["q"]);
	$aRequest=json_decode(base64_decode($_REQUEST["q"]));
	//var_dump($aRequest);
	$aResponse["rc"]=0;
	$aResponse["m"]="Login Success";
	// Payment related initialization
	SCANPayment_ConnectToDB($DBLink, $DBConn, ONPAYS_DBHOST, ONPAYS_DBUSER, ONPAYS_DBPWD, ONPAYS_DBNAME);
	if ($iErrCode != 0) {
	  $sErrMsg = 'FATAL ERROR: '.$sErrMsg;
	  if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
		error_log ("[".date("YmdHis")."][".(basename(__FILE__)).":".__LINE__."] [ERROR] [$iErrCode] $sErrMsg\n", 3, LOG_FILENAME);
	  $aResponse["rc"]=2;
	  $aResponse["m"]="Can't connect to Auth DB";
	}
	if($aResponse["rc"]==0){
		$User = new SCANCentralUser(DEBUG, LOG_FILENAME, $DBLink);
		$aResponse=login($aRequest->u,$aRequest->p);
	}
	$sResponse=(json_encode($aResponse));

}else{
	$sResponse=("{rc:1,m:'Invalid Request Parameter'}");
}

echo base64_encode($sResponse);
//echo ($sResponse);

function login($usr, $pwd) {
	global $User;
	
	$sUID = '';
	$aResponse = array();
	$auth = $User->IsAuthUserRecon($usr, $pwd, $sUID);
	if ($auth) {
		// get user info
		$arUser = $User->GetUserDetail($sUID);
		//var_dump($arUser);
		$blocked = $arUser["blocked"];
		$multLogin = $arUser["multLogin"];
		
		if ($blocked) {
			// User blocked
			$aResponse["rc"] =3;
			$aResponse["m"] = "Username '$usr' is blocked";
		}else{
			$aResponse["rc"] =0;
			$aResponse["m"] = "Login Success";
		}
	} else {
		// Jika $sUID == null, username tidak terdaftar
		// Jika $sUID != null, password salah
		$usr = nullable_htmlspecialchar($usr, ENT_QUOTES);
		if ($sUID == null) {
			// No username			
			$aResponse["rc"] =4;
			$aResponse["m"] = "Username '$usr' not registered";
		} else {
			$aResponse["rc"] =5;
			$aResponse["m"] = "Wrong Password for '$usr'";
		}
	}
	
	return $aResponse;
}

?>