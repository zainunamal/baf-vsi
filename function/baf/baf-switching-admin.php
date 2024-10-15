<?php
require_once("inc/payment/ctools.php");
require_once("inc/baf/inc-baf-config.php");
class classSwitching
{
	public $DBLINK;
	private $header;
	private $body;
	private $table;
	public $params;

	function getDataSwitching(&$rows)
	{
		$OK = false;
		/*
									  CDC_S_ID, CDC_S_NAME, CDC_S_ADDRESS, CDC_S_PHONE, CDC_S_PIC_NAME, CDC_S_PIC_PHONE, CDC_S_TYPE, CDC_S_REGISTERED, CDC_S_COD
									  */
		$sQ = "SELECT CDC_S_ID, CDC_S_NAME, CDC_S_ADDRESS, CDC_S_PHONE, CDC_S_PIC_NAME, CDC_S_PIC_PHONE, CDC_S_TYPE, CDC_S_REGISTERED, CDC_S_CODE FROM CDCCORE_SWITCHER ";


		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDC_S_ID'] = $rws['CDC_S_ID'];
				$rows[$i]['CDC_S_NAME'] = $rws['CDC_S_NAME'];
				$rows[$i]['CDC_S_ADDRESS'] = $rws['CDC_S_ADDRESS'];
				$rows[$i]['CDC_S_PHONE'] = $rws['CDC_S_PHONE'];
				$rows[$i]['CDC_S_PIC_NAME'] = $rws['CDC_S_PIC_NAME'];
				$rows[$i]['CDC_S_PIC_PHONE'] = $rws['CDC_S_PIC_PHONE'];
				$rows[$i]['CDC_S_TYPE'] = $rws['CDC_S_TYPE'];
				$rows[$i]['CDC_S_REGISTERED'] = $rws['CDC_S_REGISTERED'];
				$rows[$i]['CDC_S_CODE'] = $rws['CDC_S_CODE'];
				$i++;
				$OK = true;
			}
		} else {
			echo "Error : " . mysqli_error($this->DBLINK);
		}
		return $OK;
	}

	function createHeader()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>ID</td><td align='center'>Name</td><td align='center'>Address</td>";
		$this->header .= "<td align='center'>Phone</td><td align='center'>Contact Name</td><td align='center'>Phone</td>";
		$this->header .= "<td align='center'>Type</td><td align='center'>Registered</td><td align='center'>Kode</td><td align='center'>Option</td></tr>";
	}

	function createBody()
	{
		if ($this->getDataSwitching($rows)) {
			$i = 0;
			foreach ($rows as $row) {
				$prmEdt = "&status=edit&idSwitching=" . $row['CDC_S_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=delete&idSwitching=" . $row['CDC_S_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$prmSwt = "&status=switching&idSwitching=" . $row['CDC_S_ID'];
				$prmSwt = base64_decode($this->params) . $prmSwt;
				$prmSwt = base64_encode($prmSwt);
				$prmFee = "&status=fee&idSwitching=" . $row['CDC_S_ID'];
				$prmFee = base64_decode($this->params) . $prmFee;
				$prmFee = base64_encode($prmFee);
				$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_S_ID'] . "</td><td>" . $row['CDC_S_NAME'];
				$this->body .= "</td><td>" . $row['CDC_S_ADDRESS'] . "</td><td align='center'>" . $row['CDC_S_PHONE'] . "</td>";
				$this->body .= "<td align='center'>" . $row['CDC_S_PIC_NAME'] . "</td><td align='center'>" . $row['CDC_S_PIC_PHONE'] . "</td>";
				$this->body .= "<td align='center'>" . $row['CDC_S_TYPE'] . "</td>";
				$this->body .= "<td align='center'>" . $row['CDC_S_REGISTERED'] . "</td><td align='center'>" . $row['CDC_S_CODE'] . "</td>";
				$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>
									&nbsp;
									<a href='main.php?param=" . $prmSwt . "'><img border='0' src='image/icon/mtce.png' alt='Bank' title='Bank'></img></a>
									&nbsp;
									<a href='main.php?param=" . $prmFee . "'><img border='0' src='image/icon/money.png' alt='Misc Fee' title='Misc Fee'></img></a>
					</td></tr>";
				$i++;
			}
		}
	}

	function createTable()
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeader();
		$this->createBody();
		$this->table = '<style type="text/css">
				<!--
				#tbl-Switching {
					background-color: #aaa;
					border-style:solid;
					border-color:#000;
					border-width:0px;
					width:1000;
					margin:auto;
				}
				.styleTitleForm {
					background-color:#993333;
				}
				a {
					text-decoration:none;
					color : #ffa500;
				}
				-->
				</style>';
		$this->table .= "<div id='tbl-Switching'><table cellpadding = '4' cellspacing='1' border='0' width='100%'><tr><td class='styleTitleForm'><a href='main.php?param=" . $prmAdd . "' onClick=''><img border='0' src='image/icon/add.png' alt='Tambah Switching' title='Tambah Switching'></img> Tambah Switching</a></td></tr></table><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
	}

	public function displayForm($act, $id = NULL)
	{
		$optDay = '';
		$optMon = '';
		$optYr = '';
		$optDay2 = '';
		$optMon2 = '';
		$optYr2 = '';
		$yr = date('Y');
		$d = date('j');
		$m = date('n');

		$switching_id = '';
		$switching_name = '';
		$switching_address = '';
		$switching_phone = '';
		$switching_pic_name = '';
		$switching_pic_phone = '';
		$switching_type = 0;
		$switching_registerd = '';
		$switching_code = '';
		if ($id) {
			$sQ = "SELECT CDC_S_ID, CDC_S_NAME, CDC_S_ADDRESS, CDC_S_PHONE, CDC_S_PIC_NAME, CDC_S_PIC_PHONE, CDC_S_TYPE, CDC_S_REGISTERED, CDC_S_CODE FROM CDCCORE_SWITCHER";
			$sQ .= " WHERE CDC_S_ID ='" . mysqli_real_escape_string($this->DBLINK, $id) . "'";
			if ($res = mysqli_query($this->DBLINK, $sQ)) {
				while ($rws = mysqli_fetch_array($res)) {
					$switching_id = $rws['CDC_S_ID'];
					$switching_name = $rws['CDC_S_NAME'];
					$switching_address = $rws['CDC_S_ADDRESS'];
					$switching_phone = $rws['CDC_S_PHONE'];
					$switching_pic_name = $rws['CDC_S_PIC_NAME'];
					$switching_pic_phone = $rws['CDC_S_PIC_PHONE'];
					$switching_type = $rws['CDC_S_TYPE'];
					$switching_registered = $rws['CDC_S_REGISTERED'];
					$switching_code = $rws['CDC_S_CODE'];
				}
			} else {
				echo "Error : " . mysqli_error($this->DBLINK);
			}
			$tmp = explode(" ", $switching_registered);
			$tmpdate = explode("-", $tmp[0]);
			$yr = $tmpdate[0];
			$d = $tmpdate[1];
			$m = $tmpdate[2];
		}

		for ($t = 1; $t <= 31; $t++) {
			$asel = ($d == $t ? ' selected="selected" ' : '');
			$optDay .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $asel . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
		}
		for ($t = 1; $t <= 12; $t++) {
			$bsel = ($m == $t ? ' selected="selected" ' : '');
			$optMon .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $bsel . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
		}
		for ($t = 0; $t <= 10; $t++) {
			$csel = ($yr == $t ? ' selected="selected" ' : '');
			$optYr .= '<option value="' . str_pad(($yr + $t), 2, 0, STR_PAD_LEFT) . '" ' . $csel . '>' . ($yr + $t) . '</option>';
		}

		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		$prm = array_pop($pr);
		if ($id) {
			$prm = array_pop($pr);
		}
		$p = base64_encode(implode("&", $pr) . "&save=" . $act);

		$form = '<style type="text/css">
				<!--
				.styleDivCenter{
					width:600;
					margin:auto;
					background-color:#993333;
				}
				.styleTitleForm {
					background-color:#993333;
				}
				a {
					text-decoration:none;
					color : #ffa500;
				}
				-->
				</style>
				<div id="form" class="styleDivCenter">
				<table width="100%" border="0" cellpadding="8" cellspacing="0"><tr><td class="styleTitleForm"> 
				<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a></td></tr>
				<tr><td>
		<form id="form1" name="form1" method="post" action="main.php?param=' . $p . '">
		  <table width="100%" border="0" cellpadding="4" cellspacing="0"> 
			<tr>
			  <td>ID</td>
			  <td>:</td>
			  <td><input name="switching_id" type="text" size="7" maxlength="7" value="' . $switching_id . '"></td>
			</tr>
			<tr>
			  <td>Name</td>
			  <td>:</td>
			  <td><input name="switching_name" type="text" size="50" maxlength="100"  value="' . $switching_name . '"></td>
			</tr>
			<tr>
			  <td>Address</td>
			  <td>:</td>
			  <td><input name="switching_address" type="text" size="60" maxlength="255"  value="' . $switching_address . '"></td>
			</tr>
			<tr>
			  <td>Phone</td>
			  <td>:</td>
			  <td><input name="switching_phone" type="text" size="50" maxlength="50"  value="' . $switching_phone . '"></td>
			</tr>
			<tr>
			  <td>Contact Person</td>
			  <td>:</td>
			  <td><input name="switching_pic_name" type="text" size="60" maxlength="100"  value="' . $switching_pic_name . '"></td>
			</tr>
			<tr>
			  <td>Phone</td>
			  <td>:</td>
			  <td><input name="switching_pic_phone" type="text" size="60" maxlength="100"  value="' . $switching_pic_phone . '"></td>
			</tr>
			<tr>
			  <td>Type</td>
			  <td>:</td>
			  <td><input name="switching_type" type="text" size="1" maxlength="1"  value="' . $switching_type . '"></td>
			</tr>
			<tr>
			  <td>Registered</td>
			  <td>:</td>
			  <td><label>
				<select name="str_tgl" id="str_tgl">' . $optDay . '
				</select>
			  </label>/
				<label>
				  <select name="str_mon" id="str_mon">' . $optMon . '
				  </select>/
			  </label>
				<label>
				  <select name="str_yr" id="str_yr">' . $optYr . '
				  </select>&nbsp;-&nbsp;dd/mm/YYYY
			  </label></td>
			</tr>
			<tr>
			  <td>Code</td>
			  <td>:</td>
			  <td><input name="switching_code" type="text" size="2" maxlength="2"  value="' . $switching_code . '"></td></td>
			</tr>
			<tr>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td><label>
				<input type="submit" name="submit" id="submit" value="Submit" />
			  </label><input name="idSwitching" type="hidden" value="' . $id . '" /></td>
			</tr>
		  </table>
		</form></div></td></tr></table>';
		echo $form;
	}

	public function saveSwitching($id, $name, $address, $phone, $pic_name, $pic_phone, $type, $registered, $code)
	{
		$OK = false;
		$rand_id = "";
		srand((double) microtime() * 1000000);
		$rand_id = md5(uniqid(rand()));

		$name = nullable_htmlspecialchar($name, ENT_QUOTES);
		$address = nullable_htmlspecialchar($address, ENT_QUOTES);
		$pic_name = nullable_htmlspecialchar($pic_name, ENT_QUOTES);
		$phone = nullable_htmlspecialchar($phone, ENT_QUOTES);
		$pic_phone = nullable_htmlspecialchar($pic_phone, ENT_QUOTES);

		$sQ = "INSERT INTO CDCCORE_SWITCHER (CDC_S_ID, CDC_S_NAME, CDC_S_ADDRESS, CDC_S_PHONE, CDC_S_PIC_NAME, CDC_S_PIC_PHONE, CDC_S_TYPE, CDC_S_REGISTERED, CDC_S_CODE) ";
		$sQ .= " VALUES ('" . mysqli_real_escape_string($this->DBLINK, $id) . "','" . mysqli_real_escape_string($this->DBLINK, $name) . "','" . mysqli_real_escape_string($this->DBLINK, $address) . "','" . mysqli_real_escape_string($this->DBLINK, $phone) . "','" . mysqli_real_escape_string($this->DBLINK, $pic_name) . "','" . mysqli_real_escape_string($this->DBLINK, $pic_phone) . "','" . mysqli_real_escape_string($this->DBLINK, $type) . "','" . mysqli_real_escape_string($this->DBLINK, $registered) . "','" . mysqli_real_escape_string($this->DBLINK, $code) . "')";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			echo "Error : " . mysqli_error($this->DBLINK);
		}

		return $OK;
	}

	public function saveEditSwitching($id, $name, $address, $phone, $pic_name, $pic_phone, $type, $registered, $code)
	{
		$OK = false;

		$name = nullable_htmlspecialchar($name, ENT_QUOTES);
		$address = nullable_htmlspecialchar($address, ENT_QUOTES);
		$pic_name = nullable_htmlspecialchar($pic_name, ENT_QUOTES);
		$phone = nullable_htmlspecialchar($phone, ENT_QUOTES);
		$pic_phone = nullable_htmlspecialchar($pic_phone, ENT_QUOTES);

		$sQ = "UPDATE CDCCORE_SWITCHER SET CDC_S_NAME ='" . mysqli_real_escape_string($this->DBLINK, $name) . "', CDC_S_ADDRESS='" . mysqli_real_escape_string($this->DBLINK, $address) . "', CDC_S_PHONE='" . mysqli_real_escape_string($this->DBLINK, $phone) . "',";
		$sQ .= " CDC_S_PIC_NAME='" . mysqli_real_escape_string($this->DBLINK, $pic_name) . "', CDC_S_PIC_PHONE='" . mysqli_real_escape_string($this->DBLINK, $pic_phone) . "', CDC_S_TYPE='" . mysqli_real_escape_string($this->DBLINK, $type) . "',CDC_S_REGISTERED='" . mysqli_real_escape_string($this->DBLINK, $registered) . "',";
		$sQ .= " CDC_S_CODE = '" . mysqli_real_escape_string($this->DBLINK, $code) . "' WHERE CDC_S_ID='" . mysqli_real_escape_string($this->DBLINK, $id) . "'";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			echo "Error : " . mysqli_error($this->DBLINK);
		}

		return $OK;
	}

	public function deleteSwitching($id)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCCORE_SWITCHER WHERE CDC_S_ID ='" . mysqli_real_escape_string($this->DBLINK, $id) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			echo "Error : " . mysqli_error($this->DBLINK);
		}

		return $OK;
	}

	public function displaySwitching()
	{
		$this->createTable();
		echo $this->table;
	}

	function getDataSwitchingBank(&$rows, $idswitching)
	{
		$OK = false;
		$sQ = "SELECT A.CDC_SB_ID, A.CDC_SB_SID, A.CDC_SB_BID, A.CDC_SB_PID, B.CDC_S_NAME, C.CDC_B_NAME,A.CDC_SB_MIN_PAYMENT_ISOVERRIDE,A.CDC_SB_MIN_PAYMENT_AMOUNT FROM CDCCORE_SWITCHER_BANK A LEFT JOIN CDCCORE_SWITCHER B ON A.CDC_SB_SID = B.CDC_S_ID
		       LEFT JOIN CDCCORE_BANK C ON A.CDC_SB_BID = C.CDC_B_ID  WHERE A.CDC_SB_SID = '$idswitching' ";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDC_SB_ID'] = $rws['CDC_SB_ID'];
				$rows[$i]['CDC_SB_SID'] = $rws['CDC_SB_SID'];
				$rows[$i]['CDC_SB_BID'] = $rws['CDC_SB_BID'];
				$rows[$i]['CDC_SB_PID'] = $rws['CDC_SB_PID'];
				$rows[$i]['CDC_S_NAME'] = $rws['CDC_S_NAME'];
				$rows[$i]['CDC_B_NAME'] = $rws['CDC_B_NAME'];
				$rows[$i]['CDC_SB_MIN_PAYMENT_ISOVERRIDE'] = $rws['CDC_SB_MIN_PAYMENT_ISOVERRIDE'];
				$rows[$i]['CDC_SB_MIN_PAYMENT_AMOUNT'] = $rws['CDC_SB_MIN_PAYMENT_AMOUNT'];

				$i++;
				$OK = true;
			}
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
		return $OK;
	}

	function getDataSwitchingBankFee(&$rows, $idswitching)
	{
		$OK = false;
		$sQ = "SELECT A.CDC_BMF_ID,A.CDC_BMF_SID, A.CDC_BMF_BID, A.CDC_BMF_FEE, A.CDC_BMF_DESC, A.CDC_BMF_FULL_INFO, B.CDC_S_NAME, C.CDC_B_NAME FROM CDCMOD_MF_AUTO_BANK_MISC_FEE A LEFT JOIN CDCCORE_SWITCHER B ON A.CDC_BMF_SID = B.CDC_S_ID
		       LEFT JOIN CDCCORE_BANK C ON A.CDC_BMF_BID = C.CDC_B_ID  WHERE A.CDC_BMF_SID = '$idswitching' ";
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDC_BMF_ID'] = $rws['CDC_BMF_ID'];
				$rows[$i]['CDC_BMF_SID'] = $rws['CDC_BMF_SID'];
				$rows[$i]['CDC_BMF_BID'] = $rws['CDC_BMF_BID'];
				$rows[$i]['CDC_BMF_FEE'] = $rws['CDC_BMF_FEE'];
				$rows[$i]['CDC_BMF_DESC'] = $rws['CDC_BMF_DESC'];
				$rows[$i]['CDC_BMF_FULL_INFO'] = $rws['CDC_BMF_FULL_INFO'];
				$rows[$i]['CDC_S_NAME'] = $rws['CDC_S_NAME'];
				$rows[$i]['CDC_B_NAME'] = $rws['CDC_B_NAME'];
				$i++;
				$OK = true;
			}
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
		return $OK;
	}

	function createHeadersSwitchingBank()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>ID</td><td align='center'>Switcher</td><td align='center'>Bank</td><td align='center'>PAN</td><td align='center'>Override Minimum Payment</td><td align='center'>Minimum Payment Amount</td><td align='center'>Option</td></tr>";
	}

	function createHeadersSwitchingBankFee()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>ID</td><td align='center'>Switcher</td><td align='center'>Bank</td><td align='center'>Misc Fee</td><td align='center'>Desc</td><td align='center'>Send Full Info</td><td align='center'>Option</td></tr>";
	}

	function createComboSwitcher(&$opt, $sele = null, $filter = '')
	{
		$OK = false;
		$sQ = "SELECT CDC_B_ID, CDC_B_NAME FROM CDCCORE_BANK";
		if ($filter != "") {
			$sQ .= " WHERE CDC_B_ID NOT IN(" . $filter . ")";
		}
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			$opt = "<select name='bank'>";
			while ($rws = mysqli_fetch_array($res)) {
				$s = ($sele == $rws['CDC_B_ID']) ? "selected='selected'" : "";
				$opt .= "<option value='" . $rws['CDC_B_ID'] . "'" . $s . ">" . $rws['CDC_B_NAME'] . "</option>";
				$OK = true;
			}
			$opt .= "</select>";
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
		return $OK;
	}

	function createComboOverride(&$opt, $sele = null)
	{
		$OK = false;
		$opt = "<select name='ov'>";
		$s = ($sele == "1") ? "selected='selected'" : "";
		$opt .= "<option value='1'" . $s . ">Yes</option>";
		$s = ($sele == "0" || is_null($sele)) ? "selected='selected'" : "";
		$opt .= "<option value='0'" . $s . ">No</option>";
		$opt .= "</select>";

		return $OK;
	}

	function createBodySwitchingBank($idswitching)
	{
		$sQ = "SELECT CDC_S_ID, CDC_S_NAME FROM CDCCORE_SWITCHER WHERE CDC_S_ID='" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			while ($rws = mysqli_fetch_array($res)) {
				$swc = $rws['CDC_S_NAME'];
			}
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
		$prmAdd = "&status=addswitching&saveswitching=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$i = 0;
		$swFilter = "";
		if ($this->getDataSwitchingBank($rows, $idswitching)) {
			foreach ($rows as $row) {
				$prmEdt = "&status=editswitching&idsbank=" . $row['CDC_SB_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=deleteswitching&idsbank=" . $row['CDC_SB_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$prmSwt = "&status=switching&idsbank=" . $row['CDC_SB_ID'];
				$prmSwt = base64_decode($this->params) . $prmSwt;
				$prmSwt = base64_encode($prmSwt);
				$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_SB_ID'] . "</td><td align='center'>" . $row['CDC_S_NAME'];
				$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td><td align='center'>" . $row['CDC_SB_PID'] . "</td>";
				$this->body .= "</td><td align='center'>" . ($row['CDC_SB_MIN_PAYMENT_ISOVERRIDE'] == "1" ? "Yes" : "No") . "</td><td align='center'>" . $row['CDC_SB_MIN_PAYMENT_AMOUNT'] . "</td>";
				$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>								
					</td></tr>";
				$i++;

				if ($swFilter == "") {
					$swFilter = "'" . $row['CDC_SB_BID'] . "'";
				} else {
					$swFilter .= ",'" . $row['CDC_SB_BID'] . "'";
				}
			}
			$opti = '';
		}
		if ($this->createComboSwitcher($opt, null, $swFilter)) {
			$opti = $opt;
		}
		$opov = '';
		$this->createComboOverride($opov, null);
		if ($opti != "") {
			$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
			$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td align='center'>Otomatis</td><td align='center'>" . $swc;
			$this->body .= "</td><td align='center'>" . $opti . "</td><td align='center'><input type='text' name='pan' id='pan' size='5' maxlength='5'/></td>";
			$this->body .= "</td><td align='center'>" . $opov . "</td><td align='center'><input type='text' name='min' id='min' size='12' maxlength='12'/></td>";
			$this->body .= "<td align='center'><input type='submit' name='save' id='save' value='Simpan' />				
				</td></tr></form>";
		} else {
			$this->body .= "<tr><td  align='left' colspan='6'>Semua Bank Telah Terpasangkan dengan Switching Terpilih, silahkan tambahkan dahulu Bank Baru</td></tr>";
		}
	}

	function createBodySwitchingBankFee($idswitching)
	{
		$sQ = "SELECT CDC_S_ID, CDC_S_NAME FROM CDCCORE_SWITCHER WHERE CDC_S_ID='" . $idswitching . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			while ($rws = mysqli_fetch_array($res)) {
				$swc = $rws['CDC_S_NAME'];
			}
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
		$prmAdd = "&status=addfee&savefee=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$i = 0;
		$swFilter = "";
		if ($this->getDataSwitchingBankFee($rows, $idswitching)) {
			foreach ($rows as $row) {
				$prmEdt = "&status=editfee&idsbank=" . $row['CDC_BMF_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=deletefee&idsbank=" . $row['CDC_BMF_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$prmSwt = "&status=fee&idsbank=" . $row['CDC_BMF_ID'];
				$prmSwt = base64_decode($this->params) . $prmSwt;
				$prmSwt = base64_encode($prmSwt);
				$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_BMF_ID'] . "</td><td align='center'>" . $row['CDC_S_NAME'];
				$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td><td align='center'>" . $row['CDC_BMF_FEE'] . "</td>";
				$this->body .= "<td align='center'>" . $row['CDC_BMF_DESC'] . "</td><td align='center'>" . ($row['CDC_BMF_FULL_INFO'] == 1 ? "Yes" : "No") . "</td>";
				$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>								
					</td></tr>";
				$i++;
				if ($swFilter == "") {
					$swFilter = "'" . $row['CDC_BMF_BID'] . "'";
				} else {
					$swFilter .= ",'" . $row['CDC_BMF_BID'] . "'";
				}
			}
			$opti = '';
		}
		if ($this->createComboSwitcher($opt, null, $swFilter)) {
			$opti = $opt;
		}
		if ($opti != "") {
			$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
			$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td align='center'>Otomatis</td><td align='center'>" . $swc;
			$this->body .= "</td><td align='center'>" . $opti . "</td>";
			$this->body .= "<td align='center'><input type='text' name='fee' id='fee' size='12' maxlength='12' onkeypress='return numbersonly(this,event,0)'/></td>";
			$this->body .= "<td align='center'><input type='text' name='feedesc' id='feedesc' size='30' maxlength='255'/></td>";
			$this->body .= "<td align='center'><input type='checkbox' name='fullinfo' id='fullinfo' /></td>";
			$this->body .= "<td align='center'><input type='submit' name='save' id='save' value='Simpan' />				
				</td></tr></form>";
		} else {
			$this->body .= "<tr><td  align='left' colspan='8'>Semua Bank Telah Terpasangkan dengan Switching Terpilih, silahkan tambahkan dahulu Bank Baru</td></tr>";
		}
	}

	function createTableSwitchingBank($idswitching)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitchingBank();
		$this->createBodySwitchingBank($idswitching);
		$this->table = '<style type="text/css">
				<!--
				#tbl-bank {
					background-color: #aaa;
					border-style:solid;
					border-color:#000;
					border-width:0px;
					width:700;
					margin:auto;
				}
				.styleTitleForm {
					background-color:#993333;
				}
				
				-->
				</style>';
		$this->table .= "<div id='tbl-bank'><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
	}

	function createTableSwitchingBankFee($idswitching)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitchingBankFee();
		$this->createBodySwitchingBankFee($idswitching);
		$this->table = '<style type="text/css">
				<!--
				#tbl-bank {
					background-color: #aaa;
					border-style:solid;
					border-color:#000;
					border-width:0px;
					width:700;
					margin:auto;
				}
				.styleTitleForm {
					background-color:#993333;
				}
				
				-->
				</style>';
		$this->table .= "<div id='tbl-bank'><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
	}

	function createBodyEditSwitchingBank($idswitching, $idsbank)
	{
		if ($this->getDataSwitchingBank($rows, $idswitching)) {
			$i = 0;
			$swFilter = "";
			foreach ($rows as $row) {
				if ($idsbank != $row['CDC_SB_ID']) {
					if ($swFilter == "") {
						$swFilter = "'" . $row['CDC_SB_BID'] . "'";
					} else {
						$swFilter .= ",'" . $row['CDC_SB_BID'] . "'";
					}
				}
			}
			//var_dump($rows);
			foreach ($rows as $row) {
				$prmEdt = "&status=editswitching&idsbank=" . $row['CDC_SB_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=deleteswitching&idsbank=" . $row['CDC_SB_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$prmSwt = "&status=switching&idsbank=" . $row['CDC_SB_ID'];
				$prmSwt = base64_decode($this->params) . $prmSwt;
				$prmSwt = base64_encode($prmSwt);
				$prmAdd = "&status=editswitching&saveswitching=edit";
				$prmAdd = base64_decode($this->params) . $prmAdd;
				$prmAdd = base64_encode($prmAdd);

				if ($idsbank == $row['CDC_SB_ID']) {
					$opti = '';
					if ($this->createComboSwitcher($opt, $row['CDC_SB_BID'], $swFilter)) {
						$opti = $opt;
					}
					$opov = '';
					$this->createComboOverride($opov, $row['CDC_SB_MIN_PAYMENT_ISOVERRIDE']);

					$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
					$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_SB_ID'] . "</td><td align='center'>" . $row['CDC_S_NAME'];
					$this->body .= "</td><td align='center'>" . $opti . "</td><td align='center'><input type='text' name='pan' id='pan' size='5' maxlength='5' value='" . $row['CDC_SB_PID'] . "'/></td>";
					$this->body .= "</td><td align='center'>" . $opov . "</td><td align='center'><input type='text' name='min' id='min' size='12' maxlength='12' value='" . $row['CDC_SB_MIN_PAYMENT_AMOUNT'] . "'/></td>";
					$this->body .= "<td align='center'><input type='submit' name='save' id='save' value='Simpan' />				
						</td></tr></form>";
				} else {
					$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_SB_ID'] . "</td><td align='center'>" . $row['CDC_S_NAME'];
					$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td><td align='center'>" . $row['CDC_SB_PID'] . "</td>";
					$this->body .= "</td><td align='center'>" . ($row['CDC_SB_MIN_PAYMENT_ISOVERRIDE'] == "1" ? 'Yes' : 'No') . "</td><td align='center'>" . $row['CDC_SB_MIN_PAYMENT_AMOUNT'] . "</td>";
					$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>								
					</td></tr>";
				}
				$i++;
			}


		}
	}

	function createBodyEditSwitchingBankFee($idswitching, $idsbank)
	{
		if ($this->getDataSwitchingBankFee($rows, $idswitching)) {
			$i = 0;
			$swFilter = "";
			foreach ($rows as $row) {
				if ($idsbank != $row['CDC_BMF_ID']) {
					if ($swFilter == "") {
						$swFilter = "'" . $row['CDC_BMF_BID'] . "'";
					} else {
						$swFilter .= ",'" . $row['CDC_BMF_BID'] . "'";
					}
				}
			}
			foreach ($rows as $row) {
				$prmEdt = "&status=editfee&idsbank=" . $row['CDC_BMF_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=deletefee&idsbank=" . $row['CDC_BMF_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$prmSwt = "&status=fee&idsbank=" . $row['CDC_BMF_ID'];
				$prmSwt = base64_decode($this->params) . $prmSwt;
				$prmSwt = base64_encode($prmSwt);
				$prmAdd = "&status=editfee&savefee=edit";
				$prmAdd = base64_decode($this->params) . $prmAdd;
				$prmAdd = base64_encode($prmAdd);


				if ($idsbank == $row['CDC_BMF_ID']) {
					$opti = '';
					if ($this->createComboSwitcher($opt, $row['CDC_BMF_ID'], $swFilter)) {
						$opti = $opt;
					}
					$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
					$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_BMF_ID'] . "</td><td align='center'>" . $row['CDC_S_NAME'];
					$this->body .= "</td><td align='center'>" . $opti . "</td>";
					$this->body .= "<td align='center'><input type='text' name='fee' id='fee' size='12' maxlength='12' value='" . $row['CDC_BMF_FEE'] . "' onkeypress='return numbersonly(this,event,0)'/></td>";
					$this->body .= "<td align='center'><input type='text' name='feedesc' id='feedesc' size='30' maxlength='255' value='" . $row['CDC_BMF_DESC'] . "'/></td>";
					$this->body .= "<td align='center'><input type='checkbox' name='fullinfo' id='fullinfo' value='1' " . ($row['CDC_BMF_FULL_INFO'] == 1 ? "checked" : "") . "/></td>";
					$this->body .= "<td align='center'><input type='submit' name='save' id='save' value='Simpan' />				
						</td></tr></form>";
				} else {
					$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_BMF_ID'] . "</td><td align='center'>" . $row['CDC_S_NAME'];
					$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td><td align='center'>" . $row['CDC_BMF_FEE'] . "</td>";
					$this->body .= "<td align='center'>" . $row['CDC_BMF_DESC'] . "</td><td align='center'>" . ($row['CDC_BMF_FULL_INFO'] == 1 ? "Yes" : "No") . "</td>";
					$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>								
					</td></tr>";
				}
				$i++;
			}


		}
	}

	function createTableEditSwitchingBank($idbank, $idsbank)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitchingBank();
		$this->createBodyEditSwitchingBank($idbank, $idsbank);
		$this->table = '<style type="text/css">
				<!--
				#tbl-bank {
					background-color: #aaa;
					border-style:solid;
					border-color:#000;
					border-width:0px;
					width:700;
					margin:auto;
				}
				.styleTitleForm {
					background-color:#993333;
				}
				
				-->
				</style>';
		$this->table .= "<div id='tbl-bank'><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
	}

	function createTableEditSwitchingBankFee($idbank, $idsbank)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitchingBankFee();
		$this->createBodyEditSwitchingBankFee($idbank, $idsbank);
		$this->table = '<style type="text/css">
				<!--
				#tbl-bank {
					background-color: #aaa;
					border-style:solid;
					border-color:#000;
					border-width:0px;
					width:700;
					margin:auto;
				}
				.styleTitleForm {
					background-color:#993333;
				}
				
				-->
				</style>';
		$this->table .= "<div id='tbl-bank'><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
	}
	public function displaySwitchingBank($idswitching)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableSwitchingBank($idswitching);
		echo $this->table;
	}

	public function displaySwitchingBankFee($idswitching)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableSwitchingBankFee($idswitching);
		echo $this->table;
	}

	public function displayEditSwitchingBank($idbank, $idsbank)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableEditSwitchingBank($idbank, $idsbank);
		echo $this->table;
	}

	public function displayEditSwitchingBankFee($idbank, $idsbank)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableEditSwitchingBankFee($idbank, $idsbank);
		echo $this->table;
	}

	public function saveSwitchingBank($idbank, $idswitching, $pan, $ov, $min)
	{
		$OK = false;

		$sQ = "INSERT INTO CDCCORE_SWITCHER_BANK (CDC_SB_ID, CDC_SB_SID, CDC_SB_BID, CDC_SB_PID,CDC_SB_MIN_PAYMENT_ISOVERRIDE,CDC_SB_MIN_PAYMENT_AMOUNT) ";
		$sQ .= " VALUES (UUID(),'" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "','" . mysqli_real_escape_string($this->DBLINK, $idbank) . "','" . mysqli_real_escape_string($this->DBLINK, $pan) . "','" . mysqli_real_escape_string($this->DBLINK, isset($ov) ? $ov : '0') . "','" . mysqli_real_escape_string($this->DBLINK, $min) . "') ";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingBank($idswitching);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}

	public function saveSwitchingBankFee($idbank, $idswitching, $fee, $desc, $fullinfo)
	{
		$OK = false;

		$sQ = "INSERT INTO CDCMOD_MF_AUTO_BANK_MISC_FEE (CDC_BMF_ID, CDC_BMF_SID, CDC_BMF_BID, CDC_BMF_FEE, CDC_BMF_DESC, CDC_BMF_FULL_INFO, CDC_BMF_CREATED) ";
		$sQ .= " VALUES (UUID(),'" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "','" . mysqli_real_escape_string($this->DBLINK, $idbank) . "'," . floatval($fee) . ",'" . mysqli_real_escape_string($this->DBLINK, $desc) . "'," . intval($fullinfo) . ",NOW()) ";
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingBankFee($idswitching);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}


	public function saveEditSwitchingBank($idsbank, $bank, $pan, $idswitching, $ov, $min)
	{
		$OK = false;
		$sQ = "UPDATE CDCCORE_SWITCHER_BANK SET CDC_SB_BID = '" . mysqli_real_escape_string($this->DBLINK, $bank) . "', CDC_SB_PID ='" . mysqli_real_escape_string($this->DBLINK, $pan) . "', CDC_SB_MIN_PAYMENT_ISOVERRIDE ='" . mysqli_real_escape_string($this->DBLINK, isset($ov) ? $ov : '0') . "', CDC_SB_MIN_PAYMENT_AMOUNT ='" . mysqli_real_escape_string($this->DBLINK, $min) . "' WHERE CDC_SB_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingBank($idswitching);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}
	public function saveEditSwitchingBankFee($idsbank, $bank, $fee, $desc, $fullinfo, $idswitching)
	{
		$OK = false;
		$sQ = "UPDATE CDCMOD_MF_AUTO_BANK_MISC_FEE SET CDC_BMF_BID = '" . mysqli_real_escape_string($this->DBLINK, $bank) . "', CDC_BMF_FEE =" . floatval($fee) . ",CDC_BMF_DESC='" . mysqli_real_escape_string($this->DBLINK, $desc) . "',CDC_BMF_FULL_INFO=" . intval($fullinfo) . " WHERE CDC_BMF_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingBankFee($idswitching);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}


	public function deleteSwitchingBank($idsbank, $idswitching)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCCORE_SWITCHER_BANK WHERE CDC_SB_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingBank($idswitching);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}

	public function deleteSwitchingBankFee($idsbank, $idswitching)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCMOD_MF_AUTO_BANK_MISC_FEE WHERE CDC_BMF_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingBankFee($idswitching);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}
}
echo '<script language="javascript">
	function ConfirmDelete(url){if(confirm("Yakin Mau Melakukan Penghapusan Data?")){window.location.href=url;}}
	function numbersonly(myfield, e, dec)
{
	var key;
	var keychar;
	
	if (window.event)
	   key = window.event.keyCode;
	else if (e)
	   key = e.which;
	else
	   return true;
	keychar = String.fromCharCode(key);
	
	// control keys
	if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
	   return true;
	
	// numbers
	else if ((("0123456789").indexOf(keychar) > -1))
	   return true;
	
	// decimal point jump
	else if (dec && (keychar == "."))
	   {
	   myfield.form.elements[dec].focus();
	   return false;
	   }
	else
	   return false;
}

