<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode setting
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertSettingAction();
	} else {
		insertSetting();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editSettingAction();
	} else {
		editSetting();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteSetting();
} else if ($subMode == "c") {
	// Submode choose
	chooseSetting($id);
} else {
	// List all
	$bOK = printSetting();
	if (!$bOK) {
		return;
	}
}

// Print list all settings
function printSetting() {
	global $uid, $User, $Setting, $appsSettingId;
	$bOK = false;
	
	echo "<div class='subTitle'>List setting</div>\n";
	echo "<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=s&sm=i");
	echo "<a href='main.php?param=$url64'>Add new setting</a>\n";
	echo "<div class='spacer10'></div>\n";
	
	$arSetting = null;
	$bOK = $Setting->GetApplicationSetting($arSetting);
	if ($bOK) {
		// var_dump($arSetting);
?>
<script type='text/javascript'>
	function confirmDeleteSetting(name, id) {
		var ans = confirm("Delete setting '" + name + "' ?");
		if (ans) {
			var url = Base64.encode("setting=1&m=s&sm=d&i=" + id);
			window.location.href = 'main.php?param=' + url;
		}
	}
</script>
<?php
		echo "	<table cellspacing='3px' cellpadding='3px'>\n";
		echo "		<tr>\n";
		echo "			<th>Option</th>\n";
		echo "			<th>Id</th>\n";
		echo "			<th>Title</th>\n";
		echo "			<th>Style</th>\n";
		echo "			<th>Footer</th>\n";
		echo "		</tr>\n";
		
		foreach ($arSetting as $setting) {
			$id = $setting["id"];
			$title = $setting["title"];
			$stylePath = $setting["stylePath"];
			$footerText = $setting["footer"];
			
			echo "		<tr>\n";
			echo "			<td align='center' valign='top'>\n";
			echo "				&nbsp;\n";
			$url64 = base64_encode("setting=1&m=s&sm=e&i=$id");
			echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
			echo "				&nbsp;\n";
			echo "				<a href='#' onClick='confirmDeleteSetting(\"$title\", \"$id\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
			echo "				&nbsp;\n";
			if ($id == $appsSettingId) {
				echo "				<img border='0' src='image/icon/download-gs.gif' alt='Chosen' title='Chosen'></img></a>\n";
				echo "				&nbsp;\n";
			} else {
				$url64 = base64_encode("setting=1&m=s&sm=c&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/download.gif' alt='Choose' title='Choose'></img></a>\n";
				echo "				&nbsp;\n";
			}
			echo "			</td>\n";
			echo "			<td valign='top'>$id&nbsp;</td>\n";
			echo "			<td valign='top'>$title&nbsp;</td>\n";
			echo "			<td valign='top'>$stylePath&nbsp;</td>\n";
			echo "			<td valign='top'>$footerText&nbsp;</td>\n";
			echo "		</tr>\n";
		}
		echo "	</table>\n";
	}
	return $bOK;
}

