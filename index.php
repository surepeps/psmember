<?php


require_once('assets/init.php');


if ($wo['loggedin'] == true) {
    
    $vdata = array(
        'method' => 0,
        'user_id' => $wo['user']['user_id'],
        'adminAccess' => 1,
        'package_id' => $wo['user']['my_package'],
        'newly_loggedIn' => 1,
    );

    $userid = $wo['user']['user_id'];
    $packid = $wo['user']['my_package'];

    // create new package data if user data is not found
    if (!checkUserPackage($userid)){
        insertPackageU(['user_id' => $userid],$userid,$packid);
    }

    
//     setPackageFeaturesData($vdata);
    // exit;
    // Wo_ChangePackage_status();

    // $user_id = $wo['user']['user_id'];
    
    // $checkifpackageexist = countmyuserid($user_id);
    // if($checkifpackageexist == 0){
    //     $protype = 2;
    //     check_user_package_exist($protype);
    // }


    $update_last_seen = Wo_LastSeen($wo['user']['user_id']);
} else if (!empty($_SERVER['HTTP_HOST'])) {
    // $server_scheme = @$_SERVER["HTTPS"];
    // $pageURL = ($server_scheme == "on") ? "https://" : "http://";
    // $http_url = $pageURL . $_SERVER['HTTP_HOST'];
    // $url = parse_url($wo['config']['site_url']);
    // if (!empty($url)) {
    //     if ($url['scheme'] == 'http') {
    //         if ($http_url != 'http://' . $url['host']) {
    //            header('Location: ' . $wo['config']['site_url']);
    //            exit();
    //         }
    //     } else {
    //         if ($http_url != 'https://' . $url['host']) {
    //            header('Location: ' . $wo['config']['site_url']);
    //            exit();
    //         }
    //     }
    // }
}

if (!empty($_GET['ref']) && $wo['loggedin'] == false && !isset($_COOKIE['src'])) {
    $get_ip = get_ip_address();
    if (!isset($_SESSION['ref']) && !empty($get_ip)) {
        $_GET['ref'] = Wo_Secure($_GET['ref']);
        $ref_user_id = Wo_UserIdFromUsername($_GET['ref']);
        $user_date = Wo_UserData($ref_user_id);
        if (!empty($user_date)) {
            if (ip_in_range($user_date['ip_address'], '/24') === false && $user_date['ip_address'] != $get_ip) {
                $_SESSION['ref'] = $user_date['username'];
            }
        }
    }
}
if (!isset($_COOKIE['src'])) {
    @setcookie('src', '1', time() + 31556926, '/');
}
$page = '';


if ($wo['loggedin'] == true && !isset($_GET['link1'])) {
    $page = 'home';
} elseif (isset($_GET['link1'])) {
    $page = $_GET['link1'];
}


if ((!isset($_GET['link1']) && $wo['loggedin'] == false) || (isset($_GET['link1']) && $wo['loggedin'] == false && $page == 'home')) {
    $page = 'welcome';
}
if ($wo['config']['maintenance_mode'] == 1) {
    if ($wo['loggedin'] == false) {
        if ($page == 'admincp' || $page == 'admin-cp') {
           $page = 'welcome';
        } else {
            $page = 'maintenance';
        }
    } else {
        if (Wo_IsAdmin() === false) {
            $page = 'maintenance';
        }
    }
}
if (!empty($_GET['m'])) {
    $page = 'welcome';
}


// if (!empty($_GET['u'])) {
//     $page = $_GET['u'];
// }




