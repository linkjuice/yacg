<?php
function associatedcontent($keyword = THIS_PAGE_KEYWORD) {
  $serp = fetch('http://www.associatedcontent.com/subject/article/'.urlencode($keyword));
  
  preg_match_all("/<div\s+class=\"title\"><a href=\"http:\/\/www.associatedcontent.com\/article\/(\d+)\/[^\"]+?\">(.+?)<\/a><\/div>/ims", $serp, $articlelinks);

  //echo $articlelinks[2][0]; //title
  
  $article = fetch('http://www.associatedcontent.com/pop_print.shtml?content_type=article&content_type_id='.$articlelinks[1][0]);
  
  preg_match("/<div class=\"content_area\".+?>(.+?)<div class=\"content_footer\">/ims", $article, $content);
  
  //clean of self-promotion
  $content[1] = preg_replace(array(
    '/<div class=\"spacer_3\"><\/div>/ims', //just replacing standard spacer
    "/<!-+ (RESOURCES|TAKEAWAYS) BOX -+>.+?<!-+ END (RESOURCES|TAKEAWAYS) BOX -+>/ims" //no takeaways or additional references
    ), array('<br /><br />', ''), $content[1]);

  echo strip_tags($content[1], '<br><b><strong><span><i><p><ul><ol><li><h1><h2><h3><h4><h5><h6>');
}
?>