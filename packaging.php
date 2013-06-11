<?php
/*
* HINT: files in includes/ folder and in root folder should be encoded beforehand
*/
//security token generation
function randstr($length = 6) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$code = "";
	$clen = strlen($chars) - 1;
	while (strlen($code) < $length) {
		$code .= $chars[mt_rand(0,$clen)]; 
	}
	return $code;
}

$security_token = strtolower(randstr(32));

$sourcedir = dirname(__FILE__);
$targetdir = dirname(__FILE__).'/../yacg';
/* END CONFIG*/
// 
// require_once('includes/zip/zip_class.php');
// require_once('includes/zip/createZip_class.php');

// recursive rmdir
function full_unlink($dir, $DeleteMe = true) {
  if(!$dh = @opendir($dir)) return;
  while (false !== ($obj = readdir($dh))) {
    if($obj=='.' || $obj=='..') continue;
    if (!@unlink($dir.'/'.$obj)) full_unlink($dir.'/'.$obj, true);
  }
  if ($DeleteMe) {
    closedir($dh);
    @rmdir($dir);
  }
}

function make_archive($dir, &$zip, $extdir="") {
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
      while (($file = readdir($dh)) !== false ) {
        if( $file != "." && $file != ".." ) {
          if(is_dir($dir.$file)) {
            $zip->addDirectory($extdir.$file.'/');
            make_archive($dir.$file."/", $zip, $extdir.$file."/");          
          } else {
            $zip->addFile(file_get_contents($dir.$file), $extdir.$file);                                                        
          }
        }
      }
      closedir($dh);
    }
  }
  return true;
}

function full_copy($source, $target) {
  global $sourcedir, $targetdir, $buster;
  
  if ( is_dir( $source ) ) {
    @mkdir( $target );   
    $d = dir( $source );   
    while ( FALSE !== ( $entry = $d->read() ) ) {
      if (in_array($entry, array('.', '..', '.gitignore', '.ssh', '.git', '.DS_Store', 'salesletter.txt', 'test.php', 'packaging.php', 'categories.txt', 'keywords.txt', 'keywords.bkp', 'keywords.tr.txt', 'categories.tr.txt', 'feed.xml', 'sitemap.xml'))) continue;
      
      $Entry = $source . '/' . $entry;           
      if ( is_dir( $Entry ) ) {
        if (strstr($Entry, '/img')) continue;
        if (strstr($Entry, '/cache')) continue;
        
        full_copy( $Entry, $target . '/' . $entry );
        continue;
      }
      @copy( $Entry, $target . '/' . $entry );
    }
    $d->close();
  } else {
    @copy( $source, $target );
  }
}

//remove everything
full_unlink($targetdir.'/yacg');

//copy bfe
full_copy($sourcedir, $targetdir.'/yacg');

//create clean files/folders
mkdir($targetdir.'/yacg/img');
mkdir($targetdir.'/yacg/rootfolder/cache');
touch($targetdir.'/yacg/feed.xml');
touch($targetdir.'/yacg/sitemap.xml');

foreach(array('archives.gif', 'footer_black.gif', 'loadingAnimation.gif', 'readon_black.gif', 'search.gif', 'spinner.gif', 'trackback_pingback.gif') as $file) copy($sourcedir.'/img/'.$file, $targetdir.'/yacg/img/'.$file);

//write keywords
file_put_contents($targetdir.'/yacg/rootfolder/keywords.txt', file_get_contents($sourcedir.'/rootfolder/keywords.bkp'));

//write clean config
$config = <<<CONFIG
<?php //CONFIG FILE

\$version = '3.9.0'; // [YACG] Yet Another Content Generator Version

/* BASIC CONFIGURATION */

// FOOTPRINTS
define('DEBUG', true); // true/false - VERBOSE ERROR OUTPUT/NO ERROR OUTPUT
define('ROOT_DIR', './rootfolder/'); // ROOTFOLDER NAME

// URL FORMAT
define('FILE_EXT', ''); //FILE EXTENSION, e.g. .html, .php, etc.
define('PERMALINK', ''); //URL PREFIX, e.g. /wp-content/, /pages/, etc.

// CATEGORIES
define('CATEGORIES', true);
define('CAT_NUM', '6');

\$pages = array('contact'); //SITE PAGES

// SITE PROPERTIES
define('SITE_NAME', 'Your Site'); // SITE NAME
define('SITE_DESCRIPTION', 'Your description'); // DESCRIPTION OF YOUR SITE
define('PASSWORD', 'yacg'); // ADMIN PASSWORD
define('EMAIL', 'your@email.com'); // ADMIN EMAIL (FOR CONTACT-US PAGE)
define('FILEMODE', 0777); //DEFAULT FILE MODE

// UTF-8 SUPPORT
define('UTF', false); // true/false - UTF-8 SUPPORT ON/OFF

// SITE LIFESPAN
define('START_KEYS', '2'); // HOW MANY KEYWORDS TO START WITH
define('NEW_MIN', '2'); // AT LEAST THIS MUCH KEYWORDS WILL BE ADDED ON EACH UPDATE
define('NEW_MAX', '5'); // BUT NO MORE THAN THAT
define('UPDATE_INTERVAL', 43200); // CHANGE KEYWORD ADDITION INTERVAL(12 HOURS BY DEFAULT)

