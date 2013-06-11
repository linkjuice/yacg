<?php // YOUTUBE VIDEO SCRAPER HOOK
function video($source ='youtube', $keyword = THIS_PAGE_KEYWORD, $width = '400', $height = '300', $params = '') {
  
  if ($source == 'rand') {
    $sources = array('vimeo', 'flickr', 'bliptv', 'viddler', 'youtube');
    $source = $sources[array_rand($sources)];
  }
  
  switch ($source) {
    case 'vimeo':
      $params = $params ? $params : '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1';
      $results = fetch('http://www.vimeo.com/videos/search:'.urlencode($keyword));
      if (preg_match('/<div class=\"title\">\s*?<a href=\"\/(\d+)"/ims', $results, $video)) {
        echo '<object width="'.$width.'" height="'.$height.'">	<param name="allowfullscreen" value="true" />	<param name="allowscriptaccess" value="always" />	<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$video[1].$params.'" />	<embed src="http://vimeo.com/moogaloop.swf?clip_id='.$video[1].$params.'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$width.'" height="'.$height.'"></embed></object>';
      } else return printerror('No videos found on '.$source);
      break;
    
    case 'flickr':
      $params = $params ? $params : '&flickr_show_info_box=true';
      $results = fetch('http://www.flickr.com/search/?q='.urlencode($keyword).'&ct=0&mt=videos');
      if (preg_match('/photo_id: \'(.+?)\'/im', $results, $video)) {
        echo '<object type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" data="http://www.flickr.com/apps/video/stewart.swf?v=1.161"
        classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"> <param name="flashvars" value="photo_id='.$video[1].$params.'"></param>
        <param name="movie" value="http://www.flickr.com/apps/video/stewart.swf?v=1.161"></param><param name="bgcolor" value="#000000"></param><param name="allowFullScreen" value="true"></param><embed type="application/x-shockwave-flash" src="http://www.flickr.com/apps/video/stewart.swf?v=1.161" bgcolor="#000000" allowfullscreen="true"
        flashvars="photo_id='.$video[1].$params.'" height="'.$height.'" width="'.$width.'"></embed></object>';
      }	else return printerror('No videos found on '.$source);
      break;
      
    case 'bliptv':
      $results = fetch('http://blip.tv/?search='.urlencode($keyword).';page=1;s=search&skin=rss');
      preg_match_all('/<blip:posts_id>(\d+)<\/blip:posts_id>/im', $results, $videos);
      foreach($videos[1] as $file) {
        $results = fetch('http://blip.tv/rss/flash/'.$file);
        if (preg_match("/http:\/\/blip.tv\/play\/(.+?)[\"']/im", $results, $video)) {
          echo '<embed src="http://blip.tv/play/'.$video[1].'" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowscriptaccess="always" allowfullscreen="true"></embed>';
          return true;
        }
      }
      return printerror('No videos found on '.$source);
      break;
      
    case 'viddler':
      $results = fetch('http://www.viddler.com/search_detailed/?searchString='.urlencode($keyword));
      if (preg_match('/<img class=\"vdi-thumb\".+?src=\"http:\/\/.+?\/thumbnail_2_(.+?)\.jpg"/im', $results, $video)) {
        echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$width.'" height="'.$height.'" id="viddler"><param name="movie" value="http://www.viddler.com/player/'.$video[1].'/" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="wmode" value="transparent"/><embed src="http://www.viddler.com/player/'.$video[1].'/" width="'.$width.'" height="'.$height.'" type="application/x-shockwave-flash" allowScriptAccess="always" allowFullScreen="true" wmode="transparent" name="viddler" ></embed></object>';
      }	else return printerror('No videos found on '.$source);
      break;
      
    default:
      $params = $params ? $params : '&color1=0xb1b1b1&color2=0xcfcfcf&fs=1';
      $results = fetch('http://www.youtube.com/rss/tag/'.urlencode($keyword).'.rss');
      if (preg_match('/<enclosure url=\"(.*)\.swf/im', $results, $video)) {
        echo '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="'.$video[1].$params.'"></param><param name="allowFullScreen" value="true"></param><embed src="'.$video[1].$params.'" type="application/x-shockwave-flash" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed></object>';
      }	else return printerror('No videos found on '.$source);
      break;
  }
}
?>