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
//echo "---";
ob_start();
?>
<body data-gr-c-s-loaded="true" style="padding: 0px;margin: 0px;">
<table style="margin:auto; width:520px; font-family: -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue","Fira Sans",Ubuntu,Oxygen,"Oxygen Sans",Cantarell,"Droid Sans","Apple Color Emoji","Segoe UI Emoji","Segoe UI Emoji","Segoe UI Symbol","Lucida Grande",Helvetica,Arial,sans-serif">
<tbody><tr><td width="520px" style="    font-family: -apple-system,system-ui,BlinkMacSystemFont,&quot;Segoe UI&quot;,Roboto,&quot;Helvetica Neue&quot;,&quot;Fira Sans&quot;,Ubuntu,Oxygen,&quot;Oxygen Sans&quot;,Cantarell,&quot;Droid Sans&quot;,&quot;Apple Color Emoji&quot;,&quot;Segoe UI Emoji&quot;,&quot;Segoe UI Emoji&quot;,&quot;Segoe UI Symbol&quot;,&quot;Lucida Grande&quot;,Helvetica,Arial,sans-serif;">   
<?php
$stories = Wo_GetPosts_Whatsup(array('limit' => 5, 'publisher_id' => 0,'placement' => 'multi_image_post','anonymous' => true),$_GET['u']);
			foreach ($stories as $wo['story']) {
	
?>

<div class="post-container" style="max-width:514px">
<div class="post" id="post-<?php echo $wo['story']['id']; ?>" data-post-id="<?php echo $wo['story']['id']; ?>" data-post-type="">
        <div class="panel panel-white panel-shadow" style="border: 2.5px solid #55acee; border-radius: 4px;margin-bottom:0px;background-color: #fff;border: 1px solid transparent;border-radius: 3px;padding-bottom: 0px;">
      <!-- header -->
      <div class="post-heading" style="height: 70px;padding: 20px 15px;height: 50px;">
   <div class="pull-left image" style="float:left!important;">
            <a href="<?php echo $wo['story']['publisher']['url']; ?>" data-ajax="?link1=timeline&amp;u=<?php echo $wo['story']['publisher']['username']; ?>" class="avatar wow_post_usr_ava wow_post_usr_ava_active" style="width: 46px;height: 46px;display: block;border-radius: 50%;position: relative;margin-top: -3px;margin-right: 10px;box-shadow: 0 0 0 2.5px #55acee;">
<img src="<?php echo $wo['story']['publisher']['avatar']; ?>" id="updateImage-<?php echo $wo['story']['publisher']['user_id']?>" alt="<?php echo $wo['story']['publisher']['name']; ?> profile picture" style="width:100%;height:100%;object-fit: cover;  border-radius: 50%;vertical-align: middle;">
            <?php if($wo['story']['publisher']['verified'] == 1) { ?>     
				 <span class="verified-color" style="display:none;position: absolute;bottom: 9px;right: 2px;border-radius: 50%;width: 10px;height: 10px;/* align-items: center; *//* justify-content: center; *//* color: #55acee; *//* background-color: #fff; */">
<img src="https://app.strastic.com/themes/wowonder/img/email-images/verified-icon1.png">
            </span>
			<?php } ?>
                  </a>
         </div>
   <!-- Hide dropdown -->
      <!-- Hide dropdown -->
   <div class="pull-right" style="    float: right!important; max-height: 10px;">
            <span class="dropdown" style="position: relative;">
         <a href="#" style="outline: 0!important;cursor: pointer;text-decoration: none;color: #666;background-color: transparent;	float: right;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <span class="pointer" style="cursor: pointer;font-weight: bold;font-size: 15pt;width: 100px;">
              &#x2304;
            </span>
         </a>
         <ul class="dropdown-menu post-privacy-menu post-options post-recipient" role="menu" style="border-radius: 5px;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.23);padding: 8px 0;transform: scale3d(.8,.8,1);transform-origin: right top;display: block;opacity: 0;visibility: hidden;border: 0;">
                        <li>
               <div class="pointer" onclick="Wo_OpenPostDeleteBox(<?php echo $wo['story']['id']; ?>);">
                  Delete Post               </div>
            </li>
                        <li>
               <div class="save-post pointer" onclick="Wo_SavePost(<?php echo $wo['story']['id']; ?>);">
                                    Save Post                                 </div>
            </li>
            <li>
               <div class="report-post pointer" onclick="Wo_ReportPost(<?php echo $wo['story']['id']; ?>);">
                                    Report Post                                 </div>
            </li>
            <li>
               <a href="https://dev.strastic.com/post/<?php echo $wo['story']['id']; ?>_how-to-stop-caring-and-start-posting-this-is-the-perfect-time-for-you-to-focus-o.html" target="_blank">
               Open post in new tab               </a>
            </li>d
            <li>
               <a class="pointer" onclick="Wo_HidePost(<?php echo $wo['story']['id']; ?>);">
               Hide post               </a>    
            </li>
         </ul>
      </span>
         </div>
   <!-- Hide dropdown -->
      <!-- Hide dropdown -->
   <div class="meta">
      <div class="title h5" style="margin-bottom: 0;margin-top: 1px;font-size: 14px;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;">
                  <span class="user-popover" data-type="user" data-id="135" style="margin-bottom: 0;margin-top: 1px;font-size: 14px;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;">        
				  <a href="<?php echo $wo['story']['publisher']['url']; ?>" data-ajax="?link1=timeline&amp;u=<?php echo $wo['story']['publisher']['username']; ?>" style="color: #555;margin-right: -7px;outline: 0!important;	text-decoration: none;"><b style="margin-right: 7px;font-weight: 700;"><?php echo $wo['story']['publisher']['name']; ?></b></a>
         </span>
                                 <span style="color:#e13c4c">
                  <i class="fa fa-bolt fa-fw" title="Ultima Member" data-toggle="tooltip"></i>
                  </span>
                                                                                                                                                                           <small class="small-text">
                  </small>
                                 </div>
      <h6 style="font-size: 12px;margin-top: 7px;margin-bottom: 10px;font-family: inherit;font-weight: 500;color: inherit;line-height: 1.1;">     <span class="time">
         <a style="color:#9197a3; text-decoration: none;" class="ajax-time" href="<?php echo $wo['story']['url'];?>"  title="<?php echo date('c',$wo['story']['time']); ?>" target="_blank"><?php echo Wo_Time_Elapsed_String($wo['story']['time']); ?></a>
         </span>
         <!-- Hide privacy -->
                  <!-- Hide privacy -->
                  <span  style="color:#000" onclick="Wo_Translate($(this).attr('id'),$(this).attr('data-language'))" title="Translate" class="pointer time" id="<?php echo $wo['story']['id']; ?>" data-language="en" data-trans-btn="<?php echo $wo['story']['id']; ?>">
         - Translate         </span> 

         <?php
            $small_icon = '';
            $icon_type = '';
            if(!empty($wo['story']['postVine'])) { 
               $small_icon = 'vine';
               $icon_type = 'Vine';
            } else if (!empty($wo['story']['postVimeo'])) {
               $small_icon = 'vimeo';
               $icon_type = 'Vimeo';
            } else if (!empty($wo['story']['postFacebook'])) {
               $small_icon = 'facebook-official';
               $icon_type = 'Facebook';
            } else if (!empty($wo['story']['postDailymotion'])) {
               $small_icon = 'film';
               $icon_type = 'Dailymotion';
            } else if (!empty($wo['story']['postYoutube'])) {
               $small_icon = 'youtube-square';
               $icon_type = 'Youtube';
            } else if (!empty($wo['story']['postPlaytube'])) {
              // $small_icon = 'play-circle';
              // $icon_type = 'PlayTube';
            } else if (!empty($wo['story']['postSoundCloud'])) {
               $small_icon = 'soundcloud';
               $icon_type = 'SoundCloud';
            }
		

		if (!empty($icon_type)) {
            ?>
         <span style="color:#9197a3; text-transform: capitalize;"> - <i class="fa fa-<?php echo $small_icon; ?>"></i> <?php echo $icon_type; ?>
         </span>
         <?php  } ?>
      </h6>
   </div>
</div>      <!-- header -->
  <div class="post-description" id="post-description-<?php echo $wo['story']['id']; ?>" style="    padding: 15px 15px 8px;padding-top: 0px;">
        <a style="text-decoration:unset;color: #555;" href="<?php echo $wo['story']['url'];?>">
        		
		


	  
        <!-- shared_post -->
         <?php include 'themes/wowonder/layout/story/includes/shared_post_wu.phtml'; ?>
        <!-- shared_post -->
		
		<?php  if (empty($current_post['parent_id'])) { ?>
      <!-- product -->
       <?php include 'themes/wowonder/layout/story/includes/product_wu.phtml'; ?>
      <!-- product -->
		<?php } ?>

      <!-- feeling 1-->  
	  <p dir="auto" style="font-size: 14px;color: #555;overflow: hidden;word-wrap: break-word;margin: 0 0 10px;">
<span data-translate-text="<?php echo $wo['story']['id']; ?>" style="font-size: 14px;color: #555;overflow: hidden;word-wrap: break-word;margin: 0 0 10px;"> <?php echo $wo['story']['postText']; ?></span>
</p>
      <!-- feeling 2 -->

      <!-- colored post -->
      <?php include 'themes/wowonder/layout/story/includes/colored.phtml'; ?>
      <!-- colored post -->
       
      <!-- embed -->
	<?php if(!empty($wo['story']['postYoutube'])) {  
			$yt_thumb="http://i3.ytimg.com/vi/".$wo['story']['postYoutube']."/hqdefault.jpg"; 
			//"https://img.youtube.com/vi/".$wo['story']['postYoutube']."/sddefault.jpg";
			//list($width, $height) = getimagesize("http://i3.ytimg.com/vi/lOG9KHKP7Vg/hqdefault.jpg"); 
			//$arr = array('h' => $height, 'w' => $width );
			//echo $arr["h"];
	?>	  
		  <div class="post-youtube wo_video_post">
	

	
			<img src="<?php echo $yt_thumb;?>" style="width:100%; max-width:530px; height:auto;">
	  
		 </div>  
	<?php } ?>
	<!-- embed -->
        
        <!-- postMap -->
        <?php if(!empty($wo['story']['postMap']) && empty($wo['story']['postVine']) && empty($wo['story']['postSoundCloud']) && empty($wo['story']['postVimeo']) && empty($wo['story']['postDailymotion']) && empty($wo['story']['postYoutube']) && empty($wo['story']['postPlaytube']) && empty($wo['story']['postDeepsound']) && empty($wo['story']['postFile']) && $wo['config']['google_map'] == 1) { ?>
        <div class="post-map">
          <img src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo $wo['story']['postMap'];?>&zoom=13&size=600x250&maptype=roadmap&markers=color:red%7C<?php echo $wo['story']['postMap'];?>&key=<?php echo $wo['config']['google_map_api'];?>" width="100%">
        </div>
        <?php } ?>
        <!-- postMap -->

        <!-- fetched_url -->
         <?php include 'themes/wowonder/layout/story/includes/fetched_url_wu.phtml'; ?>
        <!-- fetched_url -->

        <!-- event -->
         <?php include 'themes/wowonder/layout/story/includes/event.phtml'; ?>
        <!-- event -->

        <!-- blog -->
         <?php include 'themes/wowonder/layout/story/includes/blog_wu.phtml'; ?>
        <!-- blog -->
        
        <!-- forum -->
         <?php include 'themes/wowonder/layout/story/includes/forum.phtml'; ?>
        <!-- forum -->

        <!-- thread -->
         <?php include 'themes/wowonder/layout/story/includes/thread.phtml'; ?>
        <!-- thread -->
        <!-- offer -->
         <?php 
         if (!empty($wo['story']['offer']) && !empty($wo['story']['offer_id'])) {
           include 'themes/wowonder/layout/story/includes/offer.phtml';
         }
        ?>
        <!-- offer -->
        
        <!-- postFile -->
        <?php if(!empty($wo['story']['postFile'])) { ?>

        <div class="post-file wo_shared_doc_file" id="fullsizeimg">
          <!-- <div style="width: 100%;height: 100%;position: absolute;background-color: rgba(0,0,0,0.3);filter: blur(5px);"></div> -->
            <?php
            $media = array(
                'type' => 'post',
                'storyId' => $wo['story']['id'],
                'filename' => $wo['story']['postFile'],
                'name' => $wo['story']['postFileName'],
                'postFileThumb' => $wo['story']['postFileThumb'],
            );
            echo Wo_DisplaySharedFile($media, '', $wo['story']['cache']);
            ?>
        </div>

        <?php } ?>
        <!-- postFile -->

        <!-- postSticker -->
        <?php if (Wo_IsUrl($wo['story']['postSticker'])): ?>
          <div class="post-file wo_video_post">
            <?php if (strpos('.mp4', $wo['story']['postSticker'])) { ?>
            <video autoplay loop><source src="<?php echo $wo['story']['postSticker']; ?>" type="video/mp4"></video>
            <?php } else { ?>
            <img src="<?php echo $wo['story']['postSticker']; ?>" alt="GIF">
            <?php } ?>
          </div>
        <?php endif; ?>
        <!-- postSticker -->

        <!-- postPhoto -->
        <?php if (Wo_IsUrl($wo['story']['postPhoto'])): ?>
          <div class="post-file" id="fullsizeimg">
            <img src="<?php echo $wo['story']['postPhoto']; ?>" alt="Picture">
          </div>
        <?php endif; ?>
        <!-- postPhoto -->

        <!-- postRecord -->
        <?php if(!empty($wo['story']['postRecord'])) { ?>
        <div class="post-file">
            <?php  
              $media = array(
                'type' => 'post',
                'storyId' => $wo['story']['id'],
                'filename' => $wo['story']['postRecord'],
                'name' => ''
              );
              echo  Wo_DisplaySharedFile($media,'record');
            ?>
        </div>
        <?php } ?>	  
	  
	  

        
<!-- poll -->
<?php
if ($wo['story']['poll_id'] == 1) {
   echo Wo_LoadPage('story/entries/options_wu');
}
?>
<!-- poll -->
		
        
        <div id="fullsizeimg" style="position: relative;max-height: 600px;margin-left: -15px;width: calc(100% + 30px);overflow: hidden;margin-bottom: 5px;">
          <!-- photo_album -->
                      <!-- photo_album -->


          <!-- multi_image -->
                      <!-- multi_image -->
        
        <div class="clear"></div>
        </div>
        <!-- poll -->
                <!-- poll -->
                 <div class="clear"></div>
				 
        <!-- footer -->
         <div class="stats post-actions pull-left" id="" style=" -webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;float: left!important;">
      <div style=" -webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;vertical-align: middle;line-height: 1.42857143;white-space: nowrap;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;user-select: none;border-radius: 3px;font-size: 11px!important;color: #999!important;display: inline-block;padding: 3px 7px;border: 0;margin-top: 5px!important;background: #fff;box-shadow: none;
" class="btn btn-default stat-item post-like-status" title="Likes" onclick="Wo_OpenPostLikedUsers(<?php echo $wo['story']['id']; ?>,'post');">
     <img src="https://app.strastic.com/themes/wowonder/img/email-images/like.png" style="margin-bottom: -3px;">
      <span id="likes">
      <?php echo $wo['story']['post_likes'];?>      </span>
   </div>
      <div style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;vertical-align: middle;line-height: 1.42857143;white-space: nowrap;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;user-select: none;border-radius: 3px;font-size: 11px!important;color: #999!important;display: inline-block;padding: 3px 7px;border: 0;margin-top: 5px!important;background: #fff;box-shadow: none;" class="btn btn-default stat-item post-wonders-status" title="Dislikes" onclick="Wo_OpenPostWonderedUsers(<?php echo $wo['story']['id']; ?>,'post');">
<img src="https://app.strastic.com/themes/wowonder/img/email-images/notlike.png" style="margin-bottom: -3px;">
	  <span id="wonders">
      <?php echo $wo['story']['post_wonders'];?>      </span>
   </div>
      <div style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;vertical-align: middle;line-height: 1.42857143;white-space: nowrap;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;user-select: none;border-radius: 3px;font-size: 11px!important;color: #999!important;display: inline-block;padding: 3px 7px;border: 0;margin-top: 5px!important;background: #fff;box-shadow: none;" class="btn btn-default stat-item post-share-status" title="Share" onclick="Wo_OpenPostSharedUsers(<?php echo $wo['story']['id']; ?>);">
    <img src="https://app.strastic.com/themes/wowonder/img/email-images/share.png" style="margin-bottom: -3px;">
      <span id="post_share"><?php echo $wo['story']['post_wonders'];?></span>
   </div>
   </div>
 
<div class="stats post-actions  pull-right" style="    float: right!important;">
	   <div style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;vertical-align: middle;line-height: 1.42857143;white-space: nowrap;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;user-select: none;border-radius: 3px;font-size: 11px!important;color: #999!important;display: inline-block;padding: 3px 7px;border: 0;margin-top: 5px!important;background: #fff;box-shadow: none;" class="btn btn-default stat-item" title="Comments" onclick="Wo_ShowComments(<?php echo $wo['story']['id']; ?>);">
         <img src="https://app.strastic.com/themes/wowonder/img/email-images/comment_orange.png" style="margin-bottom: -4px;">
      <span id="comments">
      <?php if($wo['story']['post_comments']>0){echo $wo['story']['post_comments'];}else{echo 0;}?>     </span>
   </div>
</div>

<div class="clear" style="    display: block;    clear: both;"></div>
<hr style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;border: 0;height: 0;box-sizing: content-box;border-top: 1px solid #f4f4f4;margin: 5px 0 !important;">
<div class="stats pull-left" id="wo_post_stat_button" style="width: 100%;text-align: center;margin-top: 1px;position: relative;display: flex;align-items: center;justify-content: center;    flex-wrap: wrap;">
   <a style="text-decoration:unset;color: #555;" href="<?php echo $wo['story']['url'];?>"><img src="https://app.strastic.com/themes/wowonder/img/email-images/icon-rows.jpg"></a>
 
   
   </div>
   
   
<div class="clear"></div>
</a>
</div>


<div class="post-footer post-likes" style="    border-top: 1px solid #f4f4f4;    padding: 10px;    background: #f9f9f9;"></div>
<div class="clear"></div>
<div class="post-footer post-wonders"></div>
<div class="clear"></div>
<div class="post-footer post-shared"></div>
<div class="clear"></div>
<div class="post-footer post-reacted"></div>
<div class="clear"></div>
<div class="post-footer post-comments " id="post-comments-<?php echo $wo['story']['id']; ?>" style="    border-top: 0;">
   <div id="hidden_inputbox_comment"></div>
      <div class="comments-list" style="padding: 0;list-style-type: none;">
	  <span class="comment-container"></span>
      
      <?php 
         foreach($wo['story']['get_post_comments'] as $wo['comment']) {
			 
       ?>	  
	  <div class="comment comment-container" id="comment_<?php echo $wo['comment']['id'];?>" data-comment-id="<?php echo $wo['comment']['id'];?>" style="    display: block;    width: 100%;    margin: 20px 0;    margin-top: 5px;">
     <a style="
    float: left!important;
    text-decoration: none;
    background-color: transparent;
" onclick="InjectAPI('{&quot;profile_id&quot; : &quot;<?php echo $wo['comment']['publisher']['id']?>&quot;, &quot;type&quot;:&quot;<?php echo $wo['comment']['publisher']['type']?>&quot;}', event);" class="pull-left" href="<?php echo $wo['comment']['publisher']['url']?>">
      <img style="width: 40px;height: 40px;border-radius: 50%;border: 1px solid #f4f4f4;float: left!important;" class="avatar pull-left" src="<?php echo $wo['comment']['publisher']['avatar']?>?cache=0" alt="avatar">
   </a>
      <div class="comment-body" style="    margin-left: 50px;">
      <div class="comment-heading" style="    display: block;    width: 100%;">
                 <span class="user-popover" data-id="130" data-type="user">
            <a style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;box-sizing: border-box;background-color: transparent;color: #666;text-decoration: none;outline: 0!important;" href="<?php echo $wo['comment']['publisher']['url']?>" data-ajax="?link1=timeline&amp;u=ravikant" onclick="InjectAPI('{&quot;profile_id&quot; : &quot;130&quot;, &quot;type&quot;:&quot;user&quot;}', event);">
               <h4 class="user" style=" -webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;box-sizing: border-box;font-family: inherit;line-height: 1.1;color: inherit;margin-bottom: 10px;font-size: 14px;font-weight: 700;display: inline;margin-top: 0;"><?php echo $wo['comment']['publisher']['name']?></h4>
            </a>
         </span>
                           <span class="verified-color" data-toggle="tooltip" title="Verified User" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;box-sizing: border-box;font-family: -apple-system,system-ui,BlinkMacSystemFont," segoe="" ui",roboto,"helvetica="" neue","fira="" sans",ubuntu,oxygen,"oxygen="" sans",cantarell,"droid="" sans","apple="" color="" emoji","segoe="" ui="" symbol","lucida="" grande",helvetica,arial,sans-serif;="" color:="" #55acee;="" "="">
						   <img src="https://app.strastic.com/themes/wowonder/img/email-images/verified-icon2.png" style="margin-bottom: -3px;">
						   </span>
                  <div class="pull-right comment-options comment_edele_options" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;box-sizing: border-box;float: right!important;font-size: 11px;color: #888;position: relative;display: block;margin-top: 4px;visibility: hidden;">
            
            
                        <span class="pointer comment-icons" id="editComment" onclick="Wo_OpenCommentEditBox(<?php echo $wo['comment']['id'];?>);">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><polygon points="16 3 21 8 8 21 3 21 3 16 16 3"></polygon></svg>
            </span>
                                    <span class="pointer" id="deleteComment" onclick="Wo_DeleteComment(<?php echo $wo['comment']['id'];?>);">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
            </span>
                     </div>
         <span style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 10px;color: #aaa;margin-top: 0;
display: inline;" class="time ajax-time" title="<?php echo date('c',$wo['comment']['time']);?>"><?php echo $wo['comment']['time']?></span>
         <div class="comment-text" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;box-sizing: border-box;font-size: 13px;color: #777;overflow: hidden;width: 85%;"><?php echo $wo['comment']['text']?></div>
         <div class="comment-image">
                     </div>
                  
         <div class="clear"></div>
      </div>

        		
      <span class="comment-options" style="    font-size: 11px;    color: #888;    position: relative;    display: block;    margin-top: 4px;">
                    <span class="comment-icons" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;box-sizing: border-box;margin-left:2px;margin-right:2px;">
                <span style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;box-sizing: border-box;
cursor: pointer;" class="pointer" id="LikeComment" onclick="Wo_RegisterCommentLike(109);">
                   <img src="https://app.strastic.com/themes/wowonder/img/email-images/like.png" style="margin-bottom: -3px;">
                                </span>
                <span id="comment-likes" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;box-sizing: border-box;cursor: pointer;color: #666;" class="pointer" onclick="Wo_OpenPostLikedUsers(109,'comment')">
                <?php echo $wo['comment']['comment_likes'];?>                </span> -
            </span>
            <span class="pointer" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;box-sizing: border-box;cursor: pointer;" id="WonderComment" onclick="Wo_RegisterCommentWonder(109);">
                               <img src="https://app.strastic.com/themes/wowonder/img/email-images/notlike.png" style="margin-bottom: -3px;">                           </span>
            <span id="comment-wonders" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;box-sizing: border-box;cursor: pointer;" class="pointer" onclick="Wo_OpenPostWonderedUsers(109,'comment')">
                <?php echo $wo['comment']['comment_wonders'];?>            </span>
                           -
         <span class="pointer" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;box-sizing: border-box;cursor: pointer;" id="ReplyComment" onclick="Wo_OpenReplyBox(109);">
           <img src="https://app.strastic.com/themes/wowonder/img/email-images/comment_white.png" style="margin-bottom: -3px; width:14px">                     
         </span>
         <span id="comment-replies" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;box-sizing: border-box;">
            <?php echo $replies;?></span>
               </span>

      <div class="comment-replies" style="display: none;">
         <div class="comment-replies-text">
            <div class="reply-container"></div>
         </div>
                           <div class="comment-reply">
			<div class="wo_commreply_combo" id="post-109">
				<img class="avatar" src="<?php echo $wo['user']['avatar'];?>">
				<textarea onkeyup="textAreaAdjust(this, 26)" class="reply-box form-control textarea comment-reply-textarea" onkeydown="Wo_RegisterReply(this.value,109,135, event, 0)" placeholder="Reply to comment" dir="auto"></textarea>
        
			

         <div class="image-comment">
          <div class="pull-right">
            &nbsp;<button type="button" onclick="Wo_RegisterReply2(109,135, 0)" class="btn btn-file" title="Post">
				<svg style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;list-style-type: none;font-size: 11px;color: #888;cursor: pointer;fill: none;stroke: currentcolor;stroke-width: 2;stroke-linecap: round;stroke-linejoin: round;box-sizing: border-box;overflow: hidden;vertical-align: middle;width: 14px;height: 14px;
margin-top: -3px;" fill="#009da0" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" class="feather feather-user-plus">
                  <path d="M3.741 1.408l18.462 10.154a.5.5 0 0 1 0 .876L3.741 22.592A.5.5 0 0 1 3 22.154V1.846a.5.5 0 0 1 .741-.438zM5 13v6.617L18.85 12 5 4.383V11h5v2H5z"></path>
               </svg>
			</button>
         </div>
          <span data-toggle="dropdown" role="button" aria-expanded="false" style="text-decoration: none;cursor: pointer;" onclick="load_ajax_reply_emojii('109','https://dev.strastic.com/themes/wowonder/emoji/');" class="emo-comment2">
            <span class="btn btn-file">
               <svg fill="#009da0" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" class="feather feather-user-plus">
                  <path d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
               </svg>
            </span>
         </span>
         <ul class="dropdown-menu dropdown-menu-right emo-comment-container-109" id="wo_comm_emojis"></ul>


            <form action="#" method="post" class="hidden">
               <input accept="image/x-png, image/jpeg" type="file" name="comment_image" id="comment_reply_image_109" onchange="Wo_UploadReplyCommentImage(109);">
               <input type="hidden" value="" id="comment_src_image_109">
            </form>
            <div class="comment-btn-wrapper">
               <span class="btn btn-file btn-upload-comment">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image" color="#009da0" onclick="document.getElementById('comment_reply_image_109').click(); return false">
                     <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                     <circle cx="8.5" cy="8.5" r="1.5"></circle>
                     <polyline points="21 15 16 10 5 21"></polyline>
                  </svg>
               </span>
            </div>

         </div>
		 </div>
<div class="comment-reply-image-109 comment-image-con"></div>
      <div class="clear"></div>



            <div id="hidden_inputbox_comment_reply"></div>
         </div>
             </div>
   </div>
   <div class="modal fade" id="delete-comment" role="dialog" style="display: none;">
	<div class="modal-dialog modal-sm wow_mat_mdl">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span></button>
				<h4 class="modal-title">Delete Comment</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure that you want to delete this comment ?</p>
			</div>
			<div class="modal-footer">
				<div class="ball-pulse"><div></div><div></div><div></div></div>
				<button id="delete-all-post" type="button" class="btn main btn-mat">Delete</button>
			</div>
		</div>
	</div>
</div>   
</div>
<?php } ?>

</div>


</div>   </div>
          
   <div class="post-commet-textarea dropdown" style="margin-bottom: 20px;-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;position: relative;">
      <div style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;margin-left: -10px;margin-right: -10px;padding: 10px 10px 0;margin-top: -10px;border-top: 1px solid #f9f9f9;transition: all .2s ease;" id="wo_comment_combo" class="remove_combo_on_click wo_comment_combo_<?php echo $wo['story']['id']; ?>" onclick="Wo_ShowCommentCombo(<?php echo $wo['story']['id']; ?>);">
         <div style="display: flex;">
                       <img style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;border: 0;vertical-align: middle;width: 36px;height: 36px;border-radius: 50%;" class="avatar" src="https://dev.strastic.com/upload/photos/d-avatar.jpg?cache=0">
                   <a style="text-decoration:unset;color: #555;width: 100%;max-width: 630px;text-align: left;" href="<?php echo $wo['story']['url'];?>"> <textarea style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;unicode-bidi: -webkit-plaintext;box-sizing: border-box;overflow: auto;margin: 0;font: inherit;font-family: inherit;background-image: none;line-height: 1.42857143;color: #555;display: block;padding: 2px 12px;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;resize: none!important;font-size: 13px;height: 36px;float:left;padding-top: 8px;width: calc(100% - 38px);background-color: #fff;padding-right: 10px;border-radius: 18px!important;margin-left: 10px;border: 1px solid #ededed;" class="form-control comment-textarea textarea" placeholder="Write a comment and press enter" type="text" onkeyup="Wo_LiveComment(this.value,event,<?php echo $wo['story']['id']; ?>);Wo_RegisterComment(this.value,<?php echo $wo['story']['id']; ?>,135, event, 0);" onkeydown="textAreaAdjust(this, 31,'comm'); " dir="auto" oninput="count_char(this,<?php echo $wo['story']['id']; ?>)"></textarea></a>
         </div>
         <div class="comment_combo_footer" style="-webkit-tap-highlight-color: transparent;border-spacing: 0;border-collapse: collapse;box-sizing: border-box;position: relative;padding: 8px 4px;margin-bottom: -10px;background-color: #fff;display: none;">
            <div class="ball-pulse">
               <div></div>
               <div></div>
               <div></div>
            </div>
                        <div class="wo_comment_fopt">
               <div class="pull-right">
                  <button type="button" onclick="Wo_LiveComment(this.value,event,<?php echo $wo['story']['id']; ?>,1);Wo_RegisterComment2(<?php echo $wo['story']['id']; ?>,135, 0)" class="btn btn-file" title="Post">
					<svg fill="#009da0" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" class="feather feather-user-plus">
						<path d="M3.741 1.408l18.462 10.154a.5.5 0 0 1 0 .876L3.741 22.592A.5.5 0 0 1 3 22.154V1.846a.5.5 0 0 1 .741-.438zM5 13v6.617L18.85 12 5 4.383V11h5v2H5z"></path>
					</svg>
				  </button>
               </div>
               <div class="pull-left charsLeft-post"><span id="charsLeft_<?php echo $wo['story']['id']; ?>" data_num="640">
                  640               </span></div>
               <span data-toggle="dropdown" role="button" aria-expanded="false" style="text-decoration: none;cursor: pointer;" onclick="load_ajax_emojii('<?php echo $wo['story']['id']; ?>','https://dev.strastic.com/themes/wowonder/emoji/');" class="emo-comment2">
                  <span class="btn btn-file">
                     <svg fill="#009da0" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg" class="feather feather-user-plus">
                        <path d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                     </svg>
                  </span>
               </span>
               <ul class="dropdown-menu dropdown-menu-right emo-comment-container-<?php echo $wo['story']['id']; ?>" id="wo_comm_emojis"></ul>
               <div class="image-comment">
                  <form action="#" method="post" class="hidden">
                     <input accept="image/x-png, image/jpeg" type="file" name="comment_image" id="comment_image_<?php echo $wo['story']['id']; ?>" onchange="Wo_UploadCommentImage(<?php echo $wo['story']['id']; ?>);">
                     <input type="hidden" value="" id="comment_src_image">
                  </form>
                                    <div class="comment-btn-wrapper">
                     <span class="btn btn-file btn-upload-comment">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image" color="#009da0" onclick="document.getElementById('comment_image_<?php echo $wo['story']['id']; ?>').click(); return false">
                           <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                           <circle cx="8.5" cy="8.5" r="1.5"></circle>
                           <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                     </span>
                                          <div>
                        <span data-record="0" class="btn record-comment-audio" id="<?php echo $wo['story']['id']; ?>">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mic" color="#009da0">
                              <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                              <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                              <line x1="12" y1="19" x2="12" y2="23"></line>
                              <line x1="8" y1="23" x2="16" y2="23"></line>
                           </svg>
                        </span>
                        <span class="hidden" data-comment-rtime="<?php echo $wo['story']['id']; ?>">00:00</span>
                     </div>
                                       </div>
                              </div>
            </div>
                  </div>
      </div>
   </div>
   <div id="comment-image" class="hidden comment-image-con"></div>
   </div>        <!-- footer -->
        
          </div>
	
  </div>
  
  
<?php			
			}
?>
    

      
 </td></tr>
</tbody></table>


</body></html>
<?php 
$output = ob_get_clean();
//echo "Content:<br><hr><br>";

//$regex = '#<\s*?code\b[^>]*>(.*?)</code\b[^>]*>#s';
//$code = preg_match($regex, $text, $matches);
$tagname="h4";
$pattern = "/<$tagname>(.*?)<\/$tagname>/";
preg_match($pattern, $output, $matches);

echo  $matches[1];
echo "<hr>";	
	
echo $output ;



$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Create email headers
$headers .= 'From: noreply@strastic.com'."\r\n".
    'X-Mailer: PHP/' . phpversion();

//mail("miquelle@strastic.com","(test)what's up on Strastic",$output,$headers);
mail("wrecck@gmail.com","(test)what's up on Strastic",$output,$headers);















?>