// OUTPUT CONTROL
define('INDENT', false); // true/false - INDENT/DON'T INDENT THE HTML CODE
// CACHE CONTROL
define('CACHE', true); // true/false - CACHING ON/OFF
define('CACHE_AUTO', false); // CACHE PAGES AS THEY ARE BEING ADDED TO THE KEYWORDS.TXT('START_KEYS' SHOULD BE SET FOR THIS TO WORK)
define('CACHE_TIME', '15552000'); // CACHE EXPIRE TIME (IN SECONDS)
define('LOCAL_IMAGE_CACHE', './img/'); // LOCATION OF THE IMAGE CACHE(E.G. FOR FLICKR HOOK)

// HOOKS
define('PICK_HOOKS', false); // SET TO TRUE TO LOAD ONLY HOOKS YOU NEED
\$hooks = array('google_adsense.php', 'articles_table.php', 'markov.php', 'wikipedia.php', 'social_bookmarking.php', 'flickr.php', 'youtube.php'); // ONLY THESE HOOKS WILL BE LOADED IF 'PICK_HOOKS' IS TRUE

//PREVIEW HOOK
define('FIND_PREVIEW_HOOK', true); // ON THE FIRST LAUNCH YACG WILL TRY TO FIND WHAT HOOK IS BEING CACHED IN PAGE.PHP TEMPLATE AND SET IT AS PREVIEW_HOOK, SO YOU DON'T HAVE TO
define('PREVIEW_HOOK', 'wikipedia'); // USE CACHED OUTPUT OF THIS HOOK TO GENERATE PAGE DESCRIPTION TAG, PAGE EXCERPTS AND RSS FEED

// DEFINE CLOAKING LEVEL
define('CLOAKING_LEVEL', '4'); // 0/1/2/3/4 - ALWAYS CLOAK/OFTEN CLOAK/SOMETIMES CLOAK/RARELY CLOAK/NEVER CLOAK

// CONFIGURE SCRAPING
define('USER_AGENT', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11'); // USER-AGENT USED TO SCRAPE CONTENT
define('PROXY', false); // true/false - USE/DON'T USE A PROXY FOR ALL THE SCRAPING
define('PROXY_IP', '127.0.0.1'); // PROXY IP
define('PROXY_PORT', '8080'); // PROXY PORT

// SYSTEM FILES/FOLDERS PATHS(DO NOT MODIFY THOSE)
define('LOCAL_CACHE', ROOT_DIR.'cache/'); // LOCATION OF THE CACHE
define('LOCAL_ARTICLES', ROOT_DIR.'articles/'); // LOCATION OF THE ARTICLES
define('LOCAL_TEMPLATE', ROOT_DIR.'pages/'); // LOCATION OF THE PAGES
define('LOCAL_HOOKS', ROOT_DIR.'hooks/'); // LOCATION OF THE HOOKS
define('FILE_BOTS', ROOT_DIR.'ips.txt'); // LOCATION OF THE BOT LIST FILE
define('FILE_KEYWORDS', ROOT_DIR.'keywords.txt'); // LOCATION OF THE KEYWORDS FILE
define('FILE_CATEGORIES', ROOT_DIR.'categories.txt'); // LOCATION OF THE CATEGORIES LIST
define('FILE_CATEGORIES_TR', ROOT_DIR.'categories.tr.txt'); // LOCATION OF THE TRANSLITERATED CATEGORIES LIST

/* HOOK SETTINGS */

// GENERAL SETTING FOR ALL ADVERTISING HOOKS
define('SHOW_ADS', true); // true/false - DISPLAY/DON'T DISPLAY ADS

// YAHOO IMAGES HOOKS
define('YAHOO_API', 'YahooDemo'); // YAHOO API

// GOOGLE ADSENSE HOOK
define('GOOGLE_ADSENSE_PUBID', 'pub-xxxxxxxxxxxxxx'); // GOOGLE ADSENSE PUB_ID
define('GOOGLE_ADSENSE_ADCHANNEL', 'XXXXXXXXXXXXXX'); // GOOGLE ADSENSE CHANNEL

// CLICKBANK HOOK
define('CLICKBANK_ID', 'xxxxx'); // CLICKBANK ID

// PEAKCLICK HOOK
define('PEAKCLICK_AFF', 'xxxxx'); // PEAKCLICK AFFILIATE ID
define('PEAKCLICK_SUBAFF', '1'); // PEAKCLICK SUBAFFILIATE ID

// UMAX HOOK
define('UMAX_AFF', 'xxxxx'); // UMAX AFFILIATE ID
define('UMAX_SUBAFF', '1'); // UMAX SUBAFFILIATE ID
define('UMAX_AUTH', 'xxxxxxxx'); // UMAX AUTHORIZATION KEY(https://www.umaxlogin.com/user_links_code.php)

// AUCTION ADS HOOK
define('AUCTIONADS_ADCLIENT', 'xxxx'); // AUCTIONADS ADCLIENT
define('AUCTIONADS_ADCAMPAIGN', 'xxxx'); // AUCTIONADS ADCAMPAIGN

// GOOGLE ANALYTICS HOOK
define('GOOGLE_ANALYTICS_ACCOUNT', 'XXXXXXXXXXX'); // GOOGLE ANALYTICS ACCOUNT NUMBER

// STATCOUNTER HOOK
define('STATCOUNTER_PROJECT', 'XXXXXXXXX'); // PROJECT NUMBER
define('STATCOUNTER_PARTITION', 'XX'); // PARTITION NUMBER
define('STATCOUNTER_SECURITY', 'XXXXXX'); // SECURITY NUMBER
?>
CONFIG;

file_put_contents($targetdir.'/yacg/config.inc.php', $config);

?>