// Delete setting
function deleteSetting() {
	global $Setting, $id;
	$bOK = $Setting->DeleteSetting($id);
	if ($bOK) {
		echo "<div>Setting successfully deleted...</div>\n";
	} else {
		echo "<div>Setting fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=s");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

// Insert setting
function insertSetting() {
	global $Setting, $mode, $idSetting, $titleSetting, $stylePath, $footerText;
	
	// $idSetting = (isset($_REQUEST['idSetting']) ? trim($_REQUEST['idSetting']) : '');
	// $titleSetting = (isset($_REQUEST['titleSetting']) ? trim($_REQUEST['titleSetting']) : '');
	// $stylePath = (isset($_REQUEST['stylePath']) ? trim($_REQUEST['stylePath']) : '');
	// $footerText = (isset($_REQUEST['footerText']) ? trim($_REQUEST['footerText']) : '');
	
	$idSetting = nullable_htmlspecialchar($idSetting, ENT_QUOTES);
	$titleSetting = nullable_htmlspecialchar($titleSetting, ENT_QUOTES);
	$stylePath = nullable_htmlspecialchar($stylePath, ENT_QUOTES);
	$footerText = nullable_htmlspecialchar($footerText, ENT_QUOTES);
	
	echo "<div class='subTitle'>Insert new setting</div>\n";
	echo "<div class='spacer10'></div>\n";
		
	$url64 = base64_encode("setting=1&m=s&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table border='0' class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idSetting' name='idSetting' length='20' size='20' value='$idSetting' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Title</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='titleSetting' name='titleSetting' length='100' size='30' value='$titleSetting' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Style Path</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='stylePath' name='stylePath' length='100' size='30' value='$stylePath' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Footer</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textarea cols='50' rows='3' id='footerText' name='footerText'>$footerText</textarea></td>\n";
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

// Insert setting action
function insertSettingAction() {
	global $Setting, $idSetting, $titleSetting, $stylePath, $footerText;
	
	// $idSetting = (isset($_REQUEST['idSetting']) ? trim($_REQUEST['idSetting']) : '');
	// $titleSetting = (isset($_REQUEST['titleSetting']) ? trim($_REQUEST['titleSetting']) : '');
	// $stylePath = (isset($_REQUEST['stylePath']) ? trim($_REQUEST['stylePath']) : '');
	// $footerText = (isset($_REQUEST['footerText']) ? trim($_REQUEST['footerText']) : '');
	
	$bOK = $Setting->InsertSetting($idSetting, $titleSetting, $stylePath, $footerText);
	
	$idSetting = nullable_htmlspecialchar($idSetting, ENT_QUOTES);
	$titleSetting = nullable_htmlspecialchar($titleSetting, ENT_QUOTES);
	$stylePath = nullable_htmlspecialchar($stylePath, ENT_QUOTES);
	$footerText = nullable_htmlspecialchar($footerText, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=s&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formSetting'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert Setting";
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
	echo "		<td valign='top'><input type='hidden' id='idSetting' name='idSetting' value='$idSetting' autocomplete='off'></input>$idSetting</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Title</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='titleSetting' name='titleSetting' value='$titleSetting' autocomplete='off'></input>$titleSetting</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Style Path</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='stylePath' name='stylePath' value='$stylePath' autocomplete='off'></input>$stylePath</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Footer</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='footerText' name='footerText' value='$footerText' autocomplete='off'></input>$footerText</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formSetting\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=s");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

// Edit setting
function editSetting() {
	global $Setting, $id;
	
	$arSetting = $Setting->GetSettingDetail($id);
	$title = $arSetting["title"];
	$stylePath = $arSetting["stylePath"];
	$footerText = $arSetting["footer"];
	
	// quote fix
	$title = nullable_htmlspecialchar($title, ENT_QUOTES);
	$stylePath = nullable_htmlspecialchar($stylePath, ENT_QUOTES);
	$footerText = nullable_htmlspecialchar($footerText, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit setting '$title'</div>\n";
	echo "	<div class='spacer10'></div>\n";
		
	$url64 = base64_encode("setting=1&m=s&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table border='0' class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	// echo "		<td valign='top'><input type='hidden' id='id' name='id' value='$id' autocomplete='off'></input>$id</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<input type='hidden' id='oldId' name='oldId' value='$id' autocomplete='off'></input>\n";
	echo "			<input type='text' id='id' name='id' length='100' size='30' value='$id' autocomplete='off'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Title</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='title' name='title' length='100' size='30' value='$title' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Style Path</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='stylePath' name='stylePath' length='100' size='30' value='$stylePath' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Footer</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textarea cols='50' rows='3' id='footerText' name='footerText'>$footerText</textarea></td>\n";
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

// Edit setting action
function editSettingAction() {
	global $Setting, $id, $title, $stylePath, $footerText, $oldId, $appsSettingId;
	
	$bOK = $Setting->EditSetting($oldId, $id, $title, $stylePath, $footerText);
	
	$title = nullable_htmlspecialchar($title, ENT_QUOTES);
	$stylePath = nullable_htmlspecialchar($stylePath, ENT_QUOTES);
	$footerText = nullable_htmlspecialchar($footerText, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=s&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formSetting'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit Setting";
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
	echo "		<td valign='top'><input type='hidden' id='id' name='id' value='$id' autocomplete='off'></input>$id</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Title</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='title' name='title' value='$title' autocomplete='off'></input>$title</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Style Path</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$stylePath</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Footer</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$footerText</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formSetting\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		// NEW: edit chosen style path
		if ($oldId == $appsSettingId) {
			chooseSetting($id, false);
		}
	
		$url64 = base64_encode("setting=1&m=s");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
}

// Choose setting
function chooseSetting($chosenId, $redirect = true) {
	$bOK = false;
	$sRootPath = str_replace('\\', '/', str_replace(DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'adm', '', dirname(__FILE__))).'/';
	$fStyle = fopen($sRootPath . "chosenStyle", "w");
	if ($fStyle) {
		fputs($fStyle, $chosenId);
		fclose($fStyle);
		$bOK = true;
	}
	
	if ($redirect) {
		if ($bOK) {
			echo "<div>Setting successfully changed...</div>\n";
		} else {
			echo "<div>Setting fail to change...</div>\n";
		}
		$url64 = base64_encode("setting=1&m=s");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
}

?>
