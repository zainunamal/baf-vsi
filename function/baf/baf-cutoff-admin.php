<?php
require_once("inc/payment/ctools.php");
require_once("inc/baf/inc-baf-config.php");
class classCutoff
{
	public $DBLINK;
	private $header;
	private $body;
	private $table;
	public $params;

	function getDataCutoff(&$rows)
	{
		$OK = false;
		//CDM_CO_ID, CDM_CO_TITLE, CDM_CO_DESC, CDM_CO_START, CDM_CO_END, CDM_CO_CREATED	CDCMOD_MF_AUTO_CUTOFF
		$sQ = "SELECT CDM_CO_ID, CDM_CO_TITLE, CDM_CO_DESC, CDM_CO_START, CDM_CO_END, CDM_CO_CREATED FROM CDCMOD_MF_AUTO_CUTOFF";

		if ($res = mysqli_query($this->DBLINK, $sQ)) {
			$i = 0;
			while ($rws = mysqli_fetch_array($res)) {
				$rows[$i]['CDM_CO_ID'] = $rws['CDM_CO_ID'];
				$rows[$i]['CDM_CO_TITLE'] = $rws['CDM_CO_TITLE'];
				$rows[$i]['CDM_CO_DESC'] = $rws['CDM_CO_DESC'];
				$rows[$i]['CDM_CO_START'] = $rws['CDM_CO_START'];
				$rows[$i]['CDM_CO_END'] = $rws['CDM_CO_END'];
				$rows[$i]['CDM_CO_CREATEDFROM'] = $rws['CDM_CO_CREATED'];
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
		$this->header = "<tr class='tableTitle' ><td align='center'>No.</td><td align='center'>Judul</td><td align='center'>Deskripsi</td><td align='center'>Tanggal Terdaftar</td><td align='center'>Waktu Mulai</td><td align='center'>Waktu Berakhir</td><td align='center'>Option</td></tr>";
	}

	function createBody()
	{
		if ($this->getDataCutoff($rows)) {
			$i = 0;
			foreach ($rows as $row) {
				$prmEdt = "&status=edit&idtxtinf=" . $row['CDM_CO_ID'];
				$prmEdt = base64_decode($this->params) . $prmEdt;
				$prmEdt = base64_encode($prmEdt);
				$prmDel = "&status=delete&idtxtinf=" . $row['CDM_CO_ID'];
				$prmDel = base64_decode($this->params) . $prmDel;
				$prmDel = base64_encode($prmDel);
				$this->body .= "<tr><td  align='right'>" . ($i + 1) . "</td><td>" . $row['CDM_CO_TITLE'] . "</td><td>" . $row['CDM_CO_DESC'];
				$this->body .= "</td><td align='center'>" . $row['CDM_CO_CREATEDFROM'] . "</td><td align='center'>" . $row['CDM_CO_START'] .
					"</td><td align='center'>" . $row['CDM_CO_END'] . "</td>";
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
		$this->table .= "<div id='tbl-infotex'><table cellpadding = '4' cellspacing='1' border='0' width='100%'><tr><td class='styleTitleForm'><a href='main.php?param=" . $prmAdd . "' onClick=''><img border='0' src='image/icon/add.png' alt='Delete' title='Tambah Jadwal'></img> Tambah Jadwal</a></td></tr></table><table cellpadding = '4' cellspacing='1' border='0' width='100%'>" . $this->header . $this->body . "</table></div>";
	}

	public function displayForm($act, $id = NULL)
	{
		$optDay = '';
		$optMon = '';
		$optYr = '';
		$optDay2 = '';
		$optMon2 = '';
		$optYr2 = '';
		$optDayCrea = '';
		$optMonCrea = '';
		$optYrCrea = '';
		$time_crea = date('H:i:s');
		$time_start = date('H:i:s');
		$time_end = date('H:i:s');
		$yr = date('Y');
		$d = date('j');
		$m = date('n');
		$yr2 = date('Y');
		$d2 = date('j');
		$m2 = date('n');
		$cdmcoid = '';
		$title = '';
		$description = '';
		$start_date = '';
		$end_date = '';
		$created = '';
		$yrCrea = date('Y');
		$dCrea = date('j');
		$mCrea = date('n');

		if ($id) {
			$sQ = "SELECT CDM_CO_ID, CDM_CO_TITLE, CDM_CO_DESC, CDM_CO_START, CDM_CO_END, CDM_CO_CREATED FROM CDCMOD_MF_AUTO_CUTOFF";
			$sQ .= " A WHERE A.CDM_CO_ID ='" . $id . "'";
			if ($res = mysqli_query($this->DBLINK, $sQ)) {
				while ($rws = mysqli_fetch_array($res)) {
					$cdmcoid = $rws['CDM_CO_ID'];
					$title = $rws['CDM_CO_TITLE'];
					$description = $rws['CDM_CO_DESC'];
					$start_date = $rws['CDM_CO_START'];
					$end_date = $rws['CDM_CO_END'];
					$created = $rws['CDM_CO_CREATED'];
				}
			} else {
				$sErrMsg = mysqli_error($this->DBLINK);
				if (CTOOLS_IsInFlag(DEBUG, DEBUG_ERROR))
					error_log("[" . date("YmdHis") . "][" . (basename(__FILE__)) . ":" . __LINE__ . "] [ERROR] $sErrMsg\n", 3, LOG_BAF_FILENAME);
			}
			$tmp = explode(" ", $start_date);
			$tmpdate = explode("-", $tmp[0]);

			$tmp2 = explode(" ", $end_date);
			$tmpdate2 = explode("-", $tmp2[0]);

			$tmpCrea = explode(" ", $created);
			$tmpCreaDate = explode("-", $tmpCrea[0]);

			$yr = $tmpdate[0];
			$d = $tmpdate[1];
			$m = $tmpdate[2];

			$yr2 = $tmpdate2[0];
			$d2 = $tmpdate2[1];
			$m2 = $tmpdate2[2];

			$yrCrea = $tmpCreaDate[0];
			$dCrea = $tmpCreaDate[1];
			$mCrea = $tmpCreaDate[2];

		}

		for ($t = 1; $t <= 31; $t++) {
			$asel = ($d == $t ? ' selected="selected" ' : '');
			$optDay .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $asel . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
			$asel2 = ($d2 == $t ? ' selected="selected" ' : '');
			$optDay2 .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $asel2 . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
			$aselCrea = ($dCrea == $t ? ' selected="selected" ' : '');
			$optDayCrea .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $aselCrea . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
		}
		for ($t = 1; $t <= 12; $t++) {
			$bsel = ($m == $t ? ' selected="selected" ' : '');
			$optMon .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $bsel . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
			$bsel2 = ($m2 == $t ? ' selected="selected" ' : '');
			$optMon2 .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $bsel2 . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
			$bselCrea = ($mCrea == $t ? ' selected="selected" ' : '');
			$optMonCrea .= '<option value="' . str_pad($t, 2, 0, STR_PAD_LEFT) . '" ' . $bselCrea . '>' . str_pad($t, 2, 0, STR_PAD_LEFT) . '</option>';
		}
		for ($t = 0; $t <= 10; $t++) {
			$csel = ($yr == $t ? ' selected="selected" ' : '');
			$optYr .= '<option value="' . str_pad(($yr + $t), 2, 0, STR_PAD_LEFT) . '" ' . $csel . '>' . ($yr + $t) . '</option>';
			$csel2 = ($yr2 == $t ? ' selected="selected" ' : '');
			$optYr2 .= '<option value="' . str_pad(($yr2 + $t), 2, 0, STR_PAD_LEFT) . '" ' . $csel2 . '>' . ($yr2 + $t) . '</option>';
			$cselCrea = ($yrCrea == $t ? ' selected="selected" ' : '');
			$optYrCrea .= '<option value="' . str_pad(($yrCrea + $t), 2, 0, STR_PAD_LEFT) . '" ' . $cselCrea . '>' . ($yrCrea + $t) . '</option>';
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
				  <td>Judul</td>
				  <td>:</td>
				  <td><label>
					<input name="title" type="text" size="60" maxlength="100" value="' . $title . '">
				  </label></td>
				</tr>
				<tr>
				  <td>Description</td>
				  <td>:</td>
				  <td><label>
					<input name="description" type="text" size="60" maxlength="255" value="' . $description . '">
				  </label></td>
				</tr>
				<tr>
				  <td>Tanggal Terdaftar</td>
				  <td>:</td>
				  <td><label>
					<select name="crea_tgl" id="crea_tgl">' . $optDayCrea . '
					</select>
				  </label>/
					<label>
					  <select name="crea_mon" id="crea_mon">' . $optMonCrea . '
					  </select>
				  </label>/
					<label>
					  <select name="crea_yr" id="crea_yr">' . $optYrCrea . '
					  </select>&nbsp;
				  </label>
				  <label>
					  <input name="time_crea" type="text" size="8" maxlength="8" value="' . $time_crea . '">&nbsp;-&nbsp;dd/mm/YYYY H:i:s
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
					  </select>
				  </label>/
					<label>
					  <select name="str_yr" id="str_yr">' . $optYr . '
					  </select>&nbsp;
				  </label>
				  <label>
					  <input name="time_start" type="text" size="8" maxlength="8" value="' . $time_start . '">&nbsp;-&nbsp;dd/mm/YYYY H:i:s
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
					  </select>&nbsp;
				  </label>
				  <label>
					  <input name="time_end" type="text" size="8" maxlength="8" value="' . $time_end . '">&nbsp;-&nbsp;dd/mm/YYYY H:i:s
				  </label></td>
				</tr>
				<tr>
				  <td>&nbsp;</td>
				  <td>&nbsp;</td>
				  <td><label>
					<input type="submit" name="submit" id="submit" value="Submit" />
				  </label><input name="idcutoff" type="hidden" value="' . $id . '" /></td>
				</tr>
			  </table>
			</form></div></td></tr></table>';
		echo $form;
	}

	public function saveCutoff($title, $description, $start_tgl, $end_tgl, $created)
	{
		$OK = false;
		$rand_id = "";
		srand((double) microtime() * 1000000);
		$rand_id = md5(uniqid(rand()));

		$title = nullable_htmlspecialchar($title, ENT_QUOTES);
		$description = nullable_htmlspecialchar($description, ENT_QUOTES);

		$sQ = "INSERT INTO CDCMOD_MF_AUTO_CUTOFF (CDM_CO_ID,CDM_CO_TITLE, CDM_CO_DESC, CDM_CO_START, CDM_CO_END, CDM_CO_CREATED) ";
		$sQ .= " VALUES ('" . $rand_id . "','" . $title . "','" . $description . "','" . $start_tgl . "','" . $end_tgl . "','" . $created . "') ";

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

	public function saveEditCutoff($id, $title, $description, $start_tgl, $end_tgl, $created)
	{
		$OK = false;

		$title = nullable_htmlspecialchar($title, ENT_QUOTES);
		$description = nullable_htmlspecialchar($description, ENT_QUOTES);

		$sQ = "UPDATE CDCMOD_MF_AUTO_CUTOFF SET CDM_CO_TITLE='" . $title . "', CDM_CO_DESC = '" . $description . "',";
		$sQ .= " CDM_CO_START = '" . $start_tgl . "', CDM_CO_END='" . $end_tgl . "' , CDM_CO_CREATED='" . $created . "' WHERE CDM_CO_ID ='" . $id . "'";

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

	public function deleteInfotext($id)
	{
		$OK = false;
		$sQ = "DELETE FROM CDCMOD_MF_AUTO_CUTOFF WHERE CDM_CO_ID ='" . $id . "'";
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

	public function displayInfotext()
	{
		$this->createTable();
		echo $this->table;
	}
}

$status = @isset($status) ? $status : '';
$save = @isset($_REQUEST['save']) ? $_REQUEST['save'] : false;
$sb = @isset($submit) ? $submit : '';
$cutOff = new classCutoff();
$cutOff->DBLINK = $areaDbLink;
$cutOff->params = $param;

if ($status == 'add') {
	$cutOff->displayForm($status);
}

if ($status == 'edit') {
	$cutOff->displayForm($status, $idtxtinf);
}
if ($status == 'delete') {
	if ($cutOff->deleteInfotext($idtxtinf)) {
		$cutOff->displayInfotext();
	}
	;
}

if ($save == 'add') {
	if ($sb == 'Submit') {
		$created_tgl = $crea_yr . "-" . $crea_mon . "-" . $crea_tgl . " " . $time_crea;
		$start_tgl = $str_yr . "-" . $str_mon . "-" . $str_tgl . " " . $time_start;
		$end_tgl = $end_yr . "-" . $end_mon . "-" . $end_tgl . " " . $time_end;

		$cutOff->saveCutoff($title, $description, $start_tgl, $end_tgl, $created_tgl);
	}
}
if ($save == 'edit') {
	if ($sb == 'Submit') {
		$created_tgl = $crea_yr . "-" . $crea_mon . "-" . $crea_tgl . " " . $time_crea;
		$start_tgl = $str_yr . "-" . $str_mon . "-" . $str_tgl . " " . $time_start;
		$end_tgl = $end_yr . "-" . $end_mon . "-" . $end_tgl . " " . $time_end;
		$cutOff->saveEditCutoff($idcutoff, $title, $description, $start_tgl, $end_tgl, $created_tgl);
	}
}

if ($status == '') {
	$cutOff->displayInfotext();
}
?>