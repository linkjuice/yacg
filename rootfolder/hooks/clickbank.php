<?php
function cb($keyword = THIS_PAGE_CATEGORY, $numresults = 3, $gendescr = true, $thumbs = true, $bare = false, $cbcategory = '-1', $cbsubcategory = '-1') {
  if (SHOW_ADS) {
    $result = array();

    $response = fetch('http://www.clickbank.com/marketplace.htm', 'method=Sort&c='.$cbcategory.'&subc='.$cbsubcategory.'&keywords='.urlencode($keyword).'&sortBy=popularity&billingType=ALL&locale=ALL&i=10');

    preg_match_all("/<\/b>\s+<a class=\"siteHeader\"[^>]+?href=\"(.+?)\">([^<]+?)<\/a>\s+?<\/b>\s+?(.+?)\s+?<br>/ims", $response, $matches);

    for($i=0;$i<$numresults;$i++) {
      $result[$i]['url'] = str_replace('zzzzz', CLICKBANK_ID, $matches[1][$i]).'/?tid='.THIS_DOMAIN;
      $result[$i]['title'] = $matches[2][$i];
      $result[$i]['description'] = $matches[3][$i];
      $result[$i]['thumbnail'] = '';
      if ($thumbs || $gendescr) {
        $ch = curl_init();
      	curl_setopt($ch, CURLOPT_URL, $matches[1][$i]);
      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
      	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
      	if (!ini_get('open_basedir') && !ini_get('safe_mode')) {
      		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
      	}
      	if (PROXY) curl_setopt($ch, CURLOPT_PROXY, PROXY_IP.":".PROXY_PORT);
      	$salespage = curl_exec($ch);
        if ($thumbs) {
            if (preg_match("/http:\/\/(.+?)[\?\/].*?hop=0/im", curl_getinfo($ch, CURLINFO_EFFECTIVE_URL), $location)) {
              $thumb = googleimg($location[1], 1, true);
              $result[$i]['thumbnail'] = $thumb[0]['thumbnail'];
              
            }
          
        }
        curl_close($ch);
        
        if ($gendescr) {
          if (preg_match("/<meta[^>]+?[\"']description[\"'][^>]+?content=[\"]([^\"]+?)[\"]/im", $salespage, $description) && strlen($description[1]) > 150) {
            $result[$i]['description'] = $description[1];
          } elseif (preg_match_all("/<h(\d).*?>(.+?)<\/h\d>/im", $salespage, $headers) && count($headers[2]) > 2) {
            $result[$i]['description'] = '';
            foreach($headers[2] as $header) {
              $result[$i]['description'] .= ' '.$header;
              if (strlen($result[$i]['description']) > 500) break;
            }
          }
        }
      }
    }

    if (!$bare) {
      foreach($result as $ad) {
        echo '<div style="margin:5px"><a href="'.$ad['url'].'">'.$ad['title'].'</a><br />';
        if ($thumbs) echo '<div style="float:left;margin:5px"><a href="'.$ad['url'].'"><img src="'.$ad['thumbnail'].'" /></a></div>';
        echo $ad['description'].'<br style="clear:both"></div>';
      }
      $result = true;
    }

    return $result;
  }
}
?>