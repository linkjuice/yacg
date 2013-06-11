<?php
chdir('../..');

require_once "config.inc.php";

define('THIS_DOMAIN', 'http://'.preg_replace(array("/www\./i", "/\/".preg_quote(str_replace('./', '', ROOT_DIR), '/')."admin\/[\w-]+?\.php/i"), '', $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']));

error_reporting(DEBUG ? E_ALL^E_NOTICE : 0);
clearstatcache();

// REQUIRE MAIN HOOK
require_once LOCAL_HOOKS.'main.php';

// UTF-8 SETTINGS
if (UTF) {
  require_once ROOT_DIR.'includes/utf8/utf8.php';
  require_once UTF8.'/utils/ascii.php';
  require_once UTF8.'/utf8_to_ascii.php';
}

//REQUIRE SIMPLEPIE
require_once ROOT_DIR.'includes/simplepie.inc';
// PICK HOOKS
if (PICK_HOOKS) {
  foreach ($hooks as $hook) require_once LOCAL_HOOKS.$hook;
} elseif ($dh = @opendir(LOCAL_HOOKS)) {
  while (($file = readdir($dh)) !== false) 
    if (substr($file, - 4) == '.php') require_once LOCAL_HOOKS.$file;
  closedir($dh);
}

//BUILD $keyarr AND DETERMINE IF ANY PAGE IS BEING ACCESSED
$keyarr = array();
$keyhandle = fopen(FILE_KEYWORDS, "r");
$i = 0;
while($i < START_KEYS && ($data = fgetcsv($keyhandle, 1000, ",")) !== false) {
  $keyarr[] = implode(',', $data);
  $i++;
}
fclose($keyhandle);

//LOGIN/LOGOUT
if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == 'login') {
    setcookie("yacg", md5($_POST["password"]), time()+86400);
    header("Location: index.php");
    exit;
  } else if ($_REQUEST['action'] == 'logout'){
    setcookie("yacg", "", time()-3600);
    header('Location: index.php');
    exit;
  }
}
//CHECK PASSWORD
$cookpass = isset($_COOKIE['yacg']) ? $_COOKIE['yacg'] : false;
if (!$cookpass && isset($_GET["password"])) $cookpass = md5($_GET["password"]);
if ($cookpass != md5(PASSWORD)) {
  ?>
  <center>
    <form id="login" name="login" method="post" action="index.php">
      <input type="hidden" name="action" value="login" />
      <label>Password:
        <input name="password" type="password" id="password" style="<?php if ($cookpass) echo "border:1px solid red"?>"/>
      </label>
      <input type="submit" name="Submit" value="Submit" />
    </form>
  </center>
  <?php
  exit;
  }
?>