<?php

/* === PHP environment settings === */
set_time_limit(0);
date_default_timezone_set("Asia/Jakarta");
ob_start();
error_reporting(E_ALL);

/* === File includes === */
// $sRootPath = str_replace('\\', '/', str_replace(DIRECTORY_SEPARATOR.'pc'.DIRECTORY_SEPARATOR.'svr'.DIRECTORY_SEPARATOR.'central', '', dirname(__FILE__))).'/';
$sRootPath = "";
require_once("helper.php");
require_once("inc/payment/constant.php");
require_once("inc/payment/db-payment.php");
require_once("inc/payment/ctools.php");
// require_once("inc/payment/json.php");
require_once("inc/payment/inc-dms-c.php");
require_once("inc/payment/inc-payment-c.php");
require_once("inc/payment/inc-payment-db-c.php");
require_once("inc/central/session-central.php");
require_once("inc/central/user-central.php");
require_once("inc/central/setting-central.php");
require_once("inc/central/dbspec-central.php");

/* === Start time === */
$MAINstartRender = microtime(1);
if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG)) {
	$iStart = microtime(true);
}

/* === Global variables === */
$iCentralTS = time();
$iErrCode = 0;
$sErrMsg = '';
$sResponse = '';
$DBLink = NULL;
$DBConn = NULL;
// Module
$userModuleView = null;
$moduleOnly1 = "";
$grantedModule = false;
// Function
$userFuncPage = null;
// Style path
$MAINtitle = null;
$MAINsubTitle = null;
$MAINstylePath = null;
$MAINfooterText = null;
$MAINstyle = null;

/* === Database Central Connection === */
SCANPayment_ConnectToDB($DBLink, $DBConn, ONPAYS_DBHOST, ONPAYS_DBUSER, ONPAYS_DBPWD, ONPAYS_DBNAME);
if ($iErrCode != 0) {
	$sErrMsg = 'FATAL ERROR: ' . $sErrMsg;
	if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
		error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] [$iErrCode] $sErrMsg\n", 3, LOG_DMS_FILENAME);
	exit(1);
}
$Session = new SCANCentralDBSession(DEBUG, LOG_DMS_FILENAME, $DBLink, ONPAYS_SESSION_INTERVAL);
$User = new SCANCentralUser(DEBUG, LOG_DMS_FILENAME, $DBLink);
$Setting = new SCANCentralSetting(DEBUG, LOG_DMS_FILENAME, $DBLink);

/* === Get cookie data === */
$cData = (@isset($_COOKIE['centraldata']) ? $_COOKIE['centraldata'] : '');
if (!isEmpty($cData)) {
	$decData = base64_decode($cData);
	if ($decData) {
		$data = json_decode($decData);
	}
}

/* === Parameter read === */
// NEW: parameters are able called directly with $<key>
$a = "";
$m = "";
$f = "";
$p = "";
// Management
$setting = "";
$m = "";
$sm = "";
$a = "";
$i = "";
// NEW: profile
$userProfile = "";
$param = (isset($_REQUEST['param']) ? trim($_REQUEST['param']) : '');
if ($param != "") {
	// NEW: parameter base64
	$decParam = base64_decode($param);
	$arParam = explode("&", $decParam);

	foreach ($arParam as $iParam) {
		$indexEqual = strpos($iParam, "=");

		$MAINkey = substr($iParam, 0, $indexEqual);
		$MAINvalue = substr($iParam, $indexEqual + 1);

		// NEW: masukkan ke request
		$_REQUEST[$MAINkey] = $MAINvalue;
	}
}
// Take all parameter to variables
foreach ($_REQUEST as $MAINkey => $MAINvalue) {
	$$MAINkey = $MAINvalue;
}

// Parameter
$area = $a;
$application = $area;
$module = $m;
$function = $f;
$mode = $m;
$subMode = $sm;

// Preserve backward compatibility
if (!isset($action)) {
	$action = $a;
}
if (!isset($ppid)) {
	$ppid = $p;
}
if (!isset($id)) {
	$id = $i;
}