</script>';

//var_dump( $GLOBALS);	
$status = isset($status) ? $status : '';
$saveswitching = @isset($saveswitching) ? $saveswitching : '';
$deleteswitching = @isset($deleteswitching) ? $deleteswitching : '';
$savefee = @isset($savefee) ? $savefee : '';
$deletefee = @isset($deletefee) ? $deletefee : '';
$save = isset($_REQUEST['save']) ? $_REQUEST['save'] : false;
$sb = isset($submit) ? $submit : '';

$sid = @isset($_REQUEST['switching_id']) ? $_REQUEST['switching_id'] : '';
$sname = @isset($_REQUEST['switching_name']) ? $_REQUEST['switching_name'] : '';
$saddress = @isset($_REQUEST['switching_address']) ? $_REQUEST['switching_address'] : '';
$sphone = @isset($_REQUEST['switching_phone']) ? $_REQUEST['switching_phone'] : '';
$spic_name = @isset($_REQUEST['switching_pic_name']) ? $_REQUEST['switching_pic_name'] : '';
$spic_phone = @isset($_REQUEST['switching_pic_phone']) ? $_REQUEST['switching_pic_phone'] : '';
$stype = @isset($_REQUEST['switching_type']) ? $_REQUEST['switching_type'] : '';
$sregistered = @isset($_REQUEST['switching_registered']) ? $_REQUEST['switching_registered'] : '';
$scode = @isset($_REQUEST['switching_code']) ? $_REQUEST['switching_code'] : '';
$btgl = @isset($_REQUEST['str_tgl']) ? $_REQUEST['str_tgl'] : '';
$bmon = @isset($_REQUEST['str_mon']) ? $_REQUEST['str_mon'] : '';
$byr = @isset($_REQUEST['str_yr']) ? $_REQUEST['str_yr'] : '';

