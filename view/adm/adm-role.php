<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management Role
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertRoleAction();
	} else {
		insertRole();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editRoleAction();
	} else {
		editRole();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteRole();
} else if ($subMode == "l") {
	// Submode list
	listModule();
} else if ($subMode == "g") {
	// Submode grant
	grantModule();
} else if ($subMode == "c") {
	// Submode decline
	declineModule();
} else {
	// List all
	$bOK = printRoleSetting();
	if (!$bOK) {
		return;
	}
}
	
function printShowHide($userId, $array, $index, $mode, $charLimit, $lineLimit) {
	$first = true;
	$charCounter = 0;
	$lineCounter = 0;
	$hide = false;
	foreach ($array as $iter) {
		// $iterId = $iter["id"];
		// $iterName = $iter["name"];
		$iterValue = $iter[$index];
		
		// Comma
		if ($first) {
			$first = false;
		} else {
			echo ", ";
		}
		
		// Cek new line
		$lengthChar = strlen($iterValue);
		if ($charCounter + $lengthChar > $charLimit && $charCounter > 0) {
			echo "<br />\n";
			$charCounter = 0;
			$lineCounter++;
		}
		
		// Cek show/hide
		if ($lineCounter >= $lineLimit && !$hide) {
			echo "<div id='showLink" . $mode . $userId . "'>... <a href='#' style='text-decoration:none'" .
					"onClick='showMore(\"" . $mode . "\", \"$userId\")'>(see more)</a></div>";
			echo "<div class='hide' id='more" . $mode . $userId . "'>";
			$hide = true;
		}
		
		echo $iterValue;
		$charCounter += $lengthChar;
	}
	if ($hide) {
		echo "<br /><a href='#' style='text-decoration:none' id='hideLink" . $mode . $userId . "' " .
				"onClick='hideMore(\"" . $mode . "\", \"$userId\")'>(hide)</a>";
		echo "</div>\n";
	}
}

