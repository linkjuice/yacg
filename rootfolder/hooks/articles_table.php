<?php // ARTICLES TABLE HOOK
function table($items = 2, $timeformat = '\P\u\b\l\i\s\h\e\d \o\n F j, Y, g:i a') {
  global $keyarr;
	
	$timestamp = date($timeformat, filemtime(FILE_KEYWORDS));
	
	echo "<div>\n<table>\n";
	
	for ($n=0; $n < $items; $n++) {
	  $imglink = images('flickr', 1, cut_cat($keyarr[$n]), true);
	  echo "<tr>\n<td>\n<a href=\"".k2url($keyarr[$n]).'" title="'.cut_cat($keyarr[$n]).'">'."\n<img src=\"".$imglink[0]['thumbnail']."\" /></a></td>\n<td>\n<a href=\"".k2url($keyarr[$n]).'" title="'.cut_cat($keyarr[$n]).'">'.cut_cat($keyarr[$n])."</a><br />\n";
		$cachedpage = LOCAL_CACHE.adashes(cut_cat($keyarr[$n])).'.html';
		$timestamp = file_exists($cachedpage) ? date($timeformat, filemtime($cachedpage)) : $timestamp;
		echo $timestamp."\n</td>\n</tr>\n";
	}
	
	echo "</table>\n</div>\n";
}
?>