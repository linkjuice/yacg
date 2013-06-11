<?php // WIKIPEDIA ARTICLE SCRAPER HOOK
function wikipedia($keyword = THIS_PAGE_KEYWORD, $language = "en", $format = "html", $size = 10000, $images = false) {
	$wikipedia = '';
	$pattern[] = '/<a href="(.*?)">(.*?)<\\/a>/';
	$replace[] = '$2';
	$pattern[] = '/<h3 id="siteSub">From Wikipedia, the free encyclopedia<\/h3>/';
	$replace[] = '';
	$pattern[] = '/<div id="contentSub">(.*?)<\/div><div id="jump-to-nav">Jump to: navigation, search<\/div>/';
	$replace[] = '';
	$pattern[] = '/<div class="messagebox cleanup metadata">(.*?)<p><br \/><\/p>/';
	$replace[] = '';
	$pattern[] = '/<(\w+?) class="[^"]*?messagebox[^"]*?".*?>(.*?)<\/\\1>/';
	$replace[] = '';
	$pattern[] = '/<dl>(.*?)<\/dl>/';
	$replace[] = '';
	$pattern[] = '/<script.*?>.*?<\/script>/i';
	$replace[] = '';
	$pattern[] = '/<h1 class="firstHeading">(.*?)<\/h1>/';
	$replace[] = '<h3>$1</h3>';
	$pattern[] = '/<div class="infobox sisterproject">(.*?)<\/div><\/div>/';
	$replace[] = '';
	$pattern[] = '/<sup (.*?)>(.*?)<\/sup>/';
	$replace[] = '';
	$pattern[] = '/<table style="background: transparent;" width="0">(.*?)<\/table>/';
	$replace[] = '';
	$pattern[] = '/<table class="toccolours" align="center" width="55%" cellpadding="0" cellspacing="0">(.*?)<\/table>/';
	$replace[] = '';
	$pattern[] = '/<div class="editsection"(.*?)>(.*?)<\/div>/';
	$replace[] = '';
	$pattern[] = '/<div id="bodyContent">/';
	$replace[] = '<div>';
	$pattern[] = '/<dd>(.*?)<\/dd>/';
	$replace[] = '';
	$pattern[] = '/<div class="thumbcaption">(.*?)<\/div><\/div>/';
	$replace[] = '';
	$pattern[] = '/<div class="thumb tright">/';
	$replace[] = '';
	$pattern[] = '/\[(.*?)\]/';
	$replace[] = '';
	$pattern[] = '/<div style="position:absolute; z-index:100; right:20px; top:10px; height:10px; width:300px;"><\/div>/';
	$replace[] = '';
	$pattern[] = '/<div style="position:absolute; z-index:100; right:10px; top:10px;" class="metadata" id="administrator">(.*?)<\/div><\/div>/';
	$replace[] = '';
	$pattern[] = '/<div class="dablink">(.*?)<\/div>/';
	$replace[] = '';
	$pattern[] = '/<div class="notice spoiler" id="spoiler">(.*?)<\/div>/';
	$replace[] = '';
	$pattern[] = '/<p><i>See also:(.*?)<\/i><\/p>/';
	$replace[] = '';
	$pattern[] = '/<div style="margin-left: 60px;">(.*?)<\/div>/';
	$replace[] = '';
	$pattern[] = '/<map(.*?)>(.*?)<\/map>/';
	$replace[] = '';
	$pattern[] = '/<img src="(.*?)" alt="This page is semi-protected." width="18" (.*?)\/>/';
	$replace[] = '';
	$pattern[] = '/<img[^>]+?Padlock-silver-medium[^>]+?>/';
	$replace[] = '';
	$pattern[] = '/<table style="width:100%;background:none">(.*?)<\/table>/';
	$replace[] = '';
	$pattern[] = '/<div class="messagebox merge metadata"><div class="floatleft">(.*?)<\/div>(.*?)<\/div>/';
	$replace[] = '';
	$pattern[] = '/<img src="http:\/\/upload.wikimedia.org\/wikipedia\/commons\/thumb\/f\/fa\/Padlock-silver-medium.svg\/\d{2}px-Padlock-silver-medium.svg.png" alt="" width="18" height="18" longdesc="\/wiki\/Image:Padlock-silver-medium.svg" usemap="#ImageMap_1" \/>/';
	$replace[] = '';
	$pattern[] = '/<small>(.*?)<\/small>/';
	$replace[] = '';
	$pattern[] = '/<div(.*?)>/';
	$replace[] = '';
	$pattern[] = '/<\/div>/';
	$replace[] = '';
	$pattern[] = '/<div class="boilerplate metadata" id="stub">(.*?)<\/div>/';
	$replace[] = '';
	// REMOVE TABLES
	$pattern[] = '/<tr(.*?)>(.*?)<\/tr>/';
	$replace[] = '';
	$pattern[] = '/<table(.*?)>(.*?)<\/table>/';
	$replace[] = '';
	
	// FIND AND CACHE A RELATED WIKIPEDIA ARTICLE
	$wikipedia_search = fetch('http://search.yahooapis.com/WebSearchService/V1/webSearch?appid='.YAHOO_API.'&query=site:'.$language.'.wikipedia.org+'.urlencode($keyword));
	preg_match('/<Url>(.*?)<\/Url>/', $wikipedia_search, $wikipedia_results);
	$wikipedia = fetch($wikipedia_results[1]);
	// CHECK FOR DISAMBIGUATION
	if (preg_match('/Wikipedia:Disambiguation/', $wikipedia)) {
		preg_match('/<li><a href=\"\/wiki\/(.+?)\"/', $wikipedia, $new_url);
		$wikipedia = fetch('http://'.$language.'.wikipedia.org/wiki/'.$new_url[1]);
	}
	// REMOVE DOUBLE SPACES AND NEW LINES
	$wikipedia = preg_replace(array("/\n/", "/\s\s+/"), array("", " "), $wikipedia);
	// MATCH CONTENT (HTML OR TXT)
	if ($format == "html") {
	  preg_match("/<\!-- start content --\>(.*)<div class=\"printfooter\">/is", $wikipedia, $w);
		$wikipedia = preg_replace($pattern, $replace, $w[1]);
	}	else {
	  if (preg_match('/\<\!-- start content --\>(.*)<div class=\"printfooter\">/', $wikipedia, $w)) {
			$wikipedia = preg_replace($pattern, $replace, $w[1]);
			$wikipedia = str_replace('[edit]', '', $wikipedia);
			$wikipedia = strip_tags($wikipedia);
			$wikipedia = preg_replace('/Retrieved from "http.+$/s', '', $wikipedia);
			$wikipedia = preg_replace('/External links.+$/s', '', $wikipedia);
			$wikipedia = preg_replace('/References.+$/s', '', $wikipedia);
		}	else {
		  return printerror('<strong>Error - Nothing was found</strong>');
		}
	}
	$wikipedia = sanitize_xhtml($wikipedia);
	// TRIM ARTICLE TO THE DESIRED SIZE
	$wikipedia = strlen($wikipedia) > $size ? substr($wikipedia, 0, $size) : $wikipedia;
	// CACHE ALL THE IMAGES
	if ($images == false) {
		$wikipedia = preg_replace(array("!<img src=[^>]*>!si", "!<div class=\"magnify\".*?</div>!si", "!<div class=\"thumb.*?\".*?</div>!si"), "", $wikipedia);
	}	else {
	  preg_match_all("/src\=[\"'`](.+?)\\1/i", $wikipedia, $matches);
		foreach ($matches[1] as $image) {
			$image_name = basename($image);
			if (!file_exists(LOCAL_IMAGE_CACHE.$image_name)) {
				$image_file = fetch($image);
				file_put_contents(LOCAL_IMAGE_CACHE.$image_name, $image_file);
			}
			$wikipedia = str_replace($image, THIS_DOMAIN.str_replace(array('.'), '', LOCAL_IMAGE_CACHE).$image_name, $wikipedia);
		}
	}
	echo $wikipedia."\n";
}

