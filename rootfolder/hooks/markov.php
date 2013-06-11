<?php // MARKOV CHAINS HOOK
function markov($gran = 5, $num = 200, $letters_line = 65, $content = false) {
  $combo = "";
  $output = "";
  if ($content == false) {
    if (is_dir(LOCAL_ARTICLES)) {
      if ($dh = opendir(LOCAL_ARTICLES)) {
        while (($file = readdir($dh)) !== false) {
          if (substr($file,  - 4) == '.txt') {
            $combo .= @file_get_contents(LOCAL_ARTICLES.$file);
          }
        }
        closedir($dh);
      }
    }
  } else {
    $combo = $content;
  }
  $combo = preg_replace('/\s\s+/', ' ', $combo);
  $combo = preg_replace('/\n|\r/', '', $combo);
  $combo = strip_tags($combo);
  $combo = htmlspecialchars($combo);
  $combo = explode(".",$combo);
  shuffle($combo);
  $combo = implode(".", $combo);
  $textwords = explode(" ", $combo);
  $loopmax = count($textwords) - ($gran - 2) - 1;
  $frequency_table = array();
  for ($j = 0; $j < $loopmax; $j++) {
    $key_string = " ";
    $end = $j + $gran;
    for ($k = $j; $k < $end; $k++) {
      $key_string .= $textwords[$k].' ';
    }
    $frequency_table[$key_string] = ' ';
    $frequency_table[$key_string] .= $textwords[$j + $gran]." ";
    if (($j+$gran) > $loopmax ) {
      break;
    }
  }
  $buffer = "";
  $lastwords = array();
  for ($i = 0; $i < $gran; $i++) {
    $lastwords[] = $textwords[$i];
    $buffer .= " ".$textwords[$i];
  }
  for ($i = 0; $i < $num; $i++) {
    $key_string = " ";
    for ($j = 0; $j < $gran; $j++) {
      $key_string .= $lastwords[$j]." ";
    }
    if (isset($frequency_table[$key_string])) {
      $possible = explode(" ", trim($frequency_table[$key_string]));
      mt_srand();
      $c = count($possible);
      $r = mt_rand(1, $c) - 1;
      $nextword = $possible[$r];
      $buffer .= " $nextword";
      if (strlen($buffer) >= $letters_line) {
        $output .= $buffer;
        $buffer = " ";
      }
      for ($l = 0; $l < $gran - 1; $l++) {
        $lastwords[$l] = $lastwords[$l + 1];
      }
      $lastwords[$gran - 1] = $nextword;
    } 
    else {
      $lastwords = array_splice($lastwords, 0, count($lastwords));
      for ($l = 0; $l < $gran; $l++) {
        $lastwords[] = $textwords[$l];
        $buffer .= ' '.$textwords[$l];
      }
    }
  }
  $output = trim($output);
  echo $output;
}
?>