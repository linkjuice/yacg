<?php // IPCLOACK HOOK
if (CLOAKING_LEVEL != 4) {
	$lastupdated = date("Ymd", filemtime(FILE_BOTS));
	if ($lastupdated != date("Ymd")) {
		$lists = array(
		'http://labs.getyacg.com/spiders/google.txt',
		'http://labs.getyacg.com/spiders/inktomi.txt',
		'http://labs.getyacg.com/spiders/lycos.txt',
		'http://labs.getyacg.com/spiders/msn.txt',
		'http://labs.getyacg.com/spiders/altavista.txt',
		'http://labs.getyacg.com/spiders/askjeeves.txt',
		'http://labs.getyacg.com/spiders/wisenut.txt',
		);
		foreach($lists as $list) $opt .= fetch($list);
		$opt = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $opt);
		file_put_contents(FILE_BOTS, $opt);
	}
	
	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
	$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	$host = strtolower(gethostbyaddr($ip));
	$file = implode(" ", file(FILE_BOTS));
	$exp = explode(".", $ip);
	$class = $exp[0].'.'.$exp[1].'.'.$exp[2].'.';
	$threshold = CLOAKING_LEVEL;
	$cloak = 0;
	
	if (stristr($host, "googlebot") && stristr($host, "inktomi") && stristr($host, "msn")) $cloak++;
	if (stristr($file, $class)) $cloak++;
	if (stristr($file, $agent)) $cloak++;
	if (strlen($ref) > 0) $cloak = 0;
	
	$cloakdirective = ($cloak >= $threshold) ? 1 : 0;
}
?>