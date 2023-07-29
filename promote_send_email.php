<?php

if($_POST['action']=="request_info"){
	 $name = $_POST['name'];
	 $email = $_POST['email'];
	 $phone = $_POST['phone'];
	 $message = $_POST['message'];
	 $listing_title = $_POST['listing_title'];
	 $author_username = $_POST['author_username'];
	 $author_email = $_POST['author_email'];

	 
	 $to_email = $author_email;
	 $subject  = "Request Info - $listing_title";
	 $body     = ucfirst($name)." is requesting more information about your post \"".$_POST['listing_title']."\".\n\n";
	 $body     .="$message \n\n";
	 $body     .="Contact Information.\n\nName: $name\n";
	 $body     .="Phone: $phone\n";
	 $body     .="Email: $email\n";	 
	 $from     = "noreply@strastic.com";
	 
	 if(mail($to_email,$subject,$body,"From: $from")){
	 echo "success";
	 }else{ echo "not success";}
}

if($_POST['action']=="send_friends"){
	 $name = $_POST['name'];
	 $email = $_POST['email'];
	 $email2 = $_POST['email2'];
	 $message = $_POST['message'];
	 $listing_title = $_POST['listing_title'];
	 $listing_url = $_POST['listing_url'];
	 
	 $author_username = $_POST['author_username'];
	 $author_email = $_POST['author_email'];

	 
	 $to_email = $email2;
	 $subject  = ucfirst($name)." is sharing property listing - $listing_title".".";
	 $body      = $subject."\n\n";
	 $body     .="$message \n\n";
	 $body     .="Property Link: $listing_url";
	 $from     = "noreply@strastic.com";
	 
	 if(mail($to_email,$subject,$body,"From: $from")){
	 echo "success";
	 }else{ echo "not success";}
}

die();
?>