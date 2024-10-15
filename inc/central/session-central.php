<?php
class SCANCentralDBSession
{
	private $iDebug;
	private $sLogFileName;

	private $DBLink;
	private $iInterval;

	private $sThisFile;

	private $iErrCode;
	private $sErrMsg;

	function __construct($iDebug = 0, $sLogFileName = '', $DBLink = null, $iInterval = 180)
	{
		$this->iDebug = $iDebug;
		$this->sLogFileName = $sLogFileName;

		$this->DBLink = $DBLink;
		$this->iInterval = $iInterval;

		$this->iErrCode = 0;
		$this->sErrMsg = '';

		$this->sThisFile = basename(__FILE__);
	}

	public function GenerateSession($sUID, $sUName, $sOther)
	{
		return md5($sUID . '.' . $sUName . '.' . $sOther . '.' . time());
	}

	// return  0 : valid
	//        -1 : session expired
	//        -2 : not login
	public function CheckSession($sUID, $sSID)
	{
		$iSessionStatus = -2; // not logged-in

		// check in PP system session table
		// retrieve session data
		//$sUID = (isset($_COOKIE['onpays_pp_ud']) ? base64_decode($_COOKIE['onpays_pp_ud']) : '');
		if (trim($sUID) != '') {
			$sQ = "select CTR_CUS_LASTSESSION from CENTRAL_USER_SESSION where CTR_CUS_ID = '" . CTOOLS_ValidateQueryForDB($sUID, "'", "MYSQL") .
				"' and CTR_CUS_SESSION = '" . CTOOLS_ValidateQueryForDB($sSID, "'", "MYSQL") . "'";
			if ($res = mysqli_query($this->DBLink, $sQ)) {
				$nRes = mysqli_num_rows($res);
				$nRecord = $nRes;
				if ($nRes > 0) {
					// check session expiration
					$row = mysqli_fetch_assoc($res);
					$iLastSession = intval(strtotime($row['CTR_CUS_LASTSESSION']));
					if (time() - $iLastSession <= $this->iInterval) {
						$iSessionStatus = 0; // session is still valid
					} else {
						$iSessionStatus = -1; // session is expired
					}
				}
			} else {
				$this->iErrCode = -3;
				$this->sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFileName);
			}
		}

		return $iSessionStatus;
	}

	public function CheckAnotherLogin($sUID, $usr, &$addr)
	{
		$anotherLogin = false;
		if (trim($sUID) != '') {
			$sQ = "select CTR_CUS_LASTSESSION, CTR_CUS_IP from CENTRAL_USER_SESSION where " .
				" CTR_CUS_ID = '" . CTOOLS_ValidateQueryForDB($sUID, "'", "MYSQL") . "' " .
				// " and CTR_CUS_IP = '" . CTOOLS_ValidateQueryForDB($remoteAddr, "'", "MYSQL") . "' " .
				" order by CTR_CUS_LASTSESSION desc limit 1";
			echo $sQ;
			if ($res = mysqli_query($this->DBLink, $sQ)) {
				$nRes = mysqli_num_rows($res);
				$nRecord = $nRes;
				if ($nRes > 0) {
					// check session expiration
					$row = mysqli_fetch_assoc($res);
					$addr = $row['CTR_CUS_IP'];
					$iLastSession = intval(strtotime($row['CTR_CUS_LASTSESSION']));
					if (time() - $iLastSession <= $this->iInterval) {
						$anotherLogin = true;
					}
				}
			} else {
				$this->iErrCode = -3;
				$this->sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFileName);
			}
		}

		return $anotherLogin;
	}

	public function SetSessionCookie($sCookieName, $sCookieVal)
	{
		setcookie($sCookieName, $sCookieVal, time() + $this->iInterval);
	}

	public function GetSessionCookie($sCookieName)
	{
		$sCookieVal = (isset($_COOKIE[$sCookieName]) ? $_COOKIE[$sCookieName] : '');
		return $sCookieVal;
	}

	public function SaveSessionToDB($sUID, $sSID)
	{
		$bOK = false;

		if (trim($sUID) != '') {
			$sQ = "insert into CENTRAL_USER_SESSION(CTR_CUS_ID, CTR_CUS_SESSION, CTR_CUS_IP, CTR_CUS_LASTSESSION) " .
				"values('$sUID', '$sSID','" . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') . "', '" .
				date("Y-m-d H:i:s") . "')";
			//echo $sQ;
			if (mysqli_query($this->DBLink, $sQ)) {
				$bOK = true;
			} else {
				$this->iErrCode = -3;
				$this->sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFileName);
			}
		}

		return $bOK;
	}

	public function DeleteSessionFromDB($sUID)
	{
		$bOK = false;

		if (trim($sUID) != '') {
			$sQ = "delete from CENTRAL_USER_SESSION where CTR_CUS_ID = '$sUID'";
			if (mysqli_query($this->DBLink, $sQ)) {
				$bOK = true;
			} else {
				$this->iErrCode = -3;
				$this->sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFileName);
			}
		}

		return $bOK;
	}

	public function GetIpAddress($sUID, $sSID)
	{
		$sQ = "select CTR_CUS_IP from CENTRAL_USER_SESSION where CTR_CUS_ID = '" . CTOOLS_ValidateQueryForDB($sUID, "'", "MYSQL") .
			"' and CTR_CUS_SESSION = '" . CTOOLS_ValidateQueryForDB($sSID, "'", "MYSQL") . "'";
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			$nRecord = $nRes;
			if ($nRes > 0) {
				// check session expiration
				if ($row = mysqli_fetch_array($res, mysqli_ASSOC)) {
					$ip = $row['CTR_CUS_IP'];
					return $ip;
				}
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFileName);
		}

		return null;
	}

	public function UpdateSessionInDB($sUID, $sSID)
	{
		$bOK = true;

		$this->DeleteSessionFromDB($sUID);
		$this->SaveSessionToDB($sUID, $sSID);

		return $bOK;
	}
}

?>