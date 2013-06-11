<?php
function images($source = 'flickr', $items = 3, $keyword = THIS_PAGE_KEYWORD, $bare = false) {
  $output = array();
  
  if ($source == 'rand') {
    $sources = array('google', 'photobucket', 'yahoo', 'flickr');
    $source = $sources[array_rand($sources)];
  }
  
  switch($source) {
    case 'google':
      $url = 'http://images.google.com/images?hl=en&q='.urlencode($keyword).'&btnG=Search+Images&gbv=1';
      $re = "/href=\/imgres\?imgurl=(.+?)&.+?img src=([^\s]+?) width=(\d+) height=(\d+)/im";
      break;
    case 'photobucket':
      $url = 'http://feed.photobucket.com/images/'.urlencode($keyword).'/feed.rss';
      $re = "/<item>.+?<title>.+?<\/title>.+?<guid>(.+?)<\/guid>.+?<media:thumbnail url=\"(.+?)\" \/>.+?<\/item>/ims";
      break;
    case 'yahoo':
      $url = 'http://api.search.yahoo.com/ImageSearchService/V1/imageSearch?appid='.YAHOO_API.'&query='.urlencode($keyword).'&results='.$items;
      $re = "/<Result>.+?<ClickUrl>(.+?)<\/ClickUrl><RefererUrl>.+?<\/RefererUrl>.*?<Url>(.+?)<\/Url><Height>(.+?)<\/Height><Width>(.+?)<\/Width>.*?<\/Result>/ims";
      break;
    default:
      $url = 'http://www.flickr.com/search/?q='.urlencode($keyword).'&ct=0&mt=photos&z=t';
      $re = "/<img src=\"(http:\/\/\w+?.static.flickr.com\/\d+\/[\w]+?)_t.jpg\".+?alt=\"(.+?)\".+?class=\"pc_img\".+?\/>/";
      break;
  }
  
  $serp = loadcache($keyword.'.'.$source);
  
  if (!$serp) {
    $serp = fetch($url);
    savecache($serp, $keyword.'.'.$source);
  }

  if (preg_match_all($re, $serp, $images)) {
    
    for ($i=0;$i<$items;$i++) {
      switch($source) {
        case 'google':
          $output[$i]['image'] = $images[1][$i];
          $output[$i]['thumbnail'] = $images[2][$i];
          break;
        case 'photobucket':
          $output[$i]['image'] = $images[1][$i];
          $output[$i]['thumbnail'] = html_entity_decode($images[2][$i]);
          break;
        case 'yahoo':
          $output[$i]['image'] = $images[1][$i];
          $output[$i]['thumbnail'] = $images[2][$i];
          break;
        default:
          $output[$i]['image'] = $images[1][$i].'.jpg';
          $output[$i]['thumbnail'] = $images[1][$i].'_s.jpg';
          break;
      }
      
      $filename = urldecode(urldecode(basename($output[$i]['image'])));
      
      if (!file_exists(LOCAL_IMAGE_CACHE.$filename)) {
        $img_thumb = fetch($output[$i]['thumbnail']);
        file_put_contents(LOCAL_IMAGE_CACHE.'thumb_'.$filename, $img_thumb);
        $img = fetch(urldecode($output[$i]['image']));
        file_put_contents(LOCAL_IMAGE_CACHE.$filename, (strlen($img) < 524288 ? $img : $img_thumb));
      }
      
      $output[$i]['image'] = THIS_DOMAIN.str_replace('.', '', LOCAL_IMAGE_CACHE).$filename;
      $output[$i]['thumbnail'] = THIS_DOMAIN.str_replace('.', '', LOCAL_IMAGE_CACHE).'thumb_'.$filename;

      if (!$bare) echo '<a rel="nofollow" title="'.$keyword.'" href="'.$output[$i]['image'].'"><img src="'.$output[$i]['thumbnail']."\"></a>\n";
    }
    
    return $output;
  } else {
    return printerror("Nothing was found");
  }
}

function flickr($keyword = THIS_PAGE_KEYWORD, $items = 3, $bare = false) {
	return images('flickr', $items, $keyword, $bare);
}

function googleimg($keyword = THIS_PAGE_KEYWORD, $items = 3, $bare = false) {
  return images('google', $items, $keyword, $bare);
}

function photobucket($keyword = THIS_PAGE_KEYWORD, $items = 3, $bare = false) {
  return images('photobucket', $items, $keyword, $bare);
}

function yahooimg($keyword = THIS_PAGE_KEYWORD, $items = 3, $bare = false) {
  return images('yahoo', $items, $keyword, $bare);
}
?>