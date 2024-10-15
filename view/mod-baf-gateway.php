<?php
require_once("inc/mini-monitoring/mini-monitoring.php");
require_once("baf/inc-baf-config.php");
$now = time();

$sdate = @isset($start_date) ? $start_date : date("d/m/Y");
$edate = @isset($end_date) ? $end_date : date("d/m/Y");
$bank = @isset($bank) ? $bank : '0110000';
$bank_name = @isset($bank_name) ? $bank_name:'';
$dwnxls = @isset($downloadxls) ? $downloadxls : date("d/m/Y");
$importToXls = @isset($importToXls) ? $importToXls:false;
$where='';
$data = array();
function createStrDate($sd) {
	if ($sd != '') {
		$date = explode("/",$sd);
		$dt = $date[2].$date[1].$date[0];
		//$dt = str_replace("/","",$sd);
		return $dt;
	} else {
		return $sd;
	}
}
	
if ($sdate !='') {
	$where .= " AND DATE >= '". createStrDate($sdate)."'";
}
if ($edate !='') {
	$where .= " AND DATE <= '". createStrDate($edate)."'";
}

$tmpdata = str_replace('{WHERE}',$where,$query_all);
$data['query_all'] = $tmpdata  ;
$data['query_All_Key'] = $query_All_Key ;
$data['table_lookup'] = $table_lookup;
$data['area_code'] = $area;
$data['bank_code'] = $bank;
$data['start_date'] = createStrDate($sdate);
$data['end_date'] = createStrDate($edate);

$data['colTitle_All'] = $colTitle_All;
$mod_baf_gateway = new mini_monitoring();
$mod_baf_gateway -> AREA = $area;
$mod_baf_gateway -> MODULE_NAME = "BAF";
$mod_baf_gateway -> MODULE = $module;
$mod_baf_gateway -> DATA = $data;
$mod_baf_gateway -> DBLINK = $areaDbLink;
$mod_baf_gateway -> BILLER = "DD";
$mod_baf_gateway -> TBL_TRANSACTION = "DPP_MF_AUTO";
$mod_baf_gateway -> LOOKUP_TABLE = $data['table_lookup'];
$mod_baf_gateway -> TBL_BANK = "CDCCORE_BANK";
$mod_baf_gateway -> EXCELFILE = "view/baf/export-xls.php";
$mod_baf_gateway -> HEADER_TYPE = 1;
$mod_baf_gateway -> OPTION_TYPE = 1;
$mod_baf_gateway -> TABLE_WIDTH = 1000;
$mod_baf_gateway -> DEF_START_DATE = $sdate;
$mod_baf_gateway -> DEF_END_DATE = $edate;
$mod_baf_gateway -> BANK = $bank;
$mod_baf_gateway -> BANK_NAME = $bank_name;
$mod_baf_gateway -> printData();
?>
