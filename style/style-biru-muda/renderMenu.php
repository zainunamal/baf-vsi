<?php

/*======================*/
/*                      */
/*    Rendering Menu    */
/*                      */
/*======================*/

function renderMenu($appId, $appName, $arApp, $moduleId, $moduleName, $arModule) {
	echo "			<div id='menu'>\n";

	// Print application change
	if ($arApp != null) {
		echo "				\n";
		echo "				<div id='app-change'>\n";
		echo "					<ul>\n";
		foreach ($arApp as $iApp) {
			$iAppId = $iApp["id"];
			$iAppName = $iApp["name"];

			if ($iAppId == $appId) {
				echo "				<div id='app'>\n";
				echo "					<li>$iAppName &raquo;</li>\n";
				echo "				</div>\n";
				
				// Print module change
				echo "				\n";
				echo "				<div id='module-change'>\n";
				echo "					<ul>\n";
				foreach ($arModule as $iModule) {
					$iModId = $iModule["id"];
					$iModName = $iModule["name"];

					if ($iModId == $moduleId) {
						$url64 = base64_encode("a=$iAppId&m=$iModId");
						echo "				<div id='module'>\n";
						echo "					<li><a href='main.php?param=$url64'>&rsaquo; $iModName</a></li>\n";
						echo "				</div>\n";
					} else {
						$url64 = base64_encode("a=$iAppId&m=$iModId");
						echo "						<li><a href='main.php?param=$url64'>$iModName</a></li>\n";
					}
				}
				echo "					</ul>\n";
				echo "				</div>\n";			
				
			} else {
				$url64 = base64_encode("a=$iAppId");
				echo "						<li><a href='main.php?param=$url64'>$iAppName</a></li>\n";
			}
		}
		echo "					</ul>\n";
		echo "				</div>\n";
	} else {
	}

	// End of menu
	echo "			</div>\n";
}

?>