<?php // UMAX HOOK
function umax($keyword = THIS_PAGE_KEYWORD, $items = 5, $bare = false) {
  if (SHOW_ADS) {
    $ads = fetch('http://xml.umaxfeed.com/xmlfeed.php?aid='.UMAX_AFF.'&qr='.$items.'&said='.UMAX_SUBAFF.'&ip='.$_SERVER['REMOTE_ADDR'].'&q='.urlencode($keyword).'&ref='.urlencode(THIS_PAGE_URL).'&l='.(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? rawurlencode($_SERVER['HTTP_ACCEPT_LANGUAGE']) : '').'&grw=0&qpw=0&t=txt&auth='.UMAX_AUTH);
    
    $ads = explode("\n", $ads);
    $output = array();
    if ($ads[2] == '') {
      return printerror('Nothing was found');
    } else {
      foreach($ads as $ad) {
        if ($ad[1] != '') {
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
      }
      return $output;
    }
  }
}
?>