<?php // DIGG DESCRIPTION SCRAPER HOOK
function digg($keyword = THIS_PAGE_KEYWORD, $items = 5) {
  getrss('http://digg.com/rss_search?search=[THIS_PAGE_KEYWORD]&area=all&type=both&age=all&section=news', $items);
}
?>