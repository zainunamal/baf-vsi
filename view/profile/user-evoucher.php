<?php

if ($data) {
	$uid = $data->uid;
	
	$terminal = $dbSpec->GetTerminalSMSInfo($uid);
	echo "<div class='spacer10'></div>\n";
	if ($terminal != null) {
		$name = $terminal["name"];
		$bank = $terminal["bank"];
		$accNumber = $terminal["accountNumber"];
		$bankAccNumber = $terminal["bankAccount"];
		$phoneNumber = $terminal["phoneNumber"];
		$status = $terminal["status"];
		$initSetoran = $terminal["initSetoran"];
		$initDeposit = $terminal["initDeposit"];
		$agentId = $terminal["agentId"];
		$agentName = $terminal["agentName"];

		echo "<div class='subSubTitle'>User Profile</div>\n";
		echo "<table class='transparent'>\n";
		echo "	<tr>\n";
		echo "		<td>PPID</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$uid</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>Name</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$name</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>No. Phone</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$phoneNumber</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>No. Account</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$accNumber</td>\n";
		echo "	</tr>\n";
		echo "</table>\n";
		
		echo "<div class='spacer10'></div>\n";
		echo "<div class='subSubTitle'>Bank Info</div>\n";
		echo "<table class='transparent'>\n";
		echo "	<tr>\n";
		echo "		<td>Name</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$bank</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>No. Account</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$bankAccNumber</td>\n";
		echo "	</tr>\n";
		echo "</table>\n";
		
		// DEPRECATED: tidak usah memunculkan jumlah deposit awal
		// echo "<div class='spacer10'></div>\n";
		// echo "<div class='subSubTitle'>Deposit Info</div>\n";
		// echo "<div class='spacer5'></div>\n";
		// echo "<table class='transparent'>\n";
		// echo "	<tr>\n";
		// echo "		<td>Initial Credit</td>\n";
		// echo "		<td>:</td>\n";
		// echo "		<td>Rp. " . number_format($initSetoran, 0, ',', '.') . "</td>\n";
		// echo "	</tr>\n";
		// echo "	<tr>\n";
		// echo "		<td>Initial Deposit</td>\n";
		// echo "		<td>:</td>\n";
		// echo "		<td>Rp. " . number_format($initDeposit, 0, ',', '.') . "</td>\n";
		// echo "	</tr>\n";
		// echo "</table>\n";
		
		echo "<div class='spacer10'></div>\n";
		echo "<div class='subSubTitle'>Agent Info</div>\n";
		echo "<table class='transparent'>\n";
		echo "	<tr>\n";
		echo "		<td>Agent Id</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$agentId</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td>Agent Name</td>\n";
		echo "		<td>:</td>\n";
		echo "		<td>$agentName</td>\n";
		echo "	</tr>\n";
		echo "</table>\n";
	} else {
		echo "<div>No detail found</div>\n";
	}
		
	// nama, no handphone, ppid, no rekening, bank, status, setoran awal, deposit awal, nama agen
}

?>
