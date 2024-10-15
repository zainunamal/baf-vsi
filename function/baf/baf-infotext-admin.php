<?php
require_once("inc/payment/ctools.php");
require_once("inc/baf/inc-baf-config.php");

class classInfotext
{
	public $DBLINK;
	public $TBL_INFOTEXT;
	private $header;
	private $body;
	private $table;
	public $params;

	function getDataInfotexts(&$rows)
	{
		$OK = false;
		$sQ = "SELECT A.CDM_T_ID, A.CDM_T_TEXT, A.CDM_T_START, A.CDM_T_END, A.CDM_T_BILLER_ID FROM " . $this->TBL_INFOTEXT . " A";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDM_T_ID'] = $rws['CDM_T_ID'];
				$rows[$i]['CDM_T_TEXT'] = $rws['CDM_T_TEXT'];
				$rows[$i]['CDM_T_START'] = $rws['CDM_T_START'];
				$rows[$i]['CDM_T_END'] = $rws['CDM_T_END'];
				$rows[$i]['CDM_T_BILLER_ID'] = $rws['CDM_T_BILLER_ID'];
				$i++;
				$OK = true;
			}
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
			;
		}
		return $OK;
	}

	function createHeader()
	{
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>Info Text</td><td align='center'>Tanggal Mulai</td><td align='center'>Tanggal Akhir</td><td align='center'>Biller ID</td><td align='center'>Option</td></tr>";
	}

	function createBody()
	{
		if ($this->getDataInfotexts($rows)) {
			$i = 0;
			foreach ($rows as $row) {
				$prmEdt = "&status=edit&idtxtinf=" . $row['CDM_T_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=delete&idtxtinf=" . $row['CDM_T_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDM_T_TEXT'];
				$this->body .= "</td><td align='center'>" . $row['CDM_T_START'] . "</td><td align='center'>" . $row['CDM_T_END'] .
					"</td><td align='center'>" . $row['CDM_T_BILLER_ID'] . "</td>";
				$this->body .= "<td align='center'>&nbsp;
										<a href='main.php?param=" . $prmEdt . "'><img border='0' src='image/icon/mgmt.gif' alt='Edit' title='Edit'></img></a>
										&nbsp;
										<a href='main.php?param=" . $prmDel . "'><img border='0' src='image/icon/cancel.png' alt='Delete' title='Delete'></img></a>
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
				#tbl-infotex {
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
		$this->table .= "<div id='tbl-infotex'><table cellpadding = '4' cellspacing='1' border='0' width='100%'><tr><td class='styleTitleForm'><a href='main.php?param=" . $prmAdd . "' onClick=''><img border='0' src='image/icon/add.png' alt='Delete' title='Tambah Info'></img> Tambah Info</a></td></tr></table><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
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
		$cdmtid = '';
		$product_id = '';
		$infotext = '';
		$biller_id = '';
		$start_date = '';
		$end_date = '';
		if ($id) {
			$sQ = "SELECT A.CDM_T_ID, A.CDM_T_TEXT, A.CDM_T_START, A.CDM_T_END, A.CDM_T_BILLER_ID FROM " . $this->TBL_INFOTEXT;
			$sQ .= " A WHERE A.CDM_T_ID ='" . $id . "'";
			if ($res = mysqli_query($this->DBLINK, $sQ)) {
				while ($rws = mysqli_fetch_array($res)) {
					$cdmtid = $rws['CDM_T_ID'];
					$infotext = $rws['CDM_T_TEXT'];
					$start_date = $rws['CDM_T_START'];
					$end_date = $rws['CDM_T_END'];
					$biller_id = $rws['CDM_T_BILLER_ID'];
				}
			} else {
				$sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
				;

			}
			$tmp = explode(" ", $start_date);
			$tmpdate = explode("-", $tmp[0]);
			$tmp2 = explode(" ", $end_date);
			$tmpdate2 = explode("-", $tmp2[0]);
			$yr = $tmpdate[0];
			$d = $tmpdate[1];
			$m = $tmpdate[2];
			$yr2 = $tmpdate2[0];
			$d2 = $tmpdate2[1];
			$m2 = $tmpdate2[2];

		}

		for ($t = 1; $t <= 31; $t++) {
			$asel = ($d == $t ? ' selected="selected" ' : '');
			$optDay .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $asel . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
			$asel2 = ($d2 == $t ? ' selected="selected" ' : '');
			$optDay2 .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $asel2 . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
		}
		for ($t = 1; $t <= 12; $t++) {
			$bsel = ($m == $t ? ' selected="selected" ' : '');
			$optMon .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $bsel . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
			$bsel2 = ($m2 == $t ? ' selected="selected" ' : '');
			$optMon2 .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $bsel2 . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
		}
		for ($t = 0; $t <= 10; $t++) {
			$csel = ($yr == $t ? ' selected="selected" ' : '');
			$optYr .= '<option value="' . str_pad(($yr + $t), 2, 0, STR_PAD_LEFT) . '" ' . $csel . '>' . ($yr + $t) . '</option>';
			$csel2 = ($yr2 == $t ? ' selected="selected" ' : '');
			$optYr2 .= '<option value="' . str_pad(($yr2 + $t), 2, 0, STR_PAD_LEFT) . '" ' . $csel2 . '>' . ($yr2 + $t) . '</option>';
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
				<div id="form" class="styleDivCenter"><table width="100%" border="0" cellpadding="8" cellspacing="0"><tr><td class="styleTitleForm"> 
				<a href="main.php?param=' . base64_encode(implode("&", $pr)) . '"> &laquo; Kembali </a></td></tr>
				<tr><td>
			<form id="form1" name="form1" method="post" action="main.php?param=' . $p . '">
			  <table width="100%" border="0" cellpadding="4" cellspacing="0"> 
				<tr>
				  <td>Info Text</td>
				  <td>:</td>
				  <td><label>
					<input name="infotext" type="text" size="60" maxlength="60" value="' . $infotext . '">
				  </label></td>
				</tr>
				<tr>
				  <td>Tanggal Awal</td>
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
				  <td>Tanggal Akhir</td>
				  <td>:</td>
				  <td><label>
					<select name="end_tgl" id="end_tgl">' . $optDay2 . '
					</select>
				  </label>/
					<label>
					  <select name="end_mon" id="end_mon">' . $optMon2 . '
					  </select>
					</label>/
					<label>
					  <select name="end_yr" id="end_yr">' . $optYr2 . '
					  </select>&nbsp;-&nbsp;dd/mm/YYYY
				  </label></td>
				</tr>
				<tr>
				  <td>Biller ID</td>
				  <td>:</td>
				  <td><label>
					<input name="biller_id" type="text" size="8" maxlength="8" value="' . $biller_id . '">
				  </label></td>
				</tr>
				<tr>
				  <td>&nbsp;</td>
				  <td>&nbsp;</td>
				  <td><label>
					<input type="submit" name="submit" id="submit" value="Submit" />
				  </label><input name="idinfotext" type="hidden" value="' . $id . '" /></td>
				</tr>
			  </table>
			</form></div></td></tr></table>';

		echo $form;
	}

	public function saveInfotext($infotext, $start_tgl, $end_tgl, $biller_id)
	{
		$OK = false;
		$rand_id = "";
		srand((double) microtime() * 1000000);
		$rand_id = md5(uniqid(rand()));

		$infotext = nullable_htmlspecialchar($infotext, ENT_QUOTES);

		$sQ = "INSERT INTO " . $this->TBL_INFOTEXT . " (CDM_T_ID, CDM_T_TEXT, CDM_T_START, CDM_T_END, CDM_T_BILLER_ID) ";
		$sQ .= " VALUES ('" . $rand_id . "','" . $infotext . "','" . $start_tgl . "','" . $end_tgl . "','" . $biller_id . "') ";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
			;

		}

		return $OK;
	}

	public function saveEditInfotext($id, $infotext, $start_tgl, $end_tgl, $biller_id)
	{
		$OK = false;

		$infotext = nullable_htmlspecialchar($infotext, ENT_QUOTES);

		$sQ = "UPDATE " . $this->TBL_INFOTEXT . " SET CDM_T_TEXT='" . $infotext . "',";
		$sQ .= " CDM_T_START = '" . $start_tgl . "', CDM_T_END='" . $end_tgl . "' ,CDM_T_BILLER_ID='" . $biller_id . "' WHERE CDM_T_ID ='" . $id . "'";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
			;

		}

		return $OK;
	}

	public function deleteInfotext($id)
	{
		$OK = false;
		$sQ = "DELETE FROM " . $this->TBL_INFOTEXT . " WHERE CDM_T_ID ='" . $id . "'";
		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$OK = true;
			//$this->displayInfotext();
		} else {
			$sErrMsg = mysqli_error($this->DBLINK);
			if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
				error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
			;

		}

		return $OK;
	}

	public function displayInfotext()
	{
		$this->createTable();
		echo $this->table;
	}
}

