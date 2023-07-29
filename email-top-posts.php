<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
error_reporting(E_ALL);
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);



$query = "SELECT `Wo_Users`.email,`Wo_Users`.user_id FROM `Wo_Users`";


    $query = mysqli_query($sqlConnect,$query);

    $allemails = array();

    while($Buyerdetails = mysqli_fetch_array($query)) { 

        //$user_data = Wo_UserData($Buyerdetails['user_id']);

        $allemails [] = array("email"=>$Buyerdetails['email']);
    }


/*,"email"=>"miquellehenderson@gmail.com"*/
$allemails = array("email"=>"testcheck123@mailinator.com");

$query = "SELECT * FROM Wo_Posts WHERE postType='post' ORDER BY `time` DESC LIMIT 0,5";

$query = mysqli_query($sqlConnect,$query);

$dynamicdataarray = array();


$i = 0;
while($Postsdetail = mysqli_fetch_array($query)) { 

	$userid = $Postsdetail['user_id'];


	$userdata=  Wo_UserData($userid);

	$useravatar = $userdata['avatar'];

	$fullname = $userdata['first_name'].' '.$userdata['last_name'];

	$postid = $Postsdetail['post_id'];	

	$likecounts 	= Wo_CountLikes($postid);
	$sharecounts 	= Wo_CountShares($postid);
	$commentscounts = Wo_CountPostComment($postid);


	$dynamicdataarray['subject'] = $dynamicdataarray ['postheading0'];
	$dynamicdataarray ['postheading'.$i] = urldecode(strip_tags($Postsdetail['postText']));

	$dynamicdataarray ['userimagepath'.$i] = $useravatar;
	$dynamicdataarray ['username'.$i] = $fullname;


	$dynamicdataarray ['propertytitle'.$i] = $Postsdetail['postLinkTitle'];
	$dynamicdataarray ['propertydescription'.$i] = $Postsdetail['postLinkContent'];
	$dynamicdataarray ['propertyurl'.$i] = $Postsdetail['postLink'];

	$dynamicdataarray ['commentscount'.$i] = $commentscounts;
	$dynamicdataarray ['sharecount'.$i] = $sharecounts;
	$dynamicdataarray ['likescount'.$i] = $likecounts;

/*+*/
	$dynamicdataarray ['imgpath'.$i] = "https://app.strastic.com/".$Postsdetail['postLinkImage'];

	if($Postsdetail['postLinkImage']=="")
		$dynamicdataarray ['imgpath'.$i] = "";

	if($Postsdetail['postFileThumb']!="")
		$dynamicdataarray ['imgpath'.$i] = "https://app.strastic.com/".$Postsdetail['postFileThumb'];

	if($Postsdetail['postYoutube']!="")
			$dynamicdataarray ['imgpath'.$i] = "https://img.youtube.com/vi/".$Postsdetail['postYoutube']."/0.jpg";
	/*else
		$dynamicdataarray ['imgpath'.$i] = '';*/
	
	$i++;		
}



 Wo_send_email_all_users_top_posts($allemails,$dynamicdataarray);
	
?>