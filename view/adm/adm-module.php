<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management module
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertModuleAction();
	} else {
		insertModule();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editModuleAction();
	} else {
		editModule();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteModule();
	
} else if ($subMode == "lc") {
	// Submode list
	if ($action) {
		copyModuleCfg();
	} else {
		listModuleCfg();
	}
} else if ($subMode == "ic") {
	// Submode insert module configuration
	if ($action) {
		insertModuleCfgAction();
	} else {
		insertModuleCfg();
	}
} else if ($subMode == "ec") {
	// Submode edit module configuration
	if ($action) {
		editModuleCfgAction();
	} else {
		editModuleCfg();
	}
} else if ($subMode == "dc") {
	// Submode delete module configuration
	deleteModuleCfg();
} else if ($subMode == "da") {
	// Submode delete all module configuration
	deleteAllModuleCfg();
} else {
	// List all
	$bOK = printModuleSetting();
	if (!$bOK) {
		return;
	}
}

function printModuleSetting() {
	global $cData, $data, $json, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
				
		echo "	<div class='subTitle'>List modules</div>\n";
		echo "	<div class='spacer10'></div>\n";
				
		$url64 = base64_encode("setting=1&m=m&sm=i");
		echo "	<a href='main.php?param=$url64'>Add new module</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		// print table Module
		$bOK = $Setting->GetModule($ModuleIds);
		if ($bOK) {
?>
<script type='text/javascript'>
function confirmDeleteModule(name, id) {
	var ans = confirm("Delete module '" + name + "' ?");
	if (ans) {
		var url = Base64.encode("setting=1&m=m&sm=d&i=" + id);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php
			// var_dump($ModuleIds);
			echo "	<table cellspacing='3px' cellpadding='3px'>\n";
			echo "		<tr>\n";
			echo "			<th>Option</td>\n";
			echo "			<th>Id</td>\n";
			echo "			<th>Name</td>\n";
			echo "			<th>Description</td>\n";
			echo "			<th>View</td>\n";
			echo "		</tr>\n";
			
			foreach ($ModuleIds as $ModuleId) {
				$id = $ModuleId["id"];
				$name = $ModuleId["name"];
				$desc = $ModuleId["desc"];
				$view = $ModuleId["view"];
				
				$name = nullable_htmlspecialchar($name, ENT_QUOTES);
				$desc = nullable_htmlspecialchar($desc, ENT_QUOTES);
				$view = nullable_htmlspecialchar($view, ENT_QUOTES);
				
				echo "		<tr>\n";
				echo "			<td align='center' valign='top'>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=m&sm=e&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
				echo "				&nbsp;\n";
				echo "				<a href='#' onClick='confirmDeleteModule(\"$name\", \"$id\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/tools.png' alt='List configuration' title='List configuration'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td valign='top'>$id&nbsp;</td>\n";
				echo "			<td valign='top'>$name&nbsp;</td>\n";
				echo "			<td valign='top'>$desc&nbsp;</td>\n";
				if ($view) {
					echo "			<td valign='top'>$view&nbsp;</td>\n";
				} else {
					echo "			<td valign='top'><i>default</i>&nbsp;</td>\n";
				}
				echo "		</tr>\n";
			}
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function deleteModule() {
	global $Setting, $id;
	$bOK = $Setting->DeleteModule($id);
	if ($bOK) {
		echo "<div>Module successfully deleted...</div>\n";
	} else {
		echo "<div>Module fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=m");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertModuleAction() {
	global $Setting, $idModule, $nameModule, $descModule, $viewModule;
	
	// $idModule = (isset($_REQUEST['idModule']) ? trim($_REQUEST['idModule']) : '');
	// $nameModule = (isset($_REQUEST['nameModule']) ? trim($_REQUEST['nameModule']) : '');
	// $descModule = (isset($_REQUEST['descModule']) ? trim($_REQUEST['descModule']) : '');
	// $viewModule = (isset($_REQUEST['viewModule']) ? trim($_REQUEST['viewModule']) : '');

	$bOK = $Setting->InsertModule($idModule, $nameModule, $descModule, $viewModule);
	
	$idModule = nullable_htmlspecialchar($idModule, ENT_QUOTES);
	$nameModule = nullable_htmlspecialchar($nameModule, ENT_QUOTES);
	$descModule = nullable_htmlspecialchar($descModule, ENT_QUOTES);
	$viewModule = nullable_htmlspecialchar($viewModule, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=m&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formModule'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert Module";
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
	echo "		<td valign='top'><input type='hidden' id='idModule' name='idModule' value='$idModule' autocomplete='off'></input>$idModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameModule' name='nameModule' value='$nameModule' autocomplete='off'></input>$nameModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descModule' name='descModule' value='$descModule' autocomplete='off'></input>$descModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>View</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='viewModule' name='viewModule' value='$viewModule' autocomplete='off'></input>$viewModule</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formModule\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=m");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function insertModule() {
	global $Setting, $mode, $idModule, $nameModule, $descModule, $viewModule;
	
	// $idModule = (isset($_REQUEST['idModule']) ? trim($_REQUEST['idModule']) : '');
	// $nameModule = (isset($_REQUEST['nameModule']) ? trim($_REQUEST['nameModule']) : '');
	// $descModule = (isset($_REQUEST['descModule']) ? trim($_REQUEST['descModule']) : '');
	// $viewModule = (isset($_REQUEST['viewModule']) ? trim($_REQUEST['viewModule']) : '');
	
	if ($idModule == "") {
		// Initial insert
		$idModule = "m" . $Setting->GetNextModuleId();
	}
	
	// DEPRECATED: 'view/' pasti ditambahkan
	// if ($viewModule == "") {
		// Initial insert
		// $viewModule = "view/";
	// }

	$idModule = nullable_htmlspecialchar($idModule, ENT_QUOTES);
	$nameModule = nullable_htmlspecialchar($nameModule, ENT_QUOTES);
	$descModule = nullable_htmlspecialchar($descModule, ENT_QUOTES);
	$viewModule = nullable_htmlspecialchar($viewModule, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Insert new module</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	$url64 = base64_encode("setting=1&m=m&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idModule' name='idModule' length='30' size='20' value='$idModule' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameModule' name='nameModule' length='100' size='30' value='$nameModule' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textArea id='descModule' name='descModule' cols='50' rows='3'>$descModule</textArea></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>View</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>view/<input type='text' id='viewModule' name='viewModule' length='30' size='20' value='$viewModule' autocomplete='off'></input></td>\n";
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

function editModuleAction() {
	global $Setting, $idModule, $nameModule, $descModule, $viewModule;
	
	// $idModule = (isset($_REQUEST['idModule']) ? trim($_REQUEST['idModule']) : '');
	// $nameModule = (isset($_REQUEST['nameModule']) ? trim($_REQUEST['nameModule']) : '');
	// $descModule = (isset($_REQUEST['descModule']) ? trim($_REQUEST['descModule']) : '');
	// $viewModule = (isset($_REQUEST['viewModule']) ? trim($_REQUEST['viewModule']) : '');

	$bOK = $Setting->EditModule($idModule, $nameModule, $descModule, $viewModule);
	
	$url64 = base64_encode("setting=1&m=m&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formModule'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit Module";
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
	echo "		<td valign='top'><input type='hidden' id='idModule' name='idModule' value='$idModule' autocomplete='off'></input>$idModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameModule' name='nameModule' value='$nameModule' autocomplete='off'></input>$nameModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descModule' name='descModule' value='$descModule' autocomplete='off'></input>$descModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>View</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='viewModule' name='viewModule' value='$viewModule' autocomplete='off'></input>$viewModule</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formModule\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=m");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
}

function editModule() {
	global $Setting, $id, $mode;
	
	$arModule = $Setting->GetModuleDetail($id);
	$idModule = $arModule["id"];
	$nameModule = $arModule["name"];
	$descModule = $arModule["desc"];
	$viewModule = $arModule["view"];
	
	// quote fix
	$idModule = nullable_htmlspecialchar($idModule, ENT_QUOTES);
	$nameModule = nullable_htmlspecialchar($nameModule, ENT_QUOTES);
	$descModule = nullable_htmlspecialchar($descModule, ENT_QUOTES);
	$viewModule = nullable_htmlspecialchar($viewModule, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit module '$nameModule'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=m&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idModule' name='idModule' value='$idModule' autocomplete='off'></input>$idModule</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameModule' name='nameModule' length='100' size='30' value='$nameModule' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textArea id='descModule' name='descModule' cols='50' rows='3'>$descModule</textArea></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>View</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>view/<input type='text' id='viewModule' name='viewModule' length='100' size='30' value='$viewModule' autocomplete='off'></input></td>\n";
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

function listModuleCfg() {
	global $Setting, $User, $id;
	
	$moduleName = $User->GetModuleName($id);
	echo "	<div class='subTitle'>List configuration in module '$moduleName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	$arConfig = $Setting->GetModuleConfig($id);
	
	// insert module configuration
	$url64 = base64_encode("setting=1&m=m&sm=ic&i=$id");
	echo "<a href='main.php?param=$url64'>Add new configuration</a>\n";
	if ($arConfig) {
		echo "&nbsp;&nbsp;&nbsp;\n";
		echo "<a href='#' onClick='confirmDeleteAllModuleCfg(\"$moduleName\", \"$id\")'>Delete all configuration</a>\n";
	}
	echo "<div class='spacer10'></div>\n";
	
	if ($arConfig) {
?>
<script type='text/javascript'>
function confirmDeleteModuleCfg(id, key) {
	var ans = confirm("Delete configuration '" + key + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=m&sm=dc&i=' + id + '&k=' + key);
		window.location.href = 'main.php?param=' + url;
	}
}
function confirmDeleteAllModuleCfg(name, id) {
	var ans = confirm("Delete all configuration in module '" + name + "' ?");
	if (ans) {
		var url = Base64.encode("setting=1&m=m&sm=da&i=" + id);
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
			$url64 = base64_encode("setting=1&m=m&sm=ec&i=$id&k=$mKey");
			echo "			<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit configuration' title='Edit configuration'></img></a>\n";
			echo "			&nbsp;\n";
			echo "			<a href='#' onClick='confirmDeleteModuleCfg(\"$id\", \"$mKey\")''><img border='0' src='image/icon/cancel.png' alt='Delete configuration' title='Delete configuration'></img></a>\n";
			echo "			&nbsp;\n";
			echo "		</td>\n";
			echo "		<td valign='top'>$mKey&nbsp;</td>\n";
			echo "		<td width='500px' valign='top'>" . nl2br($mValue) . "&nbsp;</td>\n";
			echo "	</tr>\n";
		}
		echo "</table>\n";
	} else {
		// echo "No configuration available.";
		
		$arModules = null;
		$bOK = $Setting->GetModule($arModules);
		if ($bOK) {
			// copy database configuration
			$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
			echo "<form method='POST' action='main.php?param=$url64'>\n";
			echo "	<input type='hidden' id='a' name='a' value='1'></input>\n";
			echo "<div>\n";
			echo "	No configuration available. Init configuration from ";
			echo "	<select id='moduleInit' name='moduleInit'>\n";
			echo "		<option value='-'>--------</option>\n";
			foreach ($arModules as $iModule) {
				$modId = $iModule["id"];
				$modName = $iModule["name"];
			
				if ($modId != $id) {
					echo "		<option value='$modId'>$modName</option>\n";
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

function copyModuleCfg() {
	global $Setting, $id, $moduleInit;
	
	if ($moduleInit != "") {
		$bOK = $Setting->CopyConfigModule($moduleInit, $id);
		
		if ($bOK) {
			echo "<div>Module configuration successfully copied...</div>\n";
		} else {
			echo "<div>Module configuration failed to copy...</div>\n";
		}
		$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
		echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
		echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
}

function insertModuleCfgAction() {
	global $Setting, $User, $id, $key, $value;

	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	$bOK = $Setting->InsertModuleConfig($id, $key, $value);
	
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$moduleName = $User->GetModuleName($id);
	
	$url64 = base64_encode("setting=1&m=m&sm=ic&i=$id");
	echo "<form method='POST' action='main.php?param=$url64' id='formModule'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert module $moduleName's configuration";
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
		echo "	<a href='#' onClick='document.getElementById(\"formModule\").submit();'>Edit configuration</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function insertModuleCfg() {
	global $id, $User, $mode, $subMode, $key, $value;
	
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$moduleName = $User->GetModuleName($id);
	echo "	<div class='subTitle'>Add new configuration for module '$moduleName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
				
	$url64 = base64_encode("setting=1&m=m&sm=ic&i=$id&a=1");
	echo "<form action='main.php?param=$url64' method='POST'>\n";
	echo "	<table class='transparent'>\n";
	echo "		<tr>\n";
	echo "			<td>Key</td>\n";
	echo "			<td>:</td>\n";
	echo "			<td><input type='text' name='key' id='key' size='30' length='45' value='$key' autocomplete='off'></input></td>\n";
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
	$url64 = base64_encode("setting=1&m=$mode&sm=lc&i=$id");
	echo "				<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	echo "				&nbsp;&nbsp;\n";
	echo "				<input type='submit' value='Save'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
}

function editModuleCfgAction() {
	global $Setting, $User, $id, $oldKey, $key, $value, $ar, $md;

	// $oldKey = (isset($_REQUEST['oldKey']) ? trim($_REQUEST['oldKey']) : '');
	// $key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '');
	// $value = (isset($_REQUEST['value']) ? trim($_REQUEST['value']) : '');
	
	// Redirect from module approve sms
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	// $moduleId = (isset($_REQUEST['md']) ? trim($_REQUEST['md']) : '');
	$areaId = $ar;
	$moduleId = $md;
	
	$bOK = $Setting->EditModuleConfig($id, $oldKey, $key, $value);
	
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$moduleName = $User->GetModuleName($id);
	
	$url64 = base64_encode("setting=1&m=m&sm=ec&i=$id&k=$oldKey");
	echo "<form method='POST' action='main.php?param=$url64' id='formModule'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit module $moduleName's configuration";
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
		echo "	<a href='#' onClick='document.getElementById(\"formModule\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		if ($areaId != "" && $moduleId != "") {
			$url64 = base64_encode("a=$areaId&m=$moduleId");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Redirect to locket SMS... <img src='image/icon/wait.gif' alt=''></img>\n";
		} else {
			$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		}
	}
	echo "</div>\n";
}

function editModuleCfg() {
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
	
	$value = $Setting->GetModuleConfigValue($id, $oldKey);
	
	// quote fix
	$oldKey = nullable_htmlspecialchar($oldKey, ENT_QUOTES);
	$key = nullable_htmlspecialchar($key, ENT_QUOTES);
	$value = nullable_htmlspecialchar($value, ENT_QUOTES);
	
	$moduleName = $User->GetModuleName($id);
	echo "	<div class='subTitle'>Edit configuration '$oldKey' in module '$moduleName'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=m&sm=ec&i=$id&a=1");
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
		echo "				<input type='text' name='key' id='key' size='30' length='45' value='$key' autocomplete='off'></input>\n";
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
	if ($areaId != "" && $moduleId != "") {
		$url64 = base64_encode("a=$areaId&m=$moduleId");
		echo "				<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	} else {
		$url64 = base64_encode("setting=1&m=$mode&sm=lc&i=$id");
		echo "				<input type='button' value='Cancel' onClick='window.location.href=\"main.php?param=$url64\"'></input>\n";
	}
	echo "				&nbsp;&nbsp;\n";
	echo "				<input type='submit' value='Save'></input>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "	</table>\n";
	echo "</form>\n";
}

function deleteModuleCfg() {
	global $Setting, $id, $k;
	// $key = (isset($_REQUEST['k']) ? trim($_REQUEST['k']) : '');
	$key = $k;
	$bOK = $Setting->DeleteModuleConfig($id, $key);
	if ($bOK) {
		echo "<div>Module configuration successfully deleted...</div>\n";
	} else {
		echo "<div>Module configuration fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function deleteAllModuleCfg() {
	global $Setting, $id;
	$bOK = $Setting->DeleteAllModuleConfig($id);
	if ($bOK) {
		echo "<div>Module configuration successfully emptied...</div>\n";
	} else {
		echo "<div>Module configuration fail to empty...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=m&sm=lc&i=$id");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}


?>