/* === Local Function === */
// Check emptiness of a string variable
function isEmpty($st)
{
	return (strlen(trim($st)) == 0);
}

// Check admin privilege from a user
function isAdmin($uid)
{
	global $User;

	return $User->IsAdmin($uid);
}

// Check supervisor privilege from a user
function isSupervisor($uid)
{
	global $User;

	return $User->IsSupervisor($uid);
}

// Check session data
function stillInSession()
{
	global $Session, $data;
	$inSession = -2;

	if ($data) {
		$uid = $data->uid;
		$sid = $data->session;

		$inSession = $Session->CheckSession($uid, $sid);
	}

	return ($inSession == 0);
}

// Print user, area, and module menu
function printBody()
{
	global $area, $module, $function, $MAINstyle, $setting, $data, $mode, $subMode, $id, $userProfile;

	// Get logo
	$logo = $MAINstyle["littleLogo"];

	// Header
	echo "	<div id='container'>\n";
	echo "		<div id='main' class='clearfix'>\n";
	echo "			\n";
	echo "			<!-- Header -->\n";
	echo "			<div id='header'>\n";

	// User
	printUser();

	// Logo
	echo "				<div id='logo'>\n";
	echo "					<a href='main.php?";
	$url64 = "";
	// NEW: base64 parameter
	if (isset($area) && $area != "") {
		$url64 = "a=$area";
	}
	if (isset($module) && $module != "") {
		$url64 .= "&m=$module";
	}
	$url64 = base64_encode($url64);
	echo "param=$url64";
	echo "'>\n";
	echo "						<img src='$logo' alt='DMS Logo' border='0'></img>\n";
	echo "					</a>\n";
	echo "				</div>\n";

	// End of header
	echo "			</div>\n";

	if ($setting == "1") {
		// SETTING
		echo "			\n";
		echo "			<!-- Sub menu -->\n";
		echo "			<div id='subMenu'>\n";
		echo "				<a href='main.php'>&laquo;&nbsp;&nbsp;Main page</a>&nbsp;&nbsp;\n";

		$uid = $data->uid;
		if (isAdmin($uid)) {
			if ($mode) {
				$url64 = base64_encode("setting=1");
				echo "				<a href='main.php?param=$url64'>Management</a>&nbsp;&nbsp;\n";
				if ($mode == "s") {
					$url64 = base64_encode("setting=1&m=s");
					echo "				<a href='main.php?param=$url64'><b>Setting&nbsp;&rsaquo;</b></a>\n";
				} else if ($mode == "a") {
					if (substr($subMode, 1, 1) == "c") {
						$url64 = base64_encode("setting=1&m=a");
						echo "				<a href='main.php?param=$url64'>Application</a>&nbsp;&nbsp;\n";

						$url64 = base64_encode("setting=1&m=a&sm=lc&i=$id");
						echo "				<a href='main.php?param=$url64'><b>Configuration&nbsp;&rsaquo;</b></a>\n";
					} else {
						$url64 = base64_encode("setting=1&m=a");
						echo "				<a href='main.php?param=$url64'><b>Application&nbsp;&rsaquo;</b></a>\n";
					}
				} else if ($mode == "m") {
					if (substr($subMode, 1, 1) == "c") {
						$url64 = base64_encode("setting=1&m=m");
						echo "				<a href='main.php?param=$url64'>Module</a>&nbsp;&nbsp;\n";

						$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
						echo "				<a href='main.php?param=$url64'><b>Configuration&nbsp;&rsaquo;</b></a>\n";
					} else {
						$url64 = base64_encode("setting=1&m=m");
						echo "				<a href='main.php?param=$url64'><b>Module&nbsp;&rsaquo;</b></a>\n";
					}
				} else if ($mode == "l") {
					if (substr($subMode, 1, 1) == "k") {
						$url64 = base64_encode("setting=1&m=l");
						echo "				<a href='main.php?param=$url64'>PP Module</a>&nbsp;&nbsp;\n";

						$url64 = base64_encode("setting=1&m=l&sm=lk");
						echo "				<a href='main.php?param=$url64'><b>Key&nbsp;&rsaquo;</b></a>\n";
					} else {
						$url64 = base64_encode("setting=1&m=l");
						echo "				<a href='main.php?param=$url64'><b>PP Module&nbsp;&rsaquo;</b></a>\n";
					}
				} else if ($mode == "u") {
					$url64 = base64_encode("setting=1&m=u");
					echo "				<a href='main.php?param=$url64'><b>User&nbsp;&rsaquo;</b></a>\n";
				} else if ($mode == "d") {
					if (substr($subMode, 1, 1) == "c") {
						$url64 = base64_encode("setting=1&m=d");
						echo "				<a href='main.php?param=$url64'>Database</a>&nbsp;&nbsp;\n";

						$url64 = base64_encode("setting=1&m=d&sm=lc&i=$id");
						echo "				<a href='main.php?param=$url64'><b>Configuration&nbsp;&rsaquo;</b></a>\n";
					} else {
						$url64 = base64_encode("setting=1&m=d");
						echo "				<a href='main.php?param=$url64'><b>Database&nbsp;&rsaquo;</b></a>\n";
					}
				} else if ($mode == "r") {
					$url64 = base64_encode("setting=1&m=r");
					echo "				<a href='main.php?param=$url64'><b>Role&nbsp;&rsaquo;</b></a>\n";
				} else if ($mode == "f") {
					$url64 = base64_encode("setting=1&m=f");
					echo "				<a href='main.php?param=$url64'><b>Function&nbsp;&rsaquo;</b></a>\n";
				} else if ($mode == "h") {
					$url64 = base64_encode("setting=1&m=h");
					echo "				<a href='main.php?param=$url64'><b>Help&nbsp;&rsaquo;</b></a>\n";
				}
			} else {
				$url64 = base64_encode("setting=1");
				echo "				<a href='main.php?param=$url64'><b>Management&nbsp;&rsaquo;</b></a>\n";
			}

			// NEW: Supervisor
		} else if (isSupervisor($uid)) {
			if ($mode) {
				$url64 = base64_encode("setting=1");
				echo "				<a href='main.php?param=$url64'>Management</a>&nbsp;&nbsp;\n";
				if ($mode == "u") {
					$url64 = base64_encode("setting=1&m=u");
					echo "				<a href='main.php?param=$url64'><b>User&nbsp;&rsaquo;</b></a>\n";
				} else if ($mode == "h") {
					$url64 = base64_encode("setting=1&m=h");
					echo "				<a href='main.php?param=$url64'><b>Help&nbsp;&rsaquo;</b></a>\n";
				}
			} else {
				$url64 = base64_encode("setting=1");
				echo "				<a href='main.php?param=$url64'><b>Management&nbsp;&rsaquo;</b></a>\n";
			}
		} else {
			echo "				<div class='error'>Illegal access!</div>\n";
		}
		echo "			</div>\n";
	} else if ($userProfile == "1") {
		// NEW: Do nothing :|
		// Any body content will be printed in below

	} else {
		// Menu application/area and module
		$bOK = printAreaAndModule();
		if (!$bOK) {
			return;
		}
	}

	// Loading text
	echo "			<div id='loadingText' style='display:none; padding:20px'>Loading... &nbsp;<img src='image/icon/wait.gif' alt='..'></img></div>\n";
}