function printRoleSetting() {
	global $cData, $data, $json, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
				
		echo "	<div class='subTitle'>List role modules</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$url64 = base64_encode("setting=1&m=r&sm=i");
		echo "	<a href='main.php?param=$url64'>Add new role</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		// print table Role
		$bOK = $Setting->GetRole($RoleIds);
		if ($bOK) {
?>
<script type='text/javascript'>
function confirmDeleteRole(name, id) {
	var ans = confirm("Delete role '" + name + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=r&sm=d&i=' + id);
		window.location.href = 'main.php?param=' + url;
	}
}

function showMore(mode, userId) {
	var div = document.getElementById('more' + mode + userId);
	div.style.visibility = "visible";
	div.style.display = "block";
	
	var link = document.getElementById('showLink' + mode + userId);
	link.style.visibility = "hidden";
	link.style.display = "none";
}

function hideMore(mode, userId) {
	var div = document.getElementById('more' + mode + userId);
	div.style.visibility = "hidden";
	div.style.display = "none";
	
	var link = document.getElementById('showLink' + mode + userId);
	link.style.visibility = "visible";
	link.style.display = "block";
}
</script>
<?php
			// var_dump($RoleIds);
			echo "	<table cellspacing='3px' cellpadding='3px'>\n";
			echo "		<tr>\n";
			echo "			<th>Option</th>\n";
			echo "			<th>Id</th>\n";
			echo "			<th>Name</th>\n";
			echo "			<th>Description</th>\n";
			echo "			<th>List Accessable Module</th>\n";
			echo "		</tr>\n";
			
			foreach ($RoleIds as $RoleId) {
				$id = $RoleId["id"];
				$name = $RoleId["name"];
				$desc = $RoleId["desc"];
				
				echo "		<tr>\n";
				echo "			<td align='center' valign='top'>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=r&sm=e&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
				echo "				&nbsp;\n";
				echo "				<a href='#' onClick='confirmDeleteRole(\"$name\", \"$id\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=r&sm=l&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/list-items.gif' alt='Edit Module Access' title='Edit Module Access'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td valign='top'>$id&nbsp;</td>\n";
				echo "			<td valign='top'>$name&nbsp;</td>\n";
				echo "			<td valign='top'>$desc&nbsp;</td>\n";
				
				// NEW: accessable module name
				echo "			<td valign='top'>\n";
				$arAccessedModule = $User->GetAccessableModuleName($id);
				if ($arAccessedModule != null) {
					printShowHide($id, $arAccessedModule, "moduleName", "Module", 50, 4);
				}
				echo "			</td>\n";
				echo "		</tr>\n";
			}
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function deleteRole() {
	global $Setting, $id;
	$bOK = $Setting->DeleteRole($id);
	if ($bOK) {
		echo "<div>Role successfully deleted...</div>\n";
	} else {
		echo "<div>Role fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=r");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertRoleAction() {
	global $Setting, $User, $id, $idRole, $nameRole, $descRole, $ref, $u, $ar;
	
	// $idRole = (isset($_REQUEST['idRole']) ? trim($_REQUEST['idRole']) : '');
	// $nameRole = (isset($_REQUEST['nameRole']) ? trim($_REQUEST['nameRole']) : '');
	// $descRole = (isset($_REQUEST['descRole']) ? trim($_REQUEST['descRole']) : '');

	$bOK = $Setting->InsertRole($idRole, $nameRole, $descRole);
	
	$idRole = nullable_htmlspecialchar($idRole, ENT_QUOTES);
	$nameRole = nullable_htmlspecialchar($nameRole, ENT_QUOTES);
	$descRole = nullable_htmlspecialchar($descRole, ENT_QUOTES);
	
	$url64 = base64_encode("setting=1&m=r&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formRole'>\n";
	
	// NEW: bisa back to change role from user
	// $ref = (isset($_REQUEST['ref']) ? trim($_REQUEST['ref']) : '');
	// $userId = (isset($_REQUEST['u']) ? trim($_REQUEST['u']) : '');
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	$userId = $u;
	$areaId = $ar;
	if ($ref == "changeRole" && $userId != "") {
		echo "	<input type='hidden' id='ref' name='ref' value='changeRole'></input>\n";
		echo "	<input type='hidden' id='u' name='u' value='$userId'></input>\n";
		echo "	<input type='hidden' id='ar' name='ar' value='$areaId'></input>\n";
	}
	
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert Role";
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
	echo "		<td valign='top'><input type='hidden' id='idRole' name='idRole' value='$idRole' autocomplete='off'></input>$idRole</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameRole' name='nameRole' value='$nameRole' autocomplete='off'></input>$nameRole</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descRole' name='descRole' value='$descRole' autocomplete='off'></input>$descRole</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formRole\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		if ($ref == "changeRole" && $userId != "" && $areaId != "") {
			$url64 = base64_encode("setting=1&m=u&sm=c&i=$userId&ar=$areaId&r=$idRole");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			
			$username = $User->GetUserName($userId);
			$areaname = $User->GetAreaName($areaId);
			
			echo "	Automatically saving granted role to user '$username' in area '$areaname'<br />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		} else {
			$url64 = base64_encode("setting=1&m=r");
			echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
			echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
		}
	}
	echo "</div>\n";
	echo "</form>\n";
}

function insertRole() {
	global $Setting, $User, $id, $idRole, $nameRole, $descRole, $ref, $u, $ar, $mode;
	
	// $idRole = (isset($_REQUEST['idRole']) ? trim($_REQUEST['idRole']) : '');
	// $nameRole = (isset($_REQUEST['nameRole']) ? trim($_REQUEST['nameRole']) : '');
	// $descRole = (isset($_REQUEST['descRole']) ? trim($_REQUEST['descRole']) : '');
	
	if ($idRole == "") {
		// initial insert
		$idRole = "rm" . $Setting->GetNextRoleId();
	}

	$idRole = nullable_htmlspecialchar($idRole, ENT_QUOTES);
	$nameRole = nullable_htmlspecialchar($nameRole, ENT_QUOTES);
	$descRole = nullable_htmlspecialchar($descRole, ENT_QUOTES);	
	
	$url64 = base64_encode("setting=1&m=r&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "	<div class='subTitle'>Insert new role</div>\n";
	echo "	<div class='spacer10'></div>\n";

	// NEW: bisa back to change role from user
	// $ref = (isset($_REQUEST['ref']) ? trim($_REQUEST['ref']) : '');
	// $userId = (isset($_REQUEST['u']) ? trim($_REQUEST['u']) : '');
	// $areaId = (isset($_REQUEST['ar']) ? trim($_REQUEST['ar']) : '');
	$areaId = $ar;
	$userId = $u;
	if ($ref == "changeRole" && $userId != "" && $areaId != "") {
		echo "	<input type='hidden' id='ref' name='ref' value='changeRole'></input>\n";
		echo "	<input type='hidden' id='u' name='u' value='$userId'></input>\n";
		echo "	<input type='hidden' id='ar' name='ar' value='$areaId'></input>\n";
		
		$username = $User->GetUserName($userId);
		$areaname = $User->GetAreaName($areaId);
		$username = nullable_htmlspecialchar($username);
		$areaname = nullable_htmlspecialchar($areaname);
		
		$url64 = base64_encode("setting=1&m=u&sm=l&i=$userId");
		echo "	<a href='main.php?param=$url64'>&lsaquo; Back to change role from user '$username' in area '$areaname'</a>\n";
		echo "	<div class='spacer10'></div>\n";
	}
	
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idRole' name='idRole' length='30' size='20' value='$idRole' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameRole' name='nameRole' length='100' size='30' value='$nameRole' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textArea id='descRole' name='descRole' cols='50' rows='3'>$descRole</textArea></td>\n";
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

function editRoleAction() {
	global $Setting, $idRole, $nameRole, $descRole;
	
	// $idRole = (isset($_REQUEST['idRole']) ? trim($_REQUEST['idRole']) : '');
	// $nameRole = (isset($_REQUEST['nameRole']) ? trim($_REQUEST['nameRole']) : '');
	// $descRole = (isset($_REQUEST['descRole']) ? trim($_REQUEST['descRole']) : '');
	
	$bOK = $Setting->EditRole($idRole, $nameRole, $descRole);
	
	$url64 = base64_encode("setting=1&m=r&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formRole'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit Role";
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
	echo "		<td valign='top'><input type='hidden' id='idRole' name='idRole' value='$idRole' autocomplete='off'></input>$idRole</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameRole' name='nameRole' value='$nameRole' autocomplete='off'></input>$nameRole</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='descRole' name='descRole' value='$descRole' autocomplete='off'></input>$descRole</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formRole\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=r");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
}

function editRole() {
	global $Setting, $id;
	
	$arRole = $Setting->GetRoleDetail($id);
	$idRole = $arRole["id"];
	$nameRole = $arRole["name"];
	$descRole = $arRole["desc"];
	
	// quote fix
	$idRole = nullable_htmlspecialchar($idRole, ENT_QUOTES);
	$nameRole = nullable_htmlspecialchar($nameRole, ENT_QUOTES);
	$descRole = nullable_htmlspecialchar($descRole, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit role '$nameRole'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=r&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idRole' name='idRole' value='$idRole' autocomplete='off'></input>$idRole</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameRole' name='nameRole' length='100' size='30' value='$nameRole' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Description</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><textArea id='descRole' name='descRole' cols='50' rows='3'>$descRole</textArea></td>\n";
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

function listModule() {
	global $Setting, $User, $id, $data;
	
	$uid = null;
	if ($data) {
		$uid = $data->uid;
	}
	
	$arModule = null;
	$arRole = null;
	
	$bOK = $Setting->GetModule($arModule);
	if ($bOK) {
		$roleName = $User->GetRoleName($id);
		echo "	<div class='subTitle'>List modules in role '$roleName'</div>\n";
		echo "	<div class='spacer10'></div>\n";
	
		// var_dump($arModule);
		foreach ($arModule as $mod) {
			$moduleId = $mod["id"];
			$moduleName = $mod["name"];
			$arFunction = null;
			$bOK = $Setting->GetFunctionInModule("", $moduleId, $arFunction);
			
			echo "	<div class='subSubTitle'>$moduleName\n";
			echo "		<span style='font-size:10pt; font-weight:normal;'>(Id: $moduleId)</span>\n";
			$url64 = base64_encode("setting=1&m=f&sm=i&mid=$moduleId&r=$id");
			echo "		<span style='font-size:10pt; font-weight:normal; padding-left:20px;'><a href='main.php?param=$url64'>Insert new function to module '$moduleName'</a></span>\n";
			echo "	</div>\n";
			// echo "	<div class='spacer10'></div>\n";
			
			if ($bOK) {
				echo "	<table cellspacing='3px' cellpadding='3px'>\n";
				echo "		<tr>\n";
				echo "			<th>Option</th>\n";
				echo "			<th>Id</th>\n";
				echo "			<th>Module</th>\n";
				echo "			<th>Name</th>\n";
				echo "			<th>Page</th>\n";
				echo "			<th>Position</th>\n";
				echo "			<th>Image</th>\n";
				echo "			<th>Permission</th>\n";
				echo "		</tr>\n";
			
				// var_dump($arFunction);
				foreach ($arFunction as $func) {
					$funcId = $func["id"];
					$funcMid = $func["mid"];
					$moduleName = $User->GetModuleName($funcMid);
					$name = $func["name"];
					$page = $func["page"];
					$pos = $func["pos"];
					$image = $func["image"];
					
					$grantedFunction = $Setting->IsFunctionGrantedInRole($funcMid, $funcId, $id);
					
					echo "		<tr>\n";
					echo "			<td align='center'>\n";
					echo "				&nbsp;\n";
					// ---- Show edit & delete
					if ($grantedFunction) {
						$url64 = base64_encode("setting=1&m=r&sm=c&i=$moduleId&r=$id&f=$funcId");
						echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/delete.png' alt='Decline permission' title='Decline permission'></img></a>\n";
						echo "				&nbsp;\n";
					} else {
						$url64 = base64_encode("setting=1&m=r&sm=g&i=$moduleId&r=$id&f=$funcId");
						echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/accept.png' alt='Grant permission' title='Grant permission'></img></a>\n";
						echo "				&nbsp;\n";
					}
					echo "			</td>\n";
					echo "			<td>$funcId&nbsp;</td>\n";
					echo "			<td>$moduleName&nbsp;</td>\n";
					echo "			<td>$name&nbsp;</td>\n";
					echo "			<td>$page&nbsp;</td>\n";
					if ($pos == 0) {
						// Per terminal
						echo "			<td>Per terminal&nbsp;</td>\n";
					} else if ($pos == 1) {
						// Per module
						echo "			<td>Per module&nbsp;</td>\n";
					} else if ($pos == 2) {
						// Hide
						echo "			<td>Hide&nbsp;</td>\n";
					}
					echo "			<td>\n";
					echo "				&nbsp;<img src='image/icon/$image' alt=''></img>&nbsp;&nbsp;$image&nbsp;\n";
					echo "			</td>\n";
					echo "			<td align='center'>\n";
					if ($grantedFunction) {
						echo "				<span style='color:green; font-weight:bold;'>Granted</span>\n";
						$granted = true;
					} else {
						echo "				<span style='color:red; font-weight:bold;'>Declined</span>\n";
					}
					echo "			</td>\n";
					echo "		</tr>\n";
				}
				
				// Grant/Decline all permission
				echo "		<tr>\n";
				echo "			<td>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=r&sm=c&i=$moduleId&r=$id&f=all");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/delete.png' alt='Decline permission' title='Decline permission'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=r&sm=g&i=$moduleId&r=$id&f=all");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/accept.png' alt='Grant permission' title='Grant permission'></img></a>\n";
				echo "				&nbsp;\n";
				echo "			</td>\n";
				echo "			<td colspan='7' align='left'>\n";
				echo "				<span style='color:red; font-weight:bold;'>Declined all</span>\n";
				echo "				/\n";
				echo "				<span style='color:green; font-weight:bold;'>Granted all</span>\n";
				echo "			</td>\n";
				echo "		</tr>\n";
				
				echo "	</table>\n";
				echo "	<div class='spacer20'></div>\n";
			} else {
				// Empty
				echo "	<div class='spacer5'></div>\n";
				echo "	<div>&nbsp;&nbsp;No function defined</div>\n";
				echo "	<div class='spacer20'></div>\n";
			}
		}
	}
}

function grantModule() {
	global $Setting, $function, $id, $r;
	// $roleId = (isset($_REQUEST['r']) ? trim($_REQUEST['r']) : '');
	$roleId = $r;
	$moduleId = $id;
	if ($roleId == "") {
		return false;
	}
	
	// Decline module
	$bOK = $Setting->GrantFunction($roleId, $moduleId, $function, true);
	if ($bOK) {
		echo "<div>Permission successfully granted...</div>\n";
	} else {
		echo "<div>Permission fail to grant...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=r&sm=l&i=$roleId");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function declineModule() {
	global $Setting, $function, $id, $r;
	// $roleId = (isset($_REQUEST['r']) ? trim($_REQUEST['r']) : '');
	$roleId = $r;
	$moduleId = $id;
	if ($roleId == "") {
		return false;
	}
	
	// Decline module
	$bOK = $Setting->GrantFunction($roleId, $moduleId, $function, false);
	if ($bOK) {
		echo "<div>Permission successfully declined...</div>\n";
	} else {
		echo "<div>Permission fail to decline...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=r&sm=l&i=$roleId");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

?>
