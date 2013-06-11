<?php // BASIC FUNCTIONS
function gettime() {
  $time = explode(' ', microtime());
  return $time[1] + $time[0];
}

//CASE-INSESITIVE ARRAY_SEARCH
function array_isearch($str, $array) {
  foreach($array as $k => $v) if (strcasecmp($str, $v) == 0) return $k;
  return false;
}

//DEFINE 'FILE_PUT_CONTENTS' FOR PHP4
if (!function_exists('file_put_contents')) {
  function file_put_contents($filename, $data) {
    $f = @fopen($filename, 'w+');
    if (!$f) {
      return false;
    } else {
      $bytes = fwrite($f, $data);
      fclose($f);
      return $bytes;
    }
  }
}

//CLEAN KEYWORD LIST
function cleankeys($keywords, $invalid = true, $titlecase = true, $badkeywords = false) {
  $keywords = array_map('trim', array_unique($keywords));
  $clean = array();
  $bad = array();
  foreach ($keywords as $key) {
    if ($badkeywords) {
      foreach ($badkeywords as $nasty) {
        if (stristr($key, $nasty)) {
          array_push($bad, $key);
          continue 2;
        }
      }  
    }
    if($invalid){
      $re = UTF ? "/[^\p{L}\p{N}\w\s\',]/u" : "/[^\w\s\',]/";
      if (preg_match($re, $key)) {
        array_push($bad, $key);
        continue;
      }
    }
    if($titlecase) {
      $key = UTF ? utf8_ucwords(utf8_strtolower($key)) : ucwords(strtolower($key));
      $re = UTF ? "/,([\p{L}\p{N}])/eu" : "/,(\w)/e";
      $replacement = UTF ? "','.utf8_strtoupper('\\1')" : "','.strtoupper('\\1')";
      $key = preg_replace($re, $replacement, $key);
    }
    array_push($clean, $key);
  }
  
  return array($clean, $bad);
}

