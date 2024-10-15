<?php
class SCANCentralUser
{
	private $iDebug;
	private $sLogFilename;
	private $DBLink;
	private $sThisFile;
	private $iErrCode;
	private $sErrMsg;

	public function __construct($iDebug = 0, $sLogFilename = '', $DBLink = null)
	{
		$this->iDebug = $iDebug;
		$this->sLogFilename = $sLogFilename;
		$this->DBLink = $DBLink;
		$this->sThisFile = basename(__FILE__);

		$this->iErrCode = 0;
		$this->sErrMsg = '';
	}

	private function SetError($iErrCode = 0, $sErrMsg = '')
	{
		$this->iErrCode = $iErrCode;
		$this->sErrMsg = $sErrMsg;
	}

	public function GetLastError(&$iErrCode, &$sErrMsg)
	{
		$iErrCode = $this->iErrCode;
		$sErrMsg = $this->sErrMsg;
	}

	public function GetUserName($uid)
	{
		// FIX: mysql escape string
		$uid = mysqli_real_escape_string($this->DBLink, $uid);

		$username = null;
		$sQ = "select * from CENTRAL_USER where CTR_U_ID = '" . $uid . "' ";
		// echo "query = $sQ<br />\n";

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [sQ] $sQ\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			$nRecord = $nRes;
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$username = $row['CTR_U_UID'];
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return $username;
	}

	public function IsAuthUser($sUName, $sUPwd, &$sUID)
	{
		// FIX: mysql escape string
		$sUName = mysqli_real_escape_string($this->DBLink, $sUName);
		$sUPwd = mysqli_real_escape_string($this->DBLink, $sUPwd);

		$bAuth = false;
		$sUID = null;
		// NEW: password dicek belakangan
		$sQ = "select * from CENTRAL_USER where CTR_U_UID = '" . $sUName . "' ";
		// . "and CTR_U_PWD = '" . md5($sUPwd) . "'";
		// echo "query = $sQ<br />\n";

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [sQ] $sQ\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			$nRecord = $nRes;
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$sUID = $row['CTR_U_ID'];
				$pwd = $row['CTR_U_PWD'];

				// Cek password
				$bAuth = (md5($sUPwd) == $pwd);
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return $bAuth;
	}

	public function IsExistUser($sUName, &$sSavedUID)
	{
		// FIX: mysql escape string
		$sUName = mysqli_real_escape_string($this->DBLink, $sUName);

		$bExist = false;

		$sQ = "select * from CENTRAL_USER where CTR_U_UID = '" . $sUName . "'";
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				if ($row = mysqli_fetch_assoc($res)) {
					$sSavedUID = $row['CTR_U_ID'];
					$bExist = true;
				}
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return $bExist;
	}

	public function IsBlockedUser($uname)
	{
		// FIX: mysql escape string
		$uname = mysqli_real_escape_string($this->DBLink, $uname);

		$sQ = "select CTR_U_BLOCKED from CENTRAL_USER where CTR_U_UID = '" . $uname . "'";

		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				if ($row = mysqli_fetch_assoc($res)) {
					$blocked = $row['CTR_U_BLOCKED'];
					return ($blocked == 1);
				}
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return false;
	}

	public function IsMultipleLogin($uid)
	{
		// FIX: mysql escape string
		$uid = mysqli_real_escape_string($this->DBLink, $uid);

		$sQ = "select CTR_U_MULT_LOGIN from CENTRAL_USER where CTR_U_ID = '" . $uid . "'";

		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				if ($row = mysqli_fetch_assoc($res)) {
					$multLogin = $row['CTR_U_MULT_LOGIN'];
					return ($multLogin == 1);
				}
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return false;
	}

