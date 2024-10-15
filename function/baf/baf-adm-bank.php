<?php
require_once("inc/payment/ctools.php");
require_once("inc/baf/inc-baf-config.php");

class classBank
{
	public $DBLINK;
	private $header;
	private $body;
	private $table;
	public $params;

	function getDataBank(&$rows)
	{
		$OK = false;
		$sQ = "SELECT CDC_B_ID, CDC_B_NAME, CDC_B_REGISTERED, CDC_B_CUSTOM FROM CDCCORE_BANK ";


		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDC_B_ID'] = $rws['CDC_B_ID'];
				$rows[$i]['CDC_B_NAME'] = $rws['CDC_B_NAME'];
				$rows[$i]['CDC_B_REGISTERED'] = $rws['CDC_B_REGISTERED'];
				$rows[$i]['CDC_B_CUSTOM'] = $rws['CDC_B_CUSTOM'];
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

	function createHeader()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>ID</td><td align='center'>Name</td><td align='center'>Registered</td><td align='center'>Custom</td><td align='center'>Option</td></tr>";
	}

	function createBody()
	{
		if ($this->getDataBank($rows)) {
			$i = 0;
			foreach ($rows as $row) {
				$prmEdt = "&status=edit&idbank=" . $row['CDC_B_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=delete&idbank=" . $row['CDC_B_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$prmSwt = "&status=switching&idbank=" . $row['CDC_B_ID'];
				$prmSwt = base64_decode($this->params) . $prmSwt;
				$prmSwt = base64_encode($prmSwt);
				$prmFee = "&status=fee&idbank=" . $row['CDC_B_ID'];
				$prmFee = base64_decode($this->params) . $prmFee;
				$prmFee = base64_encode($prmFee);
				$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_B_ID'] . "</td><td>" . $row['CDC_B_NAME'];
				$this->body .= "</td><td align='center'>" . $row['CDC_B_REGISTERED'] . "</td><td align='center'>" . $row['CDC_B_CUSTOM'] . "</td>";
				$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>
									&nbsp;
									<a href='main.php?param=" . $prmSwt . "'><img border='0' src='image/icon/mtce.png' alt='Switching' title='Switching'></img></a>
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
				a {
					text-decoration:none;
					color : #ffa500;
				}
				-->
				</style>';
		$this->table .= "<div id='tbl-bank'><table cellpadding = '4' cellspacing='1' border='0' width='100%'><tr><td class='styleTitleForm'><a href='main.php?param=" . $prmAdd . "' onClick=''><img border='0' src='image/icon/add.png' alt='Tambah Bank' title='Tambah Bank'></img> Tambah Bank</a></td></tr></table><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
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
		$yr2 = date('Y');
		$d2 = date('j');
		$m2 = date('n');
		$bank_id = '';
		$bank_name = '';
		$bank_custom = '';
		$bank_registerd = '';

		if ($id) {
			$sQ = "SELECT * FROM CDCCORE_BANK";
			$sQ .= " WHERE CDC_B_ID ='" . $id . "'";
			if ($res = mysqli_query($this->DBLINK, $sQ)) {
				while ($rws = mysqli_fetch_array($res)) {
					$bank_id = $rws['CDC_B_ID'];
					$bank_name = $rws['CDC_B_NAME'];
					$bank_registered = $rws['CDC_B_REGISTERED'];
					$bank_custom = $rws['CDC_B_CUSTOM'];
				}
			} else {
				$sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
			}
			$tmp = explode(" ", $bank_registered);
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
					width:500;
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
			  <td><input name="bank_id" type="text" size="7" maxlength="7" value="' . $bank_id . '"></td>
			</tr>
			<tr>
			  <td>Name</td>
			  <td>:</td>
			  <td><input name="bank_name" type="text" size="60" maxlength="100"  value="' . $bank_name . '"></td>
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
			  <td>Custom</td>
			  <td>:</td>
			  <td><input name="bank_custom" type="text" size="3" maxlength="3"  value="' . $bank_custom . '"></td></td>
			</tr>
			<tr>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td><label>
				<input type="submit" name="submit" id="submit" value="Submit" />
			  </label><input name="idBank" type="hidden" value="' . $id . '" /></td>
			</tr>
		  </table>
		</form></div></td></tr></table>';
		echo $form;
	}

	public function saveBank($id, $name, $registered, $custom)
	{
		$OK = false;
		$rand_id = "";
		srand((double) microtime() * 1000000);
		$rand_id = md5(uniqid(rand()));

		$txtname = nullable_htmlspecialchar($name, ENT_QUOTES);

		$sQ = "INSERT INTO CDCCORE_BANK (CDC_B_ID, CDC_B_NAME, CDC_B_REGISTERED, CDC_B_CUSTOM) ";
		$sQ .= " VALUES ('" . $id . "','" . mysqli_real_escape_string($this->DBLINK, $txtname) . "','" . mysqli_real_escape_string($this->DBLINK, $registered) . "','" . mysqli_real_escape_string($this->DBLINK, $custom) . "') ";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}

	public function saveEditBank($id, $name, $registered, $custom)
	{
		$OK = false;

		$txtname = nullable_htmlspecialchar($name, ENT_QUOTES);

		$sQ = "UPDATE CDCCORE_BANK SET CDC_B_NAME ='" . mysqli_real_escape_string($this->DBLINK, $txtname) . "', CDC_B_REGISTERED='" . mysqli_real_escape_string($this->DBLINK, $registered) . "',";
		$sQ .= " CDC_B_CUSTOM = '" . mysqli_real_escape_string($this->DBLINK, $custom) . "' WHERE CDC_B_ID='" . $id . "'";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}

	public function deleteBank($id)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCCORE_BANK WHERE CDC_B_ID ='" . $id . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}

	public function displayBank()
	{
		$this->createTable();
		echo $this->table;
	}

	function getDataSwitching(&$rows, $idbank)
	{
		$OK = false;
		$sQ = "SELECT A.CDC_SB_ID, A.CDC_SB_SID, A.CDC_SB_BID, A.CDC_SB_PID, B.CDC_S_NAME, C.CDC_B_NAME,A.CDC_SB_MIN_PAYMENT_ISOVERRIDE,A.CDC_SB_MIN_PAYMENT_AMOUNT  FROM CDCCORE_SWITCHER_BANK A LEFT JOIN CDCCORE_SWITCHER B ON A.CDC_SB_SID = B.CDC_S_ID
		       LEFT JOIN CDCCORE_BANK C ON A.CDC_SB_BID = C.CDC_B_ID  WHERE A.CDC_SB_BID = '$idbank' ";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDC_SB_ID'] = $rws['CDC_SB_ID'];
				$rows[$i]['CDC_SB_SID'] = $rws['CDC_SB_SID'];
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

	function getDataSwitchingFee(&$rows, $idbank)
	{
		$OK = false;
		$sQ = "SELECT A.CDC_BMF_ID,A.CDC_BMF_SID, A.CDC_BMF_BID, A.CDC_BMF_FEE, A.CDC_BMF_DESC, A.CDC_BMF_FULL_INFO, B.CDC_S_NAME, C.CDC_B_NAME FROM CDCMOD_MF_AUTO_BANK_MISC_FEE A LEFT JOIN CDCCORE_SWITCHER B ON A.CDC_BMF_SID = B.CDC_S_ID
		       LEFT JOIN CDCCORE_BANK C ON A.CDC_BMF_BID = C.CDC_B_ID  WHERE   A.CDC_BMF_BID = '$idbank' ";
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

	function createHeadersSwitching()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>ID</td><td align='center'>Switcher</td><td align='center'>Bank</td><td align='center'>PAN</td><td align='center'>Override Minimum Payment</td><td align='center'>Minimum Payment Amount</td><td align='center'>Option</td></tr>";
	}

	function createHeadersSwitchingFee()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>ID</td><td align='center'>Switcher</td><td align='center'>Bank</td><td align='center'>Misc Fee</td><td align='center'>Desc</td><td align='center'>Send Full Info</td><td align='center'>Option</td></tr>";
	}

	function createComboSwitcher(&$opt, $sele = null, $filter = "")
	{
		$OK = false;
		$sQ = "SELECT CDC_S_ID, CDC_S_NAME FROM CDCCORE_SWITCHER";
		if ($filter != "") {
			$sQ .= " WHERE CDC_S_ID NOT IN(" . $filter . ")";
		}
		//echo  $sele;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			$opt = "<select name='switcher'>";
			while ($rws = mysqli_fetch_array($res)) {
				$s = ($sele == $rws['CDC_S_ID']) ? "selected='selected'" : "";
				$opt .= "<option value='" . $rws['CDC_S_ID'] . "'" . $s . ">" . $rws['CDC_S_NAME'] . "</option>";
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

	function createBodySwitching($idbank)
	{
		$sQ = "SELECT CDC_B_ID, CDC_B_NAME FROM CDCCORE_BANK WHERE CDC_B_ID='" . $idbank . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			while ($rws = mysqli_fetch_array($res)) {
				$bnk = $rws['CDC_B_NAME'];
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
		if ($this->getDataSwitching($rows, $idbank)) {
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
					$swFilter = "'" . $row['CDC_SB_SID'] . "'";
				} else {
					$swFilter .= ",'" . $row['CDC_SB_SID'] . "'";
				}
			}
		}
		$opti = '';
		if ($this->createComboSwitcher($opt, null, $swFilter)) {
			$opti = $opt;
		}
		$opov = '';
		$this->createComboOverride($opov, null);

		if ($opti != "") {
			$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
			$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td align='center'>Otomatis</td><td align='center'>" . $opti;
			$this->body .= "</td><td align='center'>" . $bnk . "</td><td align='center'><input type='text' name='pan' id='pan' size='5' maxlength='5'/></td>";
			$this->body .= "</td><td align='center'>" . $opov . "</td><td align='center'><input type='text' name='min' id='min' size='12' maxlength='12'/></td>";
			$this->body .= "<td align='center'><input type='submit' name='save' id='save' value='Simpan' />				
				</td></tr></form>";
		} else {
			$this->body .= "<tr><td  align='left' colspan='6'>Semua Switching Telah Terpasangkan dengan Bank Terpilih, silahkan tambahkan dahulu Switching Baru</td></tr>";
		}
	}
	function createBodySwitchingFee($idbank)
	{
		$sQ = "SELECT CDC_B_ID, CDC_B_NAME FROM CDCCORE_BANK WHERE CDC_B_ID='" . $idbank . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			while ($rws = mysqli_fetch_array($res)) {
				$bnk = $rws['CDC_B_NAME'];
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
		if ($this->getDataSwitchingFee($rows, $idbank)) {
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
				$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td><td align='center'>" . $row['CDC_BMF_FEE'] . "</td><td align='center'>" . $row['CDC_BMF_DESC'] . "</td><td align='center'>" . ($row['CDC_BMF_FULL_INFO'] == 1 ? "Yes" : "No") . "</td>";
				$this->body .= "<td align='center'>&nbsp;
									<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
									&nbsp;
									<a href='#'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete' onclick='ConfirmDelete(\"main.php?param=" . $prmDel . "\")'></img></a>								
					</td></tr>";
				$i++;
				if ($swFilter == "") {
					$swFilter = "'" . $row['CDC_BMF_SID'] . "'";
				} else {
					$swFilter .= ",'" . $row['CDC_BMF_SID'] . "'";
				}
			}
		}
		$opti = '';
		if ($this->createComboSwitcher($opt, null, $swFilter)) {
			$opti = $opt;
		}
		if ($opti != "") {
			$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
			$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td align='center'>Otomatis</td><td align='center'>" . $opti;
			$this->body .= "</td><td align='center'>" . $bnk . "</td>";
			$this->body .= "<td align='center'><input type='text' name='fee' id='fee' size='12' maxlength='12' onkeypress='return numbersonly(this,event,0)'/></td>";
			$this->body .= "<td align='center'><input type='text' name='feedesc' id='feedesc' size='30' maxlength='255'/></td>";
			$this->body .= "<td align='center'><input type='checkbox' name='fullinfo' id='fullinfo' /></td>";
			$this->body .= "<td align='center'><input type='submit' name='save' id='save' value='Simpan' />				
				</td></tr></form>";
		} else {
			$this->body .= "<tr><td  align='left' colspan='8'>Semua Switching Telah Terpasangkan dengan Bank Terpilih, silahkan tambahkan dahulu Switching Baru</td></tr>";
		}
	}
	function createTableSwitching($idbank)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitching();
		$this->createBodySwitching($idbank);
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

	function createTableSwitchingFee($idbank)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitchingFee();
		$this->createBodySwitchingFee($idbank);
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

	function createBodyEditSwitching($idbank, $idsbank)
	{
		if ($this->getDataSwitching($rows, $idbank)) {
			$i = 0;
			$swFilter = "";
			foreach ($rows as $row) {
				if ($idsbank != $row['CDC_SB_ID']) {
					if ($swFilter == "") {
						$swFilter = "'" . $row['CDC_SB_SID'] . "'";
					} else {
						$swFilter .= ",'" . $row['CDC_SB_SID'] . "'";
					}
				}
			}
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
				$prmAdd = "&status=switching&saveswitching=edit";
				$prmAdd = base64_decode($this->params) . $prmAdd;
				$prmAdd = base64_encode($prmAdd);
				if ($idsbank == $row['CDC_SB_ID']) {
					$opti = '';
					if ($this->createComboSwitcher($opt, $row['CDC_SB_SID'], $swFilter)) {
						$opti = $opt;
					}
					$opov = '';
					$this->createComboOverride($opov, $row['CDC_SB_MIN_PAYMENT_ISOVERRIDE']);

					$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
					$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_SB_ID'] . "</td><td align='center'>" . $opti;
					$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td><td align='center'><input type='text' name='pan' id='pan' size='5' maxlength='5' value='" . $row['CDC_SB_PID'] . "'/></td>";
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

	function createBodyEditSwitchingFee($idbank, $idsbank)
	{
		if ($this->getDataSwitchingFee($rows, $idbank)) {
			$i = 0;
			$swFilter = "";
			foreach ($rows as $row) {
				if ($idsbank != $row['CDC_BMF_ID']) {
					if ($swFilter == "") {
						$swFilter = "'" . $row['CDC_BMF_SID'] . "'";
					} else {
						$swFilter .= ",'" . $row['CDC_BMF_SID'] . "'";
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
				$prmAdd = "&status=fee&savefee=edit";
				$prmAdd = base64_decode($this->params) . $prmAdd;
				$prmAdd = base64_encode($prmAdd);


				if ($idsbank == $row['CDC_BMF_ID']) {
					$opti = '';
					if ($this->createComboSwitcher($opt, $row['CDC_BMF_SID'], $swFilter)) {
						$opti = $opt;
					}
					$this->body .= "<form id='form1' name='form1' method='post' action='main.php?param=" . $prmAdd . "'>";
					$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDC_BMF_ID'] . "</td><td align='center'>" . $opti;
					$this->body .= "</td><td align='center'>" . $row['CDC_B_NAME'] . "</td";
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

	function createTableEditSwitching($idbank, $idsbank)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitching();
		$this->createBodyEditSwitching($idbank, $idsbank);
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

	function createTableEditSwitchingFee($idbank, $idsbank)
	{
		$prmAdd = "&status=add";
		$prmAdd = base64_decode($this->params) . $prmAdd;
		$prmAdd = base64_encode($prmAdd);
		$this->createHeadersSwitchingFee();
		$this->createBodyEditSwitchingFee($idbank, $idsbank);
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
	public function displaySwitching($idbank)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableSwitching($idbank);
		echo $this->table;
	}

	public function displaySwitchingFee($idbank)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableSwitchingFee($idbank);
		echo $this->table;
	}

	public function displayEditSwitching($idbank, $idsbank)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableEditSwitching($idbank, $idsbank);
		echo $this->table;
	}

	public function displayEditSwitchingFee($idbank, $idsbank)
	{
		$prms = base64_decode($this->params);
		$pr = explode("&", $prms);
		array_pop($pr);
		array_pop($pr);
		echo '<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a>';
		$this->createTableEditSwitchingFee($idbank, $idsbank);
		echo $this->table;
	}

	public function saveSwitching($idbank, $idswitching, $pan, $ov, $min)
	{
		$OK = false;

		$sQ = "INSERT INTO CDCCORE_SWITCHER_BANK (CDC_SB_ID, CDC_SB_SID, CDC_SB_BID, CDC_SB_PID,CDC_SB_MIN_PAYMENT_ISOVERRIDE,CDC_SB_MIN_PAYMENT_AMOUNT) ";
		$sQ .= " VALUES (UUID(),'" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "','" . mysqli_real_escape_string($this->DBLINK, $idbank) . "','" . mysqli_real_escape_string($this->DBLINK, $pan) . "','" . mysqli_real_escape_string($this->DBLINK, isset($ov) ? $ov : '0') . "','" . mysqli_real_escape_string($this->DBLINK, floatval($min)) . "') ";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitching($idbank);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}

	public function saveSwitchingFee($idbank, $idswitching, $fee, $desc, $fullinfo)
	{
		$OK = false;

		$sQ = "INSERT INTO CDCMOD_MF_AUTO_BANK_MISC_FEE (CDC_BMF_ID, CDC_BMF_SID, CDC_BMF_BID, CDC_BMF_FEE, CDC_BMF_DESC, CDC_BMF_FULL_INFO, CDC_BMF_CREATED) ";
		$sQ .= " VALUES (UUID(),'" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "','" . mysqli_real_escape_string($this->DBLINK, $idbank) . "'," . floatval($fee) . ",'" . mysqli_real_escape_string($this->DBLINK, $desc) . "'," . intval($fullinfo) . ",NOW()) ";
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingFee($idbank);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}

		return $OK;
	}
	public function saveEditSwitching($idbank, $idsbank, $idswitching, $pan, $ov, $min)
	{
		$OK = false;
		$sQ = "UPDATE CDCCORE_SWITCHER_BANK SET CDC_SB_SID = '" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "', CDC_SB_PID ='" . mysqli_real_escape_string($this->DBLINK, $pan) . "', CDC_SB_MIN_PAYMENT_ISOVERRIDE ='" . mysqli_real_escape_string($this->DBLINK, isset($ov) ? $ov : '0') . "', CDC_SB_MIN_PAYMENT_AMOUNT ='" . mysqli_real_escape_string($this->DBLINK, floatval($min)) . "' WHERE CDC_SB_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			//$this -> displaySwitching($idbank);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}
	public function saveEditSwitchingFee($idbank, $idsbank, $idswitching, $fee, $desc, $fullinfo)
	{
		$OK = false;
		$sQ = "UPDATE CDCMOD_MF_AUTO_BANK_MISC_FEE SET CDC_BMF_SID = '" . mysqli_real_escape_string($this->DBLINK, $idswitching) . "', CDC_BMF_FEE =" . floatval($fee) . ",CDC_BMF_DESC='" . mysqli_real_escape_string($this->DBLINK, $desc) . "',CDC_BMF_FULL_INFO=" . intval($fullinfo) . " WHERE CDC_BMF_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		//echo $sQ;
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			//$this -> displaySwitching($idbank);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}
	public function deleteSwitching($idbank, $idsbank)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCCORE_SWITCHER_BANK WHERE CDC_SB_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitching($idbank);
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
		}
	}
	public function deleteSwitchingFee($idbank, $idsbank)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCMOD_MF_AUTO_BANK_MISC_FEE WHERE CDC_BMF_ID='" . mysqli_real_escape_string($this->DBLINK, $idsbank) . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
			$this->displaySwitchingFee($idbank);
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

$status = @isset($status) ? $status : '';
$saveswitching = @isset($saveswitching) ? $saveswitching : '';
$deleteswitching = @isset($deleteswitching) ? $deleteswitching : '';
$savefee = @isset($savefee) ? $savefee : '';
$deletefee = @isset($deletefee) ? $deletefee : '';
$save = @isset($_REQUEST['save']) ? $_REQUEST['save'] : false;
$sb = @isset($submit) ? $submit : '';

$bid = @isset($_REQUEST['bank_id']) ? $_REQUEST['bank_id'] : '';
$bname = @isset($_REQUEST['bank_name']) ? $_REQUEST['bank_name'] : '';
$bcustom = @isset($_REQUEST['bank_custom']) ? $_REQUEST['bank_custom'] : '';
$btgl = @isset($_REQUEST['str_tgl']) ? $_REQUEST['str_tgl'] : '';
$bmon = @isset($_REQUEST['str_mon']) ? $_REQUEST['str_mon'] : '';
$byr = @isset($_REQUEST['str_yr']) ? $_REQUEST['str_yr'] : '';
$switcher = @isset($_REQUEST['switcher']) ? $_REQUEST['switcher'] : '';
$pan = @isset($_REQUEST['pan']) ? $_REQUEST['pan'] : '';

$bafbank = new classBank();
$bafbank->DBLINK = $areaDbLink;
$bafbank->params = $param;

if ($status == 'add') {
	$bafbank->displayForm($status);
}

if ($status == 'edit') {
	$bafbank->displayForm($status, $idbank);
}
if ($status == 'delete') {
	if ($bafbank->deleteBank($idbank)) {
		$bafbank->displayBank();
	}
	;
}

if ($save == 'add') {
	if ($sb == 'Submit') {
		$registered = $byr . "-" . $bmon . "-" . $btgl . " 00:00:00";
		$bafbank->saveBank($bid, $bname, $registered, $bcustom);
	}
}
if ($save == 'edit') {
	if ($sb == 'Submit') {
		$registered = $byr . "-" . $bmon . "-" . $btgl . " 00:00:00";
		$bafbank->saveEditBank($bid, $bname, $registered, $bcustom);
	}
}
if ($saveswitching == 'edit') {
	$prms = base64_decode($bafbank->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafbank->params = base64_encode($prms);
	$bafbank->saveEditSwitching($idbank, $idsbank, $switcher, $pan, $ov, $min);
}
//echo "savefee=$savefee";
if ($savefee == 'edit') {
	$prms = base64_decode($bafbank->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafbank->params = base64_encode($prms);
	$bafbank->saveEditSwitchingFee($idbank, $idsbank, $switcher, $fee, $feedesc, $fullinfo);
}
if ($status == 'deleteswitching') {
	$prms = base64_decode($bafbank->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafbank->params = base64_encode($prms);
	$bafbank->deleteSwitching($idbank, $idsbank);
}
if ($status == 'deletefee') {
	$prms = base64_decode($bafbank->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafbank->params = base64_encode($prms);
	$bafbank->deleteSwitchingFee($idbank, $idsbank);
}

if ($status == '') {
	$bafbank->displayBank();
}

if ($status == 'switching') {
	$bafbank->displaySwitching($idbank);
}

if ($status == 'fee') {
	$bafbank->displaySwitchingFee($idbank);
}
//$prmAdd = "&status=addswitching&idsbank=".$row['CDC_SB_ID']."&saveswitching=add";
if ($status == 'addswitching') {
	$prms = base64_decode($bafbank->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafbank->params = base64_encode($prms);
	$bafbank->saveSwitching($idbank, $switcher, $pan, $ov, $min);
}
if ($status == 'addfee') {
	$prms = base64_decode($bafbank->params);
	$pr = explode("&", $prms);
	array_pop($pr);
	array_pop($pr);
	$prms = implode("&", $pr);
	$bafbank->params = base64_encode($prms);
	$fullinfo = (isset($fullinfo) ? $fullinfo : '');
	$bafbank->saveSwitchingFee($idbank, $switcher, $fee, $feedesc, $fullinfo);
}

if ($status == 'editswitching') {
	$bafbank->displayEditSwitching($idbank, $idsbank);
}
if ($status == 'editfee') {
	$bafbank->displayEditSwitchingFee($idbank, $idsbank);
}

?>