<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management area
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertAreaAction();
	} else {
		insertArea();
	}
} else if ($subMode == "c") {
	// Submode copy
	if ($action) {
		// get parameter
		copyAreaAction();
	} else {
		copyArea();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editAreaAction();
	} else {
		editArea();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteArea();
} else if ($subMode == "t") {
	// Submode list area
	listArea();
} else if ($subMode == "lc") {
	// Submode list
	listAreaCfg();
} else if ($subMode == "ic") {
	// Submode insert area configuration
	if ($action) {
		insertAreaCfgAction();
	} else {
		insertAreaCfg();
	}
} else if ($subMode == "ec") {
	// Submode edit area configuration
	if ($action) {
		editAreaCfgAction();
	} else {
		editAreaCfg();
	}
} else if ($subMode == "dc") {
	// Submode delete area configuration
	deleteAreaCfg();
} else if ($subMode == "l") {
	// Submode list module
	if ($action) {
		saveModuleAccess();
	} else {
		listModuleAccess();
	}
} else {
	// List all
	$bOK = printAreaSetting();
	if (!$bOK) {
		return;
	}
}

?>
<script type='text/javascript'>
var winInsertDb = null;

function changeDatabase(area) {
	var dbArea = document.getElementById('dbArea').value;
	if (dbArea != "-99") {
		return;
	}

	if (area != null) {
		if (!winInsertDb) {
			var url = Base64.encode("setting=1&m=d&sm=i&ar=" + area);
			winInsertDb = window.open(
				"main.php?param=" + url,
				"Insert database", 
				"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=400, height=500");
		} else if (winInsertDb.closed) {
			var url = Base64.encode("setting=1&m=d&sm=i&ar=" + area);
			winInsertDb = window.open(
				"main.php?param=" + url,
				"Insert database", 
				"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=400, height=500");
		} else {
			winInsertDb.focus();
		}
	} else {
		if (!winInsertDb) {
			var url = Base64.encode("setting=1&m=d&sm=i&ar=-99");
			winInsertDb = window.open(
				"main.php?param=" + url,
				"Insert database", 
				"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=400, height=500");
		} else if (winInsertDb.closed) {
			var url = Base64.encode("setting=1&m=d&sm=i&ar=-99");
			winInsertDb = window.open(
				"main.php?param=" + url,
				"Insert database", 
				"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=400, height=500");
		} else {
			winInsertDb.focus();
		}
	}
}

function accessAll(value) {
	var i = 0;
	var moduleAccess = null;
	while (true) {
		moduleAccess = document.getElementById('moduleAccess' + i);
		if (moduleAccess == null) {
			break;
		} else {
			moduleAccess.checked = value;
		}
		i++;
	}
}
</script>
<?php

// Print list all applicatoin
function printAreaSetting() {
	global $data, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
		
		echo "	<div class='subTitle'>List applications</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$url64 = base64_encode("setting=1&m=a&sm=i&n=1");
		echo "	<a href='main.php?param=$url64'>Add new application</a>\n";
		echo "	&nbsp;&nbsp;&nbsp;\n";
		$url64 = base64_encode("setting=1&m=a&sm=c");
		echo "	<a href='main.php?param=$url64'>Copy application</a>\n";
		echo "	<div class='spacer10'></div>\n";
		// print table area
		$bOK = $Setting->GetArea($areaIds);
		if ($bOK) {
?>
<script type='text/javascript'>
function confirmDeleteArea(name, id) {
	var ans = confirm("Delete application '" + name + "' ?");
	if (ans) {
		var url = Base64.encode("setting=1&m=a&sm=d&i=" + id);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php
			// var_dump($areaIds);
			echo "	<table cellspacing='3px' cellpadding='3px'>\n";
			echo "		<tr>\n";
			echo "			<th>Option</th>\n";
			echo "			<th>Id</th>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Description</th>\n";
			echo "			<th>Database</th>\n";
			
			// PauL 26 aug 10: hide for BAF
			// echo "			<th width='600px'>Query</th>\n";
			
			echo "		</tr>\n";
			
			foreach ($areaIds as $areaId) {
				$id = $areaId["id"];
				$name = $areaId["name"];
				$desc = $areaId["desc"];
				$query = $areaId["query"];
				$dbId = $areaId["db"];
				
				echo "		<tr>\n";
				echo "			<td align='center' valign='top'>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=a&sm=e&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
				echo "				&nbsp;\n";
				echo "				<a href='#' onClick='confirmDeleteArea(\"$name\", \"$id\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
				echo "				&nbsp;\n";
				
				// PauL 26 aug 10: hide for BAF
				// $url64 = base64_encode("setting=1&m=a&sm=t&i=$id");
				// echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/sapu.png' alt='Test query' title='Test query'></img></a>\n";
				// echo "				&nbsp;\n";
				
				$url64 = base64_encode("setting=1&m=a&sm=l&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/list-items.gif' alt='List module' title='List module'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=a&sm=lc&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/tools.png' alt='List configuration' title='List configuration'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td valign='top'>$id&nbsp;</td>\n";
				echo "			<td valign='top' width='100px'>$name&nbsp;</td>\n";
				echo "			<td valign='top' width='100px'>$desc&nbsp;</td>\n";
				if ($dbId == null) {
					echo "			<td align='center' valign='top'>-</td>\n";
				} else {
					$dbName = $User->GetDatabaseName($dbId);
					echo "			<td valign='top' width='50px'>$dbName&nbsp;</td>\n";
				}
				
				// PauL 26 aug 10: hide for BAF
				// echo "			<td valign='top' width='500px'>$query&nbsp;</td>\n";
				
				echo "		</tr>\n";
			}
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function deleteArea() {
	global $Setting, $id;
	$bOK = $Setting->DeleteArea($id);
	if ($bOK) {
		echo "<div>Application successfully deleted...</div>\n";
	} else {
		echo "<div>Application fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=a");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertAreaAction() {
	global $Setting, $User, $idArea, $nameArea, $descArea, $dbArea, $queryArea;
	
	// $idArea = (isset($_REQUEST['idArea']) ? trim($_REQUEST['idArea']) : '');
	// $nameArea = (isset($_REQUEST['nameArea']) ? trim($_REQUEST['nameArea']) : '');
	// $descArea = (isset($_REQUEST['descArea']) ? trim($_REQUEST['descArea']) : '');
	// $dbArea = (isset($_REQUEST['dbArea']) ? trim($_REQUEST['dbArea']) : '');
	// $queryArea = (isset($_REQUEST['queryArea']) ? trim($_REQUEST['queryArea']) : '');
	
	if ($dbArea == "0") {
		$dbArea = "";
	}
	
	$bOK = $Setting->InsertArea($idArea, $nameArea, $descArea, $dbArea, $queryArea);
	
	$dbName = $User->GetDatabaseName($dbArea);
	$idArea = nullable_htmlspecialchar($idArea, ENT_QUOTES);
	$nameArea = nullable_htmlspecialchar($nameArea, ENT_QUOTES);
	$descArea = nullable_htmlspecialchar($descArea, ENT_QUOTES);	
	$dbName = nullable_htmlspecialchar($dbName, ENT_QUOTES);	
	$queryArea = nullable_htmlspecialchar($queryArea, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=a&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formArea'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert Application";
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
	echo "		<td valign='top'><input type='hidden' id='idArea' name='idArea' value='$idArea' autocomplete='off'></input>$idArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameArea' name='nameArea' value='$nameArea' autocomplete='off'></input>$nameArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descArea' name='descArea' value='$descArea' autocomplete='off'></input>$descArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Database</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='dbArea' name='dbArea' value='$dbArea' autocomplete='off'></input>$dbName</td>\n";
	echo "	</tr>\n";
	
	// PauL 26 aug 10: hide for BAF
	// echo "	<tr>\n";
	// echo "		<td valign='top'>Query</td>\n";
	// echo "		<td valign='top'>:</td>\n";
	// echo "		<td valign='top'><input type='hidden' id='queryArea' name='queryArea' value='$queryArea' autocomplete='off'></input>$queryArea</td>\n";
	// echo "	</tr>\n";
	
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formArea\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=a");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function insertArea() {
	global $Setting, $mode, $idArea, $nameArea, $descArea, $dbArea, $queryArea;
	
	// $idArea = (isset($_REQUEST['idArea']) ? trim($_REQUEST['idArea']) : '');
	// $nameArea = (isset($_REQUEST['nameArea']) ? trim($_REQUEST['nameArea']) : '');
	// $descArea = (isset($_REQUEST['descArea']) ? trim($_REQUEST['descArea']) : '');
	// $dbArea = (isset($_REQUEST['dbArea']) ? trim($_REQUEST['dbArea']) : '');
	// $queryArea = (isset($_REQUEST['queryArea']) ? trim($_REQUEST['queryArea']) : '');
	
	if ($idArea == "") {
		// Initial insert
		$idArea = "a" . $Setting->GetNextAreaId();
	}
	
	$arDatabase = null;
	$bOK = $Setting->GetDatabase($arDatabase);
	if (!$bOK) {
		return;
	}

	$idArea = nullable_htmlspecialchar($idArea, ENT_QUOTES);
	$nameArea = nullable_htmlspecialchar($nameArea, ENT_QUOTES);
	$descArea = nullable_htmlspecialchar($descArea, ENT_QUOTES);	
	$dbArea = nullable_htmlspecialchar($dbArea, ENT_QUOTES);	
	$queryArea = nullable_htmlspecialchar($queryArea, ENT_QUOTES);	
	
	echo "	<div class='subTitle'>Insert new application</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	$url64 = base64_encode("setting=1&m=a&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idArea' name='idArea' value='$idArea' length='30' size='20' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameArea' name='nameArea' value='$nameArea' length='100' size='30' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textarea id='descArea' name='descArea' cols='50' rows='3'>$descArea</textarea></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Database</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<select id='dbArea' name='dbArea' onChange='changeDatabase(null);'>\n";
	echo "				<option value='0'>-----------------</option>\n";
	foreach ($arDatabase as $db) {
		$dbId = $db["id"];
		$dbName = $db["name"];
		
		$dbId = nullable_htmlspecialchar($dbId);
		$dbName = nullable_htmlspecialchar($dbName);
	
		echo "				<option value='$dbId' ";
		if ($dbId == $dbArea) {
			echo "selected";
		}
		echo ">$dbName</option>\n";
	}
	echo "				<option value='-99'>&lt; Insert new database &gt;</option>\n";
	echo "			</select>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	
	// PauL 26 aug 10: hide for BAF
	// echo "	<tr>\n";
	// echo "		<td valign='top'>Query</td>\n";
	// echo "		<td valign='top'>:</td>\n";
	// echo "		<td valign='top'><textarea id='queryArea' name='queryArea' cols='50' rows='5'>$queryArea</textarea></td>\n";
	// echo "	</tr>\n";
	
	echo "	<tr>\n";
	echo "		<td colspan='3' height='10px'></td>\n";
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

function copyAreaAction() {
	global $Setting, $User, $idArea, $nameArea, $descArea, $copyArea;
	
	$bOK = $Setting->CopyArea($idArea, $nameArea, $descArea, $copyArea);
	
	$areaSourceName = $User->GetAreaName($copyArea);
	$idArea = nullable_htmlspecialchar($idArea, ENT_QUOTES);
	$nameArea = nullable_htmlspecialchar($nameArea, ENT_QUOTES);
	$descArea = nullable_htmlspecialchar($descArea, ENT_QUOTES);
	$areaSourceName = nullable_htmlspecialchar($areaSourceName, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=a&sm=c");
	echo "<form method='POST' action='main.php?param=$url64' id='formArea'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Copy Application";
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
	echo "		<td valign='top'><input type='hidden' id='idArea' name='idArea' value='$idArea'></input>$idArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameArea' name='nameArea' value='$nameArea'></input>$nameArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descArea' name='descArea' value='$descArea'></input>$descArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Source</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='copyArea' name='copyArea' value='$copyArea'></input>$areaSourceName</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formArea\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=a");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function copyArea() {
	global $Setting, $mode, $idArea, $nameArea, $descArea, $copyArea;
	
	if ($idArea == "") {
		// Initial insert
		$idArea = "a" . $Setting->GetNextAreaId();
	}
	
	$arDatabase = null;
	$bOK = $Setting->GetDatabase($arDatabase);
	if (!$bOK) {
		return;
	}

	$idArea = nullable_htmlspecialchar($idArea, ENT_QUOTES);
	$nameArea = nullable_htmlspecialchar($nameArea, ENT_QUOTES);
	$descArea = nullable_htmlspecialchar($descArea, ENT_QUOTES);	
	
	echo "	<div class='subTitle'>Copy application</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	$url64 = base64_encode("setting=1&m=a&sm=c&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idArea' name='idArea' value='$idArea' length='30' size='20' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameArea' name='nameArea' value='$nameArea' length='100' size='30' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textarea id='descArea' name='descArea' cols='50' rows='3'>$descArea</textarea></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Source</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<select id='copyArea' name='copyArea'>\n";
	echo "				<option value='-1'>-------</option>\n";
	$bOK = $Setting->GetArea($areaIds);
	if ($bOK) {
		foreach ($areaIds as $areaId) {
			$id = $areaId["id"];
			$name = $areaId["name"];

			if ($copyArea == $id) {
				echo "				<option value='$id' selected>$name</option>\n";
			}
			echo "				<option value='$id'>$name</option>\n";
		}
	}
	echo "			</select>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' height='10px'></td>\n";
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

function editAreaAction() {
	global $Setting, $User, $idArea, $nameArea, $descArea, $queryArea, $dbArea;
	
	// $idArea = (isset($_REQUEST['idArea']) ? trim($_REQUEST['idArea']) : '');
	// $nameArea = (isset($_REQUEST['nameArea']) ? trim($_REQUEST['nameArea']) : '');
	// $descArea = (isset($_REQUEST['descArea']) ? trim($_REQUEST['descArea']) : '');
	// $queryArea = (isset($_REQUEST['queryArea']) ? trim($_REQUEST['queryArea']) : '');
	// $dbArea = (isset($_REQUEST['dbArea']) ? trim($_REQUEST['dbArea']) : '');

	if ($dbArea == "0") {
		$dbArea = "";
	}
	
	$bOK = $Setting->EditArea($idArea, $nameArea, $descArea, $dbArea, $queryArea);
	
	$dbName = $User->GetDatabaseName($dbArea);
	$idArea = nullable_htmlspecialchar($idArea);
	$nameArea = nullable_htmlspecialchar($nameArea);
	$descArea = nullable_htmlspecialchar($descArea);
	$dbName = nullable_htmlspecialchar($dbName);
	$queryArea = nullable_htmlspecialchar($queryArea);
	
	$url64 = base64_encode("setting=1&m=a&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formArea'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit Application";
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
	echo "		<td valign='top'><input type='hidden' id='idArea' name='idArea' value='$idArea' autocomplete='off'></input>$idArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameArea' name='nameArea' value='$nameArea' autocomplete='off'></input>$nameArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descArea' name='descArea' value='$descArea' autocomplete='off'></input>$descArea</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Database</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='dbArea' name='dbArea' value='$dbArea' autocomplete='off'></input>$dbName</td>\n";
	echo "	</tr>\n";
	
	// PauL 26 aug 10: hide for BAF
	// echo "	<tr>\n";
	// echo "		<td valign='top'>Query</td>\n";
	// echo "		<td valign='top'>:</td>\n";
	// echo "		<td valign='top'><input type='hidden' id='queryArea' name='queryArea' value='$queryArea' autocomplete='off'></input>$queryArea</td>\n";
	// echo "	</tr>\n";
	
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formArea\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=a");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function editArea() {
	global $Setting, $id, $mode;
	
	$arArea = $Setting->GetAreaDetail($id);
	$idArea = $arArea["id"];
	$nameArea = $arArea["name"];
	$descArea = $arArea["desc"];
	$queryArea = $arArea["query"];
	$dbArea = $arArea["db"];
	
	// quote fix
	$idArea = nullable_htmlspecialchar($idArea, ENT_QUOTES);
	$nameArea = nullable_htmlspecialchar($nameArea, ENT_QUOTES);
	$descArea = nullable_htmlspecialchar($descArea, ENT_QUOTES);
	$queryArea = nullable_htmlspecialchar($queryArea, ENT_QUOTES);
	$dbArea = nullable_htmlspecialchar($dbArea, ENT_QUOTES);
	
	$arDatabase = null;
	$bOK = $Setting->GetDatabase($arDatabase);
	if (!$bOK) {
		return;
	}
	
	echo "	<div class='subTitle'>Edit application '$nameArea'</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	$url64 = base64_encode("setting=1&m=a&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$idArea<input type='hidden' id='idArea' name='idArea' value='$idArea' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameArea' name='nameArea' length='100' size='30' value='$nameArea' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textarea id='descArea' name='descArea' cols='50' rows='3'>$descArea</textarea></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Database</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<select id='dbArea' name='dbArea' onChange='changeDatabase(\"$id\");'>\n";
	echo "				<option value='0'>-----------------</option>\n";
	foreach ($arDatabase as $db) {
		$dbId = $db["id"];
		$dbName = $db["name"];
		
		$dbId = nullable_htmlspecialchar($dbId);
		$dbName = nullable_htmlspecialchar($dbName);
	
		echo "				<option value='$dbId' ";
		if ($dbId == $dbArea) {
			echo "selected";
		}
		echo ">$dbName</option>\n";
	}
	echo "				<option value='-99'>&lt; Insert new database &gt;</option>\n";
	echo "			</select>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	
	// PauL 26 aug 10: hide for BAF
	// echo "	<tr>\n";
	// echo "		<td valign='top'>Query</td>\n";
	// echo "		<td valign='top'>:</td>\n";
	// echo "		<td valign='top'><textarea id='queryArea' name='queryArea' cols='50' rows='5'>$queryArea</textarea></td>\n";
	// echo "	</tr>\n";
	
	echo "	<tr>\n";
	echo "		<td colspan='3' height='10px'></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' align='center'>\n";
	$url64 = base64_encode("setting=1&m=a");
	echo "			<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	echo "			&nbsp;&nbsp;\n";
	echo "			<input type='submit' value='Save'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}

function listArea() {
	global $Setting, $id, $User;
	
	$areaName = $User->GetAreaName($id);
	$areaName = nullable_htmlspecialchar($areaName, ENT_QUOTES);
	$dbId = $User->GetDatabaseFromArea($id);
	$dbName = $User->GetDatabaseName($dbId);
	$dbName = nullable_htmlspecialchar($dbName, ENT_QUOTES);
	
	$arConfig = $User->GetDatabaseConfig($dbId);
	
	$terminal = null;
	$bOK = $User->GetTerminal($id, "", $terminal);
	// var_dump($terminal);
	
	echo "	<div class='subTitle'>Terminal in application '$areaName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	if ($bOK === true) {
		echo "	<div>From database: <b>$dbName</b></div>\n";
		echo "	<div class='spacer10'></div>\n";
		echo "	<table cellspacing='3px' cellpadding='3px' id='tableTerminal'>\n";
		echo "		<tr>\n";
		foreach ($arConfig as $value) {
			$header = $value["value"];
			
			echo "			<th>$header</th>\n";
		}
		echo "		</tr>\n";
		
		// print function
		echo "		<tr>\n";
		foreach ($terminal as $term) {
			foreach ($arConfig as $value) {
				$key = $value["key"];
				$header = $value["value"];
				
				$value = $term[$header];
				echo "			<td>$value&nbsp;</td>\n";
			}
			echo "		</tr>\n";
		}
	} else if ($bOK == -1) {
		// error query not specified
		echo "	<div>Error: Query not specified</div>\n";
	} else if ($bOK == -2) {
		// error database not specified
		echo "	<div>Error: Database not specified</div>\n";
	} else if ($bOK == -3) {
		// erorr query is not select
		echo "	<div>Error: Query is not 'SELECT'</div>\n";
	} else if ($bOK == -4) {
		// database connection failed
		echo "	<div>Error: Database connection failed</div>\n";
		// echo "$dbId";
		$arDatabase = $Setting->GetDatabaseDetail($dbId);
		if ($arDatabase == null) {
			return;
		}
		$nameDb = $arDatabase["name"];
		$schemaDb = $arDatabase["schema"];
		$portDb = $arDatabase["port"];
		$hostDb = $arDatabase["host"];
		$userDb = $arDatabase["user"];
		$pwdDb = $arDatabase["pwd"];
		
		$n = strlen($pwdDb);
		$pwdDbDisp = "";
		for ($i = 0; $i < $n; $i++) {
			$pwdDbDisp .= "&bull;";
		}
	
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
		echo "		<td valign='top'>$pwdDb</td>\n";
		echo "	</tr>\n";
	} else if ($bOK == -5) {
		// no database configuration specified
		echo "	<div>Error: Database configuration not specified</div>\n";
	} else if ($bOK == -6) {
		// error database not specified
		echo "	<div>Error: Must accessed from module</div>\n";
	}
	
	// print table end
	echo "	</table>\n";
}

function listModuleAccess($confirmMessage = null) {
	global $data, $User, $Setting, $id;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
		
		$areaname = $User->GetAreaName($id);
		$areaname = nullable_htmlspecialchar($areaname);
		
		echo "	<div class='subTitle'>List module in application '$areaname'</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		echo "	<div class='confirmation'>$confirmMessage</div>\n";
				
		echo "	<div class='spacer10'></div>\n";
		// print table area
		$arModule = $User->GetModuleAccessable($id);
		$allModuleCount = $User->GetAllModuleCount();
		$bOK = $Setting->GetModule($arAllModule);
		if ($bOK) {
			// var_dump($arModule);
			// var_dump($arAllModule);
			$url64 = base64_encode("setting=1&m=a&sm=l&i=$id");
			echo "<form action='main.php?param=$url64' method='POST'>\n";
			echo "	<input type='hidden' id='a' name='a' value='1'></input>\n";
			echo "	<table cellspacing='3px' cellpadding='3px'>\n";
			echo "		<tr>\n";
			echo "			<th>\n";
			echo "				<label><input type='checkbox' id='moduleAccessAll' onClick='accessAll(this.checked)' ";
			if ($allModuleCount > 0 && $allModuleCount == count($arModule)) {
				echo "checked";
			}
			echo "></input>\n";
			echo "				Access</label>\n";
			echo "			</th>\n";
			echo "			<th>Id</th>\n";
			echo "			<th>Name</th>\n";
			echo "		</tr>\n";
			
			$i = 0;
			// var_dump($arModule);
			foreach ($arAllModule as $mod) {
				$moduleId = $mod["id"];
				$moduleName = $mod["name"];
				
				echo "		<tr>\n";
				echo "			<td align='center'>\n";
				echo "				<input type='checkbox' id='moduleAccess$i' name='moduleAccess[$i]' value='$moduleId' onClick='if (!this.checked) document.getElementById(\"moduleAccessAll\").checked = false;' ";
				if ($arModule != null && in_array($moduleId, $arModule)) {
					echo "checked";
				}
				echo "></input>\n";
				echo "			</td>\n";
				echo "			<td valign='top'>$moduleId&nbsp;</td>\n";
				echo "			<td valign='top'>$moduleName&nbsp;</td>\n";
				echo "		</tr>\n";
				
				$i++;
			}
			echo "		<tr>\n";
			echo "			<td colspan='3'>\n";
			echo "				<input type='submit' value='Save'>\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			echo "	</table>\n";
			echo "</form>\n";
		}
	}
	return $bOK;
}

function saveModuleAccess() {
	global $User, $id, $moduleAccess;

	$newModuleAccess = $moduleAccess;
	$arModuleAccess = $User->GetModuleAccessable($id);
	$confirmMessage = "Nothing changed";
	
	if ($newModuleAccess == null) {
		$User->DeleteModuleAccessable($id, null);
		$confirmMessage = "Clear all module in this area";
	} else {
		$arInsert = array();
		$i = 0;
		foreach ($newModuleAccess as $newAccess) {
			if ($arModuleAccess == null || ($arModuleAccess != null && !in_array($newAccess, $arModuleAccess))) {
				$arInsert[$i] = $newAccess;
				$i++;
			}
		}
		
		if ($i > 0) {
			$User->InsertModuleAccessable($id, $arInsert);
			$confirmMessage = "Successfully change module";
		}
		
		$arDelete = array();
		$i = 0;
		if ($arModuleAccess != null) {
			foreach ($arModuleAccess as $oldAccess) {
				if (!in_array($oldAccess, $newModuleAccess)) {
					$arDelete[$i] = $oldAccess;
					$i++;
				}
			}
		}
		
		if ($i > 0) {
			$User->DeleteModuleAccessable($id, $arDelete);
			$confirmMessage = "Successfully change module";
		}
	}
	
	// display list
	listModuleAccess($confirmMessage);
}

function listAreaCfg() {
	global $Setting, $User, $id;
	
	$areaName = $User->GetAreaName($id);
	echo "	<div class='subTitle'>List configuration in application '$areaName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	// insert module configuration
	$url64 = base64_encode("setting=1&m=a&sm=ic&i=$id");
	echo "<a href='main.php?param=$url64'>Add new configuration</a>\n";
	echo "<div class='spacer10'></div>\n";
	
	$arConfig = $Setting->GetAreaConfig($id);
	if ($arConfig) {
?>
<script	type='text/javascript'>
function confirmDeleteAreaCfg(id, key) {
	var ans = confirm("Delete configuration '" + key + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=a&sm=dc&i=' + id + '&k=' + key);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php
		echo "<table cellspacing='3px' cellpadding='3px'>\n";
		echo "	<tr>\n";
		echo "		<th>Option</th>\n";
		echo "		<th>Key</th>\n";
		echo "		<th>Value</th>\n";
		echo "	</tr>\n";
		foreach ($arConfig as $conf) {
			$mKey = $conf["key"];
			$mValue = $conf["value"];
			
			echo "	<tr>\n";
			echo "		<td valign='top'>\n";
			echo "			&nbsp;\n";
			$url64 = base64_encode("setting=1&m=a&sm=ec&i=$id&k=$mKey");
			echo "			<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit configuration' title='Edit configuration'></img></a>\n";
			echo "			&nbsp;\n";
			echo "			<a href='#' onClick='confirmDeleteAreaCfg(\"$id\", \"$mKey\")''><img border='0' src='image/icon/cancel.png' alt='Delete configuration' title='Delete configuration'></img></a>\n";
			echo "			&nbsp;\n";
			echo "		</td>\n";
			echo "		<td valign='top'>$mKey&nbsp;</td>\n";
			echo "		<td width='500px' valign='top'>" . nl2br($mValue) . "&nbsp;</td>\n";
			echo "	</tr>\n";
		}
		echo "</table>\n";
	} else {
		echo "No configuration available.";
	}
}

function insertAreaCfgAction() {
	global $Setting, $User, $id, $mode, $subMode, $key, $value;

	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	$bOK = $Setting->InsertAreaConfig($id, $key, $value);
	
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$areaName = $User->GetAreaName($id);
	
	$url64 = base64_encode("setting=1&m=a&sm=ic&i=$id");
	echo "<form method='POST' action='main.php?param=$url64' id='formArea'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert application $areaName's configuration";
	if ($bOK) {
		echo " succeed\n";
	} else {
		echo " failed\n";
	}
	echo "		</th>\n";
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
		$url64 = base64_encode("setting=1&m=$mode&sm=lc&i=$id");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function insertAreaCfg() {
	global $id, $User, $mode, $subMode, $key, $value;
	
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$areaName = $User->GetAreaName($id);
	echo "	<div class='subTitle'>Add new configuration for application '$areaName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=$mode&sm=$subMode&i=$id&a=1");
	echo "<form action='main.php?param=$url64' method='POST'>\n";
	echo "	<table class='transparent'>\n";
	echo "		<tr>\n";
	echo "			<td>Key</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='key' id='key' size='20' length='45' value='$key' autocomplete='off'></input></td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td valign='top'>Value</td>\n";
	echo "			<td valign='top'>:</td>\n";
	echo "			<td valign='top'>\n";
	echo "				<textarea name='value' id='value' cols='30' rows='2'>$value</textarea>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' height='10px'>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' align='center'>\n";
	$url64 = base64_encode("setting=1&m=$mode&sm=$subMode&i=$id");
	echo "				<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	echo "				&nbsp;&nbsp;\n";
	echo "				<input type='submit' value='Save'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
}

function editAreaCfgAction() {
	global $Setting, $User, $id, $mode, $oldKey, $key, $value, $ar, $md;

	// $oldKey = (isset($_REQUEST['oldKey']) ? trim($_REQUEST['oldKey']) : '');
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	// Redirect from module approve sms
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	// $moduleId = (isset($_REQUEST['md']) ? trim($_REQUEST['md']) : '');
	$areaId = $ar;
	$moduleId = $md;
	
	$bOK = $Setting->EditAreaConfig($id, $oldKey, $key, $value);
	
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$areaName = $User->GetAreaName($id);
	
	$url64 = base64_encode("setting=1&m=$mode&sm=ic&i=$id&k=$oldKey");
	echo "<form method='POST' action='main.php?param=$url64' id='formArea'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit module $areaName's configuration";
	if ($bOK) {
		echo " succeed\n";
	} else {
		echo " failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Key</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='key' name='key' value='$key' autocomplete='off'></input>$key</td>\n";
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
		echo "	<a href='#' onClick='document.getElementById(\"formArea\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		if ($areaId != "" && $moduleId != "") {
			$url64 = base64_encode("a=$areaId&m=$moduleId");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Redirect to locket SMS... <img src='image/icon/wait.gif' alt=''></img>\n";
		} else {
			$url64 = base64_encode("setting=1&m=$mode&sm=lc&i=$id");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		}
	}
	echo "</div>\n";
}

function editAreaCfg() {
	global $Setting, $User, $id, $mode, $subMode, $o, $k, $ar, $md;
	
	// $oldKey = (isset($_REQUEST['o']) ? trim($_REQUEST['o']) : '');
	// $key = (isset($_REQUEST['k']) ? trim($_REQUEST['k']) : '');
	$oldKey = $o;
	$key = $k;
	if ($oldKey == "") {
		$oldKey = $key;
	}
	
	// Redirect from module approve sms
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	// $moduleId = (isset($_REQUEST['md']) ? trim($_REQUEST['md']) : '');
	$areaId = $ar;
	$moduleId = $md;
	
	$value = $Setting->GetAreaConfigValue($id, $oldKey);
	
	// quote fix
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$areaName = $User->GetAreaName($id);
	echo "	<div class='subTitle'>Edit configuration '$oldKey' in application '$areaName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=$mode&sm=$subMode&i=$id&a=1");
	echo "<form action='main.php?param=$url64' method='POST'>\n";
	echo "	<table class='transparent'>\n";
	echo "		<tr>\n";
	echo "			<td>Key</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td>\n";
	if ($areaId != "" && $moduleId != "") {
		echo "				<input type='hidden' name='key' id='key' value='$key'></input>$key\n";
		echo "				<input type='hidden' name='oldKey' id='oldKey' value='$oldKey'></input>\n";
		echo "				<input type='hidden' name='ar' id='ar' value='$areaId'></input>\n";
		echo "				<input type='hidden' name='md' id='md' value='$moduleId'></input>\n";
	} else {
		echo "				<input type='text' name='key' id='key' size='20' length='45' value='$key' autocomplete='off'></input>\n";
		echo "				<input type='hidden' name='oldKey' id='oldKey' value='$oldKey' autocomplete='off'></input>\n";
	}
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td valign='top'>Value</td>\n";
	echo "			<td valign='top'>:</td>\n";
	echo "			<td valign='top'>\n";
	echo "				<textarea name='value' id='value' cols='30' rows='2'>$value</textarea>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' height='10px'>\n";
	echo "		</tr>\n";
	echo "		<tr>\n";
	echo "			<td colspan='3' align='center'>\n";
	$url64 = base64_encode("setting=1&m=$mode&sm=$subMode&i=$id");
	echo "				<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	echo "				&nbsp;&nbsp;\n";
	echo "				<input type='submit' value='Save'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
}

function deleteAreaCfg() {
	global $Setting, $id, $mode, $k;
	$key = $k;
	// $key = (isset($_REQUEST['k']) ? trim($_REQUEST['k']) : '');
	$bOK = $Setting->DeleteAreaConfig($id, $key);
	if ($bOK) {
		echo "<div>Application configuration successfully deleted...</div>\n";
	} else {
		echo "<div>Application configuration fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=$mode&sm=lc&i=$id");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}



?>