	public function ChangeBlockUser($userId, $blocked)
	{
		// FIX: mysql escape string
		$userId = mysqli_real_escape_string($this->DBLink, $userId);

		$blocked = ($blocked ? 1 : 0);

		$sQ = "update CENTRAL_USER set " .
			"CTR_U_BLOCKED = " . $blocked . " " .
			"where CTR_U_ID = '" . $userId . "' ";
		// echo $sQ;

		$bOK = false;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}
		return $bOK;
	}

	public function ChangePassword($sUID, $sUName, $sUPwd, $oldPwd = "")
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);
		$sUName = mysqli_real_escape_string($this->DBLink, $sUName);
		$sUPwd = mysqli_real_escape_string($this->DBLink, $sUPwd);
		$oldPwd = mysqli_real_escape_string($this->DBLink, $oldPwd);

		if ($oldPwd != "") {
			$uid = null;
			$authed = $this->IsAuthUser($sUName, $oldPwd, $uid);

			if (!$authed) {
				return false;
			}
		}

		$bChanged = false;

		$sQ = "update CENTRAL_USER set CTR_U_PWD = '" . md5($sUPwd) . "' " .
			"where CTR_U_ID = '" . $sUID . "' " .
			"and CTR_U_UID = '" . $sUName . "'";
		// echo $sQ;
		if (mysqli_query($this->DBLink, $sQ)) {
			$bChanged = true;
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return $bChanged;
	}

	public function IsAreaGranted($sUID, $areaId)
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		$bOK = false;

		$sQ = "select * from CENTRAL_USER_TO_AREA " .
			"where CTR_USER_ID = '" . $sUID . "' " .
			"and CTR_AREA_ID = '" . $areaId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
			}
		}

		return $bOK;
	}

	public function GetArea($sUID, &$areaIds)
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);

		// $arSetting = CTOOLS_ArrayRemoveAllElement($arSetting);
		$bOK = false;

		$sQ = "select CTR_AREA_ID, CTR_A_NAME, CTR_RM_ID from CENTRAL_USER_TO_AREA, CENTRAL_AREA " .
			"where CTR_USER_ID = '" . $sUID . "' " .
			"and CTR_A_ID = CTR_AREA_ID order by CTR_A_NAME asc";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$areaIds[$i]["id"] = $row["CTR_AREA_ID"];
					$areaIds[$i]["name"] = $row["CTR_A_NAME"];
					$areaIds[$i]["rm"] = $row["CTR_RM_ID"];
					$i++;
				}
			}
		}

		return $bOK;
	}

	public function GetAreaName($area)
	{
		// FIX: mysql escape string
		$area = mysqli_real_escape_string($this->DBLink, $area);

		$sQ = "SELECT CTR_A_NAME FROM CENTRAL_AREA C WHERE C.CTR_A_ID = '" . $area . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$name = $row["CTR_A_NAME"];
				return $name;
			}
		}
	}

	public function GetDatabaseFromArea($areaId)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		$sQ = "SELECT CTR_A_DB FROM CENTRAL_AREA WHERE CTR_A_ID = '" . $areaId . "'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$id = $row["CTR_A_DB"];
				return $id;
			}
		}
		return null;
	}

	public function GetDatabaseName($dbId)
	{
		// FIX: mysql escape string
		$dbId = mysqli_real_escape_string($this->DBLink, $dbId);

		$sQ = "SELECT CTR_DB_NAME FROM CENTRAL_DATABASE C WHERE C.CTR_DB_ID = '" . $dbId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$name = $row["CTR_DB_NAME"];
				return $name;
			}
		}
		return null;
	}

	public function GetRoleName($roleId)
	{
		// FIX: mysql escape string
		$roleId = mysqli_real_escape_string($this->DBLink, $roleId);

		$sQ = "SELECT CTR_RM_NAME FROM CENTRAL_ROLE_MODULE C WHERE C.CTR_RM_ID = '" . $roleId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$name = $row["CTR_RM_NAME"];
				return $name;
			}
		}
	}

	public function GetAllModuleCount()
	{
		$sQ = "SELECT COUNT(*) AS COUNT FROM CENTRAL_MODULE";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				if ($row = mysqli_fetch_assoc($res)) {
					$count = $row["COUNT"];
					return $count;
				}
			}
		}
		return null;
	}

	public function GetModuleAccessable($areaId)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		$arModule = null;

		$sQ = "SELECT * FROM CENTRAL_AREA_TO_MODULE WHERE CTR_AM_AID = '" . $areaId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				$arModule = array();
				while ($row = mysqli_fetch_assoc($res)) {
					// $arModule[$i]["areaId"] = $row["CTR_AM_AID"];
					// $arModule[$i]["moduleId"] = $row["CTR_AM_MID"];
					$arModule[$i] = $row["CTR_AM_MID"];
					$i++;
				}
			}
		}
		return $arModule;
	}

	public function InsertModuleAccessable($areaId, $arModuleInsert)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$bOK = false;

		$sQ = "INSERT INTO CENTRAL_AREA_TO_MODULE VALUES ";
		$first = true;
		foreach ($arModuleInsert as $modInsert) {
			// FIX: mysql escape string
			$modInsert = mysqli_real_escape_string($this->DBLink, $modInsert);

			if ($first) {
				$first = false;
			} else {
				$sQ .= ", ";
			}
			$sQ .= "('$areaId', '$modInsert')";
		}
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}
		return $bOK;
	}

	public function DeleteModuleAccessable($areaId, $arModuleDelete)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$bOK = false;

		$sQ = "DELETE FROM CENTRAL_AREA_TO_MODULE WHERE CTR_AM_AID = '" . $areaId . "' ";
		$first = true;
		if ($arModuleDelete != null && count($arModuleDelete) > 0) {
			$sQ .= "AND (";
			foreach ($arModuleDelete as $modDelete) {
				// FIX: mysql escape string
				$modDelete = mysqli_real_escape_string($this->DBLink, $modDelete);

				if ($first) {
					$first = false;
				} else {
					$sQ .= "OR ";
				}
				$sQ .= "CTR_AM_MID = '" . $modDelete . "' ";
			}
			$sQ .= ")";
		}
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}
		return $bOK;
	}

	public function GetModuleInArea($sUID, $area, &$moduleIds)
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);
		$area = mysqli_real_escape_string($this->DBLink, $area);

		$moduleIds = CTOOLS_ArrayRemoveAllElement($moduleIds);
		$bOK = false;

		$sQ = "SELECT DISTINCT C.CTR_USER_ID, F.CTR_RM2F_MID, F.CTR_RM2F_PRIV, M.CTR_M_NAME " .
			"FROM CENTRAL_USER_TO_AREA C, CENTRAL_ROLE_MODULE_TO_FUNCTION F, CENTRAL_MODULE M, CENTRAL_AREA_TO_MODULE AM " .
			"WHERE C.CTR_RM_ID = F.CTR_RM2F_ID AND M.CTR_M_ID = F.CTR_RM2F_MID AND AM.CTR_AM_MID = F.CTR_RM2F_MID AND AM.CTR_AM_AID = C.CTR_AREA_ID AND ";
		if ($area != "") {
			$sQ .= "C.CTR_AREA_ID = '" . $area . "' AND ";
		}
		$sQ .= "C.CTR_USER_ID = '" . $sUID . "' ";
		// NEW: order by
		$sQ .= "ORDER BY M.CTR_M_NAME ASC ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$moduleIds[$i]["id"] = $row["CTR_RM2F_MID"];
					$moduleIds[$i]["name"] = $row["CTR_M_NAME"];
					// $moduleIds[$i]["priv"] = $row["CTR_RM2F_PRIV"];
					$i++;
				}
			}
		}
		return $bOK;
	}

	public function GetAccessableModuleName($roleId)
	{
		// FIX: mysql escape string
		$roleId = mysqli_real_escape_string($this->DBLink, $roleId);

		$moduleIds = null;
		$sQ = "SELECT CTR_M_NAME FROM CENTRAL_ROLE_MODULE, CENTRAL_ROLE_MODULE_TO_FUNCTION, CENTRAL_MODULE " .
			"WHERE CTR_RM2F_ID = CTR_RM_ID AND CTR_RM2F_PRIV > 0 AND CTR_RM2F_MID = CTR_M_ID AND CTR_RM_ID = '$roleId'" .
			"ORDER BY CTR_RM_NAME ASC, CTR_M_NAME ASC ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			$i = 0;
			$moduleIds = array();
			while ($row = mysqli_fetch_assoc($res)) {
				$moduleIds[$i]["moduleName"] = $row["CTR_M_NAME"];
				$i++;
			}
		}

		return $moduleIds;
	}

	public function IsModuleGranted($sUID, $areaId, $moduleId)
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$bOK = false;

		$sQ = "SELECT C.CTR_USER_ID, F.CTR_RM2F_MID, F.CTR_RM2F_PRIV, M.CTR_M_NAME " .
			"FROM CENTRAL_USER_TO_AREA C, CENTRAL_ROLE_MODULE_TO_FUNCTION F, CENTRAL_MODULE M, CENTRAL_AREA_TO_MODULE AM " .
			"WHERE AM.CTR_AM_AID = C.CTR_AREA_ID AND M.CTR_M_ID = AM.CTR_AM_MID AND " .
			"C.CTR_RM_ID = F.CTR_RM2F_ID AND M.CTR_M_ID = F.CTR_RM2F_MID AND F.CTR_RM2F_PRIV > 0 AND " .
			"C.CTR_USER_ID = '" . $sUID . "' AND " .
			"C.CTR_AREA_ID = '" . $areaId . "' AND " .
			"M.CTR_M_ID = '" . $moduleId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
			}
		}

		return $bOK;
	}

	public function GetModuleName($module)
	{
		// FIX: mysql escape string
		$module = mysqli_real_escape_string($this->DBLink, $module);

		$sQ = "SELECT CTR_M_NAME FROM CENTRAL_MODULE C WHERE C.CTR_M_ID = '" . $module . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$name = $row["CTR_M_NAME"];
				return $name;
			}
		}
	}

	public function GetModuleFromRole($module)
	{
		// FIX: mysql escape string
		$module = mysqli_real_escape_string($this->DBLink, $module);

		$sQ = "SELECT CTR_M_NAME FROM CENTRAL_MODULE C WHERE C.CTR_M_ID = '" . $module . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$name = $row["CTR_M_NAME"];
				return $name;
			}
		}
	}

	public function GetModulePriv($sUID, $areaId, $moduleId)
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$bOK = false;

		$sQ = "SELECT F.CTR_RM2F_PRIV " .
			"FROM CENTRAL_USER_TO_AREA C, CENTRAL_ROLE_MODULE_TO_FUNCTION F, CENTRAL_MODULE M " .
			"WHERE C.CTR_RM_ID = F.CTR_RM2F_ID AND M.CTR_M_ID = F.CTR_RM2F_MID AND " .
			"C.CTR_USER_ID = '" . $sUID . "' AND " .
			"C.CTR_AREA_ID = '" . $areaId . "' AND " .
			"F.CTR_RM2F_MID = '" . $moduleId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$priv = $row["CTR_RM2F_PRIV"];
				return $priv;
			}
		}
		return $bOK;
	}

	public function GetFunction($moduleId, &$func)
	{
		// FIX: mysql escape string
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$func = CTOOLS_ArrayRemoveAllElement($func);
		$sQ = "SELECT * FROM CENTRAL_FUNCTION C WHERE C.CTR_FUNC_MID = '" . $moduleId . "' order by LPAD(CTR_FUNC_ID,11,'0') asc";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$func[$i]["id"] = $row["CTR_FUNC_ID"];
					$func[$i]["name"] = $row["CTR_FUNC_NAME"];
					$func[$i]["priv"] = $row["CTR_FUNC_PRIV"];
					$func[$i]["page"] = $row["CTR_FUNC_PAGE"];
					$func[$i]["image"] = $row["CTR_FUNC_IMAGE"];
					$func[$i]["pos"] = $row["CTR_FUNC_POS"];
					$i++;
				}
			}
		}
		return $bOK;
	}

	public function GetFunctionName($func, &$arFunc)
	{
		// FIX: mysql escape string
		$func = mysqli_real_escape_string($this->DBLink, $func);

		$arFunc = CTOOLS_ArrayRemoveAllElement($arFunc);
		$sQ = "SELECT * FROM CENTRAL_FUNCTION C WHERE C.CTR_FUNC_ID = '" . $func . "' ";
		// echo $sQ;

		$bOK = false;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$row = mysqli_fetch_assoc($res);
				$arFunc["CTR_FUNC_MID"] = $row["CTR_FUNC_MID"];
				$arFunc["CTR_FUNC_NAME"] = $row["CTR_FUNC_NAME"];
				$arFunc["CTR_FUNC_PRIV"] = $row["CTR_FUNC_PRIV"];
				$arFunc["CTR_FUNC_PAGE"] = $row["CTR_FUNC_PAGE"];
				$arFunc["CTR_FUNC_IMAGE"] = $row["CTR_FUNC_IMAGE"];
				return $bOK;
			}
		}
		return $bOK;
	}

	public function IsFunctionGranted($sUID, $areaId, $moduleId, $funcId)
	{
		// FIX: mysql escape string
		$sUID = mysqli_real_escape_string($this->DBLink, $sUID);
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);
		$funcId = mysqli_real_escape_string($this->DBLink, $funcId);

		$bOK = false;

		$sQ = "SELECT CF.CTR_FUNC_PRIV, F.CTR_RM2F_PRIV " .
			"FROM CENTRAL_USER_TO_AREA C, CENTRAL_ROLE_MODULE_TO_FUNCTION F, CENTRAL_MODULE M, CENTRAL_FUNCTION CF " .
			"WHERE C.CTR_RM_ID = F.CTR_RM2F_ID AND M.CTR_M_ID = F.CTR_RM2F_MID AND CF.CTR_FUNC_MID = F.CTR_RM2F_MID " .
			"AND C.CTR_USER_ID = '" . $sUID . "' " .
			"AND C.CTR_AREA_ID = '" . $areaId . "' " .
			"AND F.CTR_RM2F_MID = '" . $moduleId . "' " .
			"AND CF.CTR_FUNC_ID = '" . $funcId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$userPriv = $row["CTR_FUNC_PRIV"];
				$priv = $row["CTR_RM2F_PRIV"];
				// echo $priv . " = " . $userPriv;

				// convert to int
				$userPriv += 0;
				$priv += 0;

				$bOK = (($userPriv & $priv) != 0);
				// echo ($userPriv & $priv);
			}
		}

		return $bOK;
	}

	public function GetDbConnectionFromArea($areaId)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		$sQ = "SELECT CTR_A_DB FROM CENTRAL_AREA WHERE CTR_A_ID = '" . $areaId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				if ($row = mysqli_fetch_assoc($res)) {
					$dbId = $row["CTR_A_DB"];
					return $this->GetDbConnection($dbId);
				}
			}
		}

		return null;
	}

	public function GetDbConnection($dbId)
	{
		// FIX: mysql escape string
		$dbId = mysqli_real_escape_string($this->DBLink, $dbId);

		$sQ = "SELECT * FROM CENTRAL_DATABASE WHERE CTR_DB_ID = '" . $dbId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$host = $row["CTR_DB_HOST"];
				$port = $row["CTR_DB_PORT"];
				$hostport = $host . ":" . $port;
				$user = $row["CTR_DB_USER"];
				$pwd = $row["CTR_DB_PWD"];
				$schema = $row["CTR_DB_SCHEMA"];

				// decrypt
				$sRootPath = str_replace('\\', '/', str_replace(DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'central', '', dirname(__FILE__))) . '/';
				require_once($sRootPath . "inc/key/safe.php");
				$decPwd = decrypt($pwd);
				// echo "pwd = $pwd<br />\n";
				// echo "decPwd = $decPwd<br />\n";

				$dbLink = null;
				$dbConn = null;

				// echo "host:port = $hostport<br />\n";
				// echo "user = $user<br />\n";
				// echo "pass = $decPwd<br />\n";
				// echo "schema = $schema<br />\n";

				SCANPayment_ConnectToDB($dbLink, $dbConn, $hostport, $user, $decPwd, $schema);
				// echo "dbLink = $dbLink<br />\n";
				// echo "<br /><br />\n";
				return $dbLink;
			}
		}
		return null;
	}

	public function GetQuery($areaId)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		$query = null;
		$sQ = "SELECT CTR_A_QUERY FROM CENTRAL_AREA WHERE CTR_A_ID = '" . $areaId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$query = $row["CTR_A_QUERY"];
			}
		}

		return $query;
	}

	public function GetTerminal($areaId, $moduleId, &$terminal)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$terminal = CTOOLS_ArrayRemoveAllElement($terminal);
		$bOK = false;

		$sQ = "SELECT CTR_A_QUERY, CTR_A_DB FROM CENTRAL_AREA WHERE CTR_A_ID = '" . $areaId . "' ";
		// echo $sQ;

		$terminalQuery = null;
		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$terminalQuery = $row["CTR_A_QUERY"];
				$dbId = $row["CTR_A_DB"];
			}
		}

		// echo $terminalQuery;
		// CEK ada query
		if ($terminalQuery == null) {
			return -1;
		}
		// CEK ada database
		if ($dbId == null) {
			return -2;
		}
		// CEK query, hanya bisa select
		$cekQuery = trim(strtolower($terminalQuery));
		if (strpos($cekQuery, "update ") !== false || strpos($cekQuery, "delete ") !== false || strpos($cekQuery, "insert ") !== false) {
			// langsung keluar, return false
			return -3;
		}
		// CEK database connection
		$dbLink = $this->GetDbConnection($dbId);
		if ($dbLink == null) {
			return -4;
		}
		// NEW: CEK database configuration
		$arConfig = $this->GetDatabaseConfig($dbId);
		if ($arConfig == null) {
			return -5;
		}
		// NEW: CEK module configuration, if { found
		if (strpos($terminalQuery, "{") !== false) {
			// Get module configuration
			if ($moduleId != "") {
				$arModuleConfig = $this->GetModuleConfig($moduleId);
				if ($arModuleConfig != null) {
					while (strpos($terminalQuery, "{") !== false) {
						// Get configuration name
						$indexStart = strpos($terminalQuery, "{");
						$indexStop = strpos($terminalQuery, "}");
						$mConfKey = substr($terminalQuery, $indexStart + 1, ($indexStop - $indexStart - 1));
						$mConfValue = $arModuleConfig[$mConfKey];
						$terminalQuery = str_replace("{" . $mConfKey . "}", $mConfValue, $terminalQuery);
					}
				} else {
					return -6;
				}
			} else {
				while (strpos($terminalQuery, "{") !== false) {
					// Get configuration name
					$indexStart = strpos($terminalQuery, "{");
					$indexStop = strpos($terminalQuery, "}");
					$mConfKey = substr($terminalQuery, $indexStart + 1, ($indexStop - $indexStart - 1));

					$mConfValue = 0;
					$terminalQuery = str_replace("{" . $mConfKey . "}", $mConfValue, $terminalQuery);
				}
			}
		}
		// NEW: database spesifik area
		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $terminalQuery, $dbLink)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					foreach ($arConfig as $key => $conf) {
						$column = $conf["key"];
						$header = $conf["value"];

						// echo "column = $column<br />\n";
						// echo "header = $header<br />\n";

						$terminal[$i][$header] = $row[$column];
					}

					$i++;
				}
				$bOK = true;
			}
		}

		return $bOK;
	}

	public function IsAccessible($uid, $areaId, $terminalId, $terminalColumn)
	{
		// REVISED: Determining which the terminal can accessed by the user

		// FIX: mysql escape string
		$uid = mysqli_real_escape_string($this->DBLink, $uid);
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);
		$terminalId = mysqli_real_escape_string($this->DBLink, $terminalId);
		$terminalColumn = mysqli_real_escape_string($this->DBLink, $terminalColumn);

		$accessible = false;

		$sQ = "SELECT A.CTR_A_QUERY " .
			" FROM CENTRAL_USER_TO_AREA C, CENTRAL_ROLE_MODULE_TO_FUNCTION F, CENTRAL_MODULE M, CENTRAL_AREA A " .
			" WHERE C.CTR_RM_ID = F.CTR_RM2F_ID AND M.CTR_M_ID = F.CTR_RM2F_MID AND C.CTR_AREA_ID = A.CTR_A_ID AND " .
			" C.CTR_USER_ID = '" . $uid . "' " .
			" AND C.CTR_AREA_ID = '" . $areaId . "' LIMIT 1";
		// echo $sQ;

		$query = null;
		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				if ($row = mysqli_fetch_assoc($res)) {
					$query = $row["CTR_A_QUERY"];
				}
			}
		}

		// Query Preprocessing
		$query = str_replace("\n", " ", $query);
		$wherePos = stripos($query, " where ");
		$orderPos = stripos($query, " order ");
		$limitPos = stripos($query, " limit ");

		$whereQuery = "";
		if ($wherePos !== false) {
			// ada where
			$whereQuery .= " and " . $terminalColumn . " = '" . $terminalId . "' ";
		} else {
			// tidak ada where
			$whereQuery .= " where " . $terminalColumn . " = '" . $terminalId . "' ";
		}

		// Put whereQuery
		// jika tidak ada order, taruh whereQuery langsung
		// jika ada order, taruh whereQuery di posisi orderPos
		if ($orderPos !== false) {
			// jika ada limit, maka query --> select ... from ... order ...
			// pecah query jadi 2 --> beforeOrderQuery & afterOrderQuery
			$beforeOrderQuery = substr($query, 0, $orderPos);
			$afterOrderQuery = substr($query, $orderPos);
			$query = $beforeOrderQuery . $whereQuery . $afterOrderQuery;
		} else if ($limitPos !== false) {
			// jika ada limit, maka query --> select ... from ... limit ...
			// pecah query jadi 2 --> beforeLimitQuery & afterLimitQuery
			$beforeLimitQuery = substr($query, 0, $limitPos);
			$afterLimitQuery = substr($query, $limitPos);
			$query = $beforeLimitQuery . $whereQuery . $afterLimitQuery;
			$query .= $whereQuery;
		} else {
			$query .= $whereQuery;
		}

		// echo "query = $query\n";
		$dbArea = $this->GetDbConnectionFromArea($areaId);
		if ($dbArea != null) {
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] query [$query]\n", 3, $this->sLogFilename);
			if ($res = mysqli_query($this->DBLink, $query, $dbArea)) {
				$nRes = mysqli_num_rows($res);
				if ($nRes > 0) {
					$accessible = true;
				}
			}
		} else {
			// No connection
			$accessible = -2;
		}

		return $accessible;
	}

	public function GetModuleView($moduleId)
	{
		// FIX: mysql escape string
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$sQ = "SELECT CTR_M_VIEW FROM CENTRAL_MODULE WHERE CTR_M_ID = '" . $moduleId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$page = $row["CTR_M_VIEW"];
				return $page;
			}
		}
	}

	public function GetModuleLocketName($moduleId)
	{
		// FIX: mysql escape string
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$sQ = "SELECT CPC_M_NAME FROM CPCCORE_MODULES WHERE CPC_M_ID = '" . $moduleId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$name = $row["CPC_M_NAME"];
				return $name;
			}
		}
		return null;
	}

	public function GetModuleOnpaysConfiguration($moduleId, &$arModConf)
	{
		// FIX: mysql escape string
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$arModConf = CTOOLS_ArrayRemoveAllElement($arModConf);
		$sQ = "SELECT * FROM CENTRAL_MODULE_LOCKET WHERE CTR_L_MID = '" . $moduleId . "' ";
		// echo $sQ;
		$bOK = false;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				$bOK = true;
				$arModConf = array();
				while ($row = mysqli_fetch_assoc($res)) {
					$key = $row["CTR_L_MKEY"];
					$value = $row["CTR_L_MVALUE"];
					$arModConf[$key] = $value;
					$i++;
				}
			}
		}
		return $bOK;
	}

	public function GetModuleConfig($moduleId)
	{
		// FIX: mysql escape string
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);

		$arConfig = null;

		$sQ = "select * from CENTRAL_MODULE_CONFIG where CTR_CFG_MID = '" . $moduleId . "' " .
			"order by CTR_CFG_MKEY asc";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				$arConfig = array();
				while ($row = mysqli_fetch_assoc($res)) {
					// $arConfig[$i]["key"] = $row["CTR_CFG_MKEY"];
					// $arConfig[$i]["value"] = $row["CTR_CFG_MVALUE"];
					$key = $row["CTR_CFG_MKEY"];
					$value = $row["CTR_CFG_MVALUE"];
					$arConfig[$key] = $value;

					// echo $key . " .. " . $value;

					// TODO: replace \n jadi \n<br /> untuk fix newline
					// $arConfig[$key] = str_replace("\n", "\n<br>", $value);

					$i++;
				}
			}
		}

		return $arConfig;
	}

	// NEW: For future use
	public function GetAppConfig($appId)
	{
		return $this->GetAreaConfig($appId);
	}

	// For backward compatibility only
	public function GetAreaConfig($areaId)
	{
		// FIX: mysql escape string
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		$arConfig = null;
		$sQ = "select * from CENTRAL_AREA_CONFIG where CTR_AC_AID = '" . $areaId . "' " .
			"order by CTR_AC_KEY asc";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				$arConfig = array();
				while ($row = mysqli_fetch_assoc($res)) {
					$key = $row["CTR_AC_KEY"];
					$value = $row["CTR_AC_VALUE"];
					$arConfig[$key] = $value;

					$i++;
				}
			}
		}

		return $arConfig;
	}

	public function IsSupervisor($userId)
	{
		// FIX: mysql escape string
		$userId = mysqli_real_escape_string($this->DBLink, $userId);

		$sQ = "SELECT CTR_U_ADMIN FROM CENTRAL_USER WHERE CTR_U_ID = '" . $userId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$isAdmin = $row["CTR_U_ADMIN"];

				// convert to int
				$manageBit = $isAdmin + 0;
				return ($manageBit & 10) == 10;
			}
		}
		return false;
	}

	public function IsAdmin($userId)
	{
		// FIX: mysql escape string
		$userId = mysqli_real_escape_string($this->DBLink, $userId);

		$sQ = "SELECT CTR_U_ADMIN FROM CENTRAL_USER WHERE CTR_U_ID = '" . $userId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$isAdmin = $row["CTR_U_ADMIN"];

				// DEPRECATED: pakai manageBit
				// return ($isAdmin != 0);

				// convert to int
				$manageBit = $isAdmin + 0;
				return ($manageBit & 1) == 1;
			}
		}
		return false;
	}

	public function GetDatabaseConfig($dbId)
	{
		// FIX: mysql escape string
		$dbId = mysqli_real_escape_string($this->DBLink, $dbId);

		$arConfig = null;

		$sQ = "select * from CENTRAL_DATABASE_CONFIG where CTR_DB_AID = '" . $dbId . "' " .
			"order by CTR_DB_POS asc";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				$arConfig = array();
				while ($row = mysqli_fetch_assoc($res)) {
					$arConfig[$i]["pos"] = $row["CTR_DB_POS"];
					$arConfig[$i]["key"] = $row["CTR_DB_KEY"];
					$arConfig[$i]["value"] = $row["CTR_DB_VALUE"];
					$i++;
				}
			}
		}

		return $arConfig;
	}

	public function InsertUserSMS($user, $pwd, $moduleId, $areaId)
	{
		// FIX: mysql escape string
		$user = mysqli_real_escape_string($this->DBLink, $user);
		$pwd = mysqli_real_escape_string($this->DBLink, $pwd);
		$moduleId = mysqli_real_escape_string($this->DBLink, $moduleId);
		$areaId = mysqli_real_escape_string($this->DBLink, $areaId);

		// Insert User
		$sQ = "insert into CENTRAL_USER " .
			" (CTR_U_ID, CTR_U_UID, CTR_U_PWD, CTR_U_LASTUPDATE, CTR_U_LASTLOGIN, CTR_U_ADMIN, CTR_U_BLOCKED, CTR_U_MULT_LOGIN) " .
			" values (" .
			"'" . $user . "', " .
			"'" . $user . "', " .
			"'" . $pwd . "', " .
			"NOW(), " .
			"'0', " .
			"0 " .
			"0) ";
		// echo $sQ;
		$bOK = false;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		// Get configuration
		$arModuleConfig = $this->GetModuleConfig($moduleId);
		if ($arModuleConfig != null) {
			$roleId = $arModuleConfig["insertUser-roleId"];

			if ($bOK) {
				// Insert Role
				$sQ = "insert into CENTRAL_USER_TO_AREA values (" .
					"'" . $user . "', " .
					"'" . $areaId . "', " .
					"'" . $roleId . "') ";
				if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
					error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
				if ($res = mysqli_query($this->DBLink, $sQ)) {
					$bOK = true;
				}
			}
		}

		return $bOK;
	}

	// -------- e-Voucher --------- //
	public function addProductList($name, $code)
	{
		// FIX: mysql escape string
		$name = mysqli_real_escape_string($this->DBLink, $name);
		$code = mysqli_real_escape_string($this->DBLink, $code);

		$bOK = false;

		//get all product
		$sQ = "	INSERT INTO CENTRAL_EVOUCHER_PRODUCT (CEP_ID, CEP_NAME, CEP_CODE)
				SELECT CONCAT(\"p\", (MAX(CAST(SUBSTRING(CEP_ID, 2) AS UNSIGNED)) + 1)), '$name', '$code' 
				FROM CENTRAL_EVOUCHER_PRODUCT";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function delProductList($name, $code)
	{
		// FIX: mysql escape string
		$name = mysqli_real_escape_string($this->DBLink, $name);
		$code = mysqli_real_escape_string($this->DBLink, $code);

		$bOK = false;

		//get all product
		$sQ = "	DELETE FROM CENTRAL_EVOUCHER_PRODUCT WHERE CEP_NAME='$name' AND CEP_CODE='$code'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function getProductList(&$product)
	{
		$bOK = false;

		//get all product
		$sQ = "SELECT CEP_NAME, CEP_CODE FROM CENTRAL_EVOUCHER_PRODUCT";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				//insert data into product array
				while ($row = mysqli_fetch_assoc($res)) {
					$product[] = $row;
				}
			}
		}

		return $bOK;
	}

	public function setProductList($name, $code, $oldcode)
	{
		// FIX: mysql escape string
		$name = mysqli_real_escape_string($this->DBLink, $name);
		$code = mysqli_real_escape_string($this->DBLink, $code);
		$oldcode = mysqli_real_escape_string($this->DBLink, $oldcode);

		$bOK = false;

		//get all product
		$sQ = "UPDATE CENTRAL_EVOUCHER_PRODUCT SET CEP_NAME='$name', CEP_CODE='$code' WHERE CEP_CODE='$oldcode'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function SetLayoutUser($uId, $styleId)
	{
		// FIX: mysql escape string
		$uId = mysqli_real_escape_string($this->DBLink, $uId);
		$styleId = mysqli_real_escape_string($this->DBLink, $styleId);

		$bOK = false;

		$sQ = "UPDATE CENTRAL_USER SET CTR_U_STYLE = '$styleId' WHERE CTR_U_ID = '$uId'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function GetLayoutUser($uId)
	{
		// FIX: mysql escape string
		$uId = mysqli_real_escape_string($this->DBLink, $uId);

		$style = null;
		$sQ = "SELECT CTR_U_STYLE FROM CENTRAL_USER WHERE CTR_U_ID = '$uId'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				if ($row = mysqli_fetch_assoc($res)) {
					$style = $row["CTR_U_STYLE"];
				}
			}
		}

		return $style;
	}

	public function GetLayoutFiles($style)
	{
		// Parse XML
		if ($style != null) {
			// Include library
			require_once("inc/lib/xml2array.php");

			$descStyleFull = "style/$style/desc.xml";
			if (file_exists($descStyleFull)) {
				// XML
				$arXml = xml2array(file_get_contents($descStyleFull));
				$styleDesc = $arXml["description"];
				$arFiles = $styleDesc["files"];
				return $arFiles;
			}
		}

		return null;
	}

	// -------- GLOBAL --------- //
	public function sqlQuery($query, &$result)
	{
		$bOK = false;

		//get query
		$sQ = $query;
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($result = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_error($result);
		}

		return $bOK;
	}

	public function IsAuthUserRecon($sUName, $sUPwd, &$sUID)
	{
		// FIX: mysql escape string
		$sUName = mysqli_real_escape_string($this->DBLink, $sUName);
		$sUPwd = mysqli_real_escape_string($this->DBLink, $sUPwd);

		$bAuth = false;
		$sUID = null;
		// NEW: password dicek belakangan
		$sQ = "select * from CENTRAL_USER where CTR_U_UID = '" . $sUName . "' ";
		// . "and CTR_U_PWD = '" . md5($sUPwd) . "'";
		// echo "query = $sQ<br />\n";

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
			error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [sQ] $sQ\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			$nRecord = $nRes;
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				//var_dump($row);
				$sUID = $row['CTR_U_ID'];
				$pwd = $row['CTR_U_PWD'];

				// Cek password
				$bAuth = (md5($sUName . $pwd) == $sUPwd);
				//var_dump($sUPwd);
				//var_dump($pwd);
				//var_dump(md5($sUName.$pwd));
			}
		} else {
			$this->iErrCode = -3;
			$this->sErrMsg = mysqli_error($this->DBLink);
			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_ERROR))
				error_log("[" . date('YmdHis') . "][" . $this->sThisFile . ":" . __LINE__ . "] [ERROR] [" . $this->iErrCode . "] " . $this->sErrMsg . "\n", 3, $this->sLogFilename);
		}

		return $bAuth;
	}
}

?>