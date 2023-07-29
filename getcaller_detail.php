<?php

global $wo, $sqlConnect;

require_once('config.php');

require_once('assets/init.php');
 $phone_number  =  $_POST['contact_number'];
 
 
$contactdata = getTableData('contact', ['mobile' => $phone_number], 1);
if($contactdata){

echo   $contactdata['firstname']." ".$contactdata['lastname']."<br><br>".$contactdata['mobile'];

}else{

  echo "Unknown <br><br>".$phone_number;
}



 ?>