// Print user info, logout, and management link (admin only)
function printUser()
{
	global $data, $setting, $userProfile;

	if ($data) {
		$uid = $data->uid;
		$uname = $data->uname;

		// Start
		echo "				<!-- User menu -->\n";
		echo "				<div id='user-menu'>\n";

		// User name
		echo "					User: ";
		echo $data->uname . "&nbsp;(";
		$url64 = base64_encode("logout=1");
		echo "<a href='main.php?param=$url64'>logout</a>";
		echo ")\n";

		// User profile
		if ($userProfile == "") {
			$url64 = base64_encode("userProfile=1");
			echo "					&nbsp;\n";
			echo "					<b><a href='main.php?param=$url64'>User Profile</a></b>\n";
		}

		// ADMIN ONLY: Management
		if ((isAdmin($uid) || isSupervisor($uid)) && $setting == "") {
			$url64 = base64_encode("setting=1");
			echo "					&nbsp;\n";
			echo "					<b><a href='main.php?param=$url64'>Management</a></b>\n";
		}

		// End
		echo "				<!-- End of user menu -->\n";
		echo "				</div>\n";
	}
}

// Print user info, logout, and management link (admin only)
function printAreaAndModule()
{
	global $data, $User, $area, $module, $grantedModule, $userModuleView, $moduleOnly1, $MAINstyle;
	$grantedArea = false;

	if ($data) {
		$uid = $data->uid;
		// Area
		if (!isEmpty($area) && $User->IsAreaGranted($uid, $area)) {
			$areaName = $User->GetAreaName($area);
			$grantedArea = true;

			// NEW: to render
			$appName = $areaName;
		} else {
			$appName = "-";
		}

		// Module
		if (!isEmpty($module) && $User->IsModuleGranted($uid, $area, $module)) {
			$moduleName = $User->GetModuleName($module);
			$grantedModule = true;

		} else {
			$moduleName = "-";
		}

		// Option area
		$arApp = null;
		$bOK = $User->GetArea($uid, $areaIds);
		if ($bOK && $areaIds != null) {
			if (count($areaIds) == 1) {
				if ($area == "") {
					// Redirect ke area
					$areaId = $areaIds[0]["id"];
					$url64 = base64_encode("a=$areaId");

					header("Location: main.php?param=$url64");
					// echo "					<meta http-equiv='REFRESH' content='0;url=main.php?param=$url64'>\n";
				}
			}

			// to render
			$arApp = $areaIds;
		}

		// Option module
		$arModule = null;
		$bOK = $User->GetModuleInArea($uid, $area, $moduleIds);
		if ($bOK && $moduleIds != null) {
			$nModule = 0;

			// to render
			$i = 0;
			$arModule = array();

			foreach ($moduleIds as $moduleId) {
				$id = $moduleId["id"];
				$name = $moduleId["name"];
				if ($User->IsModuleGranted($uid, $area, $id)) {
					if ($nModule == 0) {
						$moduleOnly1 = $id;
					}
					$nModule++;

					// to render
					$arModule[$i]["id"] = $id;
					$arModule[$i]["name"] = $name;
					$i++;
				}
			}

			if ($nModule == 1 && $module != $moduleOnly1) {
				// Redirect ke module
				$url64 = base64_encode("a=$area&m=$moduleOnly1");
				// echo "					<meta http-equiv='REFRESH' content='0;url=main.php?param=$url64'>\n";
				header("Location: main.php?param=$url64");
			}
		}

		// Get View module page
		if ($grantedModule) {
			$userModuleView = $User->GetModuleView($module);
			$userModuleView = "view/" . $userModuleView;
		}

		// Get render menu
		$renderMenu = $MAINstyle["renderMenu"];
		include($renderMenu);

		echo "			\n";
		echo "			<!-- Menu application and module -->\n";
		renderMenu($area, $appName, $arApp, $module, $moduleName, $arModule);
	}
	return ($grantedArea && $grantedModule);
}

