<?php
require_once("functions.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>[YACG] Yet Another Content Generator v. <?php echo $version ?></title>
<style type="text/css">
<!--
body {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:0.8em;}
div.menu {float:left;width:300px;}
div.result {float:right;width:470px;border:1px dashed #ccc;padding:3px;}

a {text-decoration:none;color:#0000FF}
a:hover {text-decoration:underline;color:#0000FF}
a:visited {color:#0000FF}
.container {margin: 0 auto; width: 780px}
-->
</style>
</head>
<body>
<div class="container">
<h1 style="text-align:center">[YACG] Yet Another Content Generator v. <?php echo $version ?> </h1>
<div class="menu">
  <h3>Operations:</h3>
  <ul>
    <li><h3><a href="index.php">Edit config.inc.php</a></h3></li>
    <li><h3><a href="index.php?action=cacheclean">Clean Cache</a></h3></li>
    <li><h3><a href="index.php?action=feed">Generate RSS Feed</a></h3></li>
    <li><h3><a href="index.php?action=sitemap">Generate XML Sitemap</a></h3></li>
    <li><h3><a href="index.php?action=ipupdate">Update Bot IP List</a></h3></li>
    <li><h3><a href="index.php?action=ping">Send Ping to Ping-o-Matic</a></h3></li>
    <li><h3><a href="index.php?action=keywordclean">Clean Keywords.txt</a></h3></li>
    <li><h3><a href="index.php?action=logout">Logout</a></h3></li>
  </ul>
</div>
<div class="result">
  <?
  switch ($_REQUEST['action']) {
    case 'config':
      $config = file_get_contents('config.inc.php');
      foreach ($_POST as $key => $value) {
        $value = preg_match("/^(false|true|\d+)$/", $value) ? $value : "'".$value."'";
        $config = preg_replace("/^define\(\s*?'".$key."'\s*?,\s*(.+?)\s*?\);(.*?)$/im", "define('$key', $value);\\2", $config);
      }
      file_put_contents('config.inc.php', $config);
      break;
  
    case 'cacheclean':
      $cachedir = realpath(LOCAL_CACHE);
      if ($handle = opendir($cachedir)) {
        while (false !== ($file = readdir($handle)))
          if ($file != "." && $file != ".." && $file != "index.php") unlink($cachedir."/".$file);
        closedir($handle);
      }
      echo "All files in the cache folder were deleted.";
      break;
    
    case 'keywordclean':
      if (isset($_REQUEST['save'])) {
        if (isset($_POST['keywords'])) {
          $keywords = $_POST['keywords'];
        } else {
          list($clean, $bad) = cleankeys($keyarr);
          $keywords = implode("\n", $clean);
        }
        file_put_contents($file, $keywords);
        echo "Your <strong>keywords.txt</strong> file has been succesfully cleaned.";
      } else {
        list($clean, $bad) = cleankeys($keyarr);
        ?>
        <form name="saveform" method="post" action="index.php">
          <input type="hidden" name="action" value="keywordclean" />
          <input type="hidden" name="save" value="yes" />
          <input type="hidden" name="keywords" value="<?php echo implode("\n", $clean) ?>" />
          <input type="submit" name="Submit" value="Save clean keywords.txt" />
        </form>
        <h4>Will be deleted:</h4>
        <?php foreach($bad AS $keyword) echo '<span style="color:red">'.$keyword.'</span><br>'; ?>
        <h4>Will be saved:</h4>
        <?php foreach($clean AS $keyword) echo '<span>'.$keyword.'</span><br>';
      }
      break;
    
    case 'ipupdate':
      $lists = array(
      'http://labs.getyacg.com/spiders/google.txt',
      'http://labs.getyacg.com/spiders/inktomi.txt',
      'http://labs.getyacg.com/spiders/lycos.txt',
      'http://labs.getyacg.com/spiders/msn.txt',
      'http://labs.getyacg.com/spiders/altavista.txt',
      'http://labs.getyacg.com/spiders/askjeeves.txt',
      'http://labs.getyacg.com/spiders/wisenut.txt',
      );
      $opt = '';
      foreach($lists as $list) $opt .= fetch($list)."\n";
      $opt = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $opt);
      file_put_contents(FILE_BOTS, $opt);
      echo "Your <strong>Bot List</strong> has been updated.";
      break;
    
    case 'ping':
      ping();

      echo "<strong>Ping</strong> has been sent.";
      break;
    
    case 'feed':
      generatefeed();
      
      echo "Your <strong>feed</strong> has been generated.";
      break;
    
    case 'sitemap':
      generatesitemap();
      
      echo "Your <strong>sitemap.xml</strong> has been generated.";
      break;
  
    default:
      $config = file_get_contents('config.inc.php');
      $configmod = substr(sprintf('%o', fileperms('config.inc.php')), -4);
      if ($configmod !== '0777' && $configmod !== '0755') {
        echo '<span style="color:red">Your <strong>config.inc.php</strong> file has <strong>'.$configmod.'</strong> file permissions and most likely won\'t be writeable by this script. Change permissions to <strong>777</strong> to make sure that changes made here will be saved.</span>';
      }
      $skipoptions = array('PICK_HOOKS', 'LOCAL_CACHE', 'LOCAL_ARTICLES', 'LOCAL_TEMPLATE', 'LOCAL_HOOKS', 'FILE_BOTS', 'FILE_KEYWORDS', 'FILE_KEYWORDS_TMP', 'FILE_CATEGORIES','FILE_KEYWORDS_TR', 'FILE_CATEGORIES_TR');
      preg_match_all("/^define\(\s*?'(\w+?)'\s*?,\s*(.+?)\s*?\);(.*?)$/im", $config, $matches);
      ?>
      <form action="index.php" method="post">
        <input type="hidden" name="action" value="config" id="action">
        <table width="100%" style="text-align:right;">
          <tr>
            <td colspan="2" style="text-align:center;"><h3>Site config:</h3></td>
          </tr>
          <?php
          for ($i=0; $i < count($matches[0]); $i++) {
            if (!in_array($matches[1][$i], $skipoptions)) {
              echo '<tr>
                <td width="30%"><label title="'.trim(str_replace('//', '', $matches[3][$i])).'">'.$matches[1][$i].': </label></td>
                <td width="70%" style="text-align:left;"><input title="'.trim(str_replace('//', '', $matches[3][$i])).'" type="text" name="'.$matches[1][$i].'" value="'.preg_replace("/(^'|'$)/", '', $matches[2][$i]).'" style="width:200px;"/></td>
              </tr>';
            }
          }
          ?>
          <tr>
            <td></td>
            <td style="text-align:left;"><input type="submit" name="Save" value="Save Config" id="Save"></td>
          </tr>
        </table>
      </form>
      <?php
      break;
  }
  ?>
</div>
<br style="clear:both" />
</div>
</body>
</html>