$bafSwitching = new classSwitching();
$bafSwitching->DBLINK = $areaDbLink;
$bafSwitching->params = $param;

if ($status == 'add') {
	$bafSwitching->displayForm($status);
}

if ($status == 'edit') {
	$bafSwitching->displayForm($status, $idSwitching);
}
if ($status == 'delete') {
	if ($bafSwitching->deleteSwitching($idSwitching)) {
		$bafSwitching->displaySwitching();
	}
	;
}

if ($save == 'add') {
	if ($sb == 'Submit') {
		$sregistered = $byr . "-" . $bmon . "-" . $btgl . " 00:00:00";
		$bafSwitching->saveSwitching($sid, $sname, $saddress, $sphone, $spic_name, $spic_phone, $stype, $sregistered, $scode);
	}
}
if ($save == 'edit') {
	if ($sb == 'Submit') {
		$sregistered = $byr . "-" . $bmon . "-" . $btgl . " 00:00:00";
		$bafSwitching->saveEditSwitching($sid, $sname, $saddress, $sphone, $spic_name, $spic_phone, $stype, $sregistered, $scode);
	}
}

if ($status == '') {
	$bafSwitching->displaySwitching();
}
if ($status == 'switching') {
	$bafSwitching->displaySwitchingBank($idSwitching);
}
if ($status == 'fee') {
	$bafSwitching->displaySwitchingBankFee($idSwitching);
}

