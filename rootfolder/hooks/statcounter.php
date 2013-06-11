<?php // STATCOUNTER HOOK
function statcounter() {
	echo "\n<script type=\"text/javascript\">\n
	var sc_project=".STATCOUNTER_PROJECT.";\n
	var sc_invisible=1;\n
	var sc_partition=".STATCOUNTER_PARTITION.";\n
	var sc_security=\"".STATCOUNTER_SECURITY."\";\n
	</script>\n\n
	<script type=\"text/javascript\" src=\"http://www.statcounter.com/counter/counter.js\">\n
	</script>\n
	<noscript>\n
	<a href=\"http://www.statcounter.com/\" target=\"_blank\">\n
	<img src=\"http://c20.statcounter.com/counter.php?sc_project=".STATCOUNTER_PROJECT."&java=0&security=".STATCOUNTER_SECURITY."&invisible=1\">
	</a>\n
	</noscript>";
}
?>