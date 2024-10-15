<script type='text/javascript'>			
function clickManagement(obj) {
	var admin = document.getElementById('isAdmin');
	var supervisor = document.getElementById('isSupervisor');
	
	if (obj.id == admin.id) {
		// Supervisor jadi off, jika admin on
		if (obj.checked) {
			supervisor.checked = false;
		}
	} else if (obj.id == supervisor.id) {
		// Admin jadi off, jika supervisor on
		if (obj.checked) {
			admin.checked = false;
		}
	}
}
</script>
<?php

// Prevent direct access to file
if ($User == null) {
	die();
}

// Mode management User
if ($subMode == "i") {
	// Submode insert
	if ($action) {
		// get parameter
		insertUserAction();
	} else {
		insertUser();
	}
} else if ($subMode == "e") {
	// Submode edit
	if ($action) {
		// get parameter
		editUserAction();
	} else {
		editUser();
	}
} else if ($subMode == "d") {
	// Submode delete
	deleteUser();
} else if ($subMode == "l") {
	// Submode list
	listArea();
} else if ($subMode == "c") {
	// Submode change role
	changeRole();
} else if ($subMode == "b") {
	// Submode change block user
	changeBlockUser(true);
} else if ($subMode == "u") {
	// Submode change unblock user
	changeBlockUser(false);
} else {
	// List all
	$bOK = printUserSetting();
	if (!$bOK) {
		return;
	}
}

