<?php
class SCANCentralDbSpecific
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

	// -------- GLOBAL --------- //
	public function sqlQuery($query, &$result)
	{
		$bOK = false;

		$sQ = $query;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($result = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_error($this->DBLink);
		}

		return $bOK;
	}

	/* == Untuk database Devel & Demo == */
	public function GetTerminalInfo($termId, &$terminal)
	{
		// FIX: mysql escape string
		$termId = mysqli_real_escape_string($termId);

		$terminal = CTOOLS_ArrayRemoveAllElement($terminal);
		$bOK = false;

		$sQ = "SELECT * FROM CSCCORE_CENTRAL_DOWNLINE WHERE CSC_CD_ID = '" . $termId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$terminal["ppid"] = $row["CSC_CD_ID"];
				$terminal["name"] = $row["CSC_CD_NAME"];
				$terminal["address"] = $row["CSC_CD_ADDRESS"];
				$terminal["phone"] = $row["CSC_CD_PHONE"];
				$bOK = true;
			}
		}

		// terminal name
		$accountNumber = $this->GetAccountNumber($termId);
		$terminal["account"] = $accountNumber;

		return $bOK;
	}

	public function GetAccountNumber($terminal)
	{
		// FIX: mysql escape string
		$terminal = mysqli_real_escape_string($terminal);

		$sQ = "SELECT CSC_PA_ACCOUNT FROM CSCCORE_PPID_ACCOUNT WHERE CSC_PA_PPID = '" . $terminal . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$account = $row["CSC_PA_ACCOUNT"];
				return $account;
			}
		}

		return null;
	}

	public function GetTerminalBlock($termId, &$isBlocked)
	{
		$termId = mysqli_real_escape_string($termId);

		$bOK = false;

		$sQ = "SELECT CSC_CD_ISBLOCKED FROM CSCCORE_CENTRAL_DOWNLINE WHERE CSC_CD_ID = '" . $termId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$isBlocked = $row["CSC_CD_ISBLOCKED"];
				$bOK = true;
			}
		}

		return $bOK;
	}

	public function SetTerminalBlock($termId, $status)
	{
		$termId = mysqli_real_escape_string($this->DBLink, $termId);
		$status = mysqli_real_escape_string($this->DBLink, $status);

		$bOK = false;

		$sQ = "UPDATE CSCCORE_CENTRAL_DOWNLINE SET CSC_CD_ISBLOCKED=$status WHERE CSC_CD_ID = '" . $termId . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function GetModuleOnpays(&$arModule)
	{
		$$arModule = CTOOLS_ArrayRemoveAllElement($arModule);
		$sQ = "SELECT * FROM CPCCORE_MODULES C WHERE CPC_M_ID LIKE 'm%' AND CPM_M_ISPPMODULE = 1 ORDER BY LPAD(CPC_M_ID,11,'0') ASC";
		// echo $sQ;
		$bOK = false;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				$bOK = true;
				$arModule = array();
				while ($row = mysqli_fetch_assoc($res)) {
					$arModule[$i]["id"] = $row["CPC_M_ID"];
					$arModule[$i]["name"] = $row["CPC_M_NAME"];
					$arModule[$i]["desc"] = $row["CPC_M_DESC"];
					$arModule[$i]["varname"] = $row["CPC_M_VARNAME"];
					$arModule[$i]["version"] = $row["CPC_M_VERSION"];
					$arModule[$i]["installed"] = $row["CPC_M_INSTALLED"];
					$i++;
				}
			}
		}
		return $bOK;
	}

	/* === Message Complaint === */
	public function GetPPMessage($module = null)
	{
		$bOK = false;

		$sQCond = "";

		if ($module != null) {
			$sQCond .= "WHERE ";
			for ($i = 0; $i < count($module); $i++) {
				$module[$i] = mysqli_real_escape_string($module[$i]);
				if ($i > 0)
					$sQCond .= " OR ";
				$sQCond .= " CPC_PPM_MODULE='" . $module[$i] . "' ";
			}
		}

		$sQOrder = "ORDER BY CPC_PPM_SENT ASC";

		$sQ = "SELECT * FROM CPCCORE_PAYMENT_POINT_MESSAGE ";
		$sQ .= $sQCond;
		$sQ .= $sQOrder;
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$ppmessage[$i] = $row;
					$i++;
				}
			}
		}

		return $ppmessage;
	}

	public function GetPPMessageId($id, &$ppmessage)
	{
		$id = mysqli_real_escape_string($id);

		$bOK = false;

		$sQCond = ($id != "") ? "WHERE CPC_PPM_ID='$id'" : "";

		$sQ = "SELECT * FROM CPCCORE_PAYMENT_POINT_MESSAGE ";
		$sQ .= $sQCond;
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$ppmessage = mysqli_fetch_assoc($res);
			}
		}

		return $bOK;
	}

	public function GetPPMessageModule(&$module)
	{

		$bOK = false;

		$sQ = "SELECT CPC_PPM_MODULE FROM CPCCORE_PAYMENT_POINT_MESSAGE GROUP BY CPC_PPM_MODULE ORDER BY CPC_PPM_MODULE ASC";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$module[$i] = $row['CPC_PPM_MODULE'];
					$i++;
				}
			}
		}

		return $bOK;
	}

	public function SetPPMessageId($id, $status, $message)
	{
		$bOK = false;

		$sQ = "UPDATE CPCCORE_PAYMENT_POINT_MESSAGE SET CPC_PPM_STATUS='$status', CPC_PPM_REASON='$message' WHERE CPC_PPM_ID='$id'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	/* === Confirm Deposit === */
	public function GetVDeposit(&$ppmessage)
	{
		$bOK = false;

		$sQCond = "";

		$sQOrder = "ORDER BY CSC_CP_SENT_DATE ASC";

		$sQ = "SELECT * FROM CSCCORE_CONFIRMED_DEPOSIT ";
		$sQ .= $sQCond;
		$sQ .= $sQOrder;
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$ppmessage[$i] = $row;
					$i++;
				}
			}
		}

		return $bOK;
	}

	public function GetVDepositId($id, &$ppmessage)
	{
		$id = mysqli_real_escape_string($id);

		$bOK = false;

		$sQCond = ($id != "") ? "WHERE CSC_CP_ID='$id'" : "";

		$sQ = "SELECT * FROM CSCCORE_CONFIRMED_DEPOSIT ";
		$sQ .= $sQCond;
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$ppmessage = mysqli_fetch_assoc($res);
			}
		}

		return $bOK;
	}

	public function SetVDepositId($id, $status, $message)
	{
		$bOK = false;

		$sQ = "UPDATE CSCCORE_CONFIRMED_DEPOSIT SET CSC_CP_STATUS='$status', CSC_CP_REASON='$message' WHERE CSC_CP_ID='$id'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	/* === Approve/Reject SMS === */
	public function GetTerminalSMS($ppid, $phoneNumber, $agentId = "")
	{
		// FIX: mysql escape string
		$ppid = mysqli_real_escape_string($ppid);
		$phoneNumber = mysqli_real_escape_string($phoneNumber);
		$agentId = mysqli_real_escape_string($agentId);

		$terminal = null;
		$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_REG WHERE CSM_SR_PPID = '" . $ppid . "' AND CSM_SR_PHONE_NUMBER = '" . $phoneNumber . "' ";
		if ($agentId != "") {
			$sQ .= "AND CSM_SR_AGENT_ID = '" . $agentId . "' ";
		}
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$terminal = array();
				$terminal["name"] = $row["CSM_SR_NAME"];
				$terminal["bank"] = $row["CSM_SR_BANK"];
				$terminal["accountNumber"] = $row["CSM_SR_ACCOUNT_NUMBER"];
				$terminal["agentId"] = $row["CSM_SR_AGENT_ID"];
				$terminal["flag"] = $row["CSM_SR_FLAG"];
				$terminal["status"] = $row["CSM_SR_STATUS"];
				$terminal["initSetoran"] = $row["CSM_SR_INITIAL_SETORAN"];
				$terminal["initDeposit"] = $row["CSM_SR_INITIAL_DEPOSIT"];
				$terminal["pinDigest"] = $row["CSM_SR_PIN_DIGEST"];
			}
		}

		return $terminal;
	}

	public function GetTerminalSMSInfo($ppid)
	{
		// FIX: mysql escape string
		$ppid = mysqli_real_escape_string($ppid);

		$terminal = null;
		$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_REG WHERE CSM_SR_PPID = '" . $ppid . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$terminal = array();
				$terminal["name"] = $row["CSM_SR_NAME"];
				$terminal["bank"] = $row["CSM_SR_BANK"];
				$terminal["accountNumber"] = $row["CSM_SR_ACCOUNT_NUMBER"];
				$terminal["phoneNumber"] = $row["CSM_SR_PHONE_NUMBER"];
				$terminal["status"] = $row["CSM_SR_STATUS"];
				$terminal["initSetoran"] = $row["CSM_SR_INITIAL_SETORAN"];
				$terminal["initDeposit"] = $row["CSM_SR_INITIAL_DEPOSIT"];
				$terminal["agentId"] = $row["CSM_SR_AGENT_ID"];

				$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_MASTER_AGENT WHERE CSM_MA_AGENT_ID = '" . $terminal["agentId"] . "' ";
				if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
					error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
				if ($res = mysqli_query($this->DBLink, $sQ)) {
					$nRes = mysqli_num_rows($res);
					if ($nRes > 0) {
						$row = mysqli_fetch_assoc($res);
						$terminal["agentName"] = $row["CSM_MA_NAME"];
					} else {
						// Unknown error
						$terminal["agentName"] = "-";
					}
				}
			}
		}

		return $terminal;
	}

	public function ApproveSMS($uid, $ppid, $phoneNumber, $initDeposit, $approve, $reason = "")
	{
		// FIX: mysql escape string
		$ppid = mysqli_real_escape_string($ppid);
		$phoneNumber = mysqli_real_escape_string($phoneNumber);
		$uid = mysqli_real_escape_string($uid);
		$reason = mysqli_real_escape_string($reason);

		// CEK duplicated PPID: only for approval
		if ($approve) {
			$sQ = "SELECT COUNT(*) AS COUNT FROM CSCMOD_VOUCHER_SMS_REG WHERE CSM_SR_PPID = '" . $ppid . "' AND (CSM_SR_STATUS = 1 OR CSM_SR_STATUS = 3)";
			// echo $sQ;

			if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
				error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
			if ($res = mysqli_query($this->DBLink, $sQ)) {
				$nRes = mysqli_num_rows($res);
				if ($nRes > 0) {
					// terdapat PPID yang sama
					$row = mysqli_fetch_assoc($res);
					$count = $row["COUNT"];
					if ($count > 0) {
						return -1;
					}
				}
			}
		}

		$bOK = false;
		$approve = ($approve === true ? 1 : 2);
		$now = strftime("%Y-%m-%d %H:%M:%S", time());

		$sQ = "UPDATE CSCMOD_VOUCHER_SMS_REG " .
			"SET CSM_SR_STATUS = " . $approve . ", " .
			"CSM_SR_INITIAL_DEPOSIT = '" . $initDeposit . "', " .
			"CSM_SR_APPROVER_ID = '" . $uid . "', " .
			"CSM_SR_APPROVER_TIME = '" . $now . "', " .
			"CSM_SR_REASON = '" . $reason . "' " .
			"WHERE CSM_SR_PPID = '" . $ppid . "' AND CSM_SR_PHONE_NUMBER = '" . $phoneNumber . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function EditPpid($ppid, $newPpid, $phoneNumber)
	{
		// FIX: mysql escape string
		$ppid = mysqli_real_escape_string($ppid);
		$newPpid = mysqli_real_escape_string($newPpid);
		$phoneNumber = mysqli_real_escape_string($phoneNumber);

		$bOK = false;
		$sQ = "UPDATE CSCMOD_VOUCHER_SMS_REG " .
			"SET CSM_SR_PPID = '" . $newPpid . "' " .
			"WHERE CSM_SR_PPID = '" . $ppid . "' AND CSM_SR_PHONE_NUMBER = '" . $phoneNumber . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}
	public function EditDeposit($ppid, $phoneNumber, $deposit)
	{
		// FIX: mysql escape string
		$ppid = mysqli_real_escape_string($ppid);
		$deposit = mysqli_real_escape_string($deposit);
		$phoneNumber = mysqli_real_escape_string($phoneNumber);

		$bOK = false;
		$sQ = "UPDATE CSCMOD_VOUCHER_SMS_REG " .
			"SET CSM_SR_INITIAL_DEPOSIT = '" . $deposit . "' " .
			"WHERE CSM_SR_PPID = '" . $ppid . "' AND CSM_SR_PHONE_NUMBER = '" . $phoneNumber . "' ";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		}

		return $bOK;
	}

	public function SearchSMS($name, $ppid, $phoneNumber, $agentId, $status)
	{
		// FIX: mysql escape string
		$name = mysqli_real_escape_string($name);
		$ppid = mysqli_real_escape_string($ppid);
		$phoneNumber = mysqli_real_escape_string($phoneNumber);
		$agentId = mysqli_real_escape_string($agentId);

		$terminal = null;
		$first = false;
		$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_REG WHERE ";
		if ($ppid != "") {
			$sQ .= "CSM_SR_PPID LIKE '%" . $ppid . "%' ";
			$first = true;
		}
		if ($phoneNumber != "") {
			if ($first) {
				$sQ .= "AND ";
			}
			$sQ .= "CSM_SR_PHONE_NUMBER LIKE '%" . $phoneNumber . "%' ";
			$first = true;
		}
		if ($name != "") {
			if ($first) {
				$sQ .= "AND ";
			}
			$sQ .= "CSM_SR_NAME LIKE '%" . $name . "%' ";
			$first = true;
		}
		if ($agentId != "") {
			if ($first) {
				$sQ .= "AND ";
			}
			$sQ .= "CSM_SR_AGENT_ID LIKE '%" . $agentId . "%' ";
			$first = true;
		}
		if ($status != "" && $status != -1) {
			if ($first) {
				$sQ .= "AND ";
			}
			$sQ .= "CSM_SR_STATUS = '" . $status . "' ";
			$first = true;
		}
		$sQ .= "ORDER BY CSM_SR_STATUS ASC";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$terminal[$i]["name"] = $row["CSM_SR_NAME"];
					$terminal[$i]["bank"] = $row["CSM_SR_BANK"];
					$terminal[$i]["ppid"] = $row["CSM_SR_PPID"];
					$terminal[$i]["phoneNumber"] = $row["CSM_SR_PHONE_NUMBER"];
					$terminal[$i]["accountNumber"] = $row["CSM_SR_ACCOUNT_NUMBER"];
					$terminal[$i]["agentId"] = $row["CSM_SR_AGENT_ID"];
					$terminal[$i]["flag"] = $row["CSM_SR_FLAG"];
					$terminal[$i]["status"] = $row["CSM_SR_STATUS"];
					$terminal[$i]["initSetoran"] = $row["CSM_SR_INITIAL_SETORAN"];
					$terminal[$i]["initDeposit"] = $row["CSM_SR_INITIAL_DEPOSIT"];
					$i++;
				}
			}
		}

		return $terminal;
	}

	/* === e-Voucher === */
	public function GetTerminalMapping($ppid, &$map)
	{
		$ppid = mysqli_real_escape_string($ppid);

		$bOK = false;
		$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_MAPPING WHERE CSM_SM_PPID = '$ppid'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$map[$i]['PHONE_NUMBER'] = $row['CSM_SM_PHONE_NUMBER'];
					$i++;
				}
			}
		}

		return $bOK;
	}

	public function isTerminalPin($phone, $pin)
	{
		$phone = mysqli_real_escape_string($phone);

		$bOK = false;


		$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_MAPPING WHERE CSM_SM_PHONE_NUMBER = '$phone' AND CSM_SM_PIN_DIGEST = '" . md5($pin) . "'";
		// echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
			}
		}

		return $bOK;
	}

	public function setAddReg($phone, $pin, $ppid)
	{
		$phone = mysqli_real_escape_string($phone);
		$pin = mysqli_real_escape_string($pin);
		$ppid = mysqli_real_escape_string($ppid);

		$bOK = false;
		$sQ = "INSERT INTO CSCMOD_VOUCHER_SMS_MAPPING VALUES ('$phone','$ppid',1,'" . md5($pin) . "')";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_error($res);
		}

		return $bOK;
	}

	public function setNewPin($phone, $pin)
	{
		$phone = mysqli_real_escape_string($phone);
		$pin = mysqli_real_escape_string($pin);

		$bOK = false;
		$sQ = "UPDATE CSCMOD_VOUCHER_SMS_MAPPING SET CSM_SM_PIN_DIGEST='" . md5($pin) . "' WHERE CSM_SM_PHONE_NUMBER='$phone'";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_error($res);
		}

		return $bOK;
	}

	public function setConfirmDeposit($id, $number, $dt, $method, $bank, $account, $name, $ppid, $deposit)
	{
		$number = mysqli_real_escape_string($number);
		$bank = mysqli_real_escape_string($bank);
		$account = mysqli_real_escape_string($account);
		$name = mysqli_real_escape_string($name);
		$ppid = mysqli_real_escape_string($ppid);
		$deposit = mysqli_real_escape_string($deposit);

		$bOK = false;
		$sQ = "
			INSERT INTO CSCCORE_CONFIRMED_DEPOSIT (
				CSC_CP_ID, 
				CSC_CP_SENDER, 
				CSC_CP_DATE, 
				CSC_CP_METHOD, 
				CSC_CP_BANK, 
				CSC_CP_ACC_NUMBER, 
				CSC_CP_NAME, 
				CSC_CP_PPID, 
				CSC_CP_DEPOSIT, 
				CSC_CP_STATUS, 
				CSC_CP_SENT_DATE
				)
			VALUES (
				'$id',
				'$number',
				'$dt',
				'$method',
				'$bank',
				'$account',
				'$name',
				'$ppid',
				'$deposit',
				'0',
				now()
				)
				";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_error($res);
		}

		return $bOK;
	}

	public function getConfirmDeposit($ppid, $limit = null)
	{
		$ppid = mysqli_real_escape_string($ppid);
		$bOK = false;

		$sQCond = " WHERE CSC_CP_PPID='$ppid' ";

		$sQOrder = " ORDER BY CSC_CP_SENT_DATE ASC ";

		$sQLimit = ($limit != null) ? " LIMIT $limit " : "";

		$sQ = "SELECT * FROM CSCCORE_CONFIRMED_DEPOSIT ";
		$sQ .= $sQCond;
		$sQ .= $sQOrder;
		$sQ .= $sQLimit;
		//echo $sQ;

		if (CTOOLS_IsInFlag($this->iDebug, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . $this->sThisFile . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$result[$i] = $row;
					$i++;
				}
			}
		}

		return $result;
	}

	public function setKeluhan($id, $ppid, $msg, $msgType)
	{
		$msg = mysqli_real_escape_string($msg);
		$msgType = mysqli_real_escape_string($msgType);

		$bOK = false;
		$sQ = "
			INSERT INTO CPCCORE_PAYMENT_POINT_MESSAGE (
				CPC_PPM_ID, 
				CPC_PPM_PPID, 
				CPC_PPM_MODULE, 
				CPC_PPM_MSG, 
				CPC_PPM_SENT, 
				CPC_PPM_MSGTYPE
				)
			VALUES (
				'$id',
				'$ppid',
				'e-Voucher',
				'$msg',
				now(),
				'$msgType'
				)
				";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_error($res);
		}

		return $bOK;
	}

	public function getKeluhan($ppid, $msg, &$result)
	{
		$msg = mysqli_real_escape_string($msg);
		$ppid = mysqli_real_escape_string($ppid);

		$bOK = false;
		$sQ = "
			SELECT CPC_PPM_MODULE, CPC_PPM_MSG, CPC_PPM_SENT, CPC_PPM_MSGTYPE, CPC_PPM_STATUS, CPC_PPM_REASON 
			FROM CPCCORE_PAYMENT_POINT_MESSAGE 
			WHERE CPC_PPM_PPID = '$ppid'
			  AND CPC_PPM_MSG LIKE '%$msg%'
			";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$result[$i] = $row;
					$i++;
				}
			}
		} else {
			echo mysqli_error($res);
		}

		return $bOK;
	}

	/* === e-Admin === */
	public function InsertNewAgentMaster($name, $pin, &$id)
	{
		$name = mysqli_real_escape_string($name);
		$pin = mysqli_real_escape_string($pin);

		$bOK = false;

		$id = strtoupper(sprintf('%04x%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));

		$sQ = "INSERT INTO CSCMOD_VOUCHER_SMS_MASTER_AGENT(CSM_MA_AGENT_ID,CSM_MA_NAME,CSM_MA_PIN_DIGEST) VALUES ('$id','$name','" . md5($pin) . "')";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);

		}

		return $bOK;
	}

	public function InsertAgentPhone($id, $phone)
	{
		$phone = mysqli_real_escape_string($phone);

		$bOK = false;

		$sQ = "INSERT INTO CSCMOD_VOUCHER_SMS_AGENT_PHONE(CSM_AP_AGENT_ID,CSM_AP_PHONE_NUMBER) VALUES ('$id','$phone')";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);
		}

		return $bOK;
	}

	public function EraseAgentMaster($id)
	{
		$bOK = false;

		$sQ = "DELETE FROM CSCMOD_VOUCHER_SMS_MASTER_AGENT WHERE CSM_MA_AGENT_ID='$id'";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);

		}

		return $bOK;
	}

	public function getAgentMasterAll(&$result)
	{
		$bOK = false;

		$sQ = "SELECT CSM_MA_AGENT_ID,CSM_MA_NAME FROM CSCMOD_VOUCHER_SMS_MASTER_AGENT";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$result[$i] = $row;
					$i++;
				}
			}
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);

		}

		return $bOK;
	}

	public function getAgentPhone($id, &$result)
	{
		$bOK = false;

		$sQ = "SELECT CSM_AP_PHONE_NUMBER FROM CSCMOD_VOUCHER_SMS_AGENT_PHONE WHERE CSM_AP_AGENT_ID='$id'";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$result[$i] = $row['CSM_AP_PHONE_NUMBER'];
					$i++;
				}
			}
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);

		}

		return $bOK;
	}

	public function setAgentPIN($id, $oldpin, $newpin)
	{
		$bOK = false;

		$sQ = "SELECT CSM_MA_AGENT_ID FROM CSCMOD_VOUCHER_SMS_MASTER_AGENT WHERE CSM_MA_AGENT_ID='$id' AND CSM_MA_PIN_DIGEST='" . md5($oldpin) . "'";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$sQ = "UPDATE CSCMOD_VOUCHER_SMS_MASTER_AGENT SET CSM_MA_PIN_DIGEST='" . md5($newpin) . "' WHERE CSM_MA_AGENT_ID='$id'";

				if ($res = mysqli_query($this->DBLink, $sQ)) {
					$bOK = true;
				} else {
					echo mysqli_errno($res) . " : " . mysqli_error($res);
				}
			}
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);
		}

		return $bOK;
	}

	public function isAgentPIN($id, $pin)
	{
		$bOK = false;

		$sQ = "SELECT CSM_MA_AGENT_ID FROM CSCMOD_VOUCHER_SMS_MASTER_AGENT WHERE CSM_MA_AGENT_ID='$id' AND CSM_MA_PIN_DIGEST='" . md5($pin) . "'";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
			}
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);
		}

		return $bOK;
	}

	/* === e-Agent === */
	public function InsertNewPPMaster($phone, $name, $pin, $bank, $accNum, $deposit, $agent, $ppid, $vsiAcc)
	{
		$phone = mysqli_real_escape_string($phone);
		$name = mysqli_real_escape_string($name);
		$pin = mysqli_real_escape_string($pin);
		$bank = mysqli_real_escape_string($bank);
		$accNum = mysqli_real_escape_string($accNum);
		$deposit = mysqli_real_escape_string($deposit);
		$ppid = mysqli_real_escape_string($ppid);

		$bOK = false;

		$sQ = "
			INSERT INTO CSCMOD_VOUCHER_SMS_REG 
			(CSM_SR_PHONE_NUMBER, CSM_SR_PPID, CSM_SR_ACCOUNT_NUMBER, CSM_SR_NAME, CSM_SR_BANK, CSM_SR_ACC_NUMBER, CSM_SR_STATUS, CSM_SR_INITIAL_SETORAN, CSM_SR_PIN_DIGEST, CSM_SR_AGENT_ID) 
			VALUES 
			('$phone','$ppid','$vsiAcc','$name','$bank','$accNum', 0, $deposit,'" . md5($pin) . "','$agent')";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$bOK = true;
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);
		}

		return $bOK;
	}

	public function getPPMaster($agentId, &$result)
	{
		$bOK = false;

		$sQ = "SELECT * FROM CSCMOD_VOUCHER_SMS_REG WHERE CSM_SR_AGENT_ID='$agentId'";

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$bOK = true;
				$i = 0;
				while ($row = mysqli_fetch_assoc($res)) {
					$result[$i] = $row;
					$i++;
				}
			}
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);

		}

		return $bOK;
	}

	public function getMaxPPID()
	{
		$sQ = "SELECT CDC_LUP_NUMBER FROM CDCCORE_LAST_USED_PPID";
		$max = false;

		if (CTOOLS_IsInFlag(DEBUG, DEBUG_DEBUG))
			error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [DEBUG] sQ [$sQ]\n", 3, $this->sLogFilename);
		if ($res = mysqli_query($this->DBLink, $sQ)) {
			$nRes = mysqli_num_rows($res);
			if ($nRes > 0) {
				$row = mysqli_fetch_assoc($res);
				$max = $row['CDC_LUP_NUMBER'];
			}
		} else {
			echo mysqli_errno($res) . " : " . mysqli_error($res);

		}

		return $max;
	}
}
?>