if ($wo['config']['membership_system'] == 1) {



    if ($wo['loggedin'] == true) {
            if ($wo['user']['is_pro'] != 0 || Wo_IsAdmin()) {
                switch ($page) {
                    
                    case 'buyer-matches':
                        include('sources/my-property-matches.php');
                        break;
                    case 'import-files':
                        include('sources/import-files.php');
                        break;
                    
                    case 'partners':
                        include('sources/partners.php');
                        break;
                    case 'tracking-code':
                        include('sources/tracking_code.php');
                        break;
                    case 'keyword':
                        include('sources/keyword.php');
                        break;
                
                    case 'my-tasks':
                        include('sources/my-tasks.php');
                        break;
                    case 'map':
                        include('map.php');
                        break;
                    case 'buyers-matching':
                        include('sources/buyers-matching-feature.php');
                        break;
                    case 'conversations':
                        include('sources/conversations.php');
                        break;
                    case 'callHistory':
                        include('sources/callHistory.php');
                        break;
                    case 'voicemail':
                        include('sources/voicemail.php');
                            break;
                            
                    case 'lessons':
                        include('sources/blog.php');
                        break;
                    case 'my-lessons':
                        include('sources/my_blogs.php');
                        break;
                    case 'create-lesson':
                        include('sources/create_blog.php');
                        break;
                    case 'read-lesson':
                        include('sources/read_blog.php');
                        break;
                    case 'edit-lesson':
                        include('sources/edit_blog.php');
                        break;
                    case 'lesson-category':
                        include('sources/blog_category.php');
                        break;
                    case 'maintenance':
                        include('sources/maintenance.php');
                        break;
                    case 'get_news_feed':
                        include('sources/get_news_feed.php');
                        break;
                    case 'video-call':
                        include('sources/video.php');
                        break;
                    case 'video-call-api':
                        include('sources/video_call_api.php');
                        break;
                    case 'home':
                        include('sources/home.php');
                        break;
                    case 'welcome':
                        include('sources/welcome.php');
                        break;
                    case 'register':
                        include('sources/register.php');
                        break;
                    case 'confirm-sms':
                        include('sources/confirm_sms.php');
                        break;
                    case 'confirm-sms-password':
                        include('sources/confirm_sms_password.php');
                        break;
                    case 'forgot-password':
                        include('sources/forgot_password.php');
                        break;
                    case 'reset-password':
                        include('sources/reset_password.php');
                        break;
                    case 'start-up':
                        include('sources/start_up.php');
                        break;
    				case 'start-up2':
    					include('sources/start_up2.php');
    					break;
    				case 'startup-agent':
    					include('sources/startup_agent.php'); /*dvm mods*/
    					break;
    				case 'startup-buyer':
    					include('sources/startup_buyer.php');/*dvm mods*/
    					break;
                    case 'activate':
                        include('sources/activate.php');
                        break;
                    case 'search':
                        include('sources/search.php');
                        break;
                    case 'timeline':
                        include('sources/timeline.php');
                        break;
                    case 'company-directory':
                        include('sources/my_company.php');
                        break;
                    case 'suggested-pages':
                        include('sources/suggested_pages.php');
                        break;
                    case 'liked-pages':
                        include('sources/liked_pages.php');
                        break;
                    case 'joined_groups':
                        include('sources/joined_groups.php');
                        break;
                    case 'go-pro':
                        include('sources/go_pro.php');
                        break;
                    case 'company':
                        include('sources/company.php');
                        break;
                    case 'poke':
                        include('sources/poke.php');
                        break;
                    case 'most_liked':
                        include('sources/most_liked.php');
                        break;
                    case 'groups':
                        include('sources/my_groups.php');
                        break;
                    case 'suggested-groups':
                        include('sources/suggested_groups.php');
                        break;
                    case 'group':
                        include('sources/group.php');
                        break;
                    case 'create-group':
                        include('sources/create_group.php');
                        break;
                    case 'group-setting':
                        include('sources/group_setting.php');
                        break;
                    case 'deal-review':
                        include('sources/deal-review.php');
                        break;
                    case 'deal-site':
                        include('sources/deal-site.php');
                        break;
                    case 'campaign-pipeline':
                        include('sources/campaign-pipeline.php');
                        break;
                    case 'new-campaign-pipeline':
                        include('sources/new-campaign-pipeline.php');
                        break;
                    case 'campaign-builder':
                        include('sources/campaign-builder.php');
                        break;
                    case 'contacts':
                        include('sources/contacts.php');
                        break;
                    case 'find-owner':
                        include('sources/find-owner.php');
                        break;
                    case 'add-company':
                        include('sources/create_company.php');
                        break;
                    case 'all-company':
                        include('sources/all_company.php');
                        break;
                    case 'setting':
                        include('sources/setting.php');
                        break;
                    case 'mydeal-review':
                        include('sources/mydeal-review.php');
                        break;
                    case 'page-setting':
                        include('sources/page_setting.php');
                        break;
                    case 'messages':
                        include('sources/messages.php');
                        break;
                    case 'logout':
                        include('sources/logout.php');
                        break;
                    case '404':
                        include('sources/404.php');
                        break;
                    case 'post':
                        include('sources/story.php');
                        break;
                    case 'game':
                        include('sources/game.php');
                        break;
                    case 'games':
                        include('sources/games.php');
                        break;
                    case 'new-game':
                        include('sources/new_games.php');
                        break;
                    case 'saved-posts':
                        include('sources/savedPosts.php');
                        break;
                    case 'hashtag':
                        include('sources/hashtag.php');
                        break;
                    case 'terms':
                        include('sources/term.php');
                        break;
                    case 'albums':
                        include('sources/my_albums.php');
                        break;
                    case 'album':
                        include('sources/album.php');
                        break;
                    case 'create-album':
                        include('sources/create_album.php');
                        break;
                    case 'contact-us':
                        include('sources/contact.php');
                        break;
                    case 'user-activation':
                        include('sources/user_activation.php');
                        break;
                    case 'upgraded':
                        include('sources/upgraded.php');
                        break;
                    case 'oops':
                        include('sources/oops.php');
                        break;
                    case 'boosted-pages':
                        include('sources/boosted_pages.php');
                        break;
                    case 'boosted-posts':
                        include('sources/boosted_posts.php');
                        break;
                    case 'new-product':
                        include('sources/new_product.php');
                        break;
                    case 'edit-product':
                        include('sources/edit_product.php');
                        break;
                    case 'products':
                        include('sources/products.php');
                        break;
                    case 'my-products':
                        include('sources/my_products.php');
                        break;
                    case 'my-broadcast':
                        include('sources/my_broadcast.php');
                        break;
                    case 'add-list':
                        include('sources/expand_list.php');
                        break;
                    case 'add-sms-list':
                        include('sources/expand_sms_list.php');
                        break;
                    case 'view-my-list':
                        include('sources/view_user_list.php');
                        break;
                    case 'view-my-sms-list':
                        include('sources/view_user_sms_list.php');
                        break;
                    case 'lead':
                        include('sources/lead.php');
                        break;
                    case 'marketing':
                        include('sources/marketing.php');
                        break;
                    case 'my-sms':
                        include('sources/my_sms_blast_module.php');
                        break;
                    case 'my-promoted-listings':
        				include('sources/my_promoted_listings.php');
        				break;
        	     	case 'promoted-listings':
        		     	include('sources/promoted_listings.php');
        			     break;
        			case 'promote-listing':
            			include('sources/promote_listing.php');
            			break;
        			case 'promoted-listings-detail':
        				include('sources/promoted_listings_detail.php');
        				break;
                    case 'property-insight':
                        include('sources/my_insight_dash.php');
                        break;
                    /***Documents *******/

    		    	case 'new-documents':
    			    	include 'sources/new-documents.php';
    				break;
                    case 'new-listings':
                        include('sources/new_listings.php');
                        break;
                    case 'create-fbads':
                        include('sources/create-fbads.php');
                        break;
                    case 'new-property':
                        include('sources/new_single_listing.php');
                        break;
                    case 'promoted-property':
                        include('sources/promote-listing.php');
                        break;
                    case 'property-funnel':
                        include('sources/property_marketing.php');
                        break;
                    case 'fb-ads':
                        include('sources/my_fb_ads.php');
                        break;
                    case 'my-messages':
                        include('sources/my-messages.php');
                        break;
                    case 'get-new-number':
                        include('sources/buy_new_number.php');
                        break;
                    case 'deal-pipeline':
                        include('sources/deal_pipeline.php');
                        break;
                    case 'create-lead':
                        include('sources/create-lead.php');
                        break;
                    case 'site-pages':
                        include('sources/site_pages.php');
                        break;

                    case 'forum':
                        include('sources/forum/forum.php');
                        break;
                    case 'forum-members':
                        include('sources/forum/forum_members.php');
                        break;
                    case 'forum-members-byname':
                        include('sources/forum/forum_members_byname.php');
                        break;
                    case 'forum-events':
                        include('sources/forum/forum_events.php');
                        break;
                    case 'forum-search':
                        include('sources/forum/forum_search.php');
                        break;
                    case 'forum-search-result':
                        include('sources/forum/forum_search.php');
                        break;
                    case 'forum-help':
                        include('sources/forum/forum_help.php');
                        break;
                    case 'forums':
                        include('sources/forum/forumdisplay.php');
                        break;
                    case 'forumaddthred':
                        include('sources/forum/forums_add_thread.php');
                        break;
                    case 'showthread':
                        include('sources/forum/forum_showthread.php');
                        break;
                    case 'threadreply':
                        include('sources/forum/forum_threadreply.php');
                        break;
                    case 'threadquote':
                        include('sources/forum/forum_threadquote.php');
                        break;
                    case 'editreply':
                        include('sources/forum/forum_editreply.php');
                        break;
                    case 'deletereply':
                        include('sources/forum/forum_deletereply.php');
                        break;
                    case 'mythreads':
                        include('sources/forum/forum_mythreads.php');
                        break;
                    case 'mymessages':
                        include('sources/forum/forum_mymessages.php');
                        break;
                    case 'edithread':
                        include('sources/forum/forum_edithread.php');
                        break;
                    case 'deletethread':
                        include('sources/forum/forum_deletethread.php');
                        break;
                     case 'create-event':
                        include('sources/showings/create_showing.php');
                        break;
                    case 'edit-event':
                        include('sources/showings/edit_showing.php');
                        break;
                    case 'events-calendar':
                        include('sources/showings/calendar.php');
                        break;
                    case 'events':
                        include('sources/showings/showings_upcomming.php');
                        break;
                    case 'events-going':
                        include('sources/showings/showings_going.php');
                        break;
                    case 'events-interested':
                        include('sources/showings/showings_interested.php');
                        break;
                    case 'events-past':
                        include('sources/showings/showings_past.php');
                        break;
                    case 'show-event':
                        include('sources/showings/show_showing.php');
                        break;
                    case 'events-invited':
                        include('sources/showings/showings_invited.php');
                        break;
                    case 'my-events':
                        include('sources/showings/my_showings.php');
                        break;
                  case 'oauth':
                        include('sources/oauth.php');
                        break;
                    case 'app_api':
                        include('sources/apps_api.php');
                        break;
                    case 'authorize':
                        include('sources/authorize.php');
                        break;
                    case 'app-setting':
                        include('sources/app_setting.php');
                        break;
                    case 'developers':
                        include('sources/developers.php');
                        break;
                    case 'create-app':
                        include('sources/create_app.php');
                        break;
                    case 'app':
                        include('sources/app_page.php');
                        break;
                    case 'apps':
                        include('sources/apps.php');
                        break;
                    case 'sharer':
                        include('sources/sharer.php');
                        break;
                    case 'movies':
                        include('sources/movies/movies.php');
                        break;
                    case 'movies-genre':
                        include('sources/movies/movies_genre.php');
                        break;
                    case 'movies-country':
                        include('sources/movies/movies_country.php');
                        break;
                    case 'watch-film':
                        include('sources/movies/watch_film.php');
                        break;
                    case 'advertise':
                        include('sources/ads/ads.php');
                        break;
                    case 'wallet':
                        include('sources/ads/wallet.php');
                        break;
                    case 'send_money':
                        include('sources/ads/send_money.php');
                        break;
                    case 'create-ads':
                        include('sources/ads/create_ads.php');
                        break;
                    case 'edit-ads':
                        include('sources/ads/edit_ads.php');
                        break;
                    case 'chart-ads':
                        include('sources/ads/chart_ads.php');
                        break;
                    case 'manage-ads':
                        include('sources/ads/admin.php');
                        break;
                    case 'create-status':
                        include('sources/status/create.php');
                        break;
    				case 'leaderboard':
                        include('sources/leaderboard.php');
                        break;
    			/* dvm mods - to figure out later  */
    				case 'view-listings':
    				    
    					include('sources/view_listings.php');
    					break;
    				case 'testing':
    					include('sources/testing.php');
    					break;
    				case 'add-listing':
    					include('sources/add_listing.php');
    					break;
    				case 'quick-add-listing':
    					include('sources/quick_add_listing.php');
    					break;
    				case 'single-listing':
    					include('sources/single_listing.php');
    					break;
    				case 'edit-listing':
    					include('sources/edit_listing.php');
    					break;
    				case 'comp-listings':
    					include('sources/comp_listings.php');
    					break;
    				case 'fix-and-flip':
    					include('sources/fix-and-flip.php');
    					break;
    				case 'buy-and-hold':
    					include('sources/buy-and-hold.php');
    					break;
    				case 'my-buyers-match':
    					include('sources/my-buyers-match.php');
    					break;
    				case 'update-vip-buyers':
    					include('sources/update-buyers.php');
    					break;
                        case 'autodailer-vip-buyers':
                            include('sources/autodailer-buyers.php');
                            break;
    				case 'contact':
    					include('sources/contact-1.php');
    					break;
                    
    				case 'properties':
    					include('sources/properties.php');
    					break;
                    case 'recordedMessage':
                        include('sources/recordedMessage.php');
                        break;  
                        
    				case 'dashboard':
    					include('sources/dashboard.php');
    					break;
    				case 'equity-deal-finder':
    					include('sources/equity-deal-finder.php');
    					break;
    				
    				case 'buyer-finder':
    					include('sources/buyer-finder.php');
    					break;
    					
                    case 'leads':
                        include('sources/leads.php');
                        break;
                        
    				case 'my-leads':
    					include('sources/my-leads.php');
                        break;
    				
    			    case 'my-affiliates':
    					include('sources/my-affiliates.php');
    					break;  
    					
                    
    				case 'admin-crm':
    					include('sources/admin-crm.php');
    					break;

                    case 'reverse-search':
                        include('sources/reverse-search.php');
                        break;      

                    case 'submit-back':
                        include('sources/submit-back.php');
                        break;  
                        
                    case 'action-plans':
                        include('sources/action-plans.php');
                        break;  
                        
    				case 'my-buyers-match-1':
    					include('sources/my-buyers-match1.php');
    					break;
    				case 'my-power-match':
        				include('sources/power_matching.php');
        				break;
                    case 'my-power-match-buyer':
                        include('sources/power_matching_buyer.php');
                        break;
                    case 'my-power-match-property':
                        include('sources/power_matching_property.php');
                        break;
    				case 'friends-nearby':
    					include('sources/friends_nearby.php');
    		/* end dvm mods - to figure out later  */


    		case 'startup-agent':
    			include('sources/startup_agent.php');
    			break;
    		case 'startup-buyer':
    			include('sources/startup_buyer.php');
    			break;
    	   case 'stripe-payment':
    			include('sources/stripe_payment.php');
    			break;
    		case 'checkout-ultimate':
    			include('sources/checkout.php');
    			break;
    		case 'checkout-vip':
    			include('sources/checkout-vip.php');
    			break;

    		case 'checkout-pro':
    			include('sources/checkout-pro.php');
    			break;

    		case 'new-go-pro':
    			include('sources/new_go_pro.php');
    			break;


    		case 'add-criteria':
    			include('sources/add_criteria.php');
    			break;
    		case 'buyer-dashboard':
    			include('sources/dashboard_buyer.php');
    			break;
    		  case 'buyer-dashboard-find-connections':
    			include('sources/dashboard_buyer_find_connections.php');
    			break;
    		case 'buyer-dashboard-connections':
    			include('sources/dashboard_buyer_connections.php');
    			break;
    		  case 'buyer-dashboard-favorite-properties':
    			include('sources/dashboard_buyer_fav_properties.php');
    			break;
    		  case 'buyer-dashboard-match-properties':
    			include('sources/dashboard_buyer_match_properties.php');
    			break;
    		  case 'buyer-dashboard-suggested-properties':
    			include('sources/dashboard_buyer_suggested_properties.php');
    			break;
    		  case 'buyer-dashboard-offer-pending':
    			include('sources/dashboard_buyer_offer_pending.php');
    			break;
    		  case 'buyer-dashboard-offer-accepted':
    			include('sources/dashboard_buyer_offer_accepted.php');
    			break;
    		  case 'buyer-dashboard-offer-rejected':
    			include('sources/dashboard_buyer_offer_rejected.php');
    			break;
    		  case 'buyer-dashboard-offer-counter':
    			include('sources/dashboard_buyer_offer_counter.php');
    			break;
    		  case 'buyer-dashboard-offer-expired':
    			include('sources/dashboard_buyer_offer_expired.php');
    			break;
    		  case 'buyer-dashboard-visits-pending':
    			include('sources/dashboard_buyer_visits_pending.php');
    			break;
    		  case 'buyer-dashboard-visits-accepted':
    			include('sources/dashboard_buyer_visits_accepted.php');
    			break;
    		  case 'buyer-dashboard-visits-rejected':
    			include('sources/dashboard_buyer_visits_rejected.php');
    			break;
    		  case 'buyer-dashboard-visits-reschedule':
    			include('sources/dashboard_buyer_visits_reschedule.php');
    				break;
    			case 'buyer-dashboard-properties':
    			include('sources/dashboard_buyer_properties.php');
    				break;

    		/*Investor pages*/
    		case 'investor-dashboard':
    			include('sources/dashboard_buyer.php');
    			break;
    		  case 'investor-dashboard-find-connections':
    			include('sources/dashboard_buyer_find_connections.php');
    			break;
    		case 'investor-dashboard-connections':
    			include('sources/dashboard_buyer_connections.php');
    			break;
    		  case 'investor-dashboard-favorite-properties':
    			include('sources/dashboard_buyer_fav_properties.php');
    			break;
    		  case 'investor-dashboard-match-properties':
    			include('sources/dashboard_buyer_match_properties.php');
    			break;
    		  case 'investor-dashboard-suggested-properties':
    			include('sources/dashboard_buyer_suggested_properties.php');
    			break;
    		  case 'investor-dashboard-offer-pending':
    			include('sources/dashboard_buyer_offer_pending.php');
    			break;
    		  case 'investor-dashboard-offer-accepted':
    			include('sources/dashboard_buyer_offer_accepted.php');
    			break;
    		  case 'investor-dashboard-offer-rejected':
    			include('sources/dashboard_buyer_offer_rejected.php');
    			break;
    		  case 'investor-dashboard-offer-counter':
    			include('sources/dashboard_buyer_offer_counter.php');
    			break;
    		  case 'investor-dashboard-offer-expired':
    			include('sources/dashboard_buyer_offer_expired.php');
    			break;
    		  case 'investor-dashboard-visits-pending':
    			include('sources/dashboard_buyer_visits_pending.php');
    			break;
    		  case 'investor-dashboard-visits-accepted':
    			include('sources/dashboard_buyer_visits_accepted.php');
    			break;
    		  case 'investor-dashboard-visits-rejected':
    			include('sources/dashboard_buyer_visits_rejected.php');
    			break;
    		  case 'investor-dashboard-visits-reschedule':
    			include('sources/dashboard_buyer_visits_reschedule.php');
    			break;



    		case 'seller-dashboard':
    			include('sources/dashboard_seller.php');
    			break;
    		case 'seller-dashboard-email-reports':
    			include('sources/dashboard_seller_email_reports.php');
    			break;
    		case 'seller-dashboard-find-connections':
    			include('sources/dashboard_seller_find_connections.php');
    			break;
    		case 'seller-dashboard-connections':
    			include('sources/dashboard_seller_connections.php');
    			break;
    		case 'my-properties':
    			include('sources/dashboard_seller_properties.php');
    			break;
    		case 'seller-dashboard-add-property':
    			include('sources/dashboard_seller_add_property.php');
    			break;
    		case 'seller-dashboard-offer-pending':
    			include('sources/dashboard_seller_offer_pending.php');
    			break;
    		case 'seller-dashboard-offer-accepted':
    			include('sources/dashboard_seller_offer_accepted.php');
    			break;
    		case 'seller-dashboard-offer-rejected':
    			include('sources/dashboard_seller_offer_rejected.php');
    			break;
    		case 'seller-dashboard-offer-counter':
    			include('sources/dashboard_seller_offer_counter.php');
    			break;
    		case 'seller-dashboard-offer-expired':
    			include('sources/dashboard_seller_offer_expired.php');
    			break;
    		case 'seller-dashboard-visits-pending':
    			include('sources/dashboard_seller_visits_pending.php');
    			break;
    		case 'seller-dashboard-visits-accepted':
    			include('sources/dashboard_seller_visits_accepted.php');
    			break;
    		case 'seller-dashboard-visits-rejected':
    			include('sources/dashboard_seller_visits_rejected.php');
    			break;
    		case 'seller-dashboard-visits-reschedule':
    			include('sources/dashboard_seller_visits_reschedule.php');
    			break;

    		case 'seller-dashboard-property-buyers':
    			include('sources/dashboard_seller_property_buyers.php');
    			break;


    		case 'agent-dashboard':
    			include('sources/dashboard_agent.php');
    			break;
    		case 'agent-dashboard-find-connections':
    			include('sources/dashboard_agent_find_connections.php');
    			break;
    		case 'agent-dashboard-all-buyers':
    			include('sources/dashboard_agent_all_buyers.php');
    			break;
    		case 'agent-dashboard-add-buyer':
    			include('sources/dashboard_agent_add_buyer.php');
    			break;
    		case 'agent-dashboard-connections':
    			include('sources/dashboard_agent_connections.php');
    			break;
    		case 'agent-dashboard-my-properties':
    			include('sources/dashboard_agent_properties.php');
    			break;
    		case 'agent-dashboard-add-property':
    			include('sources/dashboard_agent_add_property.php');
    			break;
    		case 'agent-dashboard-offer-pending':
    			include('sources/dashboard_agent_offer_pending.php');
    			break;
    		case 'agent-dashboard-offer-accepted':
    			include('sources/dashboard_agent_offer_accepted.php');
    			break;
    		case 'agent-dashboard-offer-rejected':
    			include('sources/dashboard_agent_offer_rejected.php');
    			break;
    		case 'agent-dashboard-offer-counter':
    			include('sources/dashboard_agent_offer_counter.php');
    			break;
    		case 'agent-dashboard-offer-expired':
    			include('sources/dashboard_agent_offer_expired.php');
    			break;
    		case 'agent-dashboard-visits-pending':
    			include('sources/dashboard_agent_visits_pending.php');
    			break;
    		case 'agent-dashboard-visits-accepted':
    			include('sources/dashboard_agent_visits_accepted.php');
    			break;
    		case 'agent-dashboard-visits-rejected':
    			include('sources/dashboard_agent_visits_rejected.php');
    			break;
    		case 'agent-dashboard-visits-reschedule':
    			include('sources/dashboard_agent_visits_reschedule.php');
    			break;



    		case 'wholeseller-dashboard':
    			include('sources/dashboard_agent.php');
    			break;
    		case 'wholeseller-dashboard-find-connections':
    			include('sources/dashboard_agent_find_connections.php');
    			break;
    		case 'wholeseller-dashboard-all-buyers':
    			include('sources/dashboard_agent_all_buyers.php');
    			break;
    		case 'wholeseller-dashboard-add-buyer':
    			include('sources/dashboard_agent_add_buyer.php');
    			break;
    		case 'wholeseller-dashboard-connections':
    			include('sources/dashboard_agent_connections.php');
    			break;
    		case 'wholeseller-dashboard-my-properties':
    			include('sources/dashboard_agent_properties.php');
    			break;
    		case 'wholeseller-dashboard-add-property':
    			include('sources/dashboard_agent_add_property.php');
    			break;
    		case 'wholeseller-dashboard-offer-pending':
    			include('sources/dashboard_agent_offer_pending.php');
    			break;
    		case 'wholeseller-dashboard-offer-accepted':
    			include('sources/dashboard_agent_offer_accepted.php');
    			break;
    		case 'wholeseller-dashboard-offer-rejected':
    			include('sources/dashboard_agent_offer_rejected.php');
    			break;
    		case 'wholeseller-dashboard-offer-counter':
    			include('sources/dashboard_agent_offer_counter.php');
    			break;
    		case 'wholeseller-dashboard-offer-expired':
    			include('sources/dashboard_agent_offer_expired.php');
    			break;
    		case 'wholeseller-dashboard-visits-pending':
    			include('sources/dashboard_agent_visits_pending.php');
    			break;
    		case 'wholeseller-dashboard-visits-accepted':
    			include('sources/dashboard_agent_visits_accepted.php');
    			break;
    		case 'wholeseller-dashboard-visits-rejected':
    			include('sources/dashboard_agent_visits_rejected.php');
    			break;
    		case 'wholeseller-dashboard-visits-reschedule':
    			include('sources/dashboard_agent_visits_reschedule.php');
    			break;
    		case 'propertyinfo':
    			include('sources/propertyinfo.php');
    			break;
    		case 'make-an-offer':
    			include('sources/make-an-offer.php');
    			break;
    		case 'my-counter-offer':
    			include('sources/my-counter-offer.php');
    			break;
    		case 'my-schedule-visit':
    			include('sources/my-schedule-visit.php');
    			break;
    		case 'marketing-tools':
    			include('sources/marketing_tools.php');
    			break;



    			/* dvm mods - to figure out later ends here */

                    case 'more-status':
                        include('sources/status/more-status.php');
                        break;
                    case 'unusual-login':
                        include('sources/unusual-login.php');
                        break;
                    case 'jobs':
                        include('sources/jobs.php');
                        break;
                    case 'common_things':
                        include('sources/common_things.php');
                        break;
                    case 'funding':
                        include('sources/funding.php');
                        break;
                    case 'my_funding':
                        include('sources/my_funding.php');
                        break;
                    case 'create_funding':
                        include('sources/create_funding.php');
                        break;
                    case 'edit_fund':
                        include('sources/edit_fund.php');
                        break;
                    case 'show_fund':
                        include('sources/show_fund.php');
                        break;
                    case 'memories':
                        include('sources/memories.php');
                        break;
                    case 'refund':
                        include('sources/refund.php');
                        break;
                    case 'offers':
                        include('sources/offers.php');
                        break;
                    case 'nearby_shops':
                        include('sources/nearby_shops.php');
                        break;
                    case 'nearby_business':
                        include('sources/nearby_business.php');
                        break;
                    case 'live':
                        include('sources/live.php');
                        break;
                }
                
            }
            else{
                
                switch ($page) {
                
                
                    
                    case 'import-files':
                        include('sources/import-files.php');
                        break;
                    case 'lessons':
                        include('sources/blog.php');
                        break;
                    case 'my-lessons':
                        include('sources/my_blogs.php');
                        break;
                    case 'create-lesson':
                        include('sources/create_blog.php');
                        break;
                    case 'read-lesson':
                        include('sources/read_blog.php');
                        break;
                    case 'edit-lesson':
                        include('sources/edit_blog.php');
                        break;
                    case 'lesson-category':
                        include('sources/blog_category.php');
                        break;
                     case 'leaderboard':
                        include('sources/maintenance.php');
                        break;
                    case 'add-criteria':
                        include('sources/add_criteria.php');
                        break;
                    case 'maintenance':
                        include('sources/maintenance.php');
                        break;
                    case 'go-pro':
                        include('sources/go_pro.php');
                        break;
                    case 'welcome':
                        include('sources/welcome.php');
                        break;
                    case 'register':
                        include('sources/register.php');
                        break;

                    case 'confirm-sms-password':
                        include('sources/confirm_sms_password.php');
                        break;
                    case 'forgot-password':
                        include('sources/forgot_password.php');
                        break;
                    case 'reset-password':
                        include('sources/reset_password.php');
                        break;
                    case 'activate':
                        include('sources/activate.php');
                        break;
                    case 'logout':
                        include('sources/logout.php');
                        break;
                    case '404':
                        include('sources/404.php');
                        break;
                    case 'contact-us':
                        include('sources/contact.php');
                        break;
                    case 'user-activation':
                        include('sources/user_activation.php');
                        break;
                    case 'upgraded':
                        include('sources/upgraded.php');
                        break;
                    case 'oops':
                        include('sources/oops.php');
                        break;
                  case 'oauth':
                        include('sources/oauth.php');
                        break;
                    case 'start_up':
                        include('sources/start_up.php');
                        break;
                    case 'app_api':
                        include('sources/apps_api.php');
                        break;
                    case 'authorize':
                        include('sources/authorize.php');
                        break;
                    case 'app-setting':
                        include('sources/app_setting.php');
                        break;
                    case 'developers':
                        include('sources/developers.php');
                        break;
                    case 'create-app':
                        include('sources/create_app.php');
                        break;
                    case 'app':
                        include('sources/app_page.php');
                        break;
                    case 'apps':
                        include('sources/apps.php');
                        break;
                    case 'unusual-login':
                        include('sources/unusual-login.php');
                        break;
                    case 'view-listings':
    					include('sources/view_listings.php');
    					break;
    				case 'timeline':
                        include('sources/timeline.php');
                        break;
                }
                
            }
    }
    else{
        
        switch ($page) {
            
            
                    
            case 'import-files':
                include('sources/import-files.php');
                break;
            case 'leaderboard':
                include('sources/leaderboard.php');
                break;
            case 'add-criteria':
                include('sources/add_criteria.php');
                break;
			case 'view-listings': /*dvm mods*/
				include('sources/view_listings.php');
				break;
			case 'my-buyers-match':
				include('sources/my-buyers-match.php');
				break;
			case 'update-vip-buyers':
				include('sources/update-buyers.php');
				break;
			case 'my-power-match':
				include('sources/power_matching.php');
				break;
            case 'my-power-match-buyer':
                include('sources/power_matching_buyer.php');
                break;
            case 'my-power-match-property':
                include('sources/power_matching_property.php');
                break;
			case 'my-promoted-listings':
				include('sources/my_promoted_listings.php');
				break;
	   //  	case 'promoted-listings':
		  //   	include('sources/promoted_listings.php');
			 //    break;
			case 'promote-listing':
    			include('sources/promote_listing.php');
    			break;
			case 'promoted-listings-detail':
				include('sources/promoted_listings_detail.php');
				break;
			case 'single-listing':
				include('sources/single_listing.php');
				break;
            case 'maintenance':
                include('sources/maintenance.php');
                break;
            case 'welcome':
                include('sources/welcome.php');
                break;
            case 'register':
                include('sources/register.php');
                break;
            case 'confirm-sms':
                include('sources/confirm_sms.php');
                break;
            case 'confirm-sms-password':
                include('sources/confirm_sms_password.php');
                break;
            case 'forgot-password':
                include('sources/forgot_password.php');
                break;
            case 'reset-password':
                include('sources/reset_password.php');
                break;
            case 'activate':
                include('sources/activate.php');
                break;
            case 'logout':
                include('sources/logout.php');
                break;
            case '404':
                include('sources/404.php');
                break;
            case 'contact-us':
                include('sources/contact.php');
                break;
            case 'user-activation':
                include('sources/user_activation.php');
                break;
            case 'upgraded':
                include('sources/upgraded.php');
                break;
            case 'oops':
                include('sources/oops.php');
                break;
          case 'oauth':
                include('sources/oauth.php');
                break;
            case 'app_api':
                include('sources/apps_api.php');
                break;
            case 'authorize':
                include('sources/authorize.php');
                break;
            case 'app-setting':
                include('sources/app_setting.php');
                break;
            case 'developers':
                include('sources/developers.php');
                break;
            case 'create-app':
                include('sources/create_app.php');
                break;
            case 'app':
                include('sources/app_page.php');
                break;
            case 'apps':
                include('sources/apps.php');
                break;
            case 'unusual-login':
                include('sources/unusual-login.php');
                break;
            case 'lessons':
                include('sources/blog.php');
                break;
            case 'my-lessons':
                include('sources/my_blogs.php');
                break;
            case 'create-lesson':
                include('sources/create_blog.php');
                break;
            case 'read-lesson':
                include('sources/read_blog.php');
                break;
            case 'edit-lesson':
                include('sources/edit_blog.php');
                break;
            case 'lesson-category':
                include('sources/blog_category.php');
                break;
            case 'show-event':
                include('sources/showings/show_showing.php');
                break;
            case 'events':
                include('sources/showings/showings_upcomming.php');
                break;
            case 'openhouses':
                include('sources/openhouses/openhouses_upcomming.php');
                break;
            case 'timeline':
                include('sources/timeline.php');
                break;
            case 'reg-buyers':
                include('sources/reg-buyers.php');
                break;
            case 'new-listings':
                include('sources/new_listings.php');
                break;
            case 'create-fbads':
                include('sources/create-fbads.php');
                break;
            case 'new-property':
                include('sources/new_single_listing.php');
                break;
            case 'promoted-property':
                include('sources/promote-listing.php');
                break;
        }
        
    }


}else{
    
    switch ($page) {
        
        case 'partners':
            include('sources/partners.php');
            break;
        case 'tracking-code':
            include('sources/tracking_code.php');
            break;
        case 'keyword':
            include('sources/keyword.php');
            break;
    
        case 'my-tasks':
            include('sources/my-tasks.php');
            break;
        case 'leaderboard':
            include('sources/leaderboard.php');
            break;
        case 'add-criteria':
            include('sources/add_criteria.php');
            break;
        case 'maintenance':
            include('sources/maintenance.php');
            break;
        case 'get_news_feed':
            include('sources/get_news_feed.php');
            break;
        case 'video-call':
            include('sources/video.php');
            break;
        case 'video-call-api':
            include('sources/video_call_api.php');
            break;
        case 'home':
            include('sources/home.php');
            break;
        case 'welcome':
            include('sources/welcome.php');
            break;
        case 'register':
            include('sources/register.php');
            break;
        case 'confirm-sms':
            include('sources/confirm_sms.php');
            break;
        case 'confirm-sms-password':
            include('sources/confirm_sms_password.php');
            break;
        case 'forgot-password':
            include('sources/forgot_password.php');
            break;
        case 'reset-password':
            include('sources/reset_password.php');
            break;
        case 'start-up':
            include('sources/start_up.php');
            break;
		case 'start-up2':
			include('sources/start_up2.php');
			break;
		case 'startup-agent':
			include('sources/startup_agent.php');  /*dvm mods*/
			break;
		case 'startup-buyer':
			include('sources/startup_buyer.php'); /*dvm mods*/
			break;
        case 'activate':
            include('sources/activate.php');
            break;
        case 'search':
            include('sources/search.php');
            break;
        case 'timeline':
            include('sources/timeline.php');
            break;
        case 'company-directory':
            include('sources/my_company.php');
            break;
        case 'suggested-pages':
            include('sources/suggested_pages.php');
            break;
        case 'liked-pages':
            include('sources/liked_pages.php');
            break;
        case 'joined_groups':
            include('sources/joined_groups.php');
            break;
        case 'go-pro':
            include('sources/go_pro.php');
            break;
        case 'company':
            include('sources/company.php');
            break;
        case 'poke':
            include('sources/poke.php');
            break;
        case 'most_liked':
            include('sources/most_liked.php');
            break;
        case 'groups':
            include('sources/my_groups.php');
            break;
        case 'suggested-groups':
            include('sources/suggested_groups.php');
            break;
        case 'group':
            include('sources/group.php');
            break;
        case 'create-group':
            include('sources/create_group.php');
            break;
        case 'group-setting':
            include('sources/group_setting.php');
            break;
        case 'deal-review':
            include('sources/deal-review.php');
            break;
        case 'deal-site':
            include('sources/deal-site.php');
            break;
        case 'campaign-pipeline':
            include('sources/campaign-pipeline.php');
            break;
        case 'new-campaign-pipeline':
            include('sources/new-campaign-pipeline.php');
            break;
        case 'campaign-builder':
            include('sources/campaign-builder.php');
            break;
        case 'contacts':
            include('sources/contacts.php');
            break;
        case 'find-owner':
            include('sources/find-owner.php');
            break;
        case 'add-company':
            include('sources/create_company.php');
            break;
        case 'all-company':
            include('sources/all_company.php');
            break;
        case 'setting':
            include('sources/setting.php');
            break;
        case 'mydeal-review':
            include('sources/mydeal-review.php');
            break;
        case 'page-setting':
            include('sources/page_setting.php');
            break;
        case 'messages':
            include('sources/messages.php');
            break;
        case 'logout':
            include('sources/logout.php');
            break;
        case '404':
            include('sources/404.php');
            break;
        case 'post':
            include('sources/story.php');
            break;
        case 'game':
            include('sources/game.php');
            break;
        case 'games':
            include('sources/games.php');
            break;
        case 'new-game':
            include('sources/new_games.php');
            break;
        case 'saved-posts':
            include('sources/savedPosts.php');
            break;
        case 'hashtag':
            include('sources/hashtag.php');
            break;
        case 'terms':
            include('sources/term.php');
            break;
        case 'albums':
            include('sources/my_albums.php');
            break;
        case 'album':
            include('sources/album.php');
            break;
        case 'create-album':
            include('sources/create_album.php');
            break;
        case 'contact-us':
            include('sources/contact.php');
            break;
        case 'user-activation':
            include('sources/user_activation.php');
            break;
        case 'upgraded':
            include('sources/upgraded.php');
            break;
        case 'oops':
            include('sources/oops.php');
            break;
        case 'boosted-pages':
            include('sources/boosted_pages.php');
            break;
        case 'boosted-posts':
            include('sources/boosted_posts.php');
            break;
        case 'new-product':
            include('sources/new_product.php');
            break;
        case 'edit-product':
            include('sources/edit_product.php');
            break;
        case 'products':
            include('sources/products.php');
            break;
        case 'my-products':
            include('sources/my_products.php');
            break;
        case 'my-broadcast':
            include('sources/my_broadcast.php');
            break;
        case 'add-list':
            include('sources/expand_list.php');
            break;
        case 'add-sms-list':
            include('sources/expand_sms_list.php');
            break;
        case 'view-my-list':
            include('sources/view_user_list.php');
            break;
        case 'view-my-sms-list':
            include('sources/view_user_sms_list.php');
            break;
        case 'lead':
            include('sources/lead.php');
            break;
        case 'marketing':
            include('sources/marketing.php');
            break;
        case 'my-sms':
            include('sources/my_sms_blast_module.php');
            break;
        case 'property-insight':
            include('sources/my_insight_dash.php');
            break;
            /***Documents *******/

    	case 'new-documents':
		include 'sources/new-documents.php';
		break;
        case 'new-listings':
            include('sources/new_listings.php');
            break;
        case 'create-fbads':
            include('sources/create-fbads.php');
            break;
        case 'new-property':
            include('sources/new_single_listing.php');
            break;
        case 'promoted-property':
            include('sources/promote-listing.php');
            break;
        case 'property-funnel':
            include('sources/property_marketing.php');
            break;
        case 'fb-ads':
            include('sources/my_fb_ads.php');
            break;
        case 'my-messages':
            include('sources/my-messages.php');
            break;
        case 'get-new-number':
            include('sources/buy_new_number.php');
            break;
        case 'deal-pipeline':
            include('sources/deal_pipeline.php');
            break;
        case 'create-lead':
            include('sources/create-lead.php');
            break;
        case 'site-pages':
            include('sources/site_pages.php');
            break;
        case 'lessons':
            include('sources/blog.php');
            break;
        case 'my-lessons':
            include('sources/my_blogs.php');
            break;
        case 'create-lesson':
            include('sources/create_blog.php');
            break;
        case 'read-lesson':
            include('sources/read_blog.php');
            break;
        case 'edit-lesson':
            include('sources/edit_blog.php');
            break;
        case 'lesson-category':
            include('sources/blog_category.php');
            break;
        case 'forum':
            include('sources/forum/forum.php');
            break;
        case 'forum-members':
            include('sources/forum/forum_members.php');
            break;
        case 'forum-members-byname':
            include('sources/forum/forum_members_byname.php');
            break;
        case 'forum-events':
            include('sources/forum/forum_events.php');
            break;
        case 'forum-search':
            include('sources/forum/forum_search.php');
            break;
        case 'forum-search-result':
            include('sources/forum/forum_search.php');
            break;
        case 'forum-help':
            include('sources/forum/forum_help.php');
            break;
        case 'forums':
            include('sources/forum/forumdisplay.php');
            break;
        case 'forumaddthred':
            include('sources/forum/forums_add_thread.php');
            break;
        case 'showthread':
            include('sources/forum/forum_showthread.php');
            break;
        case 'threadreply':
            include('sources/forum/forum_threadreply.php');
            break;
        case 'threadquote':
            include('sources/forum/forum_threadquote.php');
            break;
        case 'editreply':
            include('sources/forum/forum_editreply.php');
            break;
        case 'deletereply':
            include('sources/forum/forum_deletereply.php');
            break;
        case 'mythreads':
            include('sources/forum/forum_mythreads.php');
            break;
        case 'mymessages':
            include('sources/forum/forum_mymessages.php');
            break;
        case 'edithread':
            include('sources/forum/forum_edithread.php');
            break;
        case 'deletethread':
            include('sources/forum/forum_deletethread.php');
            break;
         case 'create-event':
            include('sources/showings/create_showing.php');
            break;
        case 'edit-event':
            include('sources/showings/edit_showing.php');
            break;
        case 'events-calendar':
            include('sources/showings/calendar.php');
            break;
        case 'events':
            include('sources/showings/showings_upcomming.php');
            break;
        case 'events-going':
            include('sources/showings/showings_going.php');
            break;
        case 'events-interested':
            include('sources/showings/showings_interested.php');
            break;
        case 'events-past':
            include('sources/showings/showings_past.php');
            break;
        case 'show-event':
            include('sources/showings/show_showing.php');
            break;
        case 'events-invited':
            include('sources/showings/showings_invited.php');
            break;
        case 'my-events':
            include('sources/showings/my_showings.php');
            break;
         case 'openhouses':
            include('sources/openhouses/openhouses_upcomming.php');
            break;
      case 'oauth':
            include('sources/oauth.php');
            break;
        case 'app_api':
            include('sources/apps_api.php');
            break;
        case 'authorize':
            include('sources/authorize.php');
            break;
        case 'app-setting':
            include('sources/app_setting.php');
            break;
        case 'developers':
            include('sources/developers.php');
            break;
        case 'create-app':
            include('sources/create_app.php');
            break;
        case 'app':
            include('sources/app_page.php');
            break;
        case 'apps':
            include('sources/apps.php');
            break;
        case 'sharer':
            include('sources/sharer.php');
            break;
        case 'movies':
            include('sources/movies/movies.php');
            break;
        case 'movies-genre':
            include('sources/movies/movies_genre.php');
            break;
        case 'movies-country':
            include('sources/movies/movies_country.php');
            break;
        case 'watch-film':
            include('sources/movies/watch_film.php');
            break;
        case 'advertise':
            include('sources/ads/ads.php');
            break;
        case 'wallet':
            include('sources/ads/wallet.php');
            break;
        case 'send_money':
            include('sources/ads/send_money.php');
            break;
        case 'create-ads':
            include('sources/ads/create_ads.php');
            break;
        case 'edit-ads':
            include('sources/ads/edit_ads.php');
            break;
        case 'chart-ads':
            include('sources/ads/chart_ads.php');
            break;
        case 'manage-ads':
            include('sources/ads/admin.php');
            break;
        case 'create-status':
            include('sources/status/create.php');
            break;

		/*dvm mods - to figure out later */
		case 'view-listings':
			include('sources/view_listings.php');
			break;
		case 'my-buyers-match':
			include('sources/my-buyers-match.php');
			break;
		case 'my-power-match':
			include('sources/power_matching.php');
			break;
        case 'my-power-match-buyer':
            include('sources/power_matching_buyer.php');
            break;
        case 'my-power-match-property':
            include('sources/power_matching_property.php');
            break;
		case 'my-promoted-listings':
			include('sources/my_promoted_listings.php');
			break;
		case 'promoted-listings':
			include('sources/promoted_listings.php');
			break;
		case 'promote-listing':
			include('sources/promote_listing.php');
			break;
		case 'promoted-listings-detail':
			include('sources/promoted_listings_detail.php');
			break;
		case 'testing':
			include('sources/testing.php');
			break;
		case 'add-listing':
			include('sources/add_listing.php');
			break;
		case 'quick-add-listing':
			include('sources/quick_add_listing.php');
			break;
		case 'single-listing':
			include('sources/single_listing.php');
			break;
		case 'edit-listing':
			include('sources/edit_listing.php');
			break;
		case 'comp-listings':
			include('sources/comp_listings.php');
			break;
		case 'fix-and-flip':
			include('sources/fix-and-flip.php');
			break;
		case 'buy-and-hold':
			include('sources/buy-and-hold.php');
			break;
		case 'friends-nearby':
			include('sources/friends_nearby.php');
	/* end dvm mods - to figure out later  */


		case 'startup-agent':
			include('sources/startup_agent.php');
			break;
		case 'startup-buyer':
			include('sources/startup_buyer.php');
			break;
	   case 'stripe-payment':
			include('sources/stripe_payment.php');
			break;
		case 'checkout-ultimate':
			include('sources/checkout.php');
			break;
		case 'checkout-vip':
			include('sources/checkout-vip.php');
			break;

		case 'checkout-pro':
			include('sources/checkout-pro.php');
			break;

		case 'new-go-pro':
			include('sources/new_go_pro.php');
			break;


		case 'add-criteria':
			include('sources/add_criteria.php');
			break;
		case 'buyer-dashboard':
			include('sources/dashboard_buyer.php');
			break;
		  case 'buyer-dashboard-find-connections':
			include('sources/dashboard_buyer_find_connections.php');
			break;
		case 'buyer-dashboard-connections':
			include('sources/dashboard_buyer_connections.php');
			break;
		  case 'buyer-dashboard-favorite-properties':
			include('sources/dashboard_buyer_fav_properties.php');
			break;
		  case 'buyer-dashboard-match-properties':
			include('sources/dashboard_buyer_match_properties.php');
			break;
		  case 'buyer-dashboard-suggested-properties':
			include('sources/dashboard_buyer_suggested_properties.php');
			break;
		  case 'buyer-dashboard-offer-pending':
			include('sources/dashboard_buyer_offer_pending.php');
			break;
		  case 'buyer-dashboard-offer-accepted':
			include('sources/dashboard_buyer_offer_accepted.php');
			break;
		  case 'buyer-dashboard-offer-rejected':
			include('sources/dashboard_buyer_offer_rejected.php');
			break;
		  case 'buyer-dashboard-offer-counter':
			include('sources/dashboard_buyer_offer_counter.php');
			break;
		  case 'buyer-dashboard-offer-expired':
			include('sources/dashboard_buyer_offer_expired.php');
			break;
		  case 'buyer-dashboard-visits-pending':
			include('sources/dashboard_buyer_visits_pending.php');
			break;
		  case 'buyer-dashboard-visits-accepted':
			include('sources/dashboard_buyer_visits_accepted.php');
			break;
		  case 'buyer-dashboard-visits-rejected':
			include('sources/dashboard_buyer_visits_rejected.php');
			break;
		  case 'buyer-dashboard-visits-reschedule':
			include('sources/dashboard_buyer_visits_reschedule.php');
				break;
			case 'buyer-dashboard-properties':
			include('sources/dashboard_buyer_properties.php');
				break;

		/*Investor pages*/
		case 'investor-dashboard':
			include('sources/dashboard_buyer.php');
			break;
		  case 'investor-dashboard-find-connections':
			include('sources/dashboard_buyer_find_connections.php');
			break;
		case 'investor-dashboard-connections':
			include('sources/dashboard_buyer_connections.php');
			break;
		  case 'investor-dashboard-favorite-properties':
			include('sources/dashboard_buyer_fav_properties.php');
			break;
		  case 'investor-dashboard-match-properties':
			include('sources/dashboard_buyer_match_properties.php');
			break;
		  case 'investor-dashboard-suggested-properties':
			include('sources/dashboard_buyer_suggested_properties.php');
			break;
		  case 'investor-dashboard-offer-pending':
			include('sources/dashboard_buyer_offer_pending.php');
			break;
		  case 'investor-dashboard-offer-accepted':
			include('sources/dashboard_buyer_offer_accepted.php');
			break;
		  case 'investor-dashboard-offer-rejected':
			include('sources/dashboard_buyer_offer_rejected.php');
			break;
		  case 'investor-dashboard-offer-counter':
			include('sources/dashboard_buyer_offer_counter.php');
			break;
		  case 'investor-dashboard-offer-expired':
			include('sources/dashboard_buyer_offer_expired.php');
			break;
		  case 'investor-dashboard-visits-pending':
			include('sources/dashboard_buyer_visits_pending.php');
			break;
		  case 'investor-dashboard-visits-accepted':
			include('sources/dashboard_buyer_visits_accepted.php');
			break;
		  case 'investor-dashboard-visits-rejected':
			include('sources/dashboard_buyer_visits_rejected.php');
			break;
		  case 'investor-dashboard-visits-reschedule':
			include('sources/dashboard_buyer_visits_reschedule.php');
			break;



		case 'seller-dashboard':
			include('sources/dashboard_seller.php');
			break;
		case 'seller-dashboard-email-reports':
			include('sources/dashboard_seller_email_reports.php');
			break;
		case 'seller-dashboard-find-connections':
			include('sources/dashboard_seller_find_connections.php');
			break;
		case 'seller-dashboard-connections':
			include('sources/dashboard_seller_connections.php');
			break;
		case 'seller-dashboard-my-properties':
			include('sources/dashboard_seller_properties.php');
			break;
		case 'seller-dashboard-add-property':
			include('sources/dashboard_seller_add_property.php');
			break;
		case 'seller-dashboard-offer-pending':
			include('sources/dashboard_seller_offer_pending.php');
			break;
		case 'seller-dashboard-offer-accepted':
			include('sources/dashboard_seller_offer_accepted.php');
			break;
		case 'seller-dashboard-offer-rejected':
			include('sources/dashboard_seller_offer_rejected.php');
			break;
		case 'seller-dashboard-offer-counter':
			include('sources/dashboard_seller_offer_counter.php');
			break;
		case 'seller-dashboard-offer-expired':
			include('sources/dashboard_seller_offer_expired.php');
			break;
		case 'seller-dashboard-visits-pending':
			include('sources/dashboard_seller_visits_pending.php');
			break;
		case 'seller-dashboard-visits-accepted':
			include('sources/dashboard_seller_visits_accepted.php');
			break;
		case 'seller-dashboard-visits-rejected':
			include('sources/dashboard_seller_visits_rejected.php');
			break;
		case 'seller-dashboard-visits-reschedule':
			include('sources/dashboard_seller_visits_reschedule.php');
			break;

		case 'seller-dashboard-property-buyers':
			include('sources/dashboard_seller_property_buyers.php');
			break;


		case 'agent-dashboard':
			include('sources/dashboard_agent.php');
			break;
		case 'agent-dashboard-find-connections':
			include('sources/dashboard_agent_find_connections.php');
			break;
		case 'agent-dashboard-all-buyers':
			include('sources/dashboard_agent_all_buyers.php');
			break;
		case 'agent-dashboard-add-buyer':
			include('sources/dashboard_agent_add_buyer.php');
			break;
		case 'agent-dashboard-connections':
			include('sources/dashboard_agent_connections.php');
			break;
		case 'agent-dashboard-my-properties':
			include('sources/dashboard_agent_properties.php');
			break;
		case 'agent-dashboard-add-property':
			include('sources/dashboard_agent_add_property.php');
			break;
		case 'agent-dashboard-offer-pending':
			include('sources/dashboard_agent_offer_pending.php');
			break;
		case 'agent-dashboard-offer-accepted':
			include('sources/dashboard_agent_offer_accepted.php');
			break;
		case 'agent-dashboard-offer-rejected':
			include('sources/dashboard_agent_offer_rejected.php');
			break;
		case 'agent-dashboard-offer-counter':
			include('sources/dashboard_agent_offer_counter.php');
			break;
		case 'agent-dashboard-offer-expired':
			include('sources/dashboard_agent_offer_expired.php');
			break;
		case 'agent-dashboard-visits-pending':
			include('sources/dashboard_agent_visits_pending.php');
			break;
		case 'agent-dashboard-visits-accepted':
			include('sources/dashboard_agent_visits_accepted.php');
			break;
		case 'agent-dashboard-visits-rejected':
			include('sources/dashboard_agent_visits_rejected.php');
			break;
		case 'agent-dashboard-visits-reschedule':
			include('sources/dashboard_agent_visits_reschedule.php');
			break;



		case 'wholeseller-dashboard':
			include('sources/dashboard_agent.php');
			break;
		case 'wholeseller-dashboard-find-connections':
			include('sources/dashboard_agent_find_connections.php');
			break;
		case 'wholeseller-dashboard-all-buyers':
			include('sources/dashboard_agent_all_buyers.php');
			break;
		case 'wholeseller-dashboard-add-buyer':
			include('sources/dashboard_agent_add_buyer.php');
			break;
		case 'wholeseller-dashboard-connections':
			include('sources/dashboard_agent_connections.php');
			break;
		case 'wholeseller-dashboard-my-properties':
			include('sources/dashboard_agent_properties.php');
			break;
		case 'wholeseller-dashboard-add-property':
			include('sources/dashboard_agent_add_property.php');
			break;
		case 'wholeseller-dashboard-offer-pending':
			include('sources/dashboard_agent_offer_pending.php');
			break;
		case 'wholeseller-dashboard-offer-accepted':
			include('sources/dashboard_agent_offer_accepted.php');
			break;
		case 'wholeseller-dashboard-offer-rejected':
			include('sources/dashboard_agent_offer_rejected.php');
			break;
		case 'wholeseller-dashboard-offer-counter':
			include('sources/dashboard_agent_offer_counter.php');
			break;
		case 'wholeseller-dashboard-offer-expired':
			include('sources/dashboard_agent_offer_expired.php');
			break;
		case 'wholeseller-dashboard-visits-pending':
			include('sources/dashboard_agent_visits_pending.php');
			break;
		case 'wholeseller-dashboard-visits-accepted':
			include('sources/dashboard_agent_visits_accepted.php');
			break;
		case 'wholeseller-dashboard-visits-rejected':
			include('sources/dashboard_agent_visits_rejected.php');
			break;
		case 'wholeseller-dashboard-visits-reschedule':
			include('sources/dashboard_agent_visits_reschedule.php');
			break;

		case 'propertyinfo':
			include('sources/propertyinfo.php');
			break;
		case 'make-an-offer':
			include('sources/make-an-offer.php');
			break;
		case 'my-counter-offer':
			include('sources/my-counter-offer.php');
			break;
		case 'my-schedule-visit':
			include('sources/my-schedule-visit.php');
			break;
		case 'marketing-tools':
			include('sources/marketing_tools.php');
			break;
		/* dvm mods - to figure out later ends here */

	    case 'more-status':
            include('sources/status/more-status.php');
            break;
        case 'unusual-login':
            include('sources/unusual-login.php');
            break;
        case 'jobs':
            include('sources/jobs.php');
            break;
        case 'common_things':
            include('sources/common_things.php');
            break;
        case 'funding':
            include('sources/funding.php');
            break;
        case 'my_funding':
            include('sources/my_funding.php');
            break;
        case 'create_funding':
            include('sources/create_funding.php');
            break;
        case 'edit_fund':
            include('sources/edit_fund.php');
            break;
        case 'show_fund':
            include('sources/show_fund.php');
            break;
        case 'memories':
            include('sources/memories.php');
            break;
        case 'refund':
            include('sources/refund.php');
            break;
        case 'offers':
            include('sources/offers.php');
            break;
        case 'nearby_shops':
            include('sources/nearby_shops.php');
            break;
        case 'nearby_business':
            include('sources/nearby_business.php');
            break;
        case 'live':
            include('sources/live.php');
            break;
    }
    
}



    if (empty($wo['content'])) {
        
        if ($wo['config']['membership_system'] == 1 && $wo['loggedin'] == true) {
            
            include('sources/go_pro.php');
            
        }else{
      
            // include('sources/timeline.php');
            include('sources/404.php');
            
        }
        
    }


if($page == "pro_register"){
    
    echo Wo_Loadpage('register_container');
    
}elseif($page != "new-property" && $page != "new-listings" && $page != "promoted-property" && $page != "pro_register"){
    
    echo Wo_Loadpage('container');
    
}else{
    
    echo Wo_Loadpage('listingContainer');
    
}

// if($page != "new-property" && $page != "new-listings" && $page != "promoted-property"){
    
//     echo Wo_Loadpage('container');

// }elseif($page == "pro_register"){
    
//     echo Wo_Loadpage('register_container');
    
// }else{
    
//     echo Wo_Loadpage('listingContainer');
    
// }








mysqli_close($sqlConnect);
unset($wo);
?>
