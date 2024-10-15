<?php

if ($data) {

	if ($mode == "chPass") {
		echo "	<div class='spacer10'></div>\n";
		echo "	<div class='subTitle'>Change Password</div>\n";
		echo "	<div class='spacer5'></div>\n";
	
		// Change Password
		$changed = false;
		
		if (@isset($ac) && $ac == "1") {
			$username = $User->GetUserName($uid);
			$bOK = $User->ChangePassword($uid, $username, $pwdUser, $oldPwdUser);
			if ($bOK) {
				echo "<div>Successfully change password</div>\n";
				$url64 = base64_encode("userProfile=1");
				echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
				echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
				$changed = true;
			} else {
				echo "<div>Failed to change password</div>\n";
			}
		}

		if (!$changed) {
			echo "<div class='spacer10'></div>\n";
			$url64 = base64_encode("userProfile=1&m=chPass");
			echo "<form method='POST' action='main.php?param=$url64' onSubmit='return cekConfirmPassword(\"pwdUser\", \"pwdUser2\")'>\n";
			echo "	<table class='transparent'>\n";
			echo "		<tr>\n";
			echo "			<td>Old password</td>\n";
			echo "			<td>:</td>\n";
			echo "			<td><input type='password' id='oldPwdUser' name='oldPwdUser' length='30' size='30' autocomplete='off' value=''></input></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td>Current password</td>\n";
			echo "			<td>:</td>\n";
			echo "			<td><input type='password' id='pwdUser' name='pwdUser' length='30' size='30' autocomplete='off' value=''></input></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td>Repeat current password</td>\n";
			echo "			<td>:</td>\n";
			echo "			<td><input type='password' id='pwdUser2' name='pwdUser2' length='30' size='30' autocomplete='off' value=''></input></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td colspan='3' height='10'></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td colspan='3' align='center'><input type='submit' value='Change'></input></td>\n";
			echo "		</tr>\n";
			echo "	</table>\n";
			echo "</form>\n";
		}
	}
	
	else if ($mode == "chLayout") {
		echo "	<div class='spacer10'></div>\n";
		echo "	<div class='subTitle'>Change Layout</div>\n";
		echo "	<div class='spacer5'></div>\n";
		
		if (@isset($ac) && $ac == "1") {
			$bOK = $User->setLayoutUser($uid, $styleId);
			// echo "uid = $uid<br />\n";
			// echo "styleId = $styleId<br />\n";
			if ($bOK) {
				echo "<div>Successfully change layout</div>\n";
				$url64 = base64_encode("userProfile=1&m=chLayout");
				echo "<meta http-equiv='REFRESH' content='1;url=main.php?param=$url64'>\n";
				echo "Wait a moment... <img src='image/icon/wait.gif' alt=''></img>\n";
				$changed = true;
			} else {
				echo "<div>Failed to change layout</div>\n";
			}
			return;
		}
		
		// Include library
		require_once("inc/lib/xml2array.php");
		
		// Table header
		echo "	<table cellspacing='3' cellpadding='3'>\n";
		echo "		<tr>\n";
		echo "			<th>Choose</th>\n";
		echo "			<th width='200px'>Name</th>\n";
		echo "			<th>Creation Date</th>\n";
		echo "			<th>Author</th>\n";
		echo "			<th>Version</th>\n";
		echo "			<th>Screenshot</th>\n";
		echo "		</tr>\n";
		
		// List style directory
		$dirStyle = "style/";
		$descStyle = "desc.xml";
		if ($handle = opendir($dirStyle)) {
			while (false !== ($dir = readdir($handle))) {
				$descStyleFull = $dirStyle . $dir . "/" . $descStyle;
				if (file_exists($descStyleFull)) {
					// XML
					$arXml = xml2array(file_get_contents($descStyleFull));
					$styleDesc = $arXml["description"];
					$iName = $styleDesc["name"];
					$iDate = $styleDesc["creationDate"];
					$iAuthor = $styleDesc["author"];
					$iVersion = $styleDesc["version"];
					
					// File
					$arFiles = $styleDesc["files"];
					$iScreenshot = null;
					if (@isset($arFiles["screenshot"]) && trim($arFiles["screenshot"]) != "") {
						$iScreenshot = $arFiles["screenshot"];
					}
					
					echo "		<tr>\n";
					echo "			<td align='center'>\n";
					if ("style/$dir" == $MAINstyle["path"]) {
						echo "				-\n";
					} else {
						$url64 = base64_encode("userProfile=1&m=chLayout&ac=1&styleId=$dir");
						echo "				<a href='main.php?param=$url64'>\n";
						echo "					<img src='image/icon/accept.png' alt='' title='Choose' />\n";
						echo "				</a>\n";
					}
					echo "			</td>\n";
					echo "			<td>$iName</td>\n";
					echo "			<td>$iDate</td>\n";
					echo "			<td align='center'>$iAuthor</td>\n";
					echo "			<td align='right'>$iVersion</td>\n";
					
					// Screenshot
					echo "			<td height='62' align='center'>\n";
					if ($iScreenshot != null) {
						$sShotFull = $dirStyle . $dir . "/" . $iScreenshot;
						if (file_exists($sShotFull)) {
							echo "				<img src='$sShotFull' width='100' height='62' alt='$iName' border='0' />\n";
						} else {
							echo "				No screenshot\n";
						}
					} else {
						echo "				No screenshot\n";
					}
					echo "			</td>\n";
					echo "		</tr>\n";
				}
			}

			closedir($handle);
		}
		
		echo "	</table>\n";
	}
	
	else {
		echo "	<div class='spacer10'></div>\n";
		echo "	<div class='subTitle'>User Profile</div>\n";
		echo "	<div class='spacer5'></div>\n";
		
		$arUser = $Setting->GetUserDetail($uid);
		if ($arUser != null) {
			$username = $arUser["uid"];
			$isAdmin = $arUser["isAdmin"];
			
			$username = nullable_htmlspecialchar($username);
			$isAdmin = nullable_htmlspecialchar($isAdmin);
		
			echo "<table class='transparent'>\n";
			echo "	<tr>\n";
			echo "		<td>Id</td>\n";
			echo "		<td>:</td>\n";
			echo "		<td>$uid</td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td>Username</td>\n";
			echo "		<td>:</td>\n";
			echo "		<td>$username</td>\n";
			echo "	</tr>\n";
			echo "</table>\n";
		}
	}
}

?>
