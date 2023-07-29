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
global $wo, $sqlConnect;

    $query_one      = " SELECT `user_id` FROM " . T_USERS . " WHERE `active` = '1' AND verified = '1' ";
    

	$sql            = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $user_id = $fetched_data['user_id'];
		echo $user_id."<br>";
    }
?>