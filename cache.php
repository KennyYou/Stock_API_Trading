<?php
function beliefmedia_set_transient($transient, $data) {

  $location = '/path/to/public_html/your/cache/' . $transient . '.txt';
   
  if (is_array($data)) $data = serialize($data);

  $fp = @fopen($location, 'w');
  $result = fwrite($fp, $data);
  fclose($fp);

 return ($result !== false) ? true : false;
}


function beliefmedia_get_transient($transient, $cache = '21600') {


  $location = '/path/to/public_html/your/cache/' . $transient . '.txt';
    
  if ( file_exists($location) && (time() - $cache < filemtime($location)) ) {

    $result = @file_get_contents($location);
    if (beliefmedia_is_serialized($result) === true) $result = unserialize($result);
    
    return ($result) ? $result : false;

  } else {

    return false; 

  }
}
/*
  Borrowed Base code from this website, full credit for base code goes to owners. From PHP http://shor.tt/1P7C
	http://www.beliefmedia.com/simple-php-cache
	Returns existing data (as an option)
*/
?>
