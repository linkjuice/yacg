<?php
function getrss($url = 'http://digg.com/rss_search?search=[THIS_PAGE_KEYWORD]&area=all&type=both&age=all&section=news', $items = 5, $title = false, $description = true, $url = false, $keyword = THIS_PAGE_KEYWORD, $bare = false) {
  $feed = new SimplePie(str_replace('[THIS_PAGE_KEYWORD]', urlencode($keyword), $url));
  $feed->handle_content_type();
  
  $results = array();
  $i = 0;
  foreach($feed->get_items() as $item) {
    if ($bare) {
      $results[] = array('title' => $item->get_title(), 'description' => $item->get_description(), 'url' => $item->get_permalink());
    } else {
      if ($title) echo '<h3>'.$item->get_title().'</h3>';
      if ($description) echo '<p>'.$item->get_description().'</p>';
      if ($url) echo '<p style="text-align:right;"><a href="'.$item->get_permalink().'" rel="external nofollow">Read more...</a>';
    }
    $i++;
    if ($i > $items) break;
  }
  
  return $results;
}
?>