<?php

if (isset($_REQUEST["usr"]) && isset($_REQUEST["pwd"])) {
	require_once("login.php");

	$usr = $_REQUEST["usr"];
	$pwd = $_REQUEST["pwd"];
	$cImage = $_REQUEST["cImage"];

	// Cek CAPTCHA Image
	// $captchaAns = $_COOKIE["captcha"];
	// $equal = ($cImage === $captchaAns || $cImage == "1");

	include("image/captcha/securimage.php");
	$img = new Securimage();
	$equal = ($img->check($cImage));

	if ($equal) {
		$resp = login($usr, $pwd);
		if (isset($resp["error"])) {
			$errorLogin = $resp["error"];

			setcookie("errorLogin", $errorLogin, time() + 36000);
			header("Location: main.php");
		} else {
			$jsonResp = json_encode($resp);
			$cData = base64_encode($jsonResp);

			// var_dump($resp);
			// echo($cData);

			// set cookie
			setcookie("centraldata", $cData, time() + 36000);

			// NEW: cek area & module
			$uid = $resp["uid"];
			// jika area ada 1, langsung redirect kesana
			$areaIds = null;
			$bOK = $User->GetArea($uid, $areaIds);
			if (count($areaIds) == 1) {
				$areaId = $areaIds[0]["id"];

				$moduleIds = null;
				$bOK = $User->GetModuleInArea($uid, $areaId, $moduleIds);
				if (count($moduleIds) == 1) {
					$moduleId = $moduleIds[0]["id"];

					// echo "main.php?a=$areaId&m=$moduleId";
					$url64 = base64_encode("a=$areaId&m=$moduleId");
					header("Location: main.php?param=$url64");
				} else {
					// echo "main.php?a=$areaId";
					$url64 = base64_encode("a=$areaId");
					header("Location: main.php?param=$url64");
				}
			} else {
				// echo "main.php";
				header("Location: main.php");
			}
		}
	} else {
		setcookie("errorLogin", "Wrong verification code", time() + 36000);
		header("Location: main.php");
	}
} else {
	?>
	<script type='text/javascript'>
		function checkInput() {
			var usr = document.getElementById('usr');
			var pwd = document.getElementById('pwd');
			var cImage = document.getElementById('cImage');

			if (usr.value == "") {
				usr.focus();
				return false;
			} else if (pwd.value == "") {
				pwd.focus();
				return false;
			} else if (cImage.value == "") {
				cImage.focus();
				return false;
			}
			return true;
		}
	</script>

	<body onload='document.getElementById("usr").focus()' class='login-page'>
		<div id='header'>
			<img src='<?php echo $MAINstyle["logo"] ?>' id='logo' alt='Deposit Monitoring System' />
		</div>
		<div id='content'>
			<div id='login-form'>
				<form action='plogin.php' id='form' method='POST' onSubmit='return checkInput()'>
					<table cellpadding='3' align='center' border='0'>
						<?php
						if (isset($_COOKIE["errorLogin"])) {
							// Error message
							$errorLogin = $_COOKIE["errorLogin"];
							echo "					<tr>\n";
							echo "						<td colspan='3' align='center'><div class='error'>$errorLogin</div></td>\n";
							echo "					</tr>\n";
							setcookie("errorLogin", "", time() - 10);
						}
						?>
						<tr>
							<td colspan='3'></td>
						</tr>
						<tr>
							<td align='left'>Username</td>
							<td width='5px'>&nbsp;</td>
							<td colspan='1'><input type='text' name='usr' id='usr' value='' autocomplete='off'></input></td>
						</tr>
						<tr>
							<td align='left'>Password</td>
							<td width='5px'>&nbsp;</td>
							<td colspan='1'><input type='password' name='pwd' id='pwd' value='' autocomplete='off'></input>
							</td>
						</tr>
						<tr>
							<td colspan='3' height='5px'></td>
						</tr>
						<tr>
							<td colspan='2'></td>
							<td align='right'>
								<img src="captcha2.php" alt="Captcha Image" id="captcha-image" />
							</td>
						</tr>
						<tr>
							<td align='left'>Verification Code</td>
							<td width='5px'>&nbsp;</td>
							<td>
								<input type='text' name='cImage' id='cImage' value='' size='6' maxlength='10'
									autocomplete='off'></input>
							</td>
						</tr>
						<tr>
							<td colspan='3' height='5px'></td>
						</tr>
						<tr>
							<td colspan='3' align='center'><input type='submit' value='Login'></input></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
	<?php
}
?>