<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management locket
if ($subMode == "l") {
	// Submode list locket
	printLocketConfiguration();
} else if ($subMode == "ec") {
	// Edit configuration
	if ($action) {
		// get parameter
		editConfigurationAction();
	} else {
		editConfiguration();
	}
} else if ($subMode == "lk") {
	listKey();
} else if ($subMode == "dk") {
	deleteKey();
} else if ($subMode == "ek") {
	if ($action) {
		editKeyAction();
	} else {
		editKey();
	}
} else if ($subMode == "ik") {
	if ($action) {
		insertKeyAction();
	} else {
		insertKey();
	}
} else {
	// List all
	$bOK = printLocket();
	if (!$bOK) {
		return;
	}
}

function printLocket() {
	global $cData, $data, $json, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
		
		echo "	<div class='subTitle'>List PP Module</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		echo "	<a href='setting.php?m=l&sm=lk'>List key</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$arModule = null;
		$bOK = $dbSpec->GetModuleOnpays($arModule);
		if ($bOK) {
			// var_dump($arModule);
			echo "	<table cellspacing='1px' cellpadding='3px' border='1'>\n";
			echo "		<tr>\n";
			echo "			<th>Option</th>\n";
			echo "			<th>Id</th>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Description</th>\n";
			echo "			<th>Var Name</th>\n";
			echo "			<th>Version</th>\n";
			echo "			<th>Installed</th>\n";
			echo "		</tr>\n";
			
			foreach ($arModule as $mod) {
				$id = $mod["id"];
				$name = $mod["name"];
				$desc = $mod["desc"];
				$varname = $mod["varname"];
				$version = $mod["version"];
				$installed = $mod["installed"];
				
				echo "		<tr>\n";
				echo "			<td align='center'>\n";
				echo "				&nbsp;\n";
				echo "				<a href='setting.php?m=l&sm=l&i=$id'><img border='0' src='image/icon/mgmt.gif' alt='Edit configuration' title='Edit configuration'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td>$id&nbsp;</td>\n";
				echo "			<td>$name&nbsp;</td>\n";
				echo "			<td>$desc&nbsp;</td>\n";
				echo "			<td>$varname&nbsp;</td>\n";
				echo "			<td>$version&nbsp;</td>\n";
				echo "			<td>$installed&nbsp;</td>\n";
				echo "		</tr>\n";
			}
			
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function printLocketConfiguration() {
	global $cData, $data, $json, $User, $Setting, $id;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
		$moduleName = $User->GetModuleLocketName($id);
		
		echo "	<div class='subTitle'>Configuration in PP module '$moduleName'</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		echo "	<a href='setting.php?m=l&sm=ec&i=$id'>Edit configuration</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$arModuleConf = null;
		$bOK = $Setting->GetModuleLocketKeyConfiguration($arConfigKey);
		$bOK = $Setting->GetModuleOnpaysConfiguration($id, $arConfigKey, $arModuleConf);
		echo "	<table cellspacing='1px' cellpadding='3px' border='1'>\n";
		echo "		<tr>\n";
		echo "			<th>Key</th>\n";
		echo "			<th>Value</th>\n";
		echo "		</tr>\n";
		
		foreach ($arConfigKey as $cKey) {
			$keyName = $cKey["keyName"];
			$key = $cKey["key"];
			$value = null;
			if (isset($arModuleConf[$key])) {
				$value = $arModuleConf[$key];
			}
		
			echo "		<tr>\n";
			echo "			<td>$keyName&nbsp;</td>\n";
			if ($value) {
				echo "			<td>$value&nbsp;</td>\n";
			} else {
				echo "			<td align='center'>-</td>\n";
			}
			echo "		</tr>\n";
		}
		
		echo "	</table>\n";
	}
	return $bOK;
}

function editConfiguration() {
	global $Setting, $User, $id;
	
	$arModConf = null;
	$bOK = $Setting->GetModuleLocketKeyConfiguration($arConfigKey);
	$bOK = $Setting->GetModuleOnpaysConfiguration($id, $arConfigKey, $arModConf);

	$moduleId = (isset($_REQUEST['moduleId']) ? trim($_REQUEST['moduleId']) : '');
	$arValue = array();
	if ($bOK) {
		foreach ($arConfigKey as $cKey) {
			$key = $cKey["key"];
			$arValue[$key] = (isset($arModConf[$key]) ? trim($arModConf[$key]) : '');
		}
	}
	$moduleName = $User->GetModuleLocketName($id);
	
	echo "	<div class='subTitle'>Edit PP module configuration in '$moduleName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	echo "<form method='POST' action='setting.php?m=l&sm=ec&a=1'>\n";
	echo "	<input type='hidden' id='moduleId' name='moduleId' value='$id'></input>\n";
	echo "<table border='0'>\n";
	
	foreach ($arConfigKey as $cKey) {
		$keyName = $cKey["keyName"];
		$key = $cKey["key"];
		$keyValue = "";
		if (isset($arValue[$key])) {
			$keyValue = $arValue[$key];
		}
		
		echo "	<tr>\n";
		echo "		<td valign='top'>$keyName</td>\n";
		echo "		<td valign='top'>:</td>\n";
		echo "		<td valign='top'><input type='text' id='$key' name='$key' value='$keyValue' length='100' size='30' autocomplete='off'></input></td>\n";
		echo "	</tr>\n";
	}
	echo "	<tr>\n";
	echo "		<td colspan='3' height='10px'></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='3' align='center'>\n";
	echo "			<input type='submit' value='Save'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}

function editConfigurationAction() {
	global $Setting;
	
	$bOK = $Setting->GetModuleLocketKeyConfiguration($arConfigKey);
	$moduleId = (isset($_REQUEST['moduleId']) ? trim($_REQUEST['moduleId']) : '');
	$arValue = array();
	foreach ($arConfigKey as $cKey) {
		$key = $cKey["key"];
		$arValue[$key] = (isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '');
	}
	
	$bOK = $Setting->EditModuleOnpaysConfiguration($moduleId, $arValue);
	
	echo "<form method='POST' action='setting.php?m=l&sm=i' id='formLocket'>\n";
	echo "<table border='0'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3'>\n";
	echo "			Edit PP module Configuration";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	
	foreach ($arConfigKey as $cKey) {
		$key = $cKey["key"];
		$keyName = $cKey["keyName"];
		$keyName = nullable_htmlspecialchar($keyName);
		$keyValue = $arValue[$key];
	
		echo "	<tr>\n";
		echo "		<td valign='top'>$keyName</td>\n";
		echo "		<td valign='top'>:</td>\n";
		echo "		<td valign='top'><input type='hidden' id='$key' name='$key' value='$keyValue'></input>$keyValue</td>\n";
		echo "	</tr>\n";
	}
	
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formLocket\").submit();'>Edit locket</a>&nbsp;&nbsp;\n";
	} else {
		echo "	<meta http-equiv='REFRESH' content='1;url=setting.php?m=l&sm=l&i=$moduleId' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	// echo "	<a href='setting.php?m=a'>Manage Area</a>\n";
	echo "</div>\n";
	echo "</form>\n";
}

function listKey() {
	global $Setting;
	
	echo "	<div class='subTitle'>List key</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	echo "	<a href='setting.php?m=l&sm=ik'>Add key</a>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$arKeys = null;
	$bOK = $Setting->GetModuleLocketKeyConfiguration($arKeys);
	if ($bOK) {
		echo "<form method='POST' action='setting.php?m=l&sm=ek&a=1'>\n";
		echo "	<table cellspacing='1px' cellpadding='3px' border='1'>\n";
		echo "		<tr>\n";
		echo "			<th>Option</th>\n";
		echo "			<th>Key</th>\n";
		echo "			<th>Header</th>\n";
		echo "		</tr>\n";
		
		foreach ($arKeys as $cKey) {
			$key = $cKey["key"];
			$keyHtml = nullable_htmlspecialchar($key);
			$keyName = $cKey["keyName"];
			echo "	<tr>\n";
			echo "		<td align='center'>\n";
			echo "			&nbsp;\n";
			echo "			<a href='setting.php?m=l&sm=ek&i=$keyHtml'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
			echo "			&nbsp;\n";
			echo "			<a href='#' onClick='confirmDeleteKey(\"$keyName\", \"$key\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
			echo "			&nbsp;\n";
			echo "		</td>\n";
			echo "		<td valign='top'>$key</td>\n";
			echo "		<td valign='top'>$keyName</td>\n";
			echo "	</tr>\n";
		}
		echo "	</table>\n";
		echo "</form>\n";
	}
}

function deleteKey() {
	global $Setting, $id;
	$bOK = $Setting->DeleteModuleLocketKeyConfiguration($id);
	if ($bOK) {
		echo "<div>Key successfully deleted...</div>\n";
	} else {
		echo "<div>Key fail to delete...</div>\n";
	}
	echo "<meta http-equiv='REFRESH' content='1;url=setting.php?m=l&sm=lk'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertKey() {
	global $Setting;
	
	$key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	$keyName = (isset($_REQUEST['keyName']) ? trim($_REQUEST['keyName']) : '');

	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$keyName = nullable_htmlspecialchar($keyName, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Insert new key</div>\n";
	echo "	<div class='spacer10'></div>\n";
		
	echo "<form method='POST' action='setting.php?m=l&sm=ik&a=1'>\n";
	echo "<table border='0'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Key</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='key' name='key' length='100' size='20' value='$key' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='keyName' name='keyName' length='100' size='30' value='$keyName' autocomplete='off'></input></td>\n";
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

function insertKeyAction() {
	global $Setting;
	
	$key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	$keyName = (isset($_REQUEST['keyName']) ? trim($_REQUEST['keyName']) : '');

	$bOK = $Setting->InsertModuleLocketKeyConfiguration($key, $keyName);
	
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$keyName = nullable_htmlspecialchar($keyName, ENT_QUOTES);
	
	echo "<form method='POST' action='setting.php?m=l&sm=ik' id='formKey'>\n";
	echo "<table border='0'>\n";
	echo "	<tr>\n";
	echo "		<th>\n";
	echo "			Insert Key";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Key</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='key' name='key' value='$key' autocomplete='off'></input>$key</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='keyName' name='keyName' value='$keyName' autocomplete='off'></input>$keyName</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formKey\").submit();'>Edit key</a>&nbsp;&nbsp;\n";
	} else {
		echo "	<meta http-equiv='REFRESH' content='1;url=setting.php?m=l&sm=lk' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	// echo "	<a href='setting.php?m=u'>Manage User</a>\n";
	echo "</div>\n";
	echo "</form>\n";
}

function editKey() {
	global $Setting, $id;
	
	if ($id != "") {
		// initial edit
		$key = $id;
		$oldKey = $key;
		$keyName = $Setting->GetModuleLocketKeyConfigurationName($oldKey);
	} else {
		// next edit
		$key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
		$oldKey = (isset($_REQUEST['oldKey']) ? trim($_REQUEST['oldKey']) : '');
		$keyName = (isset($_REQUEST['keyName']) ? trim($_REQUEST['keyName']) : '');
	}
	
	// quote fix
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$keyName = nullable_htmlspecialchar($keyName, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit key '$keyName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
		
	echo "<form method='POST' action='setting.php?m=l&sm=ek&a=1'>\n";
	echo "	<input type='hidden' id='oldKey' name='oldKey' value='$oldKey'></input>\n";
	echo "<table border='0'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Key</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='key' name='key' value='$key' length='100' size='20' value='$key' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='keyName' name='keyName' length='100' size='30' value='$keyName' autocomplete='off'></input></td>\n";
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

function editKeyAction() {
	global $Setting;
	
	$oldKey = (isset($_REQUEST['oldKey']) ? trim($_REQUEST['oldKey']) : '');
	$key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	$keyName = (isset($_REQUEST['keyName']) ? trim($_REQUEST['keyName']) : '');

	$bOK = $Setting->EditModuleLocketKeyConfiguration($oldKey, $key, $keyName);
	
	echo "<form method='POST' action='setting.php?m=l&sm=ek' id='formKey'>\n";
	echo "	<input type='hidden' id='oldKey' name='oldKey' value='$oldKey' autocomplete='off'></input>\n";
	echo "<table border='0'>\n";
	echo "	<tr>\n";
	echo "		<th>\n";
	echo "			Edit Key";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Key</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='key' name='key' value='$key' autocomplete='off'></input>$key</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='keyName' name='keyName' value='$keyName' autocomplete='off'></input>$keyName</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formKey\").submit();'>Edit key</a>&nbsp;&nbsp;\n";
	} else {
		echo "	<meta http-equiv='REFRESH' content='1;url=setting.php?m=l&sm=lk' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	// echo "	<a href='setting.php?m=a'>Manage Area</a>\n";
	echo "</div>\n";
	echo "</form>\n";
}

?>