//GENERATE FEED
function generatefeed() {
  global $keyarr;
  
  $fh = @fopen('./feed.xml', 'w');
  
  fwrite($fh, '<?'.'xml version="1.0" encoding="UTF-8" '.'?>
  <rss version="2.0">
  <channel>
  <title>'.SITE_NAME.'</title>
  <link>'.THIS_DOMAIN.'/</link>
  <description><![CDATA['.SITE_DESCRIPTION.']]></description>
  <language>en-us</language>
  <pubDate>'.date("D, d M Y H:i:s T").'</pubDate>
  <lastBuildDate>'.date("D, d M Y H:i:s T").'</lastBuildDate>
  <ttl>5</ttl>');
  
  $i = 0;
  foreach($keyarr as $key) {
    fwrite($fh, "\n<item>
    <title>".cut_cat($key)."</title>
    <link>".k2url($key)."</link>
    <description><![CDATA[");
    if (PREVIEW_HOOK != 'markov') {
      fwrite($fh, substr(strip_tags(cache(PREVIEW_HOOK, array(cut_cat($key)), cut_cat($key), true)), 0, 300)."...]]></description><pubDate>".date("D, d M Y H:i:s T", ktime($key, PREVIEW_HOOK)).'</pubDate>');
    } else {
      fwrite($fh, cache('markov', array(5, 50, 65), cut_cat($key), true)."]]></description>
      <pubDate>".date("D, d M Y H:i:s T").'</pubDate>');
    }
    fwrite($fh, "\n<guid isPermaLink=\"false\">".k2url($key).'</guid>
    </item>');
    
    $i++;
    if ($i == 20) break;
  }
  
  fwrite($fh, "\n</channel>
  </rss>");

  fclose($fh);
}

//GENERATE SITEMAP
function generatesitemap() {
  global $keyarr, $pages;
  
  $fh = @fopen('./sitemap.xml', 'w');
  
  fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
  foreach ($pages as $page) {
    fwrite($fh, "\n<url>
    <loc>".THIS_DOMAIN.'/'.$page.'</loc>
    <lastmod>'.date("Y-m-d").'</lastmod>
    </url>');
  }
  foreach ($keyarr as $keyword) {
    fwrite($fh, "\n<url>
    <loc>".k2url($keyword).'</loc>
    <lastmod>'.date("Y-m-d").'</lastmod>
    </url>');
  }
  fwrite($fh, "\n</urlset>");
  
  fclose($fh);
}

//SEND A PING
function ping($url = THIS_DOMAIN, $sitename = SITE_NAME) {
  fetch('http://pingomatic.com/ping/?title='.urlencode($sitename).'e&blogurl='.urlencode($url).'&rssurl='.urlencode($url).'%2Ffeed.xml&chk_weblogscom=on&chk_blogs=on&chk_technorati=on&chk_feedburner=on&chk_syndic8=on&chk_newsgator=on&chk_myyahoo=on&chk_pubsubcom=on&chk_blogdigger=on&chk_blogrolling=on&chk_blogstreet=on&chk_moreover=on&chk_weblogalot=on&chk_icerocket=on&chk_newsisfree=on&chk_topicexchange=on&chk_google=on&chk_tailrank=on&chk_bloglines=on&chk_aiderss=on&chk_skygrid=on&chk_bitacoras=on&chk_collecta=on');
}

//RETURN GENERATED CONTENT INSTEAD OF PRINTING IT
function returntext($function = 'markov', $args = array()) {
  ob_start();
  call_user_func_array($function, $args);
  $contents = ob_get_contents();
  ob_end_clean();
  return $contents;
}

//TRIM OUTPUT
function getcontent($function = 'markov', $args = array(), $min = 100, $max = 3000) {
  $content = returntext($function, $args);
  return (strlen($content) > $min ? substr($content, 0, $max) : false);
}

//SELECTIVE CACHING FUNCTION
function cache($function = 'markov', $args = array(), $keyword = THIS_PAGE_KEYWORD, $return = false) {
  $cachedfilename = LOCAL_CACHE.adashes($keyword).'.'.$function;
  $content = '';
  if (file_exists($cachedfilename) && (time() - CACHE_TIME < filemtime($cachedfilename))) {
    $content = file_get_contents($cachedfilename);
  } elseif ($args !== false) {
    $content = returntext($function, $args);
    file_put_contents($cachedfilename, $content);
  }
  //return or print
  if ($return)
    return $content;
  else
    echo $content;
}

//CACHE HANDLING
function savecache($data, $filename) {
  if (CACHE) {
    file_put_contents(LOCAL_CACHE.adashes($filename), $data);
    return true;
  } else {
    return false;
  }
}

function loadcache($filename) {
	$file_path = LOCAL_CACHE.adashes($filename);
	if (CACHE && file_exists($file_path) && (time() - CACHE_TIME < filemtime($file_path))) {
		$cache = @file_get_contents($file_path);
		return $cache;
	}	else {
		return false;
	}
}

//PERMISSION CHECK
function perm($path) {
	clearstatcache();
	if (file_exists($path)) {
  	$configmod = substr(sprintf('%o', fileperms($path)), -4);
  	if ($configmod !== '0777' && $configmod !== '0755' && DEBUG) die('<strong>Error - Please chmod correctly your files</strong>'."($path)");
  }
}

//FETCH CONTENT
function fetch($url, $postdata = false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
	if (!ini_get('open_basedir') && !ini_get('safe_mode')) {
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	}
	if ($postdata) {
	  curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	}
	if (PROXY) curl_setopt($ch, CURLOPT_PROXY, PROXY_IP.":".PROXY_PORT);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}
//fetch from google cache
function fetchcache($url, $textonly = false) {
  $text = $textonly ? '&strip=1' : '';
  $response = fetch('http://google.com/search?q=cache:'.$url.$text, false);
  $response = strstr($response, '&copy;'.date('Y').' Google') ? false : $response;
  return $response;
}

//ADD KEYWORD(S)
function add_keyword($keyword = '') {
  global $catarr, $keyarr;
  $keyword = is_array($keyword) ? $keyword : array($keyword);
  $keynum = count($keywords);
  $keyhandle = fopen(FILE_KEYWORDS, "a");
  for ($i=0; $i < $keynum; $i++) {
    $newkey = preg_replace("/[^\w\d]/", ' ', $keyword[$i]);
    fwrite($fh, CATEGORIES ? $catarr[rand(0, CAT_NUM-1)].','.$newkey."\n" : $newkey."\n");
  }
  fclose($keyhandle);
}

//KEYWORD TRANSFORMATION
function rmdashes($value = '') {
  return str_replace("-", " ", $value);
}

function adashes($value = '') {
  return str_replace(" ", "-", $value);
}

function k2url($keyword = '') {
  $keyword = $keyword == '' ? THIS_PAGE_CATEGORY.','.THIS_PAGE_KEYWORD : $keyword;
  $keyword = array_map('adashes', explode(",", $keyword));
  if (UTF) $keyword = array_map('utf8_to_ascii',$keyword);
  
	return strtolower(THIS_DOMAIN.'/'.PERMALINK.$keyword[count($keyword)-1].FILE_EXT);
}

function c2url($category = THIS_PAGE_CATEGORY) {
  $category = explode(",", $category);
  $category = adashes($category[0]);
  if (UTF) $category = utf8_to_ascii($category);
  
  return strtolower(THIS_DOMAIN.'/category/'.$category);
}

//KEYWORD PUBLICATION TIME
function ktime($keyword = THIS_PAGE_KEYWORD, $hook = PREVIEW_HOOK) {
  $filename = LOCAL_CACHE.adashes(cut_cat($keyword)).'.'.$hook;
  if (!file_exists($filename)) $filename = LOCAL_CACHE.adashes(cut_cat($keyword)).'.html';
  
  return file_exists($filename) ? filemtime($filename) : filemtime(FILE_KEYWORDS);
}

//CATEGORIES HANDLING
function split_key($line) {
  return CATEGORIES ? explode(',', $line) : array('', $line);
}

function cut_cat($line) {
  return preg_replace("/^(.*?,)/", "", $line);
}

function strip_cats($lines) {
  return array_map('cut_cat', $lines);
}

function cut_key($line) {
  return preg_replace("/(^(.+?),.+?$|^[^,]*?$)/", "$2", $line);
}

function strip_keys($lines) {
  return array_unique(array_map('cut_key', $lines));
}

/**** BUILDING DIFFERENT ARRAYS OF LINKS ****/
//LIST OF ALL PAGES
function pages($ul = false) {
  global $pages;
  
  if ($ul) echo "<ul>";
  foreach ($pages as $page) echo '<li><a href="'.THIS_DOMAIN.'/'.$page.'" title="'.ucwords(rmdashes($page)).'">'.ucwords(rmdashes($page))."</a></li>\n";
  if ($ul) echo "</ul>";
}

//LIST OF ALL CATEGORIES
function categories($ul = false) {
  global $categories, $catarr;
  
  if ($ul) echo "<ul>\n";
  for ($i=0; $i < count($catarr); $i++) echo '<li><a href="'.c2url($catarr[$i]).'" title="'.$catarr[$i].'">'.$catarr[$i]."</a></li>\n";
  if ($ul) echo "\n</ul>";
}

//LIST OF ALL KEYS IN A CATEGORY
function list_category($bare = false, $cat = THIS_PAGE_CATEGORY, $preview = PREVIEW_HOOK) {
  global $keyarr;
  
  $links = array();
  foreach ($keyarr as $key) {
    if (preg_match("/^".preg_quote($cat, "/").",/i".UTFRE, $key)) {
      $pre = '';
      if (file_exists(LOCAL_CACHE.adashes(cut_cat($key)).".".$preview))
        $pre = substr(strip_tags(@file_get_contents(LOCAL_CACHE.adashes(cut_cat($key)).".".$preview)), 0, 300);
      $links[] = array('key' => cut_cat($key), 'url' => k2url($key), 'timestamp' => ktime($key), 'preview' => $pre);
    }
  }
  
  if ($bare) {
    return $links;
  } else {
    echo "<ul>\n";
    foreach ($links as $link) {
      echo '<li><a href="'.$link['url'].'" title="'.$link['key'].'">'.$link['key']."</a>";
      if ($link['preview'] != '') echo "<p>".$link['preview']."...</p>";
      echo "</li>\n";
    }
    echo "\n</ul>\n";
  }
}

//LIST LINKS TO ARCHIVE
function archives($ul = false, $bare = false) {
  global $keyarr;
  $time1 = ktime($keyarr[0]);
  $time2 = ktime($keyarr[count($keyarr)-1]);

  $my = date('mY', $time2);
  $list = array();
  $f = '';

  while($time1 < $time2) {
    $time1 = strtotime((date('Y-m-d', $time1).' +15days'));
    if(date('F', $time1) != $f) {
      $f = date('F', $time1);
      if(date('mY', $time1) != $my && ($time1 < $time2))
        $list[] = array('date' => date('F Y', $time1), 'url' => THIS_DOMAIN.'/'.date('Y/m/', $time1));
    }
  }

  $list[] = array('date' => date('F Y', $time2), 'url' => THIS_DOMAIN.'/'.date('Y/m/', $time2));
  
  if ($bare) {
    return $list;
  } else {
    if ($ul) echo "<ul>\n";
    foreach($list as $item) {
      echo '<li><a href="'.$item['url'].'" title="'.$item['date'].'">'.$item['date']."</a></li>\n";
    }
    if ($ul) echo "\n</ul>";
  }
}

//LIST OF ALL KEYS IN THE TIMEFRAME
function list_archive($bare = false, $cat = THIS_PAGE_CATEGORY, $preview = PREVIEW_HOOK) {
  global $keyarr;
  
  $links = array();
  $monthlater = strtotime(date('Y-m-d', $cat).' +1 month');
  foreach ($keyarr as $key) {
    $ktime = ktime($key);
    if ($ktime >= $cat) {
      if ($ktime > $monthlater) break;
      $pre = '';
      if (file_exists(LOCAL_CACHE.adashes(cut_cat($key)).".".$preview))
        $pre = substr(strip_tags(@file_get_contents(LOCAL_CACHE.adashes(cut_cat($key)).".".$preview)), 0, 300);
      $links[] = array('key' => $key, 'url' => k2url($key), 'timestamp' => $ktime, 'preview' => $pre);
    }
  }
  
  if ($bare) {
    return $links;
  } else {
    echo "<ul>\n";
    foreach ($links as $link) {
      echo '<li><a href="'.$link['url'].'" title="'.cut_cat($link['key']).'">'.cut_cat($link['key'])."</a>";
      if ($link['preview'] != '') echo "<p>".$link['preview']."...</p>";
      echo "</li>\n";
    }
    echo "\n</ul>\n";
  }
}

//LIST OF SPECIFIED NUMBER OF KEYWORDS FROM THE BEGINNING/END OF THE LIST OR RANDOM
function links($items = '20', $ord = false, $tagcloud = false, $bare = false) {
  global $keyarr;
  
  $loacalarr = array_reverse($keyarr);
  switch ($ord): 
  case 'RAND':
    @shuffle($loacalarr);
  break;
  case 'DESC':
    rsort($loacalarr);
  break;
  case 'ASCE':
    arsort($loacalarr);
  break;
  default:
    $loacalarr;
  endswitch;
  
  $links = array();
  foreach (array_slice($loacalarr, 0, $items) as $key) $links[] = array('key' => $key, 'url' => k2url($key), 'timestamp' => ktime($key));
  
  if ($bare) {
    return $links;
  } elseif ($tagcloud) {
    $popularity = array('not-popular" style="font-size: 1em;',
      'not-very-popular" style="font-size: 1.3em;',
      'somewhat-popular" style="font-size: 1.6em;',
      'popular" style="font-size: 1.9em;',
      'very-popular" style="font-size: 2.2em;',
      'ultra-popular" style="font-size: 2.5em;');
    foreach ($links as $link) {
      shuffle($popularity);
      echo "<a href='".$link['url']."' class=\"".$popularity[0]."\">".cut_cat($link['key'])."</a>";
    }
  } else {
    echo "<ul>";
    foreach ($links as $link) {
      echo '<li><a href="'.$link['url'].'" title="'.cut_cat($link['key']).'">'.cut_cat($link['key']).'</a></li>';
    }
    echo "</ul>";
  }
}

//LIST OF SPECIFIED NUMBER OF KEYWORDS FROM THE BEGINNING/END OF THE LIST OR RANDOM WITH PREVIEW
function previews($items = '5', $ord = false, $bare = false, $full = false, $preview = PREVIEW_HOOK) {
  global $keyarr;
  
  $loacalarr = array_reverse($keyarr);
  switch ($ord): 
  case 'RAND':
    @shuffle($loacalarr);
  break;
  case 'DESC':
    rsort($loacalarr);
  break;
  case 'ASCE':
    arsort($loacalarr);
  break;
  default:
    $loacalarr;
  endswitch;
  
  $links = array();
  foreach (array_slice($loacalarr, 0, $items) as $key) {
    $pre = '';
    if (file_exists(LOCAL_CACHE.adashes(cut_cat($key)).".".$preview)) {
      $pre = strip_tags(@file_get_contents(LOCAL_CACHE.adashes(cut_cat($key)).".".$preview));
      if (!$full) $pre = substr($pre, 0, 300);
    }
    $links[] = array('key' => $key, 'url' => k2url($key), 'timestamp' => ktime($key), 'preview' => $pre);
  }
  
  if ($bare) {
    return $links;
  } else {
    echo "<ul>";
    foreach ($links as $link)      
      echo '<li><a href="'.$link['url'].'" title="'.cut_cat($link['key']).'">'.cut_cat($link['key']).'</a><p>'.$link['preview'].'</p></li>';
    echo "</ul>";
  }
}

//LINKS TO PREVIOUS AND NEXT KEYWORD
function navigation($keyword = THIS_PAGE_KEYWORD, $category = THIS_PAGE_CATEGORY) {
  global $keyarr;
  
  $keyword = CATEGORIES ? $category.','.$keyword : $keyword;
	$key = array_isearch($keyword, $keyarr);
	$prev = isset($keyarr[$key-1]) ? $keyarr[$key-1] : '';
	$next = isset($keyarr[$key+1]) ? $keyarr[$key+1] : '';
	$navigation = '<div class="navigation">';
	if ($prev) {
		$navigation .= "\n".'<div style="float:left;text-align:left;">&laquo; <a href="'.k2url($prev).'" title="'.cut_cat($prev).'">'.cut_cat($prev).'</a></div>';
	}
	if ($next) {
		$navigation .= "\n".'<div style="float:right;text-align:right;"><a href="'.k2url($next).'" title="'.cut_cat($next).'">'.cut_cat($next).'</a> &raquo;</div>';
	}
	echo $navigation.'</div>';
}

//ADD NICE INDENTATION TO OUTPUT
function indenter($buffer) {
  if (INDENT == true) {
    $indenter = '  ';
    $buffer = str_replace("\n", '', $buffer);
    $buffer = str_replace("\r", '', $buffer);
    $buffer = str_replace("\t", '', $buffer);
    $buffer = ereg_replace(">( )*", ">", $buffer);
    $buffer = ereg_replace("( )*<", "<", $buffer);
    $level = 0;
    $buffer_len = strlen($buffer);
    $pt = 0;
    while ($pt < $buffer_len) {
      if ($buffer{$pt} === '<') {
        $started_at = $pt;
        $tag_level = 1;
        if ($buffer{$pt+1} === '/') $tag_level = -1;
        if ($buffer{$pt+1} === '!') $tag_level = 0;
        while ($buffer{$pt} !== '>') $pt++;
        if ($buffer{$pt-1} === '/') $tag_level = 0;
        $tag_length = $pt+1-$started_at;
        if ($tag_level === -1) $level--;
        $array[] = str_repeat($indenter, $level).substr($buffer, $started_at, $tag_length);
        if ($tag_level === 1) $level++;
      }
      if (($pt+1) < $buffer_len) {
        if ($buffer{$pt+1} !== '<') {
          $started_at = $pt+1;
          while ($buffer{$pt} !== '<' && $pt < $buffer_len) $pt++;
          if ($buffer{$pt} === '<') {
            $tag_length = $pt-$started_at;
            $array[] = str_repeat($indenter, $level).substr($buffer, $started_at, $tag_length);
          }
        } else {
          $pt++;
        }
      } else {
        break;
      }
    }
    $buffer = implode($array, "\n");
    $buffer = str_replace("<!--","<!--\n",$buffer);
    $buffer = str_replace("//-->","\n//-->",$buffer);
    preg_match_all('/<a(.*?)>\\s*/m', $buffer, $result, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($result[0]); $i++) {
      $buffer = str_replace($result[0][$i],trim($result[0][$i]),$buffer);
    }
    preg_match_all('%\\s*</a>%m', $buffer, $result, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($result[0]); $i++) {
      $buffer = str_replace($result[0][$i],trim($result[0][$i]),$buffer);
    }
    preg_match_all('/<textarea(.*?)>\\s*/m', $buffer, $result, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($result[0]); $i++) {
      $buffer = str_replace($result[0][$i],trim($result[0][$i]),$buffer);
    }
    preg_match_all('%\\s*</textarea>%m', $buffer, $result, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($result[0]); $i++) {
      $buffer = str_replace($result[0][$i],trim($result[0][$i]),$buffer);
    }
    preg_match_all('/<title>\\s*/m', $buffer, $result, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($result[0]); $i++) {
      $buffer = str_replace($result[0][$i],trim($result[0][$i]),$buffer);
    }
    preg_match_all('%\\s*</title>%m', $buffer, $result, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($result[0]); $i++) {
      $buffer = str_replace($result[0][$i],trim($result[0][$i]),$buffer);
    }
    return $buffer;
  } else {
    return $buffer;
  }
}

//SHORTCUTS
function error404() {
	header("HTTP/1.0 404 Not Found");
	die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
  <html><head><title>404 Not Found</title></head><body>
  <h1>Not Found</h1>
  <p>The requested URL '.$_SERVER['REQUEST_URI'].' was not found on this server.</p>
  <hr>
  <address>Apache/2.2.3 (Unix) Server at '.THIS_DOMAIN.' Port 80</address>
  </body></html>');
}

function redirect301($url = THIS_DOMAIN) {
	header("HTTP/1.1 301 Moved Permanently");
  header("Location: http://".$url);
  exit();
}

function printerror($message='') {
  if (DEBUG) echo $message;
  return NULL;
}

function print_pre($var) {
  echo '<pre>';
  print_r($var);
  echo '</pre>';
}

function title($keyword = THIS_PAGE) {
	if ($keyword == 'index.php') {
    echo "<title>".SITE_NAME."</title>\n";
	} else {
	  echo "<title>".SITE_NAME." &raquo; ".THIS_PAGE_KEYWORD."</title>\n";
	}
}

function metakeywords($keyword = THIS_PAGE) {
	if ($keyword == 'index.php' || $keyword == 'sitemap.php') {
	  $firstkey = SITE_NAME;
	} else {
	  $firstkey = THIS_PAGE_KEYWORD;
	}
	echo '<meta name="keywords" content="'.$firstkey.'" />'."\n";
}

function metadescription($function = PREVIEW_HOOK, $args = array()) {
	echo '<meta name="description" content="';
  echo substr(strip_tags(returntext('cache', array($function, $args))), 0, 300);
	echo "\" />\n";
}

function feed() {
  echo '<link rel="alternate" type="application/rss+xml" title="'.SITE_NAME.' RSS Feed" href="'.THIS_DOMAIN.'/feed.xml" />'."\n";
}
function domain() {
	echo THIS_DOMAIN.'/';
}
function description() {
	echo SITE_DESCRIPTION;
}
function template() {
	echo LOCAL_TEMPLATE;
}
function sitename() {
	echo SITE_NAME;
}
function keyword() {
	echo THIS_PAGE_KEYWORD;
}
function category() {
	echo THIS_PAGE_CATEGORY;
}
?>