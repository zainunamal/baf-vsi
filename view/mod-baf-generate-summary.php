<?php
require_once("inc/baf/inc-baf-config.php");
$now = time();

if (isset($data)) {
	$filtertgl = isset($_REQUEST["filtertgl"]) ? $_REQUEST["filtertgl"] : date("Y-m-d");
	$generate = isset($_REQUEST["submit-date"]) ? $_REQUEST["submit-date"] : "";
	echo "<link href='inc/datepicker/datepickercontrol.css' rel='stylesheet' type='text/css' />";
	echo '<SCRIPT LANGUAGE="JavaScript" src="inc/datepicker/datepickercontrol.js"></SCRIPT>';
	echo '<input type="hidden" id="DPC_TODAY_TEXT" value="Hari Ini">';
	echo '<input type="hidden" id="DPC_BUTTON_TITLE" value="Buka Tanggal">';
	echo "<input type='hidden' id='DPC_MONTH_NAMES' value=\"['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']\">";
	echo "<input type='hidden' id='DPC_DAY_NAMES' value=\"['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']\">";

	?>
	<br>
	<h1>Generate Summary Transaksi BAF</h1><br>
	<form name="input-tanggal" method="post" onsubmit="return confirm('Generate Summary?');">
		Tanggal Transaksi (YYYY-MM-DD)<input type="text" name="filtertgl" readonly id="filtertgl"
			value="<?php echo $filtertgl ?>" datepicker="true" datepicker_format="YYYY-MM-DD" />
		<input type="submit" name="submit-date" value="Generate" />
	</form>
	<?php
	if ($generate == "Generate" && $filtertgl != "") {
		$stdate = str_replace("-", "", $filtertgl);
		$javabin = '/usr/bin/';
		$out = null;
		//echo 'cd '.BAF_RECON_PATH.'/ReconApps;'.$javabin.'java -jar ReconCore.jar --plugin=BAF-GENERATE-REPORT --default-db=BAF-DPP --action=dump --start-date='.$stdate."<br>";
		$hasil = exec('cd ' . BAF_RECON_PATH . '/ReconApps;' . $javabin . 'java -jar ReconCore.jar --plugin=BAF-GENERATE-REPORT --default-db=BAF-DPP --action=dump --start-date=' . $stdate, $out);
		echo "<br/><div style='cursor:hand' onclick=\"if(this.innerHTML=='<b>Tampilkan Detail</b>') {document.getElementById('generate-result-detail').style.display='inline';document.getElementById('generate-result').style.display='none';this.innerHTML='<b>Sembunyikan Detail</b>';}else{document.getElementById('generate-result-detail').style.display='none';document.getElementById('generate-result').style.display='inline';this.innerHTML='<b>Tampilkan Detail</b>';}\"><b>Tampilkan Detail</b></div><br/>";
		echo "<div id='generate-result-detail' style='display:none'>";
		foreach ($out as $line) {
			echo htmlspecialchars($line) . "<br/>";
		}
		echo "</div>";
		echo "<div id='generate-result' style='display:inline'><b>" . htmlspecialchars(str_replace(BAF_RECON_PATH . "/", "", $hasil)) . "</b></div>";
	}
}
?>