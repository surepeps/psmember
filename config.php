<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate PHP Social Networking Platform
// | Copyright (c) 2016 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+


if ( file_exists( dirname( __FILE__ ) . '/config-local.php' ) ) {
	require_once dirname( __FILE__ ) . '/config-local.php';
} else {
	// MySQL Hostname
	$sql_db_host = "localhost";
	// MySQL Database User
	$sql_db_user = "psmembers_livebduser";
	// MySQL Database Password
	$sql_db_pass = "app_strastic@987";
	// MySQL Database Name
	$sql_db_name = "psmembers_livedb";

	// Site URL
	$site_url = "https://app.psmembers.com";
}

// Purchase code
$purchase_code = "772b2d92-2e99-4b87-a03d-29bb32d55b27"; // Your purchase code, don't give it to anyone.
