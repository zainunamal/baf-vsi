<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management Database
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertDatabaseAction();
	} else {
		insertDatabase();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editDatabaseAction();
	} else {
		editDatabase();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteDatabase();
} else if ($subMode == "t") {
	// Submode test connection
	testConnection();
} else if ($subMode == "lc") {
	// Submode list configuration
	if ($action) {
		copyConfiguration();
	} else {
		listConfiguration();
	}
} else if ($subMode == "ic") {
	// Submode insert database configuration
	if ($action) {
		insertDatabaseCfgAction();
	} else {
		insertDatabaseCfg();
	}
} else if ($subMode == "ec") {
	// Submode edit database configuration
	if ($action) {
		editDatabaseCfgAction();
	} else {
		editDatabaseCfg();
	}
} else if ($subMode == "dc") {
	// Submode delete database configuration
	deleteDatabaseCfg();
} else {
	// List all
	$bOK = printDatabaseSetting();
	if (!$bOK) {
		return;
	}
}

function printDatabaseSetting() {
	global $cData, $data, $json, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
				
		echo "	<div class='subTitle'>List Databases</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$url64 = base64_encode("setting=1&m=d&sm=i");
		echo "	<a href='main.php?param=$url64'>Add new database</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		// print table Database
		$arDatabases = null;
		$bOK = $Setting->GetDatabase($arDatabases);
		if ($bOK) {
?>
<script type='text/javascript'>
function confirmDeleteDb(name, id) {
	var ans = confirm("Delete database '" + name + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=d&sm=d&i=' + id);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php	
			echo "	<table cellspacing='3px' cellpadding='3px'>\n";
			echo "		<tr>\n";
			echo "			<th>Option</th>\n";
			echo "			<th>Id</th>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Schema</th>\n";
			echo "			<th>Host</th>\n";
			echo "			<th>Port</th>\n";
			echo "			<th>Username</th>\n";
			echo "		</tr>\n";
			
			foreach ($arDatabases as $db) {
				$dbId = $db["id"];
				$dbName = $db["name"];
				$dbSchema = $db["schema"];
				$dbHost = $db["host"];
				$dbPort = $db["port"];
				$dbUser = $db["user"];
				
				echo "		<tr>\n";
				echo "			<td align='center'>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=d&sm=e&i=$dbId");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
				echo "				&nbsp;\n";
				echo "				<a href='#' onClick='confirmDeleteDb(\"$dbName\", \"$dbId\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=d&sm=t&i=$dbId");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/coll_go.png' alt='Test connection' title='Test connection'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=d&sm=lc&i=$dbId");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/tools.png' alt='List configuration' title='List configuration'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td>$dbId&nbsp;</td>\n";
				echo "			<td>$dbName&nbsp;</td>\n";
				echo "			<td>$dbSchema&nbsp;</td>\n";
				echo "			<td>$dbHost&nbsp;</td>\n";
				echo "			<td>$dbPort&nbsp;</td>\n";
				echo "			<td>$dbUser&nbsp;</td>\n";
				echo "		</tr>\n";
			}
			
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function deleteDatabase() {
	global $Setting, $id;
	$bOK = $Setting->DeleteDatabase($id);
	if ($bOK) {
		echo "<div>Database successfully deleted...</div>\n";
	} else {
		echo "<div>Database fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=d");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertDatabase() {
	global $Setting, $User, $idDb, $nameDb, $schemaDb, $hostDb, $portDb, $userDb, $pwdDb, $ar, $mode;
	
	// $idDb = (isset($_REQUEST['idDb']) ? trim($_REQUEST['idDb']) : '');
	// $nameDb = (isset($_REQUEST['nameDb']) ? trim($_REQUEST['nameDb']) : '');
	// $schemaDb = (isset($_REQUEST['schemaDb']) ? trim($_REQUEST['schemaDb']) : '');
	// $hostDb = (isset($_REQUEST['hostDb']) ? trim($_REQUEST['hostDb']) : '');
	// $portDb = (isset($_REQUEST['portDb']) ? trim($_REQUEST['portDb']) : '');
	// $userDb = (isset($_REQUEST['userDb']) ? trim($_REQUEST['userDb']) : '');
	// $pwdDb = (isset($_REQUEST['pwdDb']) ? trim($_REQUEST['pwdDb']) : '');
	
	// Insert from area
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	// echo "ar = $ar<br />\n";
	$areaId = $ar;
	
	if ($idDb == "") {
		// initial insert, ambil next id 'd?'
		$idDb = "d" . $Setting->GetNextDbId();
	}

	$idDb = nullable_htmlspecialchar($idDb, ENT_QUOTES);
	$nameDb = nullable_htmlspecialchar($nameDb, ENT_QUOTES);
	$schemaDb = nullable_htmlspecialchar($schemaDb, ENT_QUOTES);
	$hostDb = nullable_htmlspecialchar($hostDb, ENT_QUOTES);
	$portDb = nullable_htmlspecialchar($portDb, ENT_QUOTES);
	$userDb = nullable_htmlspecialchar($userDb, ENT_QUOTES);
	$pwdDb = nullable_htmlspecialchar($pwdDb, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Insert new database";
	// NEW: Insert from area
	$forArea = false;
	if ($areaId) {
		$areaName = $User->GetAreaName($areaId);
		$areaName = nullable_htmlspecialchar($areaName);
		echo " for area '$areaName'";
		$forArea = true;
	}
	echo "</div>\n";
	echo "	<div class='spacer10'></div>\n";
		
	$url64 = base64_encode("setting=1&m=d&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64' onSubmit='return cekConfirmPassword(\"pwdDb\", \"pwdDb2\");'>\n";
	echo "	<input type='hidden' id='ar' name='ar' value='$areaId'></input>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idDb' name='idDb' length='30' size='20' value='$idDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameDb' name='nameDb' length='100' size='30' value='$nameDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Schema</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='schemaDb' name='schemaDb' length='100' size='30' value='$schemaDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Host</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='hostDb' name='hostDb' length='100' size='30' value='$hostDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Port</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='portDb' name='portDb' length='100' size='30' value='$portDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Username</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='userDb' name='userDb' length='100' size='30' value='$userDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='password' id='pwdDb' name='pwdDb' length='100' size='30' value='' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Confirm Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='password' id='pwdDb2' name='pwdDb2' length='100' size='30' value='' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' height='10px'>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' align='center'>\n";
	$url64 = base64_encode("setting=1&m=$mode");
	echo "			<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	echo "			&nbsp;&nbsp;\n";
	echo "			<input type='submit' value='Save'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}

function insertDatabaseAction() {
	global $Setting, $idDb, $nameDb, $schemaDb, $hostDb, $portDb, $userDb, $pwdDb, $ar;
	
	// $idDb = (isset($_REQUEST['idDb']) ? trim($_REQUEST['idDb']) : '');
	// $nameDb = (isset($_REQUEST['nameDb']) ? trim($_REQUEST['nameDb']) : '');
	// $schemaDb = (isset($_REQUEST['schemaDb']) ? trim($_REQUEST['schemaDb']) : '');
	// $hostDb = (isset($_REQUEST['hostDb']) ? trim($_REQUEST['hostDb']) : '');
	// $portDb = (isset($_REQUEST['portDb']) ? trim($_REQUEST['portDb']) : '');
	// $userDb = (isset($_REQUEST['userDb']) ? trim($_REQUEST['userDb']) : '');
	// $pwdDb = (isset($_REQUEST['pwdDb']) ? trim($_REQUEST['pwdDb']) : '');
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	$areaId = $ar;
	
	$bOK = $Setting->InsertDatabase($idDb, $nameDb, $schemaDb, $hostDb, $portDb, $userDb, $pwdDb);

	$idDb = nullable_htmlspecialchar($idDb, ENT_QUOTES);
	$nameDb = nullable_htmlspecialchar($nameDb, ENT_QUOTES);
	$schemaDb = nullable_htmlspecialchar($schemaDb, ENT_QUOTES);
	$hostDb = nullable_htmlspecialchar($hostDb, ENT_QUOTES);
	$portDb = nullable_htmlspecialchar($portDb, ENT_QUOTES);
	$userDb = nullable_htmlspecialchar($userDb, ENT_QUOTES);
	$pwdDb = nullable_htmlspecialchar($pwdDb, ENT_QUOTES);
	
	// DEPRECATED: Password database disimpan plain-text
	$n = strlen($pwdDb);
	$pwdDbDisp = "";
	for ($i = 0; $i < $n; $i++) {
		$pwdDbDisp .= "&bull;";
	}
	
	$url64 = base64_encode("setting=1&m=d&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formDb'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert Database";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idDb' name='idDb' value='$idDb'></input>$idDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameDb' name='nameDb' value='$nameDb'></input>$nameDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Schema</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='schemaDb' name='schemaDb' value='$schemaDb'></input>$schemaDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Host</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='hostDb' name='hostDb' value='$hostDb'></input>$hostDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Port</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='portDb' name='portDb' value='$portDb'></input>$portDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Username</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='userDb' name='userDb' value='$userDb'></input>$userDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='pwdDb' name='pwdDb' value='$pwdDb'></input>$pwdDbDisp</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formDb\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		// echo "areaId = $areaId<br />\n";
		if ($areaId == null) {
			$url64 = base64_encode("setting=1&m=d");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		} else {
			// Saving for area id
?>
<script type'text/javascript'>
// Db information
var idDb = document.getElementById('idDb').value;
var nameDb = document.getElementById('nameDb').value;

// Add saved database to option
var dbArea = parent.opener.document.getElementById('dbArea');
dbArea.options[dbArea.options.length - 1] = new Option(nameDb, idDb, true);

// Add new option
dbArea.options[dbArea.options.length] = new Option("< Insert new role >", -99);

// Close self
self.close();
</script>
<?php
		}
	}
	echo "</div>\n";
	echo "</form>\n";
}

function editDatabase() {
	global $Setting, $id;
	
	$arDatabase = $Setting->GetDatabaseDetail($id);
	if ($arDatabase == null) {
		return;
	}
	$idDb = $arDatabase["id"];
	$nameDb = $arDatabase["name"];
	$schemaDb = $arDatabase["schema"];
	$hostDb = $arDatabase["host"];
	$portDb = $arDatabase["port"];
	$userDb = $arDatabase["user"];
	$pwdDb = $arDatabase["pwd"];
	
	// quote fix
	$idDb = nullable_htmlspecialchar($idDb, ENT_QUOTES);
	$nameDb = nullable_htmlspecialchar($nameDb, ENT_QUOTES);
	$schemaDb = nullable_htmlspecialchar($schemaDb, ENT_QUOTES);
	$hostDb = nullable_htmlspecialchar($hostDb, ENT_QUOTES);
	$portDb = nullable_htmlspecialchar($portDb, ENT_QUOTES);
	$userDb = nullable_htmlspecialchar($userDb, ENT_QUOTES);
	$pwdDb = nullable_htmlspecialchar($pwdDb, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit database '$nameDb'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=d&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64' onSubmit='return cekConfirmPassword(\"pwdDb\", \"pwdDb2\");'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idDb' name='idDb' value='$idDb'></input>$idDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameDb' name='nameDb' length='100' size='30' value='$nameDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Schema</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='schemaDb' name='schemaDb' length='100' size='30' value='$schemaDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Host</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='hostDb' name='hostDb' length='100' size='30' value='$hostDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Port</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='portDb' name='portDb' length='100' size='30' value='$portDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Username</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='userDb' name='userDb' length='100' size='30' value='$userDb' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' height='25px' valign='bottom'>\n";
	echo "			<div style='font-size:8pt; text-align:center;'><b>for reset password only</b></div>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='password' id='pwdDb' name='pwdDb' length='100' size='30' value='' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Confirm Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='password' id='pwdDb2' name='pwdDb2' length='100' size='30' value='' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' height='10px'>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' align='center'>\n";
	echo "			<input type='submit' value='Save'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}

function editDatabaseAction() {
	global $Setting, $idDb, $nameDb, $schemaDb, $hostDb, $portDb, $userDb, $pwdDb;
	
	// $idDb = (isset($_REQUEST['idDb']) ? trim($_REQUEST['idDb']) : '');
	// $nameDb = (isset($_REQUEST['nameDb']) ? trim($_REQUEST['nameDb']) : '');
	// $schemaDb = (isset($_REQUEST['schemaDb']) ? trim($_REQUEST['schemaDb']) : '');
	// $hostDb = (isset($_REQUEST['hostDb']) ? trim($_REQUEST['hostDb']) : '');
	// $portDb = (isset($_REQUEST['portDb']) ? trim($_REQUEST['portDb']) : '');
	// $userDb = (isset($_REQUEST['userDb']) ? trim($_REQUEST['userDb']) : '');
	// $pwdDb = (isset($_REQUEST['pwdDb']) ? trim($_REQUEST['pwdDb']) : '');
	
	$n = strlen($pwdDb);
	$pwdDbDisp = "";
	for ($i = 0; $i < $n; $i++) {
		$pwdDbDisp .= "&bull;";
	}
	
	$bOK = $Setting->EditDatabase($idDb, $nameDb, $schemaDb, $hostDb, $portDb, $userDb, $pwdDb);

	$idDb = nullable_htmlspecialchar($idDb, ENT_QUOTES);
	$nameDb = nullable_htmlspecialchar($nameDb, ENT_QUOTES);
	$schemaDb = nullable_htmlspecialchar($schemaDb, ENT_QUOTES);
	$hostDb = nullable_htmlspecialchar($hostDb, ENT_QUOTES);
	$portDb = nullable_htmlspecialchar($portDb, ENT_QUOTES);
	$userDb = nullable_htmlspecialchar($userDb, ENT_QUOTES);
	$pwdDb = nullable_htmlspecialchar($pwdDb, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=d&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formDb'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit Database";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idDb' name='idDb' value='$idDb'></input>$idDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameDb' name='nameDb' value='$nameDb'></input>$nameDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Schema</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='schemaDb' name='schemaDb' value='$schemaDb'></input>$schemaDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Host</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='hostDb' name='hostDb' value='$hostDb'></input>$hostDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Port</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='portDb' name='portDb' value='$portDb'></input>$portDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Username</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='userDb' name='userDb' value='$userDb'></input>$userDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='pwdDb' name='pwdDb' value='$pwdDb'></input>$pwdDbDisp</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formDb\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=d");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
}

function listArea() {
	global $Setting, $User, $id;
	
	$arArea = null;
	$bOK = $Setting->GetArea($arArea);
	$bOK2 = $Setting->GetRole($arRole);
	
	$username = $User->GetUsername($id);
	
	echo "	<div class='subTitle'>Area & role from user '$username'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	if ($bOK && $bOK2) {
		// var_dump($arArea);
		
		echo "	<table cellspacing='3px' cellpadding='3px'>\n";
		echo "		<tr>\n";
		echo "			<th>Area</th>\n";
		echo "			<th>Role Module</th>\n";
		echo "			<th>Change</th>\n";
		echo "		</tr>\n";
		
		$i = 1;
		foreach ($arArea as $area) {
			$areaId = $area["id"];
			$areaName = $area["name"];
			
			echo "		<tr>\n";
			echo "			<td align='center'>$areaName&nbsp;</td>\n";

			echo "			<td align='center'>\n";
			$role = $Setting->GetRoleInArea($id, $areaId);
			if ($role) {
				$roleName = $User->GetRoleName($role);
				echo "				<span id='role$i' style='color:green; font-weight:bold;'>Granted as $roleName</span>\n";
			} else {
				echo "				<span id='role$i' style='color:red; font-weight:bold;'>Decline</span>\n";
			}
			echo "				<select id='selectRole$i' name='selectRole$i' style='display:none' onFocus='focusSelectRole($i);' onBlur='selectSelectRole($i);' onChange='changeRole($i, \"$id\", \"$areaId\");'>\n";
			echo "					<option value='0'>---------</option>\n";
			echo "					<option value='-1'>Decline</option>\n";
			foreach ($arRole as $rowRole) {
				$rowRoleId = $rowRole["id"];
				$rowRoleName = $rowRole["name"];
				$rowRoleName = nullable_htmlspecialchar($rowRoleName);
				echo "					<option value='$rowRoleId'>Grant as $rowRoleName</option>\n";
			}
			
			echo "					<option value='new'>&lt; Insert new role &gt;</option>\n";
			echo "				</select>\n";
			echo "			</td>\n";
			
			echo "			<td align='center'>\n";
			echo "				&nbsp;\n";
			echo "				<a href='#' onClick='showSelectRole($i)'><img border='0' src='image/icon/group_key.png' alt='Change role' title='Change role'></img></a>\n";
			echo "				&nbsp;\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			
			$i++;
		}
		
		echo "	</table>\n";
	}
}

function changeRole() {
	global $Setting, $id, $role, $ar, $r;
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	// $roleId = (isset($_REQUEST['r']) ? trim($_REQUEST['r']) : '');
	$areaId = $ar;
	$roleId = $r;

	// echo "user = $id<br />\n";
	// echo "area = $areaId<br />\n";
	// echo "role = $roleId<br />\n";
	
	$bOK = $Setting->ChangeRole($id, $areaId, $roleId);
	if ($bOK) {
		echo "<div>Role successfully changed...</div>\n";
	} else {
		echo "<div>Role fail to change...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=u&sm=lc&i=$id");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function testConnection() {
	global $Setting, $id, $role;
	
	$arDatabase = $Setting->GetDatabaseDetail($id);
	if ($arDatabase == null) {
		return;
	}
	$linkDb = null;
	$connDb = null;
	$nameDb = $arDatabase["name"];
	$schemaDb = $arDatabase["schema"];
	$portDb = $arDatabase["port"];
	$hostDb = $arDatabase["host"];
	$hostPort = $hostDb . ":" . $portDb;
	$userDb = $arDatabase["user"];
	$pwdDb = $arDatabase["pwd"];
	
	$n = strlen($pwdDb);
	$pwdDbDisp = "";
	for ($i = 0; $i < $n; $i++) {
		$pwdDbDisp .= "&bull;";
	}
	
	// HIDING ERROR/WARNING
	echo "	<span style='display:block'>\n";
	SCANPayment_ConnectToDB($linkDb, $connDb, $hostPort, $userDb, $pwdDb, $schemaDb);
	echo "	</span>\n";
	
	echo "	<div>Test connection database '$nameDb' ... ";
	$succeed = true;
	if ($linkDb != null && $connDb != null) {
		echo "succeed!</div>\n";
	} else {
		echo "failed!</div>\n";
		$succeed = false;
	}
	echo "	<div class='spacer10'></div>\n";
	
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>Detail</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Host</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$hostDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Port</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$portDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Schema</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$schemaDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Username</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$userDb</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$pwdDbDisp</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	if (!$succeed) {
		echo "<div class='spacer20'></div>\n";
		$url64 = base64_encode("setting=1&m=d&sm=e&i=$id");
		echo "<div><a href='main.php?param=$url64'>Edit form</a></div>\n";
	}
}


function listConfiguration() {
	global $Setting, $id, $User;
	
	$dbName = $User->GetDatabaseName($id);
	$dbName = nullable_htmlspecialchar($dbName, ENT_QUOTES);
	
	echo "	<div class='subTitle'>List table configuration in database '$dbName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	// insert database configuration
	$url64 = base64_encode("setting=1&m=d&sm=ic&i=$id");
	echo "<a href='main.php?param=$url64'>Add new configuration</a>\n";
	echo "<div class='spacer10'></div>\n";
	
	$arConfig = $User->GetDatabaseConfig($id);
	if ($arConfig) {
?>
<script type='text/javascript'>
function confirmDeleteDatabaseCfg(id, key) {
	var ans = confirm("Delete configuration '" + key + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=d&sm=dc&i=' + id + '&k=' + key);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php
		echo "<table cellspacing='3px' cellpadding='3px'>\n";
		echo "	<tr>\n";
		echo "		<th>Option</th>\n";
		echo "		<th>Position</th>\n";
		echo "		<th>Column</th>\n";
		echo "		<th>Header</th>\n";
		echo "	</tr>\n";
		foreach ($arConfig as $conf) {
			$pos = $conf["pos"];
			$key = $conf["key"];
			$value = $conf["value"];
			
			echo "	<tr>\n";
			echo "		<td>\n";
			echo "			&nbsp;\n";
			$url64 = base64_encode("setting=1&m=d&sm=ec&i=$id&k=$key");
			echo "			<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit configuration' title='Edit configuration'></img></a>\n";
			echo "			&nbsp;\n";
			echo "			<a href='#' onClick='confirmDeleteDatabaseCfg(\"$id\", \"$key\")''><img border='0' src='image/icon/cancel.png' alt='Delete configuration' title='Delete configuration'></img></a>\n";
			echo "			&nbsp;\n";
			echo "		</td>\n";
			echo "		<td>$pos&nbsp;</td>\n";
			echo "		<td>$key&nbsp;</td>\n";
			echo "		<td>$value&nbsp;</td>\n";
			echo "	</tr>\n";
		}
		echo "</table>\n";
	} else {
		$arDatabases = null;
		$bOK = $Setting->GetDatabase($arDatabases);
		if ($bOK) {
			// copy database configuration
			$url64 = base64_encode("setting=1&m=d&sm=lc&i=$id");
			echo "<form method='POST' action='main.php?param=$url64'>\n";
			echo "	<input type='hidden' id='a' name='a' value='1'></input>\n";
			echo "<div>\n";
			echo "	No configuration available. Init configuration from ";
			echo "	<select id='dbIdInit' name='dbIdInit'>\n";
			echo "		<option value='-'>--------</option>\n";
			foreach ($arDatabases as $db) {
				$dbId = $db["id"];
				$dbName = $db["name"];
			
				if ($dbId != $id) {
					echo "		<option value='$dbId'>$dbName</option>\n";
				}
			}
			echo "	</select>\n";
			echo "	<input type='submit' value='Init'></input>\n";
			echo "</div>\n";
			
			echo "<div class='spacer10'></div>\n";
			echo "</form>\n";
		}
	}
}

function copyConfiguration() {
	global $Setting, $id, $dbIdInit;
	
	if ($dbIdInit != "") {
		$Setting->CopyConfigDatabase($dbIdInit, $id);
	}

	listConfiguration();
}

function insertDatabaseCfgAction() {
	global $Setting, $User, $id, $pos, $key, $value;

	// $pos = (isset($_REQUEST['pos']) ? trim($_REQUEST['pos']) : '');
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	$bOK = $Setting->InsertDatabaseConfig($id, $pos, $key, $value);
	
	$dbName = $User->GetDatabaseName($id);
	$dbName = nullable_htmlspecialchar($dbName, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=d&sm=ic&i=$id");
	echo "<form method='POST' action='main.php?param=$url64' id='formArea'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert database $dbName's configuration";
	if ($bOK) {
		echo " succeed\n";
	} else {
		echo " failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Position</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top' width='200'><input type='hidden' id='pos' name='pos' value='$pos' autocomplete='off'></input>$pos</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Key</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top' width='200'><input type='hidden' id='key' name='key' value='$key' autocomplete='off'></input>$key</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Value</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='value' name='value' value='$value' autocomplete='off'></input>$value</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formArea\").submit();'>Edit configuration</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=d&sm=lc&i=$id");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function insertDatabaseCfg() {
	global $Setting, $id, $User, $pos, $key, $value;
	
	// $pos = (isset($_REQUEST['pos']) ? trim($_REQUEST['pos']) : '');
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	if ($pos == "") {
		// Initial insert
		$pos = $Setting->GetNextPosDatabaseCfg($id);
	}
	$dbName = $User->GetDatabaseName($id);
	
	$pos = nullable_htmlspecialchar($pos, ENT_QUOTES);
	$dbName = nullable_htmlspecialchar($dbName, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Add new configuration for database '$dbName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=d&sm=ic&i=$id&a=1");
	echo "<form action='main.php?param=$url64' method='POST'>\n";
	echo "	<table class='transparent'>\n";
	echo "		<tr>\n";
	echo "			<td>Position</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='pos' id='pos' size='5' length='11' value='$pos' autocomplete='off'></input></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td>Column</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='key' id='key' size='20' length='45' value='$key' autocomplete='off'></input></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td>Header</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='value' id='value' size='20' length='100' value='$value' autocomplete='off'></input></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' height='10px'>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' align='center'>\n";
	echo "				<input type='submit' value='Save'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
}

function editDatabaseCfgAction() {
	global $Setting, $User, $id, $oldKey, $key, $value, $pos;

	// $oldKey = (isset($_REQUEST['oldKey']) ? trim($_REQUEST['oldKey']) : '');
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	// $pos = (isset($_REQUEST['pos']) ? trim($_REQUEST['pos']) : '');
	
	$bOK = $Setting->EditDatabaseConfig($id, $oldKey, $pos, $key, $value);
	
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$dbName = $User->GetDatabaseName($id);
	
	$url64 = base64_encode("setting=1&m=a&sm=ec&i=$id&k=$oldKey");
	echo "<form method='POST' action='main.php?param=$url64' id='formDatabase'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit database $dbName's configuration";
	if ($bOK) {
		echo " succeed\n";
	} else {
		echo " failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Position</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='pos' name='pos' value='$pos' autocomplete='off'></input>$pos</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Column</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='key' name='key' value='$key' autocomplete='off'></input>$key</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Header</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='value' name='value' value='$value' autocomplete='off'></input>$value</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formDatabase\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=d&sm=lc&i=$id");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
}

function editDatabaseCfg() {
	global $Setting, $User, $id, $o, $k, $pos, $value;
	
	// $oldKey = (isset($_REQUEST['o']) ? trim($_REQUEST['o']) : '');
	// $key = (isset($_REQUEST['k']) ? trim($_REQUEST['k']) : '');
	$oldKey = $o;
	$key = $k;
	// $pos = (isset($_REQUEST['pos']) ? trim($_REQUEST['pos']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	if ($oldKey == "") {
		$oldKey = $key;
	}
	
	$ar = $Setting->GetDatabaseConfigValue($id, $oldKey);
	// var_dump($ar);
	// echo "pos = $pos";
	// echo "value = $value";
	if ($pos == "" && $value == "") {
		$pos = $ar["pos"];
		$value = $ar["value"];
	}
	
	// quote fix
	$dbName = $User->GetDatabaseName($id);
	$dbName = nullable_htmlspecialchar($dbName, ENT_QUOTES);
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit configuration '$oldKey' in database '$dbName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=d&sm=ec&i=$id&a=1");
	echo "<form action='main.php?param=$url64' method='POST'>\n";
	echo "	<table class='transparent'>\n";
	echo "		<tr>\n";
	echo "			<td>Position</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='pos' id='pos' size='5' length='20' value='$pos' autocomplete='off'></input></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td>Column</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td>\n";
	echo "				<input type='text' name='key' id='key' size='20' length='45' value='$key' autocomplete='off'></input>\n";
	echo "				<input type='hidden' name='oldKey' id='oldKey' value='$oldKey' autocomplete='off'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td>Header</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='value' id='value' size='20' length='100' value='$value' autocomplete='off'></input></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' height='10px'>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' align='center'>\n";
	echo "				<input type='submit' value='Save'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
}

function deleteDatabaseCfg() {
	global $Setting, $id, $k;
	// $key = (isset($_REQUEST['k']) ? trim($_REQUEST['k']) : '');
	$key = $k;
	$bOK = $Setting->DeleteDatabaseConfig($id, $key);
	if ($bOK) {
		echo "<div>Database configuration successfully deleted...</div>\n";
	} else {
		echo "<div>Database configuration fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=d&sm=lc&i=$id");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}


?>
