<?php
require_once("inc/payment/db-payment.php");

function getSwitcher(&$rw)
{
	global $jcol, $areaDbLink;
	$mOK = false;
	$i = 0;
	$sQ = "SELECT * FROM CDCCORE_SWITCHER";

	if ($res = mysqli_query($areaDbLink, $sQ)) {
		while ($row = mysqli_fetch_assoc($res)) {
			$rw[$i]['CDC_S_ID'] = $row['CDC_S_ID'];
			$rw[$i]['CDC_S_NAME'] = $row['CDC_S_NAME'];
			$i++;
		}
		$jcol = $i;
		$mOK = true;
	} else {
		echo mysqli_error($areaDbLink);
	}
	return $mOK;
}

$i = 2;
$col = '';
$a = '';
$b = '';
$colTitle_All[0] = array("title" => "Tanggal", "width" => "10%", "field" => "DATE", "align" => "center", "type" => "date", "span" => array("row", 2));
$colTitle_All[1] = array("title" => "Bank", "width" => "10%", "field" => "CDC_B_NAME", "align" => "left", "type" => "string", "span" => array("row", 2));
if (getSwitcher($prows)) {
	foreach ($prows as $prow) {
		$a .= "SUM(CASE WHEN A.SWITCH_ID ='" . $prow['CDC_S_ID'] . "' THEN TRAN_AMOUNT ELSE 0 END)+";
		$b .= "SUM(CASE WHEN A.SWITCH_ID ='" . $prow['CDC_S_ID'] . "' THEN TRAN_COUNT ELSE 0 END)+";
		$col .= "SUM(CASE WHEN A.SWITCH_ID ='" . $prow['CDC_S_ID'] . "' THEN TRAN_AMOUNT ELSE 0 END) AS \"AMT" . $prow['CDC_S_NAME'] . " \",";
		$col .= "SUM(CASE WHEN A.SWITCH_ID ='" . $prow['CDC_S_ID'] . "' THEN TRAN_COUNT ELSE 0 END) AS \"CNT" . $prow['CDC_S_NAME'] . "\" ,";
		$colSubTitle_All[0] = array("title" => "Transaksi", "width" => "10%", "field" => "CNT" . $prow['CDC_S_NAME'], "align" => "center", "type" => "int");
		$colSubTitle_All[1] = array("title" => "Jumlah (Rp)", "width" => "10%", "field" => "AMT" . $prow['CDC_S_NAME'], "align" => "right", "type" => "currency");
		$colSubTitle_Total[0] = array("title" => "Transaksi", "width" => "10%", "field" => "TOTAL_COUNT", "align" => "center", "type" => "int");
		$colSubTitle_Total[1] = array("title" => "Jumlah (Rp)", "width" => "10%", "field" => "TOTAL_AMOUNT", "align" => "right", "type" => "currency");
		$colTitle_All[$i] = array("title" => htmlspecialchars($prow['CDC_S_NAME']), "width" => "10%", "align" => "center", "type" => "date", "subTitle" => $colSubTitle_All, "span" => array("col", 2));
		$i++;
	}
	if ($a != '') {
		$colTitle_All[$i] = array("title" => "Total", "width" => "10%", "align" => "center", "type" => "date", "subTitle" => $colSubTitle_Total, "span" => array("col", 2));
		$a = substr($a, 0, -1);
		$b = substr($b, 0, -1);
		$a .= " AS TOTAL_AMOUNT,";
		$b .= " AS TOTAL_COUNT,";
		$col .= $a . $b;
	}
	$colTitle_All[($i + 1)] = array("title" => "File", "width" => "15%", "field" => "DPP_MFSM_FILE", "align" => "center", "type" => "linkUrl", "span" => array("row", 2));
}
$col = substr($col, 0, -1);
$query_all = "SELECT * FROM (SELECT A.DATE, A.CA_ID, B.CDC_B_NAME, " . $col . " FROM {TABLE} A, CDCCORE_BANK  B WHERE A.CA_ID=B.CDC_B_ID AND A.DATE IS NOT NULL {WHERE} GROUP BY A.DATE, A.CA_ID ) A LEFT JOIN DPP_MF_SUMMARY_FILE B ON A.CA_ID = B.DPP_MFSM_FILE_BANK AND A.DATE = B.DPP_MFSM_FILE_DATE";


$table_lookup = "DPP_MF_SUMMARY_LOOKUP";
$query_All_Key = "DATE";
$query_Detail_Key = "DATE";
?>