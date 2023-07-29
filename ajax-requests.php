<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2017 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+

require_once('assets/init.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

function get_stratistic_points($action) {

    global $sqlConnect;

    $query = mysqli_query($sqlConnect, "SELECT $action FROM `wo_Strastic_point`");
    
    $Poitdetails = mysqli_fetch_array($query);

    return $Poitdetails[$action];
}

mysqli_close($sqlConnect);
unset($wo);
?>