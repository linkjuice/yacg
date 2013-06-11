<?php // SITEMAP HOOK
function sitemap($letters = "\d A B C D E F G H I J K L M N O P Q R S T U V W X Y Z") {
  global $keyarr;
	$letters = explode(" ", $letters);
	
	foreach ($letters as $letter) {
	  if ($letter == "\d") {
	    echo "<a href=\"##\" title=\"#\">#</a> ";
	  } else {
	    echo "<a href=\"#$letter\" title=\"$letter\">$letter</a> ";
	  }
	}
	echo '<br />';
	$keyword_file = $keyarr;
	if (count($keyword_file) > 10) {
    $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
    $keyword_file = array_slice($keyword_file, $offset, 10);
  }
	asort($keyword_file);
	foreach($letters as $letter) {
		$links = array();
		foreach($keyword_file as $keyword) {
		  $re = CATEGORIES ? "/,\s*$letter/iu" : "/^$letter/iu";
			if (preg_match($re, $keyword)) array_push($links, $keyword);
		}
		if (count($links) > 0) {
			$letter = $letter == "\d" ? "#" : $letter;
			echo "\n<a name=\"".$letter."\" id=\"".$letter."\"></a>";
			echo "\n<h1>".$letter."</h1>";
			foreach($links as $keyword) {
				echo "\n".'<a href="'.k2url($keyword).'" title="'.cut_cat($keyword).'">'.cut_cat($keyword).'</a><br />';
			}
		}
	}
  
  if (count($keyarr) > ($offset+10)) echo '<a href="sitemap?offset='.($offset+10).'">Next Page &rarr;</a>';
}
?>