// Get function page filename
function getFunctionPage()
{
	global $data, $User, $area, $function, $userFuncPage;
	$bOK = false;

	if ($data) {
		$uid = $data->uid;
		$arFunc = array();

		if ($User->GetFunctionName($function, $arFunc)) {
			$module = $arFunc["CTR_FUNC_MID"];
			$grantedFunc = $User->IsFunctionGranted($uid, $area, $module, $function);

			if ($grantedFunc) {
				// $funcName = $arFunc["CTR_FUNC_NAME"];
				$funcPage = $arFunc["CTR_FUNC_PAGE"];
				$funcPage = "function/" . $funcPage;

				if (!isEmpty($funcPage) && file_exists($funcPage)) {
					$bOK = true;
					$userFuncPage = $funcPage;
				}
			}
		}
	}

	return $bOK;
}

// Print image menu
function printOption($arParam = null)
{
	global $arFunction;

	if ($arFunction == null) {
		echo ".";
		return;
	}

	$first = true;
	$result = "";
	foreach ($arFunction as $iLink) {
		$urlParam = $iLink["urlParam"];
		$image = $iLink["image"];
		$name = $iLink["name"];

		if ($arParam != null) {
			foreach ($arParam as $iKey => $iValue) {
				$urlParam .= "&" . $iKey . "=" . $iValue;
			}
		}

		if ($first) {
			echo "				&nbsp;\n";
			$first = false;
		}

		$url64 = base64_encode($urlParam);
		$result .= "<a href='main.php?param=$url64'><img class='imageHref' src='$image' title='$name' alt='$name' border='0' /></a>&nbsp;&nbsp;\n";
	}

	return $result;
}