function printUserSetting() {
	global $cData, $data, $json, $User, $Setting;
	$bOK = false;
	
	if ($data) {
		$uid = $data->uid;
				
		echo "	<div class='subTitle'>List users</div>\n";
		echo "	<div class='spacer10'></div>\n";
		
		$url64 = base64_encode("setting=1&m=u&sm=i");
		echo "	<a href='main.php?param=$url64'>Add new user</a>\n";
		echo "	<div class='spacer10'></div>\n";
		
		// print table User
		$bOK = $Setting->GetUser($UserIds);
		if ($bOK) {
			// var_dump($UserIds);
?>
<script type='text/javascript'>			
function confirmDeleteUser(name, id) {
	var ans = confirm("Delete user '" + name + "' ?");
	if (ans) {
		var url = Base64.encode('setting=1&m=u&sm=d&i=' + id);
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
			echo "			<th>Blocked</th>\n";
			echo "			<th>Management</th>\n";
			echo "			<th>Multiple Login</th>\n";
			echo "		</tr>\n";
			
			foreach ($UserIds as $UserId) {
				$id = $UserId["id"];
				$uid = $UserId["uid"];
				$isAdmin = $UserId["isAdmin"];
				$blocked = $UserId["blocked"];
				$multLogin = $UserId["multLogin"];
				
				// NEW: pakai manageBit
				$manageBit = $isAdmin + 0;
				
				echo "		<tr>\n";
				echo "			<td align='center'>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=u&sm=e&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>\n";
				echo "				&nbsp;\n";
				echo "				<a href='#' onClick='confirmDeleteUser(\"$uid\", \"$id\")'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>\n";
				echo "				&nbsp;\n";
				$url64 = base64_encode("setting=1&m=u&sm=l&i=$id");
				echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/list-items.gif' alt='List role' title='List role'></img></a>\n";
				echo "				&nbsp;\n";
				
				// NEW: unblok / block user
				// to block image --> block_16.png
				// to unblock image --> accept.png
				if ($blocked == 0) {
					$url64 = base64_encode("setting=1&m=u&sm=b&i=$id");
					echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/block_16.png' alt='Block user' title='Block user'></img></a>\n";
				} else {
					$url64 = base64_encode("setting=1&m=u&sm=u&i=$id");
					echo "				<a href='main.php?param=$url64'><img border='0' src='image/icon/accept.png' alt='Unblock user' title='Unblock user'></img></a>\n";
				}
				echo "				&nbsp;\n";
				
				echo "			</td>\n";
				echo "			<td>$id&nbsp;</td>\n";
				echo "			<td>$uid&nbsp;</td>\n";
				if ($blocked == 0) {
					echo "			<td align='center'>-</td>\n";
				} else {
					echo "			<td align='center'><img src='image/icon/block_16.png' alt='Blocked' /></td>\n";
				}
				if (($manageBit & 1) == 1) {
					echo "			<td align='center'>Admin</td>\n";
				} else if (($manageBit & 10) == 10) {
					echo "			<td align='center'>Supervisor</td>\n";
				} else {
					echo "			<td align='center'>-</td>\n";
				}
				
				// NEW: Multiple login
				echo "			<td align='center'>\n";
				if ($multLogin == 1) {
					echo "				Yes\n";
				} else {
					echo "				-\n";
				}
				echo "			</td>\n";
				echo "		</tr>\n";
			}
			echo "	</table>\n";
		}
	}
	return $bOK;
}

function deleteUser() {
	global $Setting, $id;
	$bOK = $Setting->DeleteUser($id);
	if ($bOK) {
		echo "<div>User successfully deleted...</div>\n";
	} else {
		echo "<div>User fail to delete...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=u");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function insertUser() {
	global $Setting, $idUser, $nameUser, $isAdmin, $mode, $multLogin;
	
	if ($idUser == "") {
		// initial insert, ambil next id 'u?'
		$idUser = "u" . $Setting->GetNextUserId();
	}

	$idUser = nullable_htmlspecialchar($idUser, ENT_QUOTES);
	$nameUser = nullable_htmlspecialchar($nameUser, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Insert new user</div>\n";
	echo "	<div class='spacer10'></div>\n";
		
	$url64 = base64_encode("setting=1&m=u&sm=i&a=1");
	echo "<form method='POST' action='main.php?param=$url64' onSubmit='return cekConfirmPassword(\"pwdUser\", \"pwdUser2\");'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='idUser' name='idUser' length='30' size='20' value='$idUser' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameUser' name='nameUser' length='100' size='30' value='$nameUser' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Management</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<div>\n";
	echo "				<label>\n";
	echo "					<input type='checkbox' id='isAdmin' name='isAdmin' value='1' onClick='clickManagement(this)'>&nbsp;Admin</input>\n";
	echo "				</label>\n";
	echo "				<input type='text' style='visibility:hidden'></input>\n";
	echo "			</div>\n";
	echo "			<div>\n";
	echo "				<label>\n";
	echo "					<input type='checkbox' id='isSupervisor' name='isSupervisor' value='1' onClick='clickManagement(this)'>&nbsp;Supervisor</input>\n";
	echo "				</label>\n";
	echo "				<input type='text' style='visibility:hidden'></input>\n";
	echo "			</div>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Multiple Login</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='checkbox' id='multLogin' name='multLogin' ";
	if ($multLogin) {
		echo "checked";
	}
	echo "></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='password' id='pwdUser' name='pwdUser' length='100' size='30' value='' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Confirm Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='password' id='pwdUser2' name='pwdUser2' length='100' size='30' value='' autocomplete='off'></input></td>\n";
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

function insertUserAction() {
	global $Setting, $idUser, $nameUser, $pwdUser, $isAdmin, $isSupervisor, $multLogin;
	
	$bOK = $Setting->InsertUser($idUser, $nameUser, $pwdUser, $isAdmin, $isSupervisor, $multLogin);
	
	$idUser = nullable_htmlspecialchar($idUser, ENT_QUOTES);
	$nameUser = nullable_htmlspecialchar($nameUser, ENT_QUOTES);
	$n = strlen($pwdUser);
	$pwdUserDisp = "";
	for ($i = 0; $i < $n; $i++) {
		$pwdUserDisp .= "&bull;";
	}
	
	$url64 = base64_encode("setting=1&m=u&sm=i");
	echo "<form method='POST' action='main.php?param=$url64' id='formUser'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Insert User";
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
	echo "		<td valign='top'><input type='hidden' id='idUser' name='idUser' value='$idUser' autocomplete='off'></input>$idUser</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameUser' name='nameUser' value='$nameUser' autocomplete='off'></input>$nameUser</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$pwdUserDisp</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Management</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	$adm = false;
	if ($isAdmin) {
		echo "		<li>Admin</li><br />\n";
		$adm = true;
	}
	if ($isSupervisor) {
		echo "		<li>Supervisor</li><br />\n";
		$adm = true;
	}
	if (!$adm) {
		echo "		-\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Multiple Login</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	if ($multLogin) {
		echo "			Yes\n";
	} else {
		echo "			No\n";
	}
	echo "			<input type='hidden' id='multLogin' name='multLogin' value='$multLogin'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer20'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formUser\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=u");
		echo "	<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64' />\n";
		echo "	Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
	}
	echo "</div>\n";
	echo "</form>\n";
}

function editUser() {
	global $Setting, $id;
	
	$arUser = $Setting->GetUserDetail($id);
	$idUser = $arUser["id"];
	$nameUser = $arUser["uid"];
	$isAdmin = $arUser["isAdmin"];
	$multLogin = $arUser["multLogin"];
	
	// NEW: manageBit
	$manageBit = $isAdmin + 0;
	
	// quote fix
	$idUser = nullable_htmlspecialchar($idUser, ENT_QUOTES);
	$nameUser = nullable_htmlspecialchar($nameUser, ENT_QUOTES);
	
	echo "	<div class='subTitle'>Edit user '$nameUser'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	$url64 = base64_encode("setting=1&m=u&sm=e&a=1");
	echo "<form method='POST' action='main.php?param=$url64' onSubmit='return cekConfirmPassword(\"pwdUser\", \"pwdUser2\")'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Id</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='idUser' name='idUser' value='$idUser' autocomplete='off'></input>$idUser</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='text' id='nameUser' name='nameUser' length='100' size='30' value='$nameUser' autocomplete='off'></input></td>\n";
	echo "	</tr>\n";
	
	echo "	<tr>\n";
	echo "		<td valign='top'>Management</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<div>\n";
	echo "				<label>\n";
	echo "					<input type='checkbox' id='isAdmin' name='isAdmin' value='1' onClick='clickManagement(this)' ";
	if (($manageBit & 1) == 1) {
		echo "checked='true'";
	}
	echo ">&nbsp;Admin</input>\n";
	echo "				</label>\n";
	echo "				<input type='text' style='visibility:hidden'></input>\n";
	echo "			</div>\n";
	echo "			<div>\n";
	echo "				<label>\n";
	echo "					<input type='checkbox' id='isSupervisor' name='isSupervisor' value='1' onClick='clickManagement(this)' ";
	if (($manageBit & 10) == 10) {
		echo "checked='true'";
	}
	echo ">&nbsp;Supervisor</input>\n";
	echo "				</label>\n";
	echo "				<input type='text' style='visibility:hidden'></input>\n";
	echo "			</div>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>Multiple Login</td>\n";
	echo "		<td>:</td>\n";
	echo "		<td>\n";
	echo "			<input type='checkbox' id='multLogin' name='multLogin' ";
	if ($multLogin == 1) {
		echo "checked";
	}
	echo "></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	
	echo "	<tr>\n";
	echo "		<td colspan='3' height='25px' valign='bottom'>\n";
	echo "			<div style='font-size:8pt; text-align:center;'><b>for reset password only</b></div>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<input type='password' id='pwdUser' name='pwdUser' length='100' size='30' value='' autocomplete='off'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Confirm Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	echo "			<input type='password' id='pwdUser2' name='pwdUser2' length='100' size='30' value='' autocomplete='off'></input>\n";
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

function editUserAction() {
	global $Setting, $idUser, $nameUser, $pwdUser, $isAdmin, $isSupervisor, $multLogin;
	
	$n = strlen($pwdUser);
	$pwdUserDisp = "";
	for ($i = 0; $i < $n; $i++) {
		$pwdUserDisp .= "&bull;";
	}
	
	$bOK = $Setting->EditUser($idUser, $nameUser, $pwdUser, $isAdmin, $isSupervisor, $multLogin);
	
	$url64 = base64_encode("setting=1&m=u&sm=e");
	echo "<form method='POST' action='main.php?param=$url64' id='formUser'>\n";
	echo "<table class='transparent'>\n";
	echo "	<tr>\n";
	echo "		<th colspan='3' align='left'>\n";
	echo "			Edit User";
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
	echo "		<td valign='top'><input type='hidden' id='idUser' name='idUser' value='$idUser' autocomplete='off'></input>$idUser</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Name</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'><input type='hidden' id='nameUser' name='nameUser' value='$nameUser' autocomplete='off'></input>$nameUser</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Password</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>$pwdUserDisp</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Management</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	$adm = false;
	if ($isAdmin) {
		echo "		<li>Admin</li><br />\n";
		$adm = true;
	}
	if ($isSupervisor) {
		echo "		<li>Supervisor</li><br />\n";
		$adm = true;
	}
	if (!$adm) {
		echo "		-\n";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td valign='top'>Multiple Login</td>\n";
	echo "		<td valign='top'>:</td>\n";
	echo "		<td valign='top'>\n";
	if ($multLogin) {
		echo "			Yes\n";
	} else {
		echo "			No\n";
	}
	echo "			<input type='hidden' id='multLogin' name='multLogin' value='$multLogin'></input>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	
	echo "<div class='spacer10'></div>\n";
	echo "<div>\n";
	if (!$bOK) {
		echo "	<a href='#' onClick='document.getElementById(\"formUser\").submit();'>Edit form</a>&nbsp;&nbsp;\n";
	} else {
		$url64 = base64_encode("setting=1&m=u");
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
	
	echo "	<div class='subTitle'>Application & role from user '$username'</div>\n";
	echo "	<div class='spacer10'></div>\n";
	
	if ($bOK && $bOK2) {
		// var_dump($arArea);
		
?>
<script type='text/javascript'>
function showSelectRole(index) {
	// alert('selectRole' + index);
	document.getElementById('selectRole' + index).style.display = 'inline';
	document.getElementById('role' + index).style.display = 'none';
	document.getElementById('selectRole' + index).focus();
}

function hideSelectRole(index) {
	document.getElementById('selectRole' + index).style.display = 'none';
	document.getElementById('role' + index).style.display = 'inline';
}

function selectSelectRole(index) {
	// hide select
	var valueSelect = document.getElementById('selectRole' + index).value;
	hideSelectRole(index);
	// removeEvent(document.getElementById('selectRole' + index), 'blur', selectSelectRole);
}

function focusSelectRole(index) {
	// addEvent(document.getElementById('selectRole' + index), 'blur', selectSelectRole);
}

function changeRole(index, user, area, module) {
	var loadingRole = document.getElementById('loadingTextRole');
	if (loadingRole != null) {
		loadingRole.style.display = 'inline';
	}

	var selectRole = document.getElementById('selectRole' + index);
	var role = selectRole.value;
	if (role == '-99') {
		var url = Base64.encode('setting=1&m=r&sm=i&ref=changeRole&u=' + user + '&ar=' + area);
		window.location.href = 'main.php?param=' + url;
	} else if (role != '0') {
		var url = Base64.encode('setting=1&m=u&sm=c&i=' + user + '&ar=' + area + '&r=' + role);
		window.location.href = 'main.php?param=' + url;
	}
}
</script>
<?php
		
		echo "	<table cellspacing='3px' cellpadding='3px'>\n";
		echo "		<tr>\n";
		echo "			<th>Application</th>\n";
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
			
			echo "					<option value='-99'>&lt; Insert new role &gt;</option>\n";
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
	$url64 = base64_encode("setting=1&m=u&sm=l&i=$id");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

function changeBlockUser($blocked) {
	global $User, $id;
	
	$stBlock = "Unblock";
	if ($blocked) {
		$stBlock = "Block";
	}
	
	$bOK = $User->ChangeBlockUser($id, $blocked);
	if ($bOK) {
		echo "<div>$stBlock user successfully...</div>\n";
	} else {
		echo "<div>$stBlock user failed...</div>\n";
	}
	$url64 = base64_encode("setting=1&m=u");
	echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
	echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
}

?>
