<?php // SOCIAL BOOKMARKING HOOK
function bookmarking() {
  echo <<<HTML
  <!-- AddThis Button BEGIN -->
  <script type="text/javascript">addthis_pub  = '';</script>
  <a href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()"><img src="http://s7.addthis.com/button1-share.gif" width="125" height="16" border="0" alt="" /></a><script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
  <!-- AddThis Button END -->
HTML;
}