function readSetting()
{
	global $User, $Setting, $data, $MAINtitle, $MAINfooterText, $MAINstyle;

	// Read setting
	$arSetting = $Setting->GetSettingDetail();
	if ($arSetting != null) {
		$MAINtitle = $arSetting["title"];
		$MAINfooterText = $arSetting["footer"];
	}

	// Use default
	if ($MAINtitle == null || trim($MAINtitle) == "") {
		$MAINtitle = DEFAULT_TITLE;
		$MAINfooterText = DEFAULT_FOOTER_TEXT;
	}

	// Read style
	$styleFolder = null;
	if ($data != null) {
		$uid = $data->uid;
		$styleFolder = $User->GetLayoutUser($uid);
	}
	if ($styleFolder == null) {
		$styleFolder = "default";
	}
	$useDefault = false;
	if ($styleFolder == null) {
		// Style not set, use default
		$useDefault = true;
	} else {
		if (!file_exists("style/$styleFolder")) {
			// Style path not exist, use default
			$useDefault = true;
		}
	}

	// Parse XML
	$arDefault = null;
	if (!$useDefault) {
		$MAINstyle = $User->GetLayoutFiles($styleFolder);
		$arDefault = $User->GetLayoutFiles(DEFAULT_STYLE_PATH);
	}

	// No XML Declaration found
	if ($useDefault || $MAINstyle == null) {
		$MAINstyle = $User->GetLayoutFiles(DEFAULT_STYLE_PATH);

		$styleFolder = DEFAULT_STYLE_PATH;
	}

	// Add style path to all component
	foreach ($MAINstyle as $iKey => $iValue) {
		$MAINstyle[$iKey] = "style/$styleFolder/$iValue";
	}

	// Check existence of style component
	// If not exist, use default
	if (!@isset($MAINstyle["renderMenu"])) {
		$MAINstyle["renderMenu"] = DEFAULT_STYLE_PATH . RENDER_MENU;
	}

	// Set style path
	$MAINstyle["path"] = "style/$styleFolder";
}

?>
<html>

<head>
	<?php
	// Read setting
	readSetting();

	// NEW: title with added sub-title
	if ($MAINsubTitle && trim($MAINsubTitle) != "") {
		echo "	<title>$MAINtitle :: $MAINsubTitle</title>\n";
	} else {
		echo "	<title>$MAINtitle</title>\n";
	}

	// Get style
	$style = $MAINstyle["style"];
	$icon = $MAINstyle["icon"];

	echo "	<link rel='stylesheet' href='$style' type='text/css'/>\n";
	echo "	<link rel='shortcut icon' href='$icon'/>\n";
	echo "	<script src='ext-core.js'></script>\n";
	echo "	<script src='func.js'></script>\n";
	echo "	<script src='base64.js'></script>\n";
	?>
