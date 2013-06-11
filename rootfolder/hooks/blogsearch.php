<?php
function cmp($a, $b) {
  list($a, $b) = array_map('strip_tags', array($a, $b));
  list($a, $b) = array_map('strlen', array($a, $b));
  if ($a == $b) return 0;
  return $a > $b ? -1 : 1;
}

function blogsearch($keyword = THIS_PAGE_KEYWORD, $engine = 'technorati', $articles = 1, $authority = "n") {  
  switch ($engine) {
    case 'google':
      $query = 'http://blogsearch.google.com/blogsearch?hl=en&q='.urlencode($keyword);
      $re = "/<a href=\"(http:\/\/[^\"]+?)\" id=\"p-1/ism";
      break;
    
    case 'bloglines':
      $query = 'http://www.bloglines.com/search?q='.urlencode($keyword).'&ql=en&s=fr&pop=l&news=m';
      $re = "/bl_cite_url=\"(http:\/\/.+?)\"/ism";
      break;
      
    case 'icerocket':
      $query = 'http://www.icerocket.com/search?tab=blog&q='.urlencode($keyword);
      $re = "/<a class=\"main_link\" href=\"(http:\/\/.+?)\"/ism";
      break;
      
    default:
      $query = 'http://technorati.com/search/'.urlencode($keyword).'?language=en&authority='.$authority.'&page=1';
      $re = "/<a\s+class=\"offsite\"\s+href=\"(http:\/\/.+?)\".+?>/ism";
      break;
  }
  
  $results = fetch($query);
  
  if (preg_match("/(There are no blog posts|There are no posts|did not match any documents|did not match with any results|We didn't find any posts)/ism", $results)) return false;
  
  if (preg_match_all($re, $results, $matches)) {
    for($i=0;$i<=$articles;$i++) {
      $temphtml = fetch($matches[1][$i]);
      
      $temphtml = preg_replace(array("/<head.*?>.*?<\/head>/ism", "/<style.*?>.*?<\/style>/ism", "/<script.*?>.*?<\/script>/ism", "/<noscript.*?>.*?<\/noscript>/ism", "/<select.*?>.*?<\/select>/ism", "/<object.*?>.*?<\/object>/ism"), "", $temphtml);
    
      $temphtml = preg_replace("/<\/?(div|table|tr|td|tbody|thead|tfoot|th).*?>/im", "~~", $temphtml);
    
      $contentblock = preg_split("/~~+/", $temphtml);
    
      usort($contentblock, "cmp");
    
      foreach ($contentblock as $block) {
        $tempblock = preg_replace("/<\/?(a|span|p|ul|li|dl|dd|dt|ol|menu|center|form|hr|h1|h2|h3|h4|h5|h6|pre|code|blockquote|address|abbr|acronym|b|base|big|br|del|em|font|i|ins|q|small|strike|strong|sub|sup|textarea).*?>/ism", "##", $block);
        if (preg_match("/\w[^#]{100,}\w/si", $tempblock)) {
          $content = strip_tags($block, "<br><i><em><strong><b><h1><h2><h3><h4><h5><h6><span><code><blockquote><q><p><ul><ol><li>");
          break;
        }
      }
      echo $content;
    }
  } else {
    return false;
  }
}
?>