if ($saveswitching == 'edit') {
	$prms = base64_decode($bafSwitching->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafSwitching->params = base64_encode($prms);
	$bafSwitching->saveEditSwitchingBank($idsbank, $bank, $pan, $idSwitching, $ov, $min);
}
if ($savefee == 'edit') {
	$prms = base64_decode($bafSwitching->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafSwitching->params = base64_encode($prms);
	$bafSwitching->saveEditSwitchingBankFee($idsbank, $bank, $fee, $feedesc, $fullinfo, $idSwitching);
}
if ($status == 'deleteswitching') {
	$prms = base64_decode($bafSwitching->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafSwitching->params = base64_encode($prms);
	$bafSwitching->deleteSwitchingBank($idsbank, $idSwitching);
}
if ($status == 'deletefee') {
	$prms = base64_decode($bafSwitching->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafSwitching->params = base64_encode($prms);
	$bafSwitching->deleteSwitchingBankFee($idsbank, $idSwitching);
}
if ($status == 'addswitching') {
	$prms = base64_decode($bafSwitching->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafSwitching->params = base64_encode($prms);
	$bafSwitching->saveSwitchingBank($bank, $idSwitching, $pan, $ov, $min);
}
if ($status == 'addfee') {
	$prms = base64_decode($bafSwitching->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafSwitching->params = base64_encode($prms);
	$bafSwitching->saveSwitchingBankFee($bank, $idSwitching, $fee, $feedesc, $fullinfo);
}

if (($status == 'editswitching') && ($saveswitching != 'edit')) {
	$bafSwitching->displayEditSwitchingBank($idSwitching, $idsbank);
}
if (($status == 'editfee') && ($savefee != 'edit')) {
	$bafSwitching->displayEditSwitchingBankFee($idSwitching, $idsbank);
}
?>