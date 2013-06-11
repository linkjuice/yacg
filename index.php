<?php
header('Content-Type: text/html;charset=utf-8');
//GETTING/SETTING CONFIGURATION OPTIONS
require_once './config.inc.php';
//SITE DOMAIN
define('THIS_DOMAIN', 'http://'.str_replace(array('www.', '/index.php'), '', $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']));
//ABSOLUTE URL OF THE CURRENT PAGE
define('THIS_PAGE_URL', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

error_reporting(DEBUG ? E_ALL^E_NOTICE : 0);
clearstatcache();

//REQUIRE MAIN HOOK
require_once LOCAL_HOOKS.'main.php';

//UTF-8 SETTINGS
if (UTF) {
  require_once ROOT_DIR.'includes/utf8/utf8.php';
  require_once UTF8.'/utils/ascii.php';
  require_once UTF8.'/utf8_to_ascii.php';
  define('UTFRE', 'u');
} else {
	define('UTFRE', '');
}

if (DEBUG) {
  //PERMISSIONS CHECK
  if (PHP_SHLIB_SUFFIX != 'dll') foreach (array('./config.inc.php', './feed.xml', './sitemap.xml', ROOT_DIR, LOCAL_CACHE, LOCAL_IMAGE_CACHE, FILE_KEYWORDS) as $file) perm($file);
  //START TIMER
  $start_time = gettime();
}

//BUILD CATEGORIES LIST ON FIRST LAUNCH
if (CATEGORIES && !file_exists(FILE_CATEGORIES)) {
  $categories = array();
  $keyhandle = fopen(FILE_KEYWORDS, "r");
  
  $data = fgetcsv($keyhandle, 1000, ",");
  $categories[] = $data[0]."\n";
  
  if (count($data) > 1) {
    while (($data = fgetcsv($keyhandle, 1000, ",")) !== false) if (isset($data[1])) $categories[] = $data[0]."\n";
    $categories = array_unique($categories);
  } else {
    $keywords = array();
    $catcount = CAT_NUM ? CAT_NUM : rand(5, 15);
    for($i=0;$i<$catcount-1;$i++) $categories[] = fgets($keyhandle, 1000);
    while (!feof($keyhandle)) $keywords[] = rtrim($categories[rand(0, $catcount-1)]).','.fgets($keyhandle, 1000);
    file_put_contents(FILE_KEYWORDS, implode('', $keywords));
    @chmod(FILE_KEYWORDS, FILEMODE);
  }
  
  fclose($keyhandle);
  
  $categories = rtrim(implode('', $categories));
  file_put_contents(FILE_CATEGORIES, $categories);
  if (UTF) file_put_contents(FILE_CATEGORIES_TR, utf8_to_ascii($categories));
  
  //FIND PREVIEW_HOOK
  if (FIND_PREVIEW_HOOK) {
    $page = file_get_contents(LOCAL_TEMPLATE.'page.php');
    if (preg_match("/cache(\s*[\"'](.+?)[\"'])/ims", $page, $previewhook)) {
      $config = file_get_contents('./config.inc.php');
      $config = preg_replace("/^define\(\s*?'PREVIEW_HOOK'\s*?,\s*'(.+?)'\s*?\);(.*?)$/im", "define('PREVIEW_HOOK', '".$previewhook[1]."');\\2", $config);
      file_put_contents('./config.inc.php', $config);
    }
  }
}

//ADD KEYWORDS
if (START_KEYS && filemtime('./config.inc.php') < time() - UPDATE_INTERVAL) {
  $newkeys = rand(NEW_MIN, NEW_MAX);
  
  $config = file_get_contents('./config.inc.php');
  $config = preg_replace("/^define\(\s*?'START_KEYS'\s*?,\s*'(.+?)'\s*?\);(.*?)$/im", "define('START_KEYS', '".(START_KEYS+$newkeys)."');\\2", $config);
  file_put_contents('./config.inc.php', $config);
  
  if (CACHE && CACHE_AUTO) for($i=count($keyarr)-1;$i>count($keyarr)-$newkeys;$i--) fetch(k2url($keyarr[$i]));
}

$catarr = @array_map('rtrim', @file(FILE_CATEGORIES));
if (UTF && CATEGORIES) $catarrtr = @array_map('rtrim', @file(FILE_CATEGORIES_TR));
$keyarr = array();
$category = false;

//GET IMPORTANT PARTS OF THE URL
$urlparts = explode("?", THIS_PAGE_URL);
$urlparts = array_values(array_map('urldecode', array_filter(explode('/', rmdashes(str_replace(array('www.', THIS_DOMAIN, PERMALINK, FILE_EXT), '', $urlparts[0]))))));
$parts = count($urlparts);

//BUILD $keyarr AND DETERMINE IF ANY PAGE IS BEING ACCESSED
$keyhandle = fopen(FILE_KEYWORDS, "r");
$i = 0;
while($i < START_KEYS && ($data = fgetcsv($keyhandle, 1000, ",")) !== false) {
  $keyarr[] = implode(',', $data);
  
  if (UTF) $data[(isset($data[1]) ? 1 : 0)] = utf8_to_ascii($data[(isset($data[1]) ? 1 : 0)]);
  
  if ($parts == 1 && strcasecmp($urlparts[0], (isset($data[1]) ? $data[1] : $data[0])) == 0) {
    define('THIS_PAGE', 'page.php');
    define('THIS_PAGE_KEYWORD', cut_cat($keyarr[$i]));
    $category = isset($data[1]) ? $data[0] : 'Uncategorized';
  }
  
  $i++;
}
fclose($keyhandle);

//IF IT'S NOT A KEYWORD PAGE
if (!defined('THIS_PAGE')) {
  //IT'S THE HOMEPAGE
  if ($parts == 0) {
    define('THIS_PAGE', 'index.php');
    define('THIS_PAGE_KEYWORD', SITE_NAME);
  }
  //IT'S A CATEGORY PAGE
  elseif ($parts == 2 && $urlparts[0] == 'category' && ($i = array_isearch($urlparts[1], (UTF ? $catarrtr: $catarr))) !== false) {
    define('THIS_PAGE', 'category.php');
    define('THIS_PAGE_KEYWORD', 'Category');
    $category = $catarr[$i];
  }
  //IT'S AN ARCHIVE PAGE
  elseif ($parts == 2 && preg_match("/^\d+$/", implode('', $urlparts))) {
    define('THIS_PAGE', 'archive.php');
    define('THIS_PAGE_KEYWORD', 'Archive');
    $category = strtotime($urlparts[0].'-'.$urlparts[1].'-01');
  }
  //IT'S SOME PAGE FROM $pages ARRAY
  elseif ($parts == 1 && ($i = array_isearch(adashes($urlparts[0]), $pages)) !== false) {
    define('THIS_PAGE', adashes($pages[$i].'.php'));
    define('THIS_PAGE_KEYWORD', ucfirst($pages[$i]));
  }
  //IT'S 404 ERROR PAGE
  else {
    header("HTTP/1.0 404 Not Found");
    define('THIS_PAGE', '404.php');
    define('THIS_PAGE_KEYWORD', 'Page not found');
  }
}

define('THIS_PAGE_CATEGORY', $category);

//LOAD CACHED PAGE
$cachefile_path = LOCAL_CACHE.str_replace(' ', '-', THIS_PAGE_KEYWORD).'.html';
if (CACHE && THIS_PAGE == 'page.php' && file_exists($cachefile_path) && (time() - CACHE_TIME < filemtime($cachefile_path))) {  
  echo @file_get_contents($cachefile_path);
  if (DEBUG) echo "\n".'<!-- Cached on '.date('F jS, Y H:i', filemtime($cachefile_path)).' -->';
}
//OR GENERATE PAGE
else {
  //LOAD HOOKS
  require_once ROOT_DIR.'includes/simplepie.inc';
  if (PICK_HOOKS) {
    foreach ($hooks as $hook) require_once LOCAL_HOOKS.$hook;
  } elseif ($dh = @opendir(LOCAL_HOOKS)) {
    while (($file = readdir($dh)) !== false) if (substr($file, -4) == '.php') require_once LOCAL_HOOKS.$file;
    closedir($dh);
  }
  
  //LOAD TEMPLATE
  ob_start('indenter');
  require_once LOCAL_TEMPLATE.THIS_PAGE;
  //SAVE THE OUTPUT
  if (CACHE && THIS_PAGE == 'page.php') file_put_contents($cachefile_path, (INDENT == false ? ob_get_contents() :indenter(ob_get_contents())));

  if (DEBUG) echo "\n".'<!-- Generated in '.(gettime() - $start_time).' seconds -->';
  ob_flush();
}

//PING, UPDATE RSS FEED AND SITEMAP
if (START_KEYS && filemtime('./sitemap.xml') < time() - UPDATE_INTERVAL) {
  ping();
  generatefeed();
  generatesitemap();
}
?>