function replacer($text) {
	$check = "/:/s";
	$replace = "&#58;";
	return preg_replace($check, $replace, $text[0]);
}

function sanitize_xhtml($source) {
	$exceptions = "text-align|text-decoration";
	$source = "<parse>".$source."</parse>";
	$source = preg_replace_callback("@>(.*)<@Us", "replacer", $source);
	$source = preg_replace('@([^;"]+)?(?<!'.$exceptions.')(?<!\>\w):(?!\/\/(.+?)\/|<|>)((.*?)[^;"]+)(;)?@is', '', $source);
	$source = preg_replace(array('/[a-z]*=""/is', '/class="(.*?)"/', '/id="(.*?)"/', '/usemap="(.*?)"/', '/name="(.*?)"/', '/xml:lang="(.*?)"/', '/lang="(.*?)"/', '/<span><\/span>/', '/<p><\/p>/', '/<a><\/a>/', '/<\/?parse>/'), '', $source);
	$source = preg_replace('/\s\s+/', ' ', $source);
	$source = preg_replace('/ >/','>', $source);
	$source = preg_replace('/<b>/', '<strong>', $source);
	$source = preg_replace('/<\/b>/', '</strong>', $source);
	$source = preg_replace('/<br>/', '<br />', $source);
	return $source;
}
?>