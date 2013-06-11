<?php // PEAKCLICK HOOK
function peakclick($keyword = THIS_PAGE_KEYWORD, $items = 5, $thumbs = true, $bare = false) {
  if (SHOW_ADS) {
    $ip = (DEBUG && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') ? gethostbyname('www.google.com') : $_SERVER['REMOTE_ADDR'];
    $ads = fetch('http://feed.peakclick.com/res.php?aff='.PEAKCLICK_AFF.'&subaff='.PEAKCLICK_SUBAFF.'&ref='.THIS_DOMAIN.'&keyword='.urlencode($keyword).'&num='.$items.'&ip='.$ip.'&ua='.urlencode($_SERVER['HTTP_USER_AGENT']).($thumbs ? '&thumbs=1' : ''));
    
    $output = array();
    $ads = explode("\n", $ads);
    if (strstr($ads[0], 'ERROR:')) {
      return printerror($ads[0]);
    } else {
      foreach($ads as $ad) {
        $ad = explode('|', $ad);
        if ($bare) {
          $output[] = array('title' => $ad[1], 'description' => $ad[2], 'link' => $ad[3], 'afflink' => $ad[4]);
        } else {
          echo <<<HTML
<h3><a href="{$ad[4]}">{$ad[1]}</a></h3>
<p>{$ad[2]}</p>
<a href="{$ad[4]}">{$ad[3]}</a>      
HTML;
        }
      }
      return $output;
    }
  }
}
?>