</head>

<?php

if (@isset($logout) && $logout == 1) {
	if (@isset($data)) {
		// Delete from session central
		$uid = $data->uid;
		$Session->DeleteSessionFromDB($uid);

		// Delete cookies
		setcookie("centraldata", "", time() - 3600);
		$centralData = (@isset($_COOKIE['centraldata']) ? $_COOKIE['centraldata'] : '');
	}

	// Redirect
	// echo "<meta http-equiv='REFRESH' content='0;url=main.php'>";
	header("Location: main.php");
} else if (stillInSession()) {
	// Body start
	echo "<body>\n";
	printBody();

	$uid = null;
	$uname = null;
	if ($data) {
		$uid = $data->uid;
		$uname = $User->GetUserName($uid);
	}

	if ($setting == "1") {
		echo "\n\n";
		echo "			<!-- Management Menu -->\n";
		echo "			<div id='management'>\n";
		echo "				<div class='spacer10'></div>\n";
		if (isAdmin($uid)) {
			if ($mode == "a") {
				include_once("view/adm/adm-area.php");
			} else if ($mode == "s") {
				include_once("view/adm/adm-setting.php");
			} else if ($mode == "m") {
				include_once("view/adm/adm-module.php");
			} else if ($mode == "l") {
				global $arConfigKey;
				include_once("view/adm/adm-locket.php");
			} else if ($mode == "u") {
				include_once("view/adm/adm-user.php");
			} else if ($mode == "d") {
				include_once("view/adm/adm-database.php");
			} else if ($mode == "r") {
				include_once("view/adm/adm-role.php");
			} else if ($mode == "f") {
				include_once("view/adm/adm-function.php");
			} else if ($mode == "h") {
				include_once("view/adm/adm-help.php");
			} else {
				echo "				<div class='subTitle'>Management Tools</div>\n";
				echo "				<div class='spacer10'></div>\n";

				// Array of management menu
				$arManagementMenu = array(
					array("urlParam" => "setting=1&m=s", "imageMenu" => "tools64.png", "header" => "Setting"),
					array("urlParam" => "setting=1&m=a", "imageMenu" => "target64.png", "header" => "Application"),
					array("urlParam" => "setting=1&m=m", "imageMenu" => "box64.png", "header" => "Module"),
					array("urlParam" => "setting=1&m=u", "imageMenu" => "purba-64.png", "header" => "User"),
					array("urlParam" => "setting=1&m=d", "imageMenu" => "save64.png", "header" => "Database"),
					array("urlParam" => "setting=1&m=r", "imageMenu" => "key64.png", "header" => "Role"),
					array("urlParam" => "setting=1&m=f", "imageMenu" => "wired64.png", "header" => "Function"),
					array("urlParam" => "setting=1&m=h", "imageMenu" => "help64.png", "header" => "Help")
				);

				echo "				<table cellpadding='0px' border='0' class='transparent'>\n";
				echo "					<tr>\n";
				// Print icon
				foreach ($arManagementMenu as $iMenu) {
					$url64 = base64_encode($iMenu["urlParam"]);
					$imageMenu = $iMenu["imageMenu"];
					$header = $iMenu["header"];

					echo "						<td>\n";
					echo "							<a href='main.php?param=$url64'><img src='image/menu/$imageMenu' alt='$header' border='0' class='imageHrefIcon'></img></a>\n";
					echo "						</td>\n";
					echo "						<td></td>\n";
				}
				echo "					</tr>\n";
				echo "					<tr>\n";
				// Print icon menu
				foreach ($arManagementMenu as $iMenu) {
					$url64 = base64_encode($iMenu["urlParam"]);
					$header = $iMenu["header"];

					echo "						<td align='center'>\n";
					echo "							<b><a href='main.php?param=$url64'>$header</a></b>\n";
					echo "						</td>\n";
					echo "						<td width='10px'></td>\n";
				}
				echo "					</tr>\n";
				echo "				</table>\n";
			}
		} else if (isSupervisor($uid)) {
			if ($mode == "u") {
				include_once("view/adm/adm-user.php");
			} else if ($mode == "h") {
				include_once("view/adm/adm-help.php");
			} else {
				echo "				<div class='subTitle'>Management Tools</div>\n";
				echo "				<div class='spacer10'></div>\n";

				// Array of management menu
				$arManagementMenu = array(
					array("urlParam" => "setting=1&m=u", "imageMenu" => "purba-64.png", "header" => "User"),
					array("urlParam" => "setting=1&m=h", "imageMenu" => "help64.png", "header" => "Help")
				);

				echo "				<table cellpadding='0px' border='0' class='transparent'>\n";
				echo "					<tr>\n";
				// Print icon
				foreach ($arManagementMenu as $iMenu) {
					$url64 = base64_encode($iMenu["urlParam"]);
					$imageMenu = $iMenu["imageMenu"];
					$header = $iMenu["header"];

					echo "						<td>\n";
					echo "							<a href='main.php?param=$url64'><img src='image/menu/$imageMenu' alt='$header' border='0' class='imageHrefIcon'></img></a>\n";
					echo "						</td>\n";
					echo "						<td></td>\n";
				}
				echo "					</tr>\n";
				echo "					<tr>\n";
				// Print icon menu
				foreach ($arManagementMenu as $iMenu) {
					$url64 = base64_encode($iMenu["urlParam"]);
					$header = $iMenu["header"];

					echo "						<td align='center'>\n";
					echo "							<b><a href='main.php?param=$url64'>$header</a></b>\n";
					echo "						</td>\n";
					echo "						<td width='10px'></td>\n";
				}
				echo "					</tr>\n";
				echo "				</table>\n";
			}
		}
		echo "				</div>\n";
	} else if ($userProfile == "1") {
		echo "			\n";

		// Sub menu
		echo "<!-- Sub menu -->\n";
		echo "<div id='subMenu'>\n";
		echo "	<a href='main.php'>&laquo;&nbsp;&nbsp;Main page</a>\n";
		echo "	&nbsp;&nbsp;\n";

		// View profile
		if ($mode != "") {
			$url64 = base64_encode("userProfile=1");
			echo "	<a href='main.php?param=$url64'><b>User profile</b></a>\n";
		} else {
			echo "	<b>User profile</b>\n";
		}
		echo "	&nbsp;&nbsp;\n";

		// Change password
		if ($mode != "chPass") {
			$url64 = base64_encode("userProfile=1&m=chPass");
			echo "	<a href='main.php?param=$url64'>Change Password</a>\n";
		} else {
			echo "	Change Password\n";
		}
		echo "	&nbsp;&nbsp;\n";

		// Change layout
		if ($mode != "chLayout") {
			$url64 = base64_encode("userProfile=1&m=chLayout");
			echo "	<a href='main.php?param=$url64'>Change Layout</a>\n";
		} else {
			echo "	Change Layout\n";
		}
		echo "	&nbsp;&nbsp;\n";

		// End
		echo "</div>\n";
		echo "<!-- End of sub menu -->\n";

		echo "			<!-- Content -->\n";
		echo "			<div id='user-profile'>\n";

		include("view/profile/userProfile.php");

		echo "			</div>\n";

	} else if ($function != "") {
		$bOK = getFunctionPage();
		if ($bOK && $userFuncPage != "") {
			// Content
			// NOTE: Don't print content in local function
			echo "			\n";
			echo "			<!-- Content -->\n";
			echo "			<div id='content'>\n";

			// Submenu navigasi main page
			if ($function != "") {
				echo "			<div id='subMenu'>\n";
				$url64 = base64_encode("a=$area&m=$module");
				echo "				<a href='main.php?param=$url64'>&laquo;&nbsp;&nbsp;Main page</a>\n";
				echo "			</div>\n";
				echo "			<div class='spacer10'></div>\n";
			}

			$areaDbLink = $User->GetDbConnectionFromArea($area);
			$dbSpec = new SCANCentralDbSpecific(DEBUG, LOG_DMS_FILENAME, $areaDbLink);

			// NEW: Read arFunction
			$func = null;
			$bOK = $User->GetFunction($module, $func);
			if ($bOK) {
				$i = 0;
				foreach ($func as $funcValue) {
					$funcId = $funcValue["id"];
					$funcName = $funcValue["name"];
					$funcPos = $funcValue["pos"];
					$funcImage = $funcValue["image"];

					if ($User->IsFunctionGranted($uid, $area, $module, $funcId)) {
						if ($funcPos == 0) {
							// NEW: print 'per terminal' function
							$arFunction[$i] = array("urlParam" => "a=$area&m=$module&f=$funcId", "image" => "image/icon/$funcImage", "name" => "$funcName");
							$i++;
						}
					}
				}
			}

			echo "\n\n<!-- From page view -->\n";
			include($userFuncPage);
			echo "\n<!-- End of page view -->\n\n";

			// End of content
			echo "			</div>\n";
		} else {
			echo "				<div class='spacer10'></div>\n";
			echo "				<div class='error'>No view</div>\n";
		}
	} else if ($module != "" && !isEmpty($userModuleView)) {
		if (file_exists($userModuleView)) {
			// Content
			// NOTE: Don't print content in local function
			echo "			\n";
			echo "			<!-- Content -->\n";
			echo "			<div id='content'>\n";

			// Connect to switcher database
			$areaDbLink = $User->GetDbConnectionFromArea($area);
			$dbSpec = new SCANCentralDbSpecific(DEBUG, LOG_DMS_FILENAME, $areaDbLink);

			// print function
			$func = null;
			$bOK = $User->GetFunction($module, $func);
			if ($bOK) {
				$i = 0;
				$writeSubMenu = false;
				foreach ($func as $funcValue) {
					$funcId = $funcValue["id"];
					$funcName = $funcValue["name"];
					$funcPos = $funcValue["pos"];
					$funcImage = $funcValue["image"];

					if ($User->IsFunctionGranted($uid, $area, $module, $funcId)) {
						if ($funcPos == 0) {
							// NEW: print 'per terminal' function
							$arFunction[$i] = array("urlParam" => "a=$area&m=$module&f=$funcId", "image" => "image/icon/$funcImage", "name" => "$funcName");
							$i++;
						} else if ($funcPos == 1) {
							// NEW: print 'per module' function
							$url64 = base64_encode("a=$area&m=$module&f=$funcId");
							if (!$writeSubMenu) {
								echo "				<div id='subMenu'>\n";
								$writeSubMenu = true;
							}
							echo "					<a href='main.php?param=$url64'>$funcName</a>&nbsp;&nbsp;&nbsp;\n";
						}
					}
				}
				if ($writeSubMenu) {
					echo "				</div>\n";
				}
			}

			echo "\n\n<!-- From page view -->\n";
			include($userModuleView);
			echo "\n<!-- End of page view -->\n\n";

			// End of content
			echo "			</div>\n";
		} else {
			echo "<div class='spacer10'></div>\n";
			echo "<div>Error</div>\n";
		}
	}
	echo "		</div>\n";
	echo "	</div>\n";

	// Footer
	$MAINendRender = microtime(1);
	$MAINdurasiRender = ($MAINendRender - $MAINstartRender);
	$MAINdurasiRender = substr($MAINdurasiRender, 0, 7);
	$MAINfooterText = str_replace("{\$durasiRender}", $MAINdurasiRender, $MAINfooterText);
	echo "	\n";
	echo "	<!-- Footer -->\n";
	echo "	<div id='footer'>" . nl2br($MAINfooterText) . "</div>\n";

	// Body end
	echo "</body>\n";
} else {
	include("plogin.php");
}

ob_end_flush();
?>

</html>