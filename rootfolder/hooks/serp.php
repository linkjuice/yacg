<?php
function serp($engine = 'live', $items = 5, $offset = 0, $keyword = THIS_PAGE_KEYWORD, $bare = false) {
  if ($engine == 'google') {
  	$serp = fetch('http://www.google.com/search?q='.urlencode($keyword).'&hl=en&start='.$offset.'&sa=N&filter=0&num='.$items);
  	preg_match_all("/<!--m-->.+?a href=\"([^\"]+)\" class=l.*?>(.*?)<\/a>.+?div class=\"s\">(.*?)(<br>|<cite>).+?<!--n-->/ims", $serp, $matches);
  	$results = array('url' => $matches[1], 'title' => $matches[2], 'description' => strip_tags($matches[3]));
  } else if ($engine == 'yahoo') {
    $serp = fetch('http://api.search.yahoo.com/WebSearchService/V1/webSearch?appid='.YAHOO_API.'&query='.urlencode($keyword).'&adult_ok=1&results='.$items.'&output=xml&start='.$offset);
  	preg_match_all("/<Title>(.+?)<\/Title>\s*(<Summary.+?)(\/>|<\/Summary>).*?<Url>(.+?)<\/Url>/ims", $serp, $matches);
  	$results = array('url' => $matches[4], 'title' => $matches[1], 'description' => strip_tags($matches[2]));
  } else if ($engine == 'live') {
    $serp = fetch('http://api.search.live.net/rss.aspx?query='.urlencode($keyword).'&source=web&Market=en-US&web.count='.$items.'&web.offset='.$offset);
    preg_match_all("/<item><title>(.+?)<\/title>.+?<link>(.+?)<\/link>(<description>(.+?)<\/description>)?.+?<\/item>/im", $serp, $matches);
  	$results = array('url' => $matches[2], 'title' => $matches[1], 'description' => $matches[4]);
  }
  
  if ($bare) {
    return $results;
  } else {
    for ($i=0;$i<$items;$i++) {
      echo <<<HTML
<h3>{$results['title'][$i]}</h3>
<p>{$results['description'][$i]}</p>
<p style="text-align:right;">
  <a href="{$results['url'][$i]}" rel="external nofollow">Read more&hellip;</a>
</p>
HTML;
    }
  }
}
?>