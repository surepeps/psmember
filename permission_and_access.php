<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

$package_type = $_POST['package_type'];
$Strastic_Points = $_POST['Strastic_Points'];

$Create_Blogs=0;
if($_POST['Create_Blogs']){
	$Create_Blogs=$_POST['Create_Blogs'];
}

$Create_Events=0;
if($_POST['Create_Events']){
	$Create_Events=$_POST['Create_Events'];
}
$Create_Blog=0;
if($_POST['Create_Blog']){
	$Create_Blog=$_POST['Create_Blog'];
}
$Boosted_Post=0;
if($_POST['Boosted_Post']){
	$Boosted_Post=$_POST['Boosted_Post'];
}

$Create_Event=0;
if($_POST['Create_Event']){
	$Create_Event=$_POST['Create_Event'];
}

$Create_Ad=0;
if($_POST['Create_Ad']){
	$Create_Ad=$_POST['Create_Ad'];
}
$Create_Pages=0;
if($_POST['Create_Pages']){
	$Create_Pages=$_POST['Create_Pages'];
}
$Create_Groups=0;
if($_POST['Create_Groups']){
	$Create_Groups=$_POST['Create_Groups'];
}
$Free_Listings = $_POST['Free_Listings'];
$Free_Offers = $_POST['Free_Offers'];
$Free_Featured_Listings = $_POST['Free_Featured_Listings'];
$Free_Schedule_Visits=$_POST['Free_Schedule_Visits'];

/*4 june add more fields*/
$social_automation=0;
if($_POST['social_automation']){
	$social_automation=$_POST['social_automation'];
}
$que_post = $_POST['que_post'];
$social_account = $_POST['social_account'];
$integrations = $_POST['integrations'];
$connected_domains=$_POST['connected_domains'];

$ultimited_actions=0;
if($_POST['ultimited_actions']){
	$ultimited_actions=$_POST['ultimited_actions'];
}
$sub_account = $_POST['sub_account'];
$pages = $_POST['pages'];
$storage = $_POST['storage'];
$funnel=$_POST['funnel'];
$website_admins=$_POST['website_admins'];

$analusis_and_tracking=0;
if($_POST['analusis_and_tracking']){
	$analusis_and_tracking=$_POST['analusis_and_tracking'];
}
$store_products=$_POST['store_products'];

$order_bump=0;
if($_POST['order_bump']){
	$order_bump=$_POST['order_bump'];
}

$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Package_permission` WHERE package_type= '$package_type' ");

if($query->num_rows>0){
	

$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_Package_permission` SET `stastic_point`='$Strastic_Points',`create_blogs`='$Create_Blogs',`create_posts`='$Boosted_Post',`create_events`='$Create_Events',`create_blog`='$Create_Blog',`create_event`='$Create_Event',`create_ad`='$Create_Ad',`create_pages`='$Create_Pages',`create_group`='$Create_Groups',`free_listing`='$Free_Listings',`free_offer`='$Free_Offers',`free_featured_listing`='$Free_Featured_Listings',`free_visits`='$Free_Schedule_Visits',`social_automation`='$social_automation',`que_post`='$que_post',`social_account`='$social_account',`integrations`='$integrations',`connected_domains`='$connected_domains',`ultimited_actions`='$ultimited_actions',`sub_account`='$sub_account',`pages`='$pages',`storage`='$storage',`funnel`='$funnel',`website_admins`='$website_admins',`analusis_and_tracking`='$analusis_and_tracking',`store_products`='$store_products',`order_bump`='$order_bump' WHERE `package_type`='$package_type' ");
	die;
}

else{
	$query   = "INSERT INTO Wo_Package_permission (`package_type`, `stastic_point`, `create_blogs`, `create_posts`, `create_events`, `create_blog`, `create_event`, `create_ad`, `create_pages`, `create_group`, `free_listing`, `free_offer`, `free_featured_listing`,`free_visits`,`social_automation`,`que_post`,`social_account`,`integrations`,`connected_domains`,`ultimited_actions`,`sub_account`,`pages`,`storage`,`funnel`,`website_admins`,`analusis_and_tracking`,`store_products`,`order_bump`) VALUES ('{$package_type}','{$Strastic_Points}','{$Create_Blogs}','{$Boosted_Post}','{$Create_Events}','{$Create_Blog}','{$Create_Event}','{$Create_Ad}','{$Create_Pages}','{$Create_Groups}','{$Free_Listings}','{$Free_Offers}','{$Free_Featured_Listings}','{$Free_Schedule_Visits}','{$social_automation}','{$que_post}','{$social_account}','{$integrations}','{$connected_domains}','{$ultimited_actions}','{$sub_account}','{$pages}','{$storage}','{$funnel}','{$website_admins}','{$analusis_and_tracking}','{$store_products}','{$order_bump}')";


	$sql_query = mysqli_query($sqlConnect, $query);

die;

}


?>
