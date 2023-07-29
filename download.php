<?php 
  
  global $wo, $sqlConnect;
  $root=__DIR__;
  require_once($root.'/config.php');
  require_once('assets/init.php');
    $url = filter('f');
          
      // Use basename() function to return the base name of file
      $file_name = basename($url);
    
      $file = file_get_contents($url);
    
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.basename($url).'"');
      header('Expires: 0');
      header('Pragma: public');
      header('Content-Length: ' . filesize($url));
    
      // Clear output buffer
      flush();
      readfile($url);
      exit; 


?>