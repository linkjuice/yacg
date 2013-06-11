======What is YACG======

YACG(Yet Another Content Generator) is a highly flexible and customizable website creator that 
can automatically create tons of pages so you can throw your ads an them and make a sh*tload of 
money. You should already know this if you are reading this.
======What You Need======

  * Text Editor (notepad will do)

  * Local HTTP server for testing purposes ([[http://www.wampserver.com/en/| WAMP]] is great and easy)

  * Basic [http://php.net PHP] knowledge although not required, but will be very helpful, if you want get the most out of YACG. Look into the php files when you read about them, you will see what I'm talking about and understand things much better.

  * And of course you'll need a YACG distribution. Get everything you need at [[http://forums.getyacg.com | YACG forums]]

======YACG structure======

YACG itself is a php script that grabs a list of keywords and generates a website based on it.
You have to have this keyword list (you could also generate one automatically, but this is not
todays topic).

The structure is always the same:

  - every http request is redirected to the index.php file in the root of your yacg folder (.htaccess does this). DONT MESS THIS WITH FILE unless you know what you are doing. .htaccess files are used by apache to redirect all kind of requests. This file has to be saved as ANSI or it wont work. With some tricks and a .htaccess its possible to create .html output. (f.e. www.myyacgsite.com/keyword.html ) this will make the searchengine think you have static, more trustworthier pages. Would be a good topic for a later tut, but this needs deeper modifications (can't be done by hooks).  
  - the index.php loads everything you need, this is always your startpoint. The paths to every file is relative to the index.php, this means to your yacg directory (this is importent if you want to write your own hooks that interact with others). it is checked if the requested file already exists in the cache, and if so, is load from it. this saves LOTS of time, as your hooks dont need to grab the content over and over again. if the page isnt found in the cache, index.php calls the needed template (in the template folder). from this point, you can use all the functions in the hooks
  - in the template folder are different files, this are the different pages a yacg site has:<code>
  -index.php        this is the startpage of you site. it will get displayed when someone open www.myyacgsite.com. more details below
  -page.php         this is the main page, every page except the index, sitemap and contact will be created from this page. i will go into detail later
  -sitemap.php      (sitemap with links to every page of your yacg installation) when you spam (*coff*), POST links to your site somewhere, you should always post you sitemap so search engines can index your pages better
  -contact-us.php   (contact form site. you could display anything other here)</code> EVERY call of you site gets redirected to one of this pages (except the 404, page not found error).
  - index.php (TEMPLATE!) this is the root / index of your site. You can display an articles table, there is a nice hook. i think i will go into details about hooks later. It is a good idea to make this page look legit and not totally spammed with ads. If someons wants to check out your site and sees a legit looking page, he might get off without checking the rest. the most traffic will come from search engines and will go directly into the keyword-pages, so you dont have to be afraid to loose lots of money because you didnt place 20 ads on you indexpage.
  - page.php This page generates all your content and holds your ads. the page.php is called with a specific keyword from your keywords.txt. from this keyword, all the content on the particular page is created. BUT if you want to add code / modify something, dont do it here, add a hook and do your stuff there. this ensures that you dont break the structure. most hooks are used here, and if you want for example add a popup or something, this is also the place.
  - hooks: the hooks folder holds a bunch of files that are all loaded when yacg fires up. Every file here holds at least one function that can be called from within the template files. There can be problems when hooks are called recursively, but I wont go into detail as this is next tutorials topic.
  - cache: the cache saves every content and picture you load from somewhere, so you don’t have to do it again. This will significantly speed up your page loading! Make sure to activate it. A proper implemented hook should make use of the cache.
  - articles: the article folder holds *.txt files with text. The markov.php hook uses them and also I think the metadescription hook. If you have a niche make sure to place ONTOPIC articles! There is a modified markov that doesn’t depent on articles, this is MUCH better. The modified markov hook and why you should use the new one can be read in the next tutorial. I think yacg throws an error if there is no file in the articles folder, so even if you don’t use it, leave it untouched!
  - admin: this is the admin section to control your yacg installation. I don’t think this is the best solution, as you can only do things by hand. I changed this to be remotely accessible, or even by hook. Also, its not sure if I will release this as I use it myself. If you want to know more, I can share possibly in a later tutorial.

  ======REQUIREMENTS======
  * Apache with PHP (4.2.3+)
  * Curl should be enabled in PHP
  * only *nix servers are officially supported

======INSTALLATION======
  - Download and unzip the [YACG] package, if you haven't already.
  - Rename the config.inc.php.example file to config.inc.php
  - Open config.inc.php in your favorite text editor and fill in your configuration details.
  - Open //rootfolder/keywords.txt// and put your keywords.
  - Place the [YACG] files in the desired location on your web server.
  - Change file permissions to 0777 for the following files:<code>
- config.inc.php
- feed.xml
- sitemap.xml
- img/
- rootfolder/
- rootfolder/cache/
- rootfolder/keywords.txt
</code>
  - YACG comes with a DEBUG mode. By default, it is ON - This means that all the PHP errors and other alerts (Nothing was found!) will be printed. \\ You need to turn DEBUG mode OFF after you have installed/tested everything to avoid leaving fingerprints. \\ \\ To turn it off, open config.inc.php and change this:\\ <code>define('DEBUG', true); // SET DEBUG MODE ON OR OFF</code> To this: <code>define('DEBUG', false); // SET DEBUG MODE ON OR OFF</code>
  - That’s it! [YACG] should now be installed


Default YACG folder layout looks like this:

>css/
>img/
>rootfolder/
>>admin/
>>articles/
>>cache/
>>hooks/
>>includes/
>>pages/
>>COPYING.txt
>>keywords.txt
>>README.txt
>.htaccess
>config.inc.php
>feed.xml
>robots.txt
>sitemap.xml

The first thing you should do when installing your YACG sites is to rename //rootfolder// folder and change the ROOT_DIR option in your config.inc.php accordingly. Why is it so important? Well, in this game, called Black Hat SEO, the aim is too look as "white-hat" as possible. Letting Google know that you're using YACG doesn't help at all.

Now, when it's out of our way let's discuss all other files and subfolders we have there.

=== .htaccess ===

All it does is redirecting requests to the //index.php//. This makes custom URLs in YACG possible(FILE_EXT and PERMALINK options). Since YACG 3.0 it learned one more trick - compressing YACG output to speedup page load times(your host should have mod_deflate or mod_gzip enabled for this to work.

=== config.inc.php ===

This file contains all configuration options of your YACG site. You can read more about it [[yacg_configuration|here]].

=== feed.xml ===

That's an RSS feed for your site. Contains links to 20 latest articles from your site(if START_KEYS option is //false//, then it's just 20 first items from your keywords.txt). There is a FEED_HOOK option which defines what will be but into the //description// section(the stuff you'll see in your rss-reader). FEED_HOOK works by taking cached hook output from //cache// folder, stripping tags and taking 300 character long sample. Therefore, you need to call feed hook in your //page.php// template via cache function(e.g. //<? cache('markov', array(6, 500)) ?>//). If YACG won't be able to find the hook output in the //cache// folder, it will just generate some text with markov. You can also use cache of your whole pages by setting FEED_HOOK to 'html', but depending on your template layout, this might turn out to be very ugly.

=== robots.txt ===

Nothing fancy here, just letting search engines know that we like 'em.

=== sitemap.xml ===

Just plain xml sitemap for faster indexing.

=== css, img, js and rootfolder/pages folders ===

These folders contain your site's template. //Css//, //img// and //js// contain template stylesheets, images and javascript files respectively. The //pages// folder contains php templates of your site's pages(index.php, page.php, category.php, contact-us.php and sitemap.php by default). Since YACG 3.0 you can add any number of additional custom pages just by placing a php file in this folder and specifying it's name(without the .php extension) in //$pages// array in your config file.

Since YACG 3.0, //templates// folder contents are separated into those four. Again, this is made to avoid footprints.

=== rootfolder ===

This is a root YACG folder and as you'd probably guessed, it should be renamed to avoid footprints(don't forget to change ROOT_DIR in //config.inc.php// accordingly). The rest of this article will describe it's numerous subfolders:

== admin ==

YACG admin section. Contains some useful little scripts as:

  * **cache-clean.php**: simply wipes out contents of the //cache// folder
  * **feed-generator.php**: updates your feed.xml. If START_KEYS is not //false// will be run automatically on every site update.
  * **ip-update.php**: will collect fresh bot ip lists for cloaking hook
  * **keyword-cleaner.php**: will help you remove illegal characters and "bad" words from your keywords.txt
  * **pinger.php**: will let know a handful of "blog ping-services" that your feed.xml has been updated
  * **sitemap-generator.php**: creates your sitemap.xml. If START_KEYS is not //false// will be run automatically on every site update.

If you want to run these scripts from a remote server, simply pass your YACG password(PASSWORD in //config.inc.php//) as a parameter(e.g. "http://somesite.com/rootfolder/admin/feed-generator.php?password=12345")

== articles ==

From these text files markov hook takes inspiration. File count and their names doesn't matter.

== cache ==

Here YACG stores cached content, either created by site-wide caching(.html extension) or //cache()// hook(.hookname extension).

== hooks ==

This is the place where default or found in the [[http://forums.getyacg.com/4/|Code Contributions]] hooks are stored.

== includes ==

Contains different non-YACG libraries, like utf8 or xmlrpc class. Also contains a CurlSnoopy class for advanced scraping needs.

== pages ==

Here is where your templates go. Only //index.php//, //page.php//, //404.php//, //archive.php// and //categories.php//(if you use categories) are obligatory, others can be optionally defined via $pages config option.

== COPYING.txt and README.txt ==

Usual boring stuff.

== keywords.txt ==

Cornerstone of your site. Contains list of site keyphrases, each of them will define separate page of your YACG site.


Line-by-line description of config.inc.php file:

====== YACG Configuration ======

<code php>
$version = '3.5'; // [YACG] Yet Another Content Generator Version
</code>
Version of YACG you have. See [[http://forums.getyacg.com|forum]] for latest.

<code php>
define('DEBUG', true); // true/false - VERBOSE ERROR OUTPUT/NO ERROR OUTPUT
</code>
Debug mode is for testing purposes, when you want to see all of the error messages produced by PHP. Should be set to //false// on production sites.
<code php>
define('ROOT_DIR', './rootfolder/'); // ROOT FOLDER NAME
</code>
If you won't rename your root folder to something else, you'll show everyone that you're using YACG. Big G will come and getcha for this.
<code php>
// define('THIS_DOMAIN', 'yourdomain.tld'); // THE URL OF YOUR SITE (NO HTTP:// OR TRAILING SLASH)
</code>
YACG 1.X-2.X legacy. Specifying THIS_DOMAIN is now optional.
<code php>
define('FILE_EXT', '.html'); // FILE EXTENSION TO ADD TO THE URLS
</code>
This will be added at the end of each URL.
<code php>
define('PERMALINK', 'blog/'); // CUSTOM PERMALINKS
</code>
This and the previous option form your site's urls(e.g. http://your.site.com/path/to/yacg/blog/your-keyword.html)
<code php>
define('CATEGORIES', true); // SPLIT KEYWORDS INTO CATEGORIES
</code>
Setting this option enables you to split your site into several sections. Google will like you for this, and the site will look more white-hat.
<code php>
define('CAT_NUM', '6'); // IF KEYWORDS ARE NOT CATEGORIZED, AUTOMATICALLY SPLIT INTO 'CAT_NUM' CATEGORIES(false - random, between 5 and 15)
</code>
If you haven't split keywords in the keywords.txt by hand, YACG will do it automatically. It will take several(CAT_NUM) keywords from the top of your keywords.txt file and make them your category names.
<code php>
define('SITE_NAME', 'Your Site'); // SITE NAME
</code>
Well, name pretty much says it all. Print it via //<? sitename() ?>// hook
<code php>
define('SITE_DESCRIPTION', 'Your description'); // DESCRIPTION OF YOUR SITE
</code>
Same thing here.(<? description() ?> hook)
<code php>
$pages = array('contact'); //SITE PAGES
</code>
Additional pages, present on your site(besides those generated from your keywords). You can add any number of additional pages by adding new item to this list and placing corresponding template file into //pages// folder. E.g.
<code php>$pages = array('sitemap','contact-us', 'terms-of-use.html'); //SITE PAGES</code>
//rootfolder/pages//:
> index.php
> page.php
> category.php
> sitemap.php
> contact-us.php
> terms-of-use.html.php
<code php>
define('PASSWORD', '12345'); // ADMIN PASSWORD
</code>
Password for admin section of your site(//renametisfolder/admin//)
<code php>
define('EMAIL', 'your@email.com'); // ADMIN EMAIL (FOR CONTACT-US.PHP)
</code>
This email will be used by //contact_form.php// hook, which is used on the //contact-us// page.
<code php>
define('FILEMODE', '0777'); //DEFAULT FILE MODE
</code>
These file permission will be assigned to all keywords files created by YACG(keywords.tmp.txt, categories.txt, keywords.tr.txt, etc.). This is also used in debug mode when checking permissions as a value to compare against. The only case when you would want to change those is when your hosting provider is too restrictive and setting files to 777 permissions makes them unreadable.
<code php>
define('UTF', false); // true/false - UTF-8 SUPPORT ON/OFF
</code>
If it's turned on, you can use letters from any language in your keywords.txt.(don't forget to check that your text editor saves your files in UTF-8 encoding)
<code php>
define('TRANSLIT', false); // true/false - TRANSLITERATE UTF-8 STRINGS
</code>
This option defines how the non-latin characters will be treated in urls. If this option set to false, they will be just url-encoded; if //true// - then they will be transliterated.(e.g. 'Düsseldorf' will be transliterated as 'Dusseldorf')
<code php>
define('TRANSLIT_ADVANCED', true); // true/false - TURN ADVANCED TRANSLITERATION ON/OFF(if turned off only accents will be transliterated)
</code>
Although basic transliteration is good enough for most european languages, in more difficult cases(e.g. cyrillic alphabet) you should use the advanced mode.
<code php>
define('START_KEYS', '3'); // HOW MANY KEYS TO START WITH(false - publish all at once)
define('DAILY_MIN', '3'); // AT LEAST THIS MUCH KEYWORDS WILL BE ADDED EACH DAY
define('DAILY_MAX', '10'); // BUT NO MORE THAN THAT
</code>
With these options you can set your site to grow organically, publishing random number(between DAILY_MIN and DAILY_MAX) of keywords daily.
<code php>
define('INDENT', true); // true/false - INDENT/DON'T INDENT THE HTML CODE
</code>
Setting this option will prettify html code, produced by YACG.
<code php>
define('CACHE', false); // true/false - CACHING ON/OFF
</code>
This option will make YACG cache all pages after they have been generated. Will lower CPU load significantly.
<code php>
define('CACHE_AUTO', false); // CACHE PAGES AS THEY ARE BEING ADDED TO THE KEYWORDS.TXT('START_KEYS' SHOULD BE SET FOR THIS TO WORK)
</code>
If site is set to grow daily, YACG will automatically cache all pages right after they have been added.
<code php>
define('CACHE_TIME', '15552000'); // CACHE EXPIRE TIME (IN SECONDS)
</code>
For how long cached pages will be stored in cache before regeneration.
<code php>
define('LOCAL_IMAGE_CACHE', './img/'); // LOCATION OF THE IMAGE CACHE(E.G. FOR FLICKR HOOK)
</code>
This is the place, where images are stored. It's different from LOCAL_CACHE path(see below) to avoid footprints.
<code php>
define('PICK_HOOKS', false); // SET TO TRUE TO LOAD ONLY HOOKS YOU NEED
</code>
Cherry-pick only the hooks you need. Will help reduce the server load. Use this option only if you're fully aware of what you're doing.
<code php>
$hooks = array('articles_table.php', 'markov.php'); // ONLY THESE HOOKS WILL BE LOADED IF 'PICK_HOOKS' IS TRUE
</code>
List of hooks to load.
<code php>
define('PREVIEW_HOOK', 'wikipedia'); // USE CACHED OUTPUT OF THIS HOOK TO GENERATE RSS FEED EXCERPTS
</code>
Cached output of this hook will be used in rss feed to generate previews, and also by //ktime//, //metadescription//, //list_category//, //list_archive// and //previews// hooks
<code php>
define('CLOAKING_LEVEL', '4'); // 0/1/2/3/4 - ALWAYS CLOAK/OFTEN CLOAK/SOMETIMES CLOAK/RARELY CLOAK/NEVER CLOAK
</code>
How often //$cloakdirective// will be set to 1.(Cloaking is ip-based)
<code php>
define('USER_AGENT', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11'); // USER-AGENT USED TO SCRAPE CONTENT
</code>
YACG will use this user agent while fetching content from other sites(via //fetch// or //fetchcache// functions)
<code php>
define('PROXY', false); // true/false - USE/DON'T USE A PROXY FOR ALL THE SCRAPING
define('PROXY_IP', '127.0.0.1'); // PROXY IP
define('PROXY_PORT', '8080'); // PROXY PORT
</code>
Proxy server to be used by //fetch()// hook.
<code php>
// SYSTEM FILES/FOLDERS PATHS
define('LOCAL_CACHE', ROOT_DIR.'cache/'); // LOCATION OF THE CACHE
define('LOCAL_ARTICLES', ROOT_DIR.'articles/'); // LOCATION OF THE ARTICLES
define('LOCAL_TEMPLATE', ROOT_DIR.'pages/'); // LOCATION OF THE PAGES
define('LOCAL_HOOKS', ROOT_DIR.'hooks/'); // LOCATION OF THE HOOKS
define('FILE_BOTS', ROOT_DIR.'ips.txt'); // LOCATION OF THE BOT LIST FILE
define('FILE_KEYWORDS', ROOT_DIR.'keywords.txt'); // LOCATION OF THE KEYWORDS FILE
define('FILE_KEYWORDS_TMP', ROOT_DIR.'keywords.tmp.txt'); // LOCATION OF YET TO BE ADDED KEYS
define('FILE_KEYWORDS_TR', ROOT_DIR.'keywords.tr.txt'); // LOCATION OF TRANSLITERATED KEYS
define('FILE_CATEGORIES', ROOT_DIR.'categories.txt'); // LOCATION OF THE CATEGORIES LIST
define('FILE_CATEGORIES_TR', ROOT_DIR.'categories.tr.txt'); // LOCATION OF THE TRANSLITERATED CATEGORIES LIST
</code>
Since YACG 3.0 you shouldn't care much about those options. The only folder you ever need to modify is defined in ROOT_DIR option.
====== Hook Configuration ======
The following are options used by default hooks, provided by YACG.
<code php>
define('SHOW_ADS', true); // true/false - DISPLAY/DON'T DISPLAY ADS
</code>
Control output of //peakclick//, //umax//, //adsense//, //auctionads// or any other advertising hook.
<code php>
define('FLICKR_API', false); // FLICKR API
</code>
*Update* As of YACG 3.02 this option is no longer required, so you don't have to worry about a flickr api key.

If you get "undefined value supplied for foreach on line ..." error, this option is to blame. Get API key here http://www.flickr.com/services/api/keys/apply/ or just don't use the flickr hook(use another image hook instead).

Rest of the lines are pretty much self-explanatory, but if you have any problems with them feel free to ask on the [[http://forums.getyacg.com|forums]] or look at the default hooks reference.
<code php>
define('YAHOO_API', 'YahooDemo'); // YAHOO API

// GOOGLE ADSENSE HOOK
define('GOOGLE_ADSENSE_PUBID', 'pub-xxxxxxxxxxxxxx'); // GOOGLE ADSENSE PUB_ID
define('GOOGLE_ADSENSE_ADCHANNEL', 'XXXXXXXXXXXXXX'); // GOOGLE ADSENSE CHANNEL
// PEAKCLICK HOOK
define('PEAKCLICK_AFF', 'XXXX'); // PEAKCLICK AFFILIATE ID
define('PEAKCLICK_SUBAFF', ''); // PEAKCLICK SUBAFFILIATE ID
define('PEAKCLICK_THUMBS', true); // true/false - DISPLAY/DON'T DISPLAY THUMBNAILS
// UMAX HOOK
define('UMAX_AFF', 'xxxxx'); // UMAX AFFILIATE ID
define('UMAX_SUBAFF', 'xxxx'); // UMAX SUBAFFILIATE ID
// AUCTION ADS HOOK
define('AUCTIONADS_ADCLIENT', 'xxxx'); // AUCTIONADS ADCLIENT
define('AUCTIONADS_ADCAMPAIGN', 'xxxx'); // AUCTIONADS ADCAMPAIGN
// GOOGLE ANALYTICS HOOK
define('GOOGLE_ANALYTICS_ACCOUNT', 'XXXXXXXXXXX'); // GOOGLE ANALYTICS ACCOUNT NUMBER
// STATCOUNTER HOOK
define('STATCOUNTER_PROJECT', 'XXXXXXXXX'); // PROJECT NUMBER
define('STATCOUNTER_PARTITION', 'XX'); // PARTITION NUMBER
define('STATCOUNTER_SECURITY', 'XXXXXX'); // SECURITY NUMBER
</code>


>**NOTE:** Creating templates for YACG requires some understanding of HTML, so before proceeding, take a quick look at an excellent online tutorial by [[http://htmldog.com|HtmlDog]](it's very clear and compact). Also, some PHP and CSS understanding will help you put your templates to the next level.

As you might've learned in the [[folder_layout|YACG structure overview]], YACG templates are split into two folders: the stylesheets(//css//), images(//img//) and javascript(//js//) are placed in the root of your site, and actual page templates are located in the //rootfolder/pages// folder. In this section we'll look closer at how YACG templates are built by dissecting pages of the default template.

====== page.php ======

<code html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <? title() ?>
  <? metadescription() ?>
  <? feed() ?>
  <link rel="stylesheet" href="<? domain() ?>css/style.css" type="text/css" media="screen" />
</head>
<body>
  <div id="header">
    <div class="inside">
      <div id="search">
        <form method="get" id="sform" action="<? domain() ?>">
          <div class="searchimg"></div>
          <input type="text" id="q" value="" name="s" size="15" />
        </form>
      </div>

      <h2><a href="<? domain() ?>"><? sitename() ?></a></h2>
      <p class="description"><? description() ?></p>
      <br />
      <div style="text-align:center"><? adsense("728x90_as", '000000', '000000', 'FFFFFF', 'FFFFFF', 'BFBFBF') ?></div>
    </div>
  </div>
  <div id="primary" class="single-post">
    <div class="inside">
      <div class="primary">
        <h1><? keyword() ?></h1>
        <? navigation() ?><br />
        <p><? cache('wikipedia') ?></p>
      </div>
      <hr class="hide" />
      <div class="secondary">
        <h2>About this entry</h2>
        <div class="featured">
          <p>You&rsquo;re currently reading &ldquo;<? keyword() ?>&rdquo;, an entry on <? sitename() ?></p>
          <dl>
            <dt>Published:</dt>
            <dd><?=date('j.m.y / ga', ktime()) ?></dd>
          </dl>
          <dl>
            <dt>Category:</dt>
            <dd><a href="<?=c2url() ?>" title="View all posts in <? category() ?>" rel="category tag"><? category() ?></a></dd>
          </dl>
          <dl>
            <dt>On Flickr:</dt>
            <dd><br />
            <? flickr(THIS_PAGE_KEYWORD, 9) ?></dd>
          </dl>
          <dl>
            <dt>Bookmark it:</dt>
            <dd><? bookmarking() ?></dd>
          </dl>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>

  <hr class="hide" />
  <div id="secondary">
    <div class="inside">

      <div class="comment-head">
        <h2><? keyword() ?> on YouTube:</h2>
      </div>
      
      <div id="comment-form">
        <? youtube() ?>
      </div>
    </div>
  </div>
  
  <hr class="hide" />
  <div id="ancillary">
    <div class="inside">
      <div class="block first">
        <h2>About</h2>
        <p>This is the default YACG template. Use it for testing, learning, but please replace it with another one if you don't want to get banned.</p>
        <h2>Pages</h2>
        <ul class="counts">
        <? pages(false) ?>
        </ul>
      </div>

      <div class="block">
        <h2>Recently</h2>
        <ul class="dates">
          <? foreach(links(7, false, false, true) as $link): ?>
          <li><a href="<?=$link['url']?>"><span class="date"><?=date('j.m', $link['timestamp'])?></span> <?=cut_cat($link['key'])?> </a></li>
          <? endforeach; ?>
        </ul>
      </div>

      <div class="block">
        <h2>Categories</h2>
        <ul class="counts">
          <? categories(false) ?>
        </ul>
      </div>

      <div class="clear"></div>
    </div>
  </div>

  <hr class="hide" />
  <div id="footer">
    <div class="inside">
      <p class="copyright">Powered by <a href="http://warpspire.com/hemingway">Hemingway</a> flavored <a href="http://getyacg.com">YACG</a>.</p>
      <p class="attributes"><a href="feed:<? domain() ?>feed.xml">Entries RSS</a></p>
    </div>
  </div>
</body>
</html>
</code>

Most of your site's pages are being created with this template(each keyword from your keywords.txt file has it's own page based on this template) and that's where most of our traffic will go. We'll take a closer look at it's source code and analyze some important lines from it and the output they produce on an example of such a page - http://demo.getyacg.com/linkin-park.

**Code:**
<code php><? title() ?>
<? metadescription() ?>
<? feed() ?></code>
These are calls to some of the simplest hooks, you can recognize them by the opening and closing php tags(//<? ?>//). As you might learn from the [[hooks_reference|hook reference]], title() hook will put a title tag into our page, metadescription() a "meta description" tag and feed() will print an autodiscovery link to site's RSS feed. 

**Output:**
<code html>
<title>Your Site &raquo; GameFAQs</title>
<meta name="description" content="It's of a swamp house on a bayou and has a Louisiana feel to it.I have loved folk art paintings that depict the world of black Americans." />
<link rel="alternate" type="application/rss+xml" title="Your Site RSS Feed" href="http://demo.getyacg.com/feed.xml" /></code>

Notice the content of the meta tag - it has been generated by markov hook from sample article in the //articles// folder, so it's pretty much irrelevant. To solve this problem you may want to change contents of //articles// folder according to your site thematics or change the default hook for something else:

<code php><? metadescription('digg', array(THIS_PAGE_KEYWORD, 1)) ?></code>

Now we'll have one relevant item from digg hook as a description.

**Code:**
<code html><link rel="stylesheet" href="<? domain() ?>css/style.css" type="text/css" media="screen" /></code>

Here we used the domain() hook to get absolute path to our YACG installation and construct correct url for our stylesheet.

**Otput:**
<code html><link rel="stylesheet" href="http://demo.getyacg.com/css/style.css" type="text/css" media="screen" /></code>

**Code:**
<code html><div style="text-align:center"><? adsense("728x90_as", '000000', '000000', 'FFFFFF', 'FFFFFF', 'BFBFBF') ?></div></code>

Previously we've only seen hooks called without parameters(with their default values), but here we can see how you can customize hook output by passing arguments to it.

**Otput:**
<code html>
<script type="text/javascript">
            <!--
google_ad_client = "pub-XXXXXXXXXXXXXX";
google_ad_width = 728;
google_ad_height = 90;
google_ad_format = "728x90_as";
google_ad_type = "text";
google_ad_channel ="XXXXXXXXXXXXXX";
google_color_border = "000000";
google_color_bg = "000000";
google_color_link = "FFFFFF";
google_color_url = "FFFFFF";
google_color_text = "BFBFBF";
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</code>
Here you can clearly see how our parameters altered hook output, but not all of hooks nor parameters result in such a simple behavior, as you'll see in the next line:

**Code:**
<code php><p><? cache('wikipedia') ?></p></code>

What we see here is a call to the //cache// hook which will in turn call //wikipedia// hook with it's default arguments(we can alter this behavior by passing second parameter to //cache// hook, see more in the [[hooks_reference|hook reference]]) and cache it's output(wikipedia article related to our current keyword). Why call cache hook and not just //wikipedia//? Because the next time this page loads cache hook won't be calling wikipedia hook and just load article from cache which will reduce page load time and save some processing power.

**Output:**
<code html>
<p>
  <p>
  <strong>
  GameFAQs
  </strong>
  is a website that hosts FAQs and walkthroughs for video games. It was created in November 1995 by Jeff "CJayC" Veasey and has been owned by CNET Networks since May 2003. The site has a database of video game information and also hosts cheat codes, reviews, game saves, and credits submitted by volunteer gamers. The systems covered include the 8-bit Atari platform through modern consoles, as well as computer games. Contributions are reviewed by the site's editor, Allen "SBAllen" Tyner.
  </p>
...
</code>

**Code:**
<code php><dd><?=date('j.m.y / ga', ktime()) ?></dd></code>

Here we can see even more advanced hook call. First of all, notice the modified PHP open tag(//<?=//) - it's a shorthand of <code php><? echo date('j.m.y / ga', ktime()) ?></code> Then we pass //ktime// hook call as a second parameter to PHP //date// function(read more about it [[http://php.net/date|here]]). As you know, //ktime// will return publication time of the current keyword, so the final output will be:

**Output:**
<code html>
<dd>
  24.04.08 / 9pm
</dd>
</code>

**Code:**
<code php>
<ul class="dates">
  <? foreach(links(7, false, false, true) as $link): ?>
  <li><a href="<?=$link['url']?>"><span class="date"><?=date('j.m', $link['timestamp'])?></span> <?=cut_cat($link['key'])?> </a></li>
  <? endforeach; ?>
</ul>
</code>

Now let's look at some advanced YACG trickery. What we are doing here, is building list of links to 7 latest articles, but instead of calling //links// hook with default parameters and printing a simple list:
<code php><? links(7) ?></code>
<code html>
<ul>
<li><a href="http://demo.getyacg.com/D%C3%BCsseldorf/blog/Konwencja-Praw-Cz%C5%82owieka.html" title="Konwencja Praw Człowieka">Konwencja Praw Człowieka</a></li>
...
<li><a href="http://demo.getyacg.com/Pluto/blog/V-for-Vendetta.html">V for Vendetta</a></li>
</ul>
</code>
we set //$bare// parameter to //true// to get an array of links and pass it to PHP [[http://php.net/foreach|//foreach//]] loop(see [[hooks_reference|hook reference]] for exact structure of this array). In the loop, you can notice already familiar to you //date// function we use to extend default output of the links hook and print publication date.

**Output:**
<code html>
<ul class="dates">
  <li>
    <a href="http://demo.getyacg.com/D%C3%BCsseldorf/blog/Konwencja-Praw-Cz%C5%82owieka.html"><span class="date">
      24.04
    </span>
    Konwencja Praw Człowieka</a>
  </li>
...
  <li>
    <a href="http://demo.getyacg.com/Pluto/blog/V-for-Vendetta.html"><span class="date">
      23.04
    </span>
    V for Vendetta</a>
  </li>
</ul>
</code>

====== index.php ======

Index.php is  much like page.php, except for a one interesting code section:

**Code:**
<code php>
<? 
$firstkey = end($keyarr); 
$secondkey = $keyarr[key($keyarr)-1]; 
?>
<div id="primary" class="twocol-stories">
  <div class="inside">
    <div class="story first">
      <h3><a href="<?=k2url($firstkey) ?>" rel="bookmark" title="Permanent Link to <?=cut_cat($firstkey) ?>"><?=cut_cat($firstkey) ?></a></h3>
      <p><div style="float:left;margin:5px;"><? adsense("200x200_as", '000000', '000000', 'FFFFFF', 'FFFFFF', 'BFBFBF') ?></div> <? cache('wikipedia', array(cut_cat($firstkey)), cut_cat($firstkey)) ?></p>
      <div class="details">
        Posted at <?=date('ga \o\n j/m/y', ktime($firstkey, 'wikipedia'))?> | Filed Under: <a href="<?=c2url($firstkey) ?>" title="View all posts in <?=cut_key($firstkey) ?>" rel="category tag"><?=cut_key($firstkey) ?></a> <span class="read-on"><a href="<?=k2url($firstkey) ?>">read on</a></span>
      </div>
    </div>
    <div class="story">
      <h3><a href="<?=k2url($secondkey) ?>" rel="bookmark" title="Permanent Link to <?=cut_cat($secondkey) ?>"><?=cut_cat($secondkey) ?></a></h3>
      <p><div style="float:left;margin:5px;"><? adsense("200x200_as", '000000', '000000', 'FFFFFF', 'FFFFFF', 'BFBFBF') ?></div> <? cache('wikipedia', array(cut_cat($secondkey)), cut_cat($secondkey)) ?>
      </p>
      <div class="details">
        Posted at <?=date('ga \o\n j/m/y', ktime($secondkey, 'wikipedia'))?> | Filed Under: <a href="<?=c2url($secondkey) ?>" title="View all posts in <?=cut_key($secondkey) ?>" rel="category tag"><?=cut_key($secondkey) ?></a> <span class="read-on"><a href="<?=k2url($secondkey) ?>">read on</a></span>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
</code>
It produces two nice formatted articles from the end of the //keywords.txt// with adsense ads embedded in them.
**Code:**
<code php>
<? 
$firstkey = end($keyarr); 
$secondkey = $keyarr[key($keyarr)-1]; 
?>
</code>
//$firstkey// and //$secondkey// are set to last two values from [[constants |//$keyarr//]] array. You can see them being passed as a parameters to all of the hooks in this section:

<code php><? cache('wikipedia', array(cut_cat($firstkey))) ?></code>

====== sitemap.php, contact-us.php, category.php, archive.php, 404.php ======

**Code:**
<code php>
<? contact_form(); ?>
</code>

As you can see on the contact-us page, hooks can not only produce output but also accept and process some output from your visitors:

**Output:**
<code html>
<strong>
  To contact us about this site, please use the form below...
</strong>
<form id="contact" method="post" action="">
  <table style="width:50%;">
    <tr>
      <td style="text-align:right">
        Name:
      </td>
      <td>
        <input name="name" type="text" size="50" />
      </td>
    </tr>
    <tr>
      <td style="text-align:right">
        Email:
      </td>
      <td>
        <input name="email" type="text" size="50" />
      </td>
    </tr>
    <tr>
      <td style="text-align:right">
        Subject:
      </td>
      <td>
        <input name="subject" type="text" size="50" />
      </td>
    </tr>
    <tr>
      <td style="text-align:right">
        Message:
      </td>
      <td>
        <textarea name="message" cols="50" rows="10"></textarea>
      </td>
    </tr>
    <tr>
      <td style="text-align:right">
      </td>
      <td>
        <input type="submit" name="Submit" value="Submit" />
      </td>
    </tr>
  </table>
</form>
</code>

=====Bonus Topic: Creating simple hook=====

Let's see how hooks are built on an example of category listing from category.php:

**Code:**
<code php>
<ul class="dates">
  <? 
  $catlist = list_category(true);
  foreach($catlist as $link): ?>
  <li>
    <span class="date"><?=date('j.m.y', $link['timestamp']) ?></span>
    <a href="<?=$link['url']?>"><?=$link['key']?></a> 
    posted in 
    <a href="<?=c2url() ?>" title="View all posts in <? category() ?>" rel="category tag"><? category() ?></a>  		 
  </li>
  <? endforeach; ?>
</ul>
</code>

This code construct is very similar to the //links// hook code we've seen in //page.php//, so you should get the general idea on how it's built. Now you'll see how to package this code into simple hook:

<code php>
<?php
function advanced_list_category($cat = THIS_PAGE_CATEGORY) {
  ?>
  <ul class="dates">
  <? 
  $catlist = list_category(true, $cat);
  foreach($catlist as $link): ?>
  <li>
    <span class="date"><?=date('j.m.y', $link['timestamp']) ?></span>
    <a href="<?=$link['url']?>"><?=$link['key']?></a> 
    posted in 
    <a href="<?=c2url($cat) ?>" title="View all posts in $cat" rel="category tag">$cat</a>  		 
  </li>
  <? endforeach; ?>
</ul>
<?
}
?>
</code>

What we did here is not only packaged the code into simple hook, but also made it "template-independent", meaning that we can use it not only in category.php but on any other page, by specifying an optional parameter. All we have to do now is save our hook in some file with *.php extension(filename does not matter, it could be "blablabla.php" or "whatever.php") and put it into the //hooks// folder. Then we can call it from any page of our template like this:
**Code:**
<code php><? advanced_list_category('United States') ?></code>

**Output:**
<code html>
<ul class="dates">
  <li>
    <span class="date">
      16.04.08
    </span>
    <a href="http://demo.getyacg.com/United-States/blog/Neighbours.html">Neighbours</a>
    posted in
    <a href="http://demo.getyacg.com/United-States" title="View all posts in United States" rel="category tag">United States</a>
  </li>
  <li>
...
  <li>
    <span class="date">
      15.04.08
    </span>
    <a href="http://demo.getyacg.com/United-States/blog/Morocco.html">Morocco</a>
    posted in
    <a href="http://demo.getyacg.com/United-States" title="View all posts in United States" rel="category tag">United States</a>
  </li>
</ul>
</code>


Since YACG 3.0 you can enable UTF-8 support for your YACG site with the following options:

<code>
// UTF-8 SUPPORT
define('UTF', true); // true/false - UTF-8 SUPPORT ON/OFF
// HOW TO HANDLE UTF-8 STRINGS IN URLS
define('TRANSLIT', false); // true/false - TRANSLITERATE UTF-8 STRINGS
define('TRANSLIT_ADVANCED', true); // true/false - TURN ADVANCED TRANSLITERATION ON/OFF(if turned off only accents will be transliterated)
</code>
See [[yacg_configuration|config.inc.php]] description for additional explanations.

Because supporting unicode is a big performance hit(PHP doesn't have native UTF-8 support), it's disabled by default.

====== New Functions ======

There is a number of new functions which are being enabled if you turn unicode support on:

  * utf8_strlen
  * utf8_strpos
  * utf8_strrpos
  * utf8_substr
  * utf8_strtolower
  * utf8_strtoupper
  * utf8_substr_replace
  * utf8_substr_replace
(as you probably guessed, they are all UTF-8-aware counterparts of built-in PHP functions)
  * also you can find some more functions in utf8 directory: ucwords, stristr, etc. If your hook uses them you will have to require corresponding file:<code>require_once UTF8 . '/ucwords.php';
$utf8_string = utf8_ucwords('bla bla');//$utf_string now contains 'Bla Bla'</code>
  * sitemap($letters) - sitemap hook has optional parameter to specify your alphabet. By default english is provided "\d A B C D E F G H I J K L M N O P Q R S T U V W X Y Z". You won't break anything if you'll specify letters in wrong order or leave out some letters

====== Some links for reference: ======

  * More about php functions that can break your UTF-8 content - http://www.phpwact.org/php/i18n/utf-8
  * YACG utf-8 support provided via 'phphutf8' library(http://sourceforge.net/projects/phputf8), therefore you can use all functions(http://phputf8.sourceforge.net/api/) from that library(they're all in utf8 folder). 
  * utf8 support in perl-compatible regular expressions - http://www.php.net/manual/en/reference.pcre.pattern.syntax.php#regexp.reference.unicode

====== Important notes ======


  * UTF-8 is a superset of ASCII, so don't worry english-speaking users - even with UTF-8 on, all your utf8-unaware hooks will still work. 
  * For performance reasons, YACG does not perform any validation of text for "well-formedness", so you have to ensure yourself that all scraped content is encoded in UTF-8 and your keyword files should be UTF-8 too.
  * I wont recommend changing UTF-8 settings after your site have been set up.

  ======Hooks======
 
The hooks are what makes the yacg systems so powerful and flexible.

All hooks are placed in the hooks folder, from where they are all automatically loaded. The name of
The file is irrelevant (it has to have a .php ending). What matters are the functions inside the file.
You can rename all hooks and they will still work.

======Functions====== 

The function name for example in the adsense hook is “adsense()”, but the filename is
google_adsense.php

Take the google_adsense.php hook and look inside:

<code>function adsense($ad_format, $color_border="FFFFFF", $color_bg="FFFFFF", $color_link="1480CD",
$color_url="000000", $color_text="000000", $return = false)
...
</code>

As you can see, there are 7 variables, but only 6 of them are given a value. This 6 values are the
default values, the first ( $ad_format ) has none, so we HAVE to give at least this value when we call
it.

We CAN set the others too, but we MUST set this one.
<code>
adsense();                          // error ( $ad_format isn’t given )
adsense(“250x250_as”);              // works
</code>

Most hooks have default values, so that you can call them without setting any variable, i.e.
<code>
markov();                           // works
</code>

What you also need to learn is that some hooks can return a string instead of directly writing it to the
page. If you set $return=true, the output is returned and we can work with it. This works with many
hooks, just open the hook and look if the function has a $return variable.
<code>
$my_adsense_ad = adsense($ad_format=“250x250_as”, $return=true);
</code>
This will store the generated ad into $my_adsense_ad.

This is important if we want to change content we grab. And we want to do this (not with ads, but
with content)!

When we are finished, its easy to output everything:
<code>
print $my_adsene_ad;
</code>

======Content======

When we grab content from other sites/feeds, we don’t want everybody to know that
we “borrowed” it. We need it to look fresh and self-made. A site that has 10k pages with content
copied 1:1 from Wikipedia will be spotted by the searchengines / other webmasters very quickly. So
what we are gonna do is markov the content. First we grab it from Wikipedia, then we store it, use a
modified markov and then print it on our page.

Always remember: obviously copied content is very bad. Heavy modification is the first rule.

=====markov.php===== 

This markov (yacg version 2.1) is slightly modified so that it doesn’t only use articles
(which wont mostly be on the topic of our keyword) but also text we pass to it directly. Just copy it
over your old one, everything will still work.
<code>
wikipedia();              // works, but prints the original wikipedia content. Easy to spot.
$my_wiki_content = wikipedia($return = true);               //saves it to $my_wiki_content.
markov($text=$my_wiki_content);                             //outputs the markoved content
</code>

Now we have markoved Wikipedia content on our page, so its on topic AND unique

We can also do this in one step (without saving it to a variable):
<code>
markov( $text = wikipedia( $return = true ) );
</code>

If you want to use the markov function on a hook (e.g. live(); ), always remember to set $return=true
or it wont work!

=====your own hook=====

We will write a small hook so that you get the idea of how this is done. You could also use an other
script to start with, but we will start from scratch. This is very basic, just to show how this works:

Our hook will get a string (content we get from Wikipedia, or live, or wherever) and remove all html
tags and all Signs that are not alphanumeric. Make a string alphanumeric is very usefull when you
add keywords automatically. A non-alphanumeric char in keywords.txt will break your YACG installation, 
so you have to make sure all keywords are clean.

Also we will add a link to our main site at the end.

As YACG is well structured, we don’t want to break the system and will include error_reporting and
$return possibility like the other hooks have it.
<code>
<?php // alphanumeric HOOK , © HK 2007, drdoomgod
if (DEBUG == false) {  error_reporting(0);}
function alphanumeric($text, $return=false){
//here is what our function actually does
  if ($return == true)  {  
    return $output;
  } else {
    print $output;
  }
}
?>
</code>

This is the basic stuff we will try to include in all hooks, so all work the same way and its easy to
combine them.

Lets add the stuff in the middle that does what we want. The content we want to work with is stored
in $text . when we are finished we copy it into $output so that we can print or return it.

=====Complete alphanumeric function/hook:=====
<code>
<?php // alphanumeric HOOK , © HK 2007, drdoomgod
if (DEBUG == false) {  
  error_reporting(0);
}
function alphanumeric($text, $return=false){
$text=strip_tags($text);
$text = htmlentities($text);
$text= preg_replace("/[^a-zA-Z0-9 ]/","",$text);
$text.=’<a href=”http://’.THIS_DOMAIN.’”>Visite our main site too!</a>’;
$output=$text;
if ($return == true) {  
  return $output;
  } else {  
  print $output;
  }
}
?>
</code>

This is very short, but does everything we wanted. Strip_tags and htmlentities remove the html stuff,
preg replace filters all chars except numbers 0-9 and letters a-Z, and then a link to the main page is
added.

Removing links and scripts makes a lot of sense since we don’t want to display ads from other sites or
link back to them.

All very easy. Now we save this code to a file in the hooks folder (e.g. 'hk_alphanumeric.php' ) and can
use it.
<code>
alphanumeric( markov( $text = $my_wiki_content, $return = true ) );
</code>
and in one line, without saving the content in $my_wiki_content:
<code>
alphanumeric( markov ($text = Wikipedia( $return = true ), $return = true );
</code>

This will grab content from Wikipedia, shuffle it with the markov algorithm, remove all html and non
alphanumeric chars and add a link at the end.

=====Users_online hook=====

This is another short hook you can use to make you page look a little bit more legit. The hook will
output

'''''XXX users online now, Max: XXX users ( XX.XX.XXXX)'''''

The usercount will be generated random, max users and date will be random based on the url the
YACG is installed on (so the user will always see a different usercount, but the same max users and
date on every page BUT a different one on another YACG installation).

Source is very short, very easy. We will of course use error_reporting and $return.
<code>
<?php // users_online HOOK , © HK 2007, drdoomgod
if (DEBUG == false) {  error_reporting(0);}
function users_online($min=20, $max=60, $return=false){
mt_srand((double)microtime() * 1000000);
$users= $min+(mt_rand(0,$max-$min));
$users.=‘ users online now, Max: ‘;
$day=strlen(THIS_DOMAIN)%28;
$mon=strlen(THIS_DOMAIN)%12;
$date=$day.'.'.$mon.'.2007';
$users.= strlen(THIS_DOMAIN)%10+$max+$min.’ users (‘.$date.’)’;
if ($return == true) {  
  return $users;
  } else {  
    print $users;
  }
}
?>
</code>

You could write this hook in one line, but I think it easier for you to read in the long version.
mt_srand is needed to get random numbers, it initializes the random generator.

When $min and $max is set the hook will output user count between these two values.
If not the default count will be between 20 and 60.
Max usercount is between $min+$max+5 (+5/-5).
By rewriting this a bit, you can also display a fake comment counter with date of post etc.

====== General Info ======

  * To call a hook, enclose it in php tags and specify list of parameters, i.e. <?php adsense('468x60_as','CCCCCC','EEEEEE') ?>

  * $bare option makes hooks return content as an array instead of printing it. You can explore output of any specific hook by using PHP's //print_r// function

====== List of YACG hooks ======

== adashes($value='') (main.php) == 

Replaces all spaces for dashes in the passed string.

== adsense($ad_format, $color_border="FFFFFF", $color_bg="FFFFFF", $color_link="1480CD", $color_url="000000", $color_text="000000") (google_adsense.php) == 

Insert google adsense adblock into your page. Possible $ad_format values:

(**Rectangles**)
  * 728x90_as
  * 468x60_as
  * 234x60_as
  * 120x600_as
  * 160x600_as
  * 120x240_as
  * 336x280_as
  * 300x250_as
  * 250x250_as
  * 200x200_as
  * 180x150_as
  * 125x125_as
(**4-link units**)
  * 728x15_0ads_al
  * 468x15_0ads_al
  * 200x90_0ads_al
  * 180x90_0ads_al
  * 160x90_0ads_al
  * 120x90_0ads_al
(**5-link units**)
  * 728x15_0ads_al_s
  * 468x15_0ads_al_s
  * 200x90_0ads_al_s
  * 180x90_0ads_al_s
  * 160x90_0ads_al_s
  * 120x90_0ads_al_s

e.g.
<code php>
<? adsense('200x90_0ads_al', "FFFFFF", "FFFFFF", "1480CD", "000000", "000000") ?>
</code>

== analytics() (google_analytics.php) == 

Inserts google analytics tracking code.

== array_isearch($str, $array) (main.php) == 

Case-insensitive array_search.

== auctionads($ad_format, $color_border="CFF8A3", $color_bg="FFFFFF", $color_heading ="00A0E2", $color_text="000000", $color_link="008000") (auctionads.php) == 

Dispalays AuctionAds ad block. Possible $ad_format values:

  * 728x90
  * 468x60
  * 300x250
  * 250x250
  * 180x150
  * 160x600
  * 120x600
  * 160x160
  * 468x180
  * 336x160

== blogsearch($keyword = THIS_PAGE_KEYWORD, $engine = 'technorati', $articles = 1, $authority = "n") (blogsearch.php) ==

Will scrape results of a specified blogsearch engine(google, bloglines, icerocket or technorati) and return specified number($articles) of blog articles. Only wikipedia hook can compare to that one in terms of content quality and readability.

== bookmarking($keyword = THIS_PAGE_KEYWORD, $services = 'addthis') (social_bookmarking.php) == 

Displays array of links allowing to quickly add current page of your yacg site to a number of bookmarking services.
By default will output http://addthis.com widget, but you can pass a comma-separated list of services as a second parameter to hand-pick the links that will be shown or pass 'all' to show them all as a list.
e.g.
<code php>
<? bookmarking() ?>
<? bookmarking('digg,delicious,reddit') ?>
</code>

== cache($function = 'markov', $args = array(), $keyword = THIS_PAGE_KEYWORD, $return = false) (main.php) == 

Will call the hook, specified in the $function parameter with $args as a hook parameters and cache it's output. The $keyword parameter defines name, under which the cache file will be saved. If $return parameter is set to true, it will "intercept" and return hooks output. E.g.
<code php>
<?
cache('markov'); // calls markov hook and saves it's output in a //keyword-of-the-current-page.markov// file
$live = cache('live_results', array(THIS_PAGE_KEYWORD, 7), THIS_PAGE_KEYWORD, true); // $live variable now has output of the //liv_results(THIS_PAGE_KEYWORD, 7)// hook
?> 

== c2url($category = THIS_PAGE_CATEGORY) (main.php) == 

Transforms category name into category url.
<code php>
c2url('category name'); //http://your.site.name/path/to/yacg/category/category-name
</code>

== categories($ul = false) (main.php) == 

Outputs an unordered list of all categories. If $ul option set to //false//, enclosing <ul> tags won't be displayed.

== category() (main.php) == 

Prints current category name.

== cb($keyword = THIS_PAGE_CATEGORY, $numresults = 3, $gendescr = true, $thumbs = true, $bare = false, $cbcategory = '-1', $cbsubcategory = '-1') (clickbank.php) ==

Will print specified number of relevant offers from http://clickbank.com. If $gendescr option is set to true, hook will attempt to generate offer description based on actual sales letter. If $thumbs is true, hook will fetch thumbnails from google images to accompany offer's description.

== contact_form() (contact_form.php) == 

Prints contact form.

== cut_cat($line) (main.php) == 

Accepts a line from keywords.txt or //$keyarr//("category,keyword" or "keyword") and returns keyword.

== cut_key($line) (main.php) == 

Accepts a line from keywords.txt or //$keyarr//("category,keyword" or "keyword") and returns category name.

== description() (main.php) == 

Prints contents of SITE_DESCRIPTION option from the //config.inc.php//.

== digg($keyword = THIS_PAGE_KEYWORD, $items = 5) (digg.php) == 

Scrapes specified number of digg search results items and adds them to your page.

== domain() (main.php) == 

Prints your site URL.
<code php>
domain(); //http://your.site.name/path/to/yacg/
</code>

== error404() (main.php) == 


Returns HTTP 404 (Not found) error.

== feed() (main.php) == 

Prints link to your site's RSS feed.
<code php>
feed(); //<link rel="alternate" type="application/rss+xml" title="Your Site RSS Feed" href="http://your.site.name/path/to/yacg/feed.xml" />
</code>

== fetch($url, $postdata = false) (main.php) == 

Fetches remote page contents. You can specify $postdata option to make a POST request.

== fetchcache($url, $textonly = false) (main.php) == 


Fetch specified URL from google cache. Setting $textonly to //true// will automatically strip all formatting.

== flickr($keyword = THIS_PAGE_KEYWORD, $items = 8, $bare = false) (flickr.php) == 

Will display chosen number of images from flickr, related to $keyword. If you'll set $bare to //true//, array of links to the images will be returned, e.g.
<code php>
<? print_r(flickr(THIS_PAGE_KEYWORD, 2, true)) ?>
</code>
will print:
<code>
Array(
    [0] =>Array
        (
            [0] =>http://farm2.static.flickr.com/1190/1394366430_fae2d54d71.jpg
            [1] =>http://farm2.static.flickr.com/1190/1394366430_fae2d54d71_s.jpg
            [2] =>TV news trucks at St George courthouse for Warren Jeffs' trial
        )
    [1] =>Array
        (
            [0] =>http://farm2.static.flickr.com/1437/1466535148_a0a127aedf.jpg
            [1] =>http://farm2.static.flickr.com/1437/1466535148_a0a127aedf_s.jpg
            [2] =>OMG!  Warren Jeffs has escaped, and is out looking for more wives at the biker rally!
        )
)
</code>

== googleimg($keyword = THIS_PAGE_KEYWORD, $items = 5, $bare = false) (google_images.php) ==

Will display specified number of images from google image search.

== k2url($keyword = '') (main.php) == 

Transforms line from keywords.txt into full url of corresponding page, e.g.
<code php>
$url = k2url(); //$url = 'http://your.site.name/path/to/yacg/blog/current-page-keyword.html'
$url = k2url($keyarr[5]); //$url = 'http://your.site.name/path/to/yacg/blog/sixth-keyword.html'
</code>

== keyword() (main.php) == 

Will print current page's keyword.

== ktime($keyword = THIS_PAGE_KEYWORD, $hook = PREVIEW_HOOK) (main.php) == 

Will return keyword publication timestamp, based on cache file modification time(PREVIEW_HOOK by default). You will usually pass it on to PHP's //date// function to get nicely formatted date.

== links($items = '20', $ord = false, $tagcloud = false, $bare = false) (main.php) == 

Will return list of links of specified length($items), either as simple unordered list(<ul>) or tagcloud-formatted(set $tagcloud to //true//). $ord cat take the following values:
  * 'RAND': list will sorted randomly
  * 'ASCE': ascending sort order
  * 'DESC': descending sort order
  * false: will be ordered by publication time

If $bare is set to true, associative array will be returned, e.g.:
<code php>
<? print_r(links(7, false, false, true)) ?>
</code>
will print:
<code>
Array(
    [0] =>Array
        (
            [key] =>United States,C programming language
            [url] =>http://yacg.com/United-States/blog/C-programming-language.html
            [timestamp] =>1208528165
        )
    [1] =>Array
        (
            [key] =>Hurricane Katrina,Warren Jeffs
            [url] =>http://yacg.com/Hurricane-Katrina/blog/Warren-Jeffs.html
            [timestamp] =>1208528179
        )
)
</code>

== list_category($bare = false, $cat = THIS_PAGE_CATEGORY, $preview = PREVIEW_HOOK) (main.php) == 

This hook only works in //category.php// template and displays unordered list of current category articles. YACG will search for cached content of PREVIEW_HOOK for each article and if found, will accompany link with a 300-symbol preview. As with //links// or //flickr// hook, setting $bare to //false//, will make it return associative array of links:
<code php>
<? print_r(list_category(true)) ?>
</code>
will print:
<code>
Array(
    [0] =>Array
        (
            [key] =>C programming language
            [url] =>http://yacg.com/United-States/blog/C-programming-language.html
            [timestamp] =>1208528165
            [preview] =>C is a general-purpose, block structured, procedural, imperative computer programming language developed in 1972 by Dennis Ritchie at the Bell Telephone Laboratories for use with the Unix operating system. It has since spread to many other platforms. Although C was designed as a system implementati
        )
    [1] =>Array
        (
            [key] =>Priyanka Chopra
            [url] =>http://yacg.com/United-States/blog/Priyanka-Chopra.html
            [timestamp] =>1206898347
            [preview] =>Priyanka Chopra (Hindi: प्रियंका चोपड़ा; born July 18, 1982) is an Indian film actress and former Miss World who works in Bollywood films.After winning the title of Miss India World and later becoming Miss World 2000, Chopra made her acting debut with Anil Sharma's T
        )
)
</code>

== live($keyword = THIS_PAGE_KEYWORD, $items = 5, $bare = false, $market = 'en-US') (live_results.php) == 

Will scrape and print search.msn.com SERPs. Number of search results to print is defined through $items parameter. Change $market to get local results.

== loadcache($filename) (main.php) == 

Will return contents of specified cache file if such file exists and has not expired(see CACHE_TIME), otherwise returns false.

== markov($gran = 5, $num = 200, $letters_line = 65, $content = false) (markov.php) == 

Will return content generated by markov algorithm based on contents of //articles// folder OR content passed via //$content// variable. The content will be of length specified by $num parameter. Line length is specified by $letters_line parameter.

== metadescription($function = PREVIEW_HOOK, $args = array()) (main.php) == 

Prints "meta description" tag. By default content is being generated by PREVIEW_HOOK, but this can be modified by changing hook arguments, which are exactly the same as //cache// hook parameters.

== metakeywords($keyword = THIS_PAGE) (main.php) == 

Prints "meta keywords" tag with current page keyword as a content. e.g.
<code html>
<meta name="keywords" content="My current keyword" />
</code>

== navigation($keyword = THIS_PAGE_KEYWORD, $category = THIS_PAGE_CATEGORY) (main.php) == 

Works only on //page.php// templates. Will print links to previous and next articles(keywords).

== pages($ul = false) (main.php) == 

Prints unordered list of site's pages($pages array). If $ul parameter set to //true// will omit  opening and closing tags(<ul>).

== peakclick($keyword = THIS_PAGE_KEYWORD, $items = 5, $thumbs = true, $bare = false) (peakclick.php) == 

Will print peakclick ads.

== photobucket($keyword = THIS_PAGE_KEYWORD, $items = 6, $bare = false) (photobucket.php) ==

Will display specified number of relevant images from photobucket.com.

== returntext($function = 'markov', $args = array()) (main.php) ==

Will return output of the hook, instead of printing it on the page.

== savecache($data, $file) (main.php) == 

If CACHE is set to true, will save passed data to //$file// in //cache// folder.

== sitemap($letters = "\d A B C D E F G H I J K L M N O P Q R S T U V W X Y Z") (sitemap.php) == 

Will print site sitemap - list of links sorted alphabetically. Accepts custom alphabet as a parameter.

== sitename() (main.php) == 

Will print contents of SITE_NAME option.

== statcounter() (statcounter.php) == 

Prints [[http://statcounter.com|statcounter]] tracking code.

== strip_cats($lines) (main.php) == 

Will remove category name from each line of specified array of keywords and return resulting array.

== strip_keys($lines) (main.php) == 

Extracts list of categories from specified array of keywords(e.g. array of keywords.txt lines).

== table($items = 2, $timeformat = '\P\u\b\l\i\s\h\e\d \o\n F j, Y, g:i a') (articles_table.php) == 

Prints specified number($items) of links to articles, accompanied by relevant //flickr// images and publication time.

== template() (main.php) == 

Will print contents of LOCAL_TEMPLATE option.(YACG 2.x legacy)

== title($keyword = THIS_PAGE) (main.php) == 

Will print //title// tag for current page, e.g.
<code html>
<title>Your site &raquo; Current keyword</title>
</code>

== umax($keyword = THIS_PAGE_KEYWORD, $items = 5, $bare = false) (umax.php) == 

Will print [[http://umaxlogin.com/|umax]] ads.

== video($source ='youtube', $keyword = THIS_PAGE_KEYWORD, $width = '400', $height = '300', $params = '') (video.php) == 

Depending on $source option will display relevant video from youtube, vimeo, flickr, bliptv or viddler.

== wikipedia($keyword = THIS_PAGE_KEYWORD, $language = "en", $format = "html", $size = 10000, $images = false) (wikipedia.php) == 

Will print relevant wikipedia article. You can choose language($language parameter), limit article size($size) and choose to scrape images or not($images).

== yahooimg($keyword = THIS_PAGE_KEYWORD, $items = 5, $bare = false) (yahoo_images.php) == 

Scrapes specified number of images from yahoo image search.

== youtube($keyword = THIS_PAGE_KEYWORD, $width = '400', $height = '300', $params = '') (youtube.php) == 

Embeds relevant video from youtube.
