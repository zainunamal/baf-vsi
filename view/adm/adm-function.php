<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management Func
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertFuncAction();
	} else {
		insertFunc();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editFuncAction();
	} else {
		editFunc();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteFunc();
} else {
	// List all
	$bOK = printFuncSetting();
	if (!$bOK) {
		return;
	}
}

?>
<script type='text/javascript'>
var winImage = null;

function showImageList() {
	if (!winImage) {
		// if (winImage.closed) {
		winImage = window.open(
			"view/adm/showImage.php",
			"Image List", 
			"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=300, height=500");
		// }
	} else if (winImage.closed) {
		winImage = window.open(
			"view/adm/showImage.php",
			"Image List", 
			"toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=1, resizable=1, width=300, height=500");
	} else {
		winImage.focus();
	}
}
</script>
<?php

function printFuncSetting() {
	global $cData, $data, $json, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
				
		echo "	<div class='subTitle'>List functions</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$url64 = base64_encode("setting=1&m=f&sm=i");
		echo "	<a href='main.php?param=$url64'>Add new function</a>\n";
		echo "	<div class='spacer10'></div>\n";
			
		// print table Func
		$bOK = $Setting->GetFunction($FuncIds);
		if ($bOK) {
			// var_dump($FuncIds);
?>
<script type='text/javascript'>
function confirmDeleteFunction(name, id) {
	var ans = confirm("Delete function '" + name + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=f&sm=d&i=' + id);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php
			echo "	<table cellspacing='3px' cellpadding='3px'>\n";
			echo "		<tr>\n";
			echo "			<th>Option</td>\n";
			echo "			<th>Id</td>\n";
			echo "			<th>Module</td>\n";
			echo "			<th>Name</td>\n";
			echo "			<th>Page</td>\n";
			echo "			<th>Image</td>\n";
			echo "			<th>Position</td>\n";
			echo "		</tr>\n";
			
			foreach ($FuncIds as $FuncId) {
				$id = $FuncId["id"];
				$mid = $FuncId["mid"];
				$mname = $FuncId["mname"];
				$name = $FuncId["name"];
				$page = $FuncId["page"];
				$image = $FuncId["image"];
				$pos = $FuncId["pos"];
				
				echo "		<tr>\n";
				echo "			<td align='center'>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=f&sm=e&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
				echo "				&nbsp;\n";
				echo "				<a href='#' onClick='confirmDeleteFunction(\"$name\", \"$id\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td>$id&nbsp;</td>\n";
				echo "			<td>$mname&nbsp;</td>\n";
				echo "			<td>$name&nbsp;</td>\n";
				echo "			<td>$page&nbsp;</td>\n";
				echo "			<td>&nbsp;<img src='image/icon/$image' alt=''></img> $image&nbsp;</td>\n";
				
				if ($pos == 0) {
					echo "			<td>Per Terminal&nbsp;</td>\n";
				} else if ($pos == 1) {
					echo "			<td>Per Module&nbsp;</td>\n";
				} else if ($pos == 2) {
					echo "			<td>Hide&nbsp;</td>\n";
				}
				echo "		</tr>\n";
			}
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function deleteFunc() {
	global $Setting, $id;
	$bOK = $Setting->DeleteFunction($id);
	if ($bOK) {
		echo "<div>Function successfully deleted...</div>\n";
	} else {
		echo "<div>Function fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=f");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertFunc() {
	global $Setting, $User, $idFunc, $moduleFunc, $nameFunc, $pageFunc, $imageFunc, $posFunc, $mid, $r, $mode;
	
	// $idFunc = (isset($_REQUEST['idFunc']) ? trim($_REQUEST['idFunc']) : '');
	// $moduleFunc = (isset($_REQUEST['moduleFunc']) ? trim($_REQUEST['moduleFunc']) : '');
	// $nameFunc = (isset($_REQUEST['nameFunc']) ? trim($_REQUEST['nameFunc']) : '');
	// $pageFunc = (isset($_REQUEST['pageFunc']) ? trim($_REQUEST['pageFunc']) : '');
	// $imageFunc = (isset($_REQUEST['imageFunc']) ? trim($_REQUEST['imageFunc']) : '');
	// $posFunc = (isset($_REQUEST['posFunc']) ? trim($_REQUEST['posFunc']) : '');
	
	if ($idFunc == "") {
		// Initial insert
		$idFunc = "f" . $Setting->GetNextFunctionId();
	}
	
	$idFunc = nullable_htmlspecialchar($idFunc, ENT_QUOTES);
	$moduleFunc = nullable_htmlspecialchar($moduleFunc, ENT_QUOTES);
	$nameFunc = nullable_htmlspecialchar($nameFunc, ENT_QUOTES);
	$pageFunc = nullable_htmlspecialchar($pageFunc, ENT_QUOTES);
	$imageFunc = nullable_htmlspecialchar($imageFunc, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=f&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "	<div class='subTitle'>Insert new function</div>\n";
	echo "	<div class='spacer10'></div>\n";
			
	// NEW: direfer dari list module di role
	// $mid = (isset($_REQUEST['mid']) ? trim($_REQUEST['mid']) : '');
	// $roleId = (isset($_REQUEST['r']) ? trim($_REQUEST['r']) : '');
	$roleId = $r;
	if ($mid != "" && $roleId != "") {
		$rolename = $User->GetRoleName($roleId);
		
		echo "	<input type='hidden' id='mid' name='mid' value='$mid'></input>\n";
		echo "	<input type='hidden' id='r' name='r' value='$roleId'></input>\n";
	
		$url64 = base64_encode("setting=1&m=r&sm=l&i=$roleId");
		echo "	<a href='main.php?param=$url64'>&lsaquo; Back to list modules in role '$rolename'</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		// override moduleId
		$moduleFunc = $mid;
	}
	
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Module</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	// module choice
	if ($Setting->GetModule($arModule)) {
		echo "			<select id='moduleFunc' name='moduleFunc'>\n";
		echo "				<option value='0'";
		if (!$moduleFunc || $moduleFunc == "0") {
			echo " selected";
		}
		echo ">--------</option>\n";
		foreach ($arModule as $mod) {
			$moduleId = $mod["id"];
			$moduleName = $mod["name"];
			echo "				<option value='$moduleId'";
			if ($moduleFunc == $moduleId) {
				echo " selected";
			}
			echo ">$moduleName</option>\n";
		}
		echo "			</select>\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idFunc' name='idFunc' length='30' size='20' value='$idFunc' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameFunc' name='nameFunc' length='100' size='30' value='$nameFunc' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Page</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>function/<input type='text' id='pageFunc' name='pageFunc' length='100' size='30' value='$pageFunc' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Image</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>image/icon/\n";
	echo "			<input type='text' id='imageFunc' name='imageFunc' length='100' size='15' value='$imageFunc' autocomplete='off'></input>\n";
	if ($imageFunc != "") {
		echo "			<img id='imageSrcFunc' name='imageSrcFunc' src='image/icon/$imageFunc' alt=''></img>\n";
	} else {
		echo "			<img id='imageSrcFunc' name='imageSrcFunc' src='' alt=''></img>\n";
	}
	echo "			<input type='button' value='List' onClick='showImageList()'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Position</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<select id='posFunc' name='posFunc'>\n";
	echo "				<option value='0'>Per terminal</option>\n";
	echo "				<option value='1'>Per module</option>\n";
	echo "				<option value='2'>Hide</option>\n";
	echo "			</select>\n";
	echo "		</td>\n";
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

function insertFuncAction() {
	global $Setting, $User, $idFunc, $moduleFunc, $nameFunc, $pageFunc, $imageFunc, $posFunc, $mid, $r;
	
	// $idFunc = (isset($_REQUEST['idFunc']) ? trim($_REQUEST['idFunc']) : '');
	// $moduleFunc = (isset($_REQUEST['moduleFunc']) ? trim($_REQUEST['moduleFunc']) : '');
	// $nameFunc = (isset($_REQUEST['nameFunc']) ? trim($_REQUEST['nameFunc']) : '');
	// $pageFunc = (isset($_REQUEST['pageFunc']) ? trim($_REQUEST['pageFunc']) : '');
	// $imageFunc = (isset($_REQUEST['imageFunc']) ? trim($_REQUEST['imageFunc']) : '');
	// $posFunc = (isset($_REQUEST['posFunc']) ? trim($_REQUEST['posFunc']) : '');

	$bOK = $Setting->InsertFunction($idFunc, $moduleFunc, $nameFunc, $pageFunc, $imageFunc, $posFunc);

	$idFunc = nullable_htmlspecialchar($idFunc, ENT_QUOTES);
	$moduleNameFunc = $User->GetModuleName($moduleFunc);
	$moduleNameFunc = nullable_htmlspecialchar($moduleNameFunc, ENT_QUOTES);
	$nameFunc = nullable_htmlspecialchar($nameFunc, ENT_QUOTES);
	$pageFunc = nullable_htmlspecialchar($pageFunc, ENT_QUOTES);
	$imageFunc = nullable_htmlspecialchar($imageFunc, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=f&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formFunc'>\n";
	
	// NEW: direfer dari list module di role
	// $mid = (isset($_REQUEST['mid']) ? trim($_REQUEST['mid']) : '');
	// $roleId = (isset($_REQUEST['r']) ? trim($_REQUEST['r']) : '');
	$roleId = $r;
	if ($mid != "" && $roleId != "") {
		$rolename = $User->GetRoleName($roleId);
		
		echo "	<input type='hidden' id='mid' name='mid' value='$mid'></input>\n";
		echo "	<input type='hidden' id='r' name='r' value='$roleId'></input>\n";
	}
	
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert Function";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Module</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='moduleFunc' name='moduleFunc' value='$moduleFunc'></input>$moduleNameFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idFunc' name='idFunc' value='$idFunc'></input>$idFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameFunc' name='nameFunc' value='$nameFunc'></input>$nameFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Page</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='pageFunc' name='pageFunc' value='$pageFunc'></input>$pageFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Image</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<input type='hidden' id='imageFunc' name='imageFunc' value='$imageFunc'></input>\n";
	echo "			<img src='image/icon/$imageFunc' alt=''></img>\n";
	echo "			($imageFunc)\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Position</td>\n";
	echo "		<td valign='top'>:</td>\n";
	if ($posFunc == 0) {
		echo "		<td>Per Terminal&nbsp;</td>\n";
	} else if ($posFunc == 1) {
		echo "		<td>Per Module&nbsp;</td>\n";
	} else if ($posFunc == 2) {
		echo "		<td>Hide&nbsp;</td>\n";
	}
	echo "			<input type='hidden' id='posFunc' name='posFunc' value='$posFunc'></input>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formFunc\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		if ($mid != "" && $roleId != "") {
			// redirect ke list module
			$url64 = base64_encode("setting=1&m=r&sm=l&i=$roleId");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		} else {
			$url64 = base64_encode("setting=1&m=f");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		}
	}
	echo "</div>\n";
	echo "</form>\n";
}

function editFuncAction() {
	global $Setting, $User, $idFunc, $moduleFunc, $nameFunc, $pageFunc, $imageFunc, $posFunc;
	
	// $idFunc = (isset($_REQUEST['idFunc']) ? trim($_REQUEST['idFunc']) : '');
	// $moduleFunc = (isset($_REQUEST['moduleFunc']) ? trim($_REQUEST['moduleFunc']) : '');
	// $nameFunc = (isset($_REQUEST['nameFunc']) ? trim($_REQUEST['nameFunc']) : '');
	// $pageFunc = (isset($_REQUEST['pageFunc']) ? trim($_REQUEST['pageFunc']) : '');
	// $imageFunc = (isset($_REQUEST['imageFunc']) ? trim($_REQUEST['imageFunc']) : '');
	// $posFunc = (isset($_REQUEST['posFunc']) ? trim($_REQUEST['posFunc']) : '');

	$bOK = $Setting->EditFunction($idFunc, $moduleFunc, $nameFunc, $pageFunc, $imageFunc, $posFunc);
	
	$idFunc = nullable_htmlspecialchar($idFunc, ENT_QUOTES);
	$moduleNameFunc = $User->GetModuleName($moduleFunc);
	$moduleNameFunc = nullable_htmlspecialchar($moduleNameFunc, ENT_QUOTES);
	$nameFunc = nullable_htmlspecialchar($nameFunc, ENT_QUOTES);
	$pageFunc = nullable_htmlspecialchar($pageFunc, ENT_QUOTES);
	$imageFunc = nullable_htmlspecialchar($imageFunc, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=f&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formFunc'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit Function";
	if ($bOK) {
		echo " Succeed\n";
	} else {
		echo " Failed\n";
	}
	echo "		</th>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Module</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='moduleFunc' name='moduleFunc' value='$moduleFunc'></input>$moduleNameFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idFunc' name='idFunc' value='$idFunc'></input>$idFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameFunc' name='nameFunc' value='$nameFunc'></input>$nameFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Page</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='pageFunc' name='pageFunc' value='$pageFunc'></input>$pageFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Image</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<input type='hidden' id='imageFunc' name='imageFunc' value='$imageFunc'></input>\n";
	echo "			<img src='image/icon/$imageFunc' alt=''></img>\n";
	if ($imageFunc)
		echo "			($imageFunc)\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Position</td>\n";
	echo "		<td valign='top'>:</td>\n";
	if ($posFunc == 0) {
		echo "		<td>Per Terminal&nbsp;</td>\n";
	} else if ($posFunc == 1) {
		echo "		<td>Per Module&nbsp;</td>\n";
	} else if ($posFunc == 2) {
		echo "		<td>Hide&nbsp;</td>\n";
	}
	echo "			<input type='hidden' id='posFunc' name='posFunc' value='$posFunc'></input>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formFunc\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=f");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
}

function editFunc() {
	global $Setting, $id;
	
	$arFunc = $Setting->GetFunctionDetail($id);
	$idFunc = $arFunc["id"];
	$moduleFunc = $arFunc["mid"];
	$nameFunc = $arFunc["name"];
	// $privFunc = $arFunc["priv"];
	$pageFunc = $arFunc["page"];
	$imageFunc = $arFunc["image"];
	$posFunc = $arFunc["pos"];

	$idFunc = nullable_htmlspecialchar($idFunc, ENT_QUOTES);
	$moduleFunc = nullable_htmlspecialchar($moduleFunc, ENT_QUOTES);
	$nameFunc = nullable_htmlspecialchar($nameFunc, ENT_QUOTES);
	// $privFunc = nullable_htmlspecialchar($privFunc, ENT_QUOTES);
	$pageFunc = nullable_htmlspecialchar($pageFunc, ENT_QUOTES);
	$imageFunc = nullable_htmlspecialchar($imageFunc, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit function '$nameFunc'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=f&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Module</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	// module choice
	if ($Setting->GetModule($arModule)) {
		echo "			<select id='moduleFunc' name='moduleFunc'>\n";
		echo "				<option value='0'";
		if (!$moduleFunc || $moduleFunc == "0") {
			echo " selected";
		}
		echo ">--------</option>\n";
		foreach ($arModule as $mod) {
			$moduleId = $mod["id"];
			$moduleName = $mod["name"];
			echo "				<option value='$moduleId'";
			if ($moduleFunc == $moduleId) {
				echo " selected";
			}
			echo ">$moduleName</option>\n";
		}
		echo "			</select>\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idFunc' name='idFunc' value='$idFunc'></input>$idFunc</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameFunc' name='nameFunc' length='100' size='30' value='$nameFunc' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Page</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>function/<input type='text' id='pageFunc' name='pageFunc' length='100' size='20' value='$pageFunc' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Image</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>image/icon/\n";
	echo "			<input type='text' id='imageFunc' name='imageFunc' length='100' size='25' value='$imageFunc' autocomplete='off'></input>\n";
	echo "			<img id='imageSrcFunc' name='imageSrcFunc' src='image/icon/$imageFunc' alt=''></img>\n";
	echo "			<input type='button' value='List' onClick='showImageList()'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Position</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<select id='posFunc' name='posFunc'>\n";
	echo "				<option value='0'>Per terminal</option>\n";
	if ($posFunc == 1) {
		echo "				<option value='1' selected>Per module</option>\n";
	} else {
		echo "				<option value='1'>Per module</option>\n";
	}
	if ($posFunc == 2) {
		echo "				<option value='2' selected>Hide</option>\n";
	} else {
		echo "				<option value='2'>Hide</option>\n";
	}
	echo "			</select>\n";
	echo "		</td>\n";
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

?>