$status = @isset($status) ? $status : '';
$save = @isset($_REQUEST['save']) ? $_REQUEST['save'] : false;
$sb = @isset($submit) ? $submit : '';
$ddInfoText = new classInfotext();
$ddInfoText->DBLINK = $areaDbLink;
$ddInfoText->TBL_INFOTEXT = 'CDCMOD_MF_AUTO_TEXT';
$ddInfoText->params = $param;

if ($status == 'add') {
	$ddInfoText->displayForm($status);
}

if ($status == 'edit') {
	$ddInfoText->displayForm($status, $idtxtinf);
}
if ($status == 'delete') {
	if ($ddInfoText->deleteInfotext($idtxtinf)) {
		$ddInfoText->displayInfotext();
	}
	;
}

if ($save == 'add') {
	if ($sb == 'Submit') {
		$start_tgl = $str_yr . "-" . $str_mon . "-" . $str_tgl . " 00:00:00";
		$end_tgl = $end_yr . "-" . $end_mon . "-" . $end_tgl . " 00:00:00";

		$ddInfoText->saveInfotext($infotext, $start_tgl, $end_tgl, $biller_id);
	}
}
if ($save == 'edit') {
	if ($sb == 'Submit') {
		$start_tgl = $str_yr . "-" . $str_mon . "-" . $str_tgl . " 00:00:00";
		$end_tgl = $end_yr . "-" . $end_mon . "-" . $end_tgl . " 00:00:00";

		$ddInfoText->saveEditInfotext($idinfotext, $infotext, $start_tgl, $end_tgl, $biller_id);
	}
}

if ($status == '') {
	$ddInfoText->displayInfotext();
}
?>