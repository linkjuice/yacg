<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <?php title() ?>
  <?php metadescription() ?>
  <?php feed() ?>
  <link rel="stylesheet" href="<?php domain() ?>css/style.css" type="text/css" media="screen" />
</head>
<body>
  <div id="header">
    <div class="inside">
      <div id="search">
        <form method="get" id="sform" action="<?php domain() ?>">
          <div class="searchimg"></div>
          <input type="text" id="q" value="" name="s" size="15" />
        </form>
      </div>

      <h2><a href="<?php domain() ?>"><?php sitename() ?></a></h2>
      <p class="description"><?php description() ?></p>
      <br />
      <div style="text-align:center"><?php adsense("728x90_as", '000000', '000000', 'FFFFFF', 'FFFFFF', 'BFBFBF') ?></div>
    </div>
  </div>

  <div id="primary" class="single-post">
    <div class="inside">
      <div class="primary">
        <h2>404 - Not Found</h2>
        <p>Sorry, no posts matched your criteria.</p>
      </div>

      <div class="secondary">
        <h2><?php keyword() ?></h2>
        <div class="featured">
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>

  <hr class="hide" />
  <div id="ancillary">
    <div class="inside">
      <div class="block first">
        <h2>About</h2>
        <p>This is the default YACG template. Use it for testing, learning, but please replace it with another one if you don't want to get kicked out of google index.</p>
        <h2>Archives</h2>
        <ul class="counts">
          <?php archives() ?>
        </ul>
      </div>

      <div class="block">
        <h2>Recently</h2>
        <ul class="dates">
          <?php foreach(links(7, false, false, true) as $link): ?>
          <li><a href="<?php echo $link['url']?>"><span class="date"><?php echo date('j.m', $link['timestamp'])?></span> <?php echo cut_cat($link['key'])?> </a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="block">
      <h2>Categories</h2>
      <ul class="counts">
        <?php categories(false) ?>
      </ul>
    </div>

    <div class="clear"></div>
  </div>
</div>

<hr class="hide" />
<div id="footer">
  <div class="inside">
    <p class="copyright">Powered by <a href="http://warpspire.com/hemingway">Hemingway</a> flavored <a href="http://getyacg.com">YACG</a>. <a href="contact">Contact Us</a></p>
    <p class="attributes"><a href="feed:<?php domain() ?>feed.xml">Entries RSS</a></p>
  </div>
</div>
</body>
</html>