<?php
 
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');





// VARIABLE DECLARATION
$user_id = $wo['user']['user_id'];
$pathName = "psmembers";

// 
// 
// 
// ACTION CODE START
// 
// 

// Check if domain name already exist......
if(isset($_POST['action']) && $_POST['action']=="check_siteName" && isset($_POST['domain_name_check']) && $_POST['domain_name_check'] == 1) {
    
    $domainName = $_POST['domain_name'];
    $col = "domain";
    
    $result = GetDeal_SingleDetails($col,$domainName);
    $res = checkIfDomainExist($domainName);
    
    if( $result > 0 || $res == 1 ){
        
        $data = array(
            'status' => 400,
            'message' => 'taken',
        );
        
    }else{
        
        $data = array(
            'status' => 200,
            'message' => 'not_taken',
        );
        
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Add new custom domain 
if( isset($_POST['action']) && $_POST['action'] == "enterCustomDname" ){
    
    $uri = $_POST['custom_domain'];
    
    if(!empty($uri) || $uri != ""){
        
        if(validate_url($uri)) {
            
            $FNBD =  extract_domain($uri);
            
            $domain = parse_url($uri, PHP_URL_HOST);
            
            $u_id = $wo['user']['user_id'];
            
            $col = "custom_domain";
            
            $pathName = "psmembers";
            
            $subdomain = $_POST['subdomian'];
            
            pre($_POST); exit; 
            
            $dir = "public_html/subdomains/".$subdomain;
            
            $buildRequest = "/frontend/paper_lantern/addon/doadddomain.html?domain=".$domain."&user=".$subdomain."&dir=".$dir."&pass=".$subdomain."";
        	
        	$cPanelUser = C_USER;
            $cpanelPass = C_PASS;
            $rootDomain = C_ROOTD;
            
        	$openSocket = fsockopen('localhost',2082);
            if(!$openSocket) {
                return "Socket error";
                exit();
            }
         
            $authString = $cPanelUser . ":" . $cPanelPass;
            $authPass = base64_encode($authString);
            $buildHeaders  = "GET " . $buildRequest ."\r\n";
            $buildHeaders .= "HTTP/1.0\r\n";
            $buildHeaders .= "Host:localhost\r\n";
            $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
            $buildHeaders .= "\r\n";
         
            fputs($openSocket, $buildHeaders);
            
            while(!feof($openSocket)) {
                fgets($openSocket,128);
            }
    
            $data2 = array(
                'custom_domain' => $domain,
                'new_domain' => $FNBD,
                'custom_domain_url' => $uri, 
                'domain_status' => 1
            );
        
            $updateDomain = UpdateDealSiteData($data2,$u_id);
            
            if( $updateDomain ){
                 
                 
                $data = array(
                    'status' => 200,
                    'message' => 'Custom Domain successfully created.',
                );
                
            }else{
                
                $data = array(
                    'status' => 400,
                    'message' => 'Sorry the system Could not process your request',
                );
                
                
            }
            
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => 'Invalid Url Format',
            );
            
        }
    
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Please enter your domain name.',
        );
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Delete or disable custom domain name
if( isset($_POST['action']) && $_POST['action'] == "DeleteCustomDname" ){
    
    $CD = $_POST['custom_domain'];
    $u_id = $_POST['user_id'];
    $subdomain = $_POST['subdomian'];
    
    
    if($u_id === $user_id){
    
    
        $getDealD = GetDealSiteDetailsByCol("domain",$subdomain);
        if(!empty($getDealD)){
            
                $FNBD =  extract_domain($getDealD['custom_domain_url']);
                
                $domain = parse_url($getDealD['custom_domain_url'], PHP_URL_HOST);
                
                $col = "custom_domain";
                
                $pathName = "psmembers";
                
                // $dir = "public_html/subdomains/".$subdomain;
                
                $buildRequest = "/frontend/paper_lantern/addon/dodeldomain.html?domain=".$domain."&user=".$subdomain."&pass=".$subdomain."";
            	
            	$cPanelUser = C_USER;
                $cpanelPass = C_PASS;
                $rootDomain = C_ROOTD;
                
            	$openSocket = fsockopen('localhost',2082);
                if(!$openSocket) {
                    return "Socket error";
                    exit();
                }
             
                $authString = $cPanelUser . ":" . $cPanelPass;
                $authPass = base64_encode($authString);
                $buildHeaders  = "GET " . $buildRequest ."\r\n";
                $buildHeaders .= "HTTP/1.0\r\n";
                $buildHeaders .= "Host:localhost\r\n";
                $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
                $buildHeaders .= "\r\n";
             
                fputs($openSocket, $buildHeaders);
                
                while(!feof($openSocket)) {
                    fgets($openSocket,128);
                }
                
        
                $data2 = array(
                    'custom_domain' => "",
                    'new_domain' => $subdomain,
                    'custom_domain_url' => "", 
                    'domain_status' => 0
                );
            
                $updateDomain = UpdateDealSiteData($data2,$u_id);
                
                if( $updateDomain ){
                     
                     
                    $data = array(
                        'status' => 200,
                        'message' => 'Custom Domain successfully created.',
                    );
                    
                }else{
                    
                    $data = array(
                        'status' => 400,
                        'message' => 'Sorry the system Could not process your request',
                    );
                    
                    
                }
            
            
            
        }else{
            
            $data = array(
                'status' => 400,
                'message' => 'Error While getting Domain details',
            );
            
        }
        
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Error Invalid Autorization',
        ); 
        
    }
    
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Enter domain name and process the request accordingly 
if(isset($_POST['action']) && $_POST['action']=="enterDname"){
    
    $domainName = $_POST['domain_name'];
    $col = "domain";
    
    function create_subdomain($subDomain,$cPanelUser,$cPanelPass,$rootDomain) {
 
        $buildRequest = "/frontend/paper_lantern/subdomain/doadddomain.html?rootdomain=" . $rootDomain . "&domain=" . $subDomain . "&dir=newsubdomains";
     
        $openSocket = fsockopen('localhost',2082);
        if(!$openSocket) {
            return "Socket error";
            exit();
        }
     
        $authString = $cPanelUser . ":" . $cPanelPass;
        $authPass = base64_encode($authString);
        $buildHeaders  = "GET " . $buildRequest ."\r\n";
        $buildHeaders .= "HTTP/1.0\r\n";
        $buildHeaders .= "Host:localhost\r\n";
        $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
        $buildHeaders .= "\r\n";
     
        fputs($openSocket, $buildHeaders);
        
        while(!feof($openSocket)) {
            fgets($openSocket,128);
        }
        
    
        
        fclose($openSocket);
     
        $newDomain = "https://" . $subDomain . "." . $rootDomain . "/";
     
     return true;
     
    }
    
    function xcopy($source, $dest, $permissions = 0755)
    {
        $sourceHash = hashDirectory($source);
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }
    
        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }
    
        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }
    
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
    
            // Deep copy directories
            if($sourceHash != hashDirectory($source."/".$entry)){
                 xcopy("$source/$entry", "$dest/$entry", $permissions);
            }
        }
    
        // Clean up
        $dir->close();
        return true;
    }
    
    // In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated
    
    function hashDirectory($directory){
        if (! is_dir($directory)){ return false; }
    
        $files = array();
        $dir = dir($directory);
    
        while (false !== ($file = $dir->read())){
            if ($file != '.' and $file != '..') {
                if (is_dir($directory . '/' . $file)) { $files[] = hashDirectory($directory . '/' . $file); }
                else { $files[] = md5_file($directory . '/' . $file); }
            }
        }
    
        $dir->close();
    
        return md5(implode('', $files));
    }
    
    // function recursiveCopy($source, $destination){
    //     if (!file_exists($destination)) {
    //         mkdir($destination);
    //     }
    
    //     $splFileInfoArr = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
    
    //     foreach ($splFileInfoArr as $fullPath => $splFileinfo) {
    //         //skip . ..
    //         if (in_array($splFileinfo->getBasename(), [".", ".."])) {
    //             continue;
    //         }
    //         //get relative path of source file or folder
    //         $path = str_replace($source, "", $splFileinfo->getPathname());
    
    //         if ($splFileinfo->isDir()) {
    //             mkdir($destination . "/" . $path);
    //         } else {
    //         copy($fullPath, $destination . "/" . $path);
    //         }
    //     }
    // }
    
    // Copy index file 
    function copyIndexFile($domain){
    
        $from = "/home/psmembers/public_html/subdomains/parent/index.php";
        $to = "/home/psmembers/public_html/subdomains/{$domain}/index.php";
        
        if(!file_exists($to)){
            copy($from, $to);
        }
        
    }
    
    
    $cPanelUser = C_USER;
    $cpanelPass = C_PASS;
    $rootDomain = C_ROOTD;
    
    $OpenD = create_subdomain($domainName,$cPanelUser,$cpanelPass,$rootDomain);
    
    // Auto generateAPI KEY
    function getGUID(){
       
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = md5(uniqid(rand(), true));
            $hyphen = "";// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        
    }
    
    $title = "Welcome to ".$domainName." Site";
    $desc = $domainName." Description";
    $pathDomain = "/home/".$pathName."/public_html/subdomains/".$domainName;
    $copyright = "© ".date("Y")." ".$rootDomain;
    $keywords = $rootDomain.",".$domainName.",property,salers";
    
    // Database Datas
    $dV['domain'] = $domainName;
    $dV['folder_path'] = $pathDomain;
    $dV['title'] = $title;
    $dV['new_domain'] = $domainName;
    $dV['description'] = $desc;
    $dV['keywords'] = $keywords;
    $dV['number_propery_fetched'] = 10;
    $dV['server_key'] = getGUID();
    $dV['copyright'] = $copyright;
    $dV['status'] = 1;
    $dV['primary_color'] = "#f58220";
    $dV['secondary_color'] = "#02254C";
    $dV['user_id'] = $wo['user']['user_id'];
    
    $getEnterD = CreateDomainNameDetails($dV);
    
    // $source = "/home/".$pathName."/public_html/deal_site_clone/app_deisgn";
    // $destination = "/home/".$pathName."/public_html/subdomains/".$domainName;
    // copyIndexFile($domainName);
    // xcopy($source,$destination);
    
    if( $getEnterD && $OpenD ){
        
        $data = array(
            'status' => 200,
            'message' => 'Deal site successfully created.',
        );
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Sorry the system Could not process your request',
        );
        
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Add the domain logo code start here...........
if(isset($_POST['action']) && $_POST['action']=="upload_domain_logo"){
    
    if (isset($_FILES['file']) && !empty($_FILES['file'])) {

		if (!empty($_FILES['file']["tmp_name"])) {
			$orignalname = $_FILES['file']["name"];
			$filename = "";
			$fileInfo = array(
				'file' => $_FILES["file"]["tmp_name"],
				'name' => $_FILES['file']['name'],
				'size' => $_FILES["file"]["size"],
				'type' => $_FILES["file"]["type"],
				'types' => 'jpg,png,gif,jpeg',
			);

			$media = Wo_ShareFile($fileInfo, 0, false);
			if (!empty($media)) {
				$filename = $media['filename'];

			}
			
			$user_id = $_POST['user_id'];
			
			$update_logo = UpdateUserDeal_siteLogo($filename,$user_id);
			
			if($update_logo){
			    
			    $data = array(
                    'status' => 200,
                    'message' => 'success',
                );
        
			}else{
			    
			    $data = array(
                    'status' => 400,
                    'message' => 'error',
                );
                
			}
			

		}
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Add new favicon 
if(isset($_POST['action']) && $_POST['action']=="upload_domain_favicon"){
    
    if (isset($_FILES['file']) && !empty($_FILES['file'])) {

		if (!empty($_FILES['file']["tmp_name"])) {
			$orignalname = $_FILES['file']["name"];
			$filename = "";
			$fileInfo = array(
				'file' => $_FILES["file"]["tmp_name"],
				'name' => $_FILES['file']['name'],
				'size' => $_FILES["file"]["size"],
				'type' => $_FILES["file"]["type"],
				'types' => 'jpg,png,gif,jpeg',
			);

			$media = Wo_ShareFile($fileInfo, 0, false);
			if (!empty($media)) {
				$filename = $media['filename'];

			}
			
			$user_id = $_POST['user_id'];
			
			$update_logo = UpdateUserDeal_siteFavicon($filename,$user_id);
			
			if($update_logo){
			    
			    $data = array(
                    'status' => 200,
                    'message' => 'success',
                );
        
			}else{
			    
			    $data = array(
                    'status' => 400,
                    'message' => 'error',
                );
                
			}
			

		}
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Delete Domain logo
if(isset($_POST['action']) && $_POST['action']=="delete_domain_logo"){
    
    $u_id = $_POST['user_id'];
    $fileName = $_POST['fileName'];
    
    $deleteDomain = DeleteDomainLogo($u_id,$fileName);
    
    if($deleteDomain){
			    
	    $data = array(
            'status' => 200,
            'message' => 'success',
        );

	}else{
	    
	    $data = array(
            'status' => 400,
            'message' => 'error',
        );
        
	}
	
	header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// Add new style form
if(isset($_POST['action']) && $_POST['action']=="enterAddStyle"){
    
    $u_id = $_POST['user_id'];
    $style = $_POST['add_style'];
    
    $data2 = array('additional_style' => $style);
    
    $updateStyle = UpdateDealSiteData($data2,$u_id);
    
    if($updateStyle){
        
        $data = array(
            'status' => 200,
            'message' => 'Succesfully updated Deal site Setting',
        );
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Sorry could not process your request.',
        );
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
}

// UPDATE ALL DEAL SITE SETTING
if(isset($_POST['action']) && $_POST['action']=="UpdateDd"){
    
    $u_id = $_POST['user_id'];
    
    $data2 = array(
        'title' => $_POST['site_title'],
        'keywords' => $_POST['keyword'],
        'description' => $_POST['about_property'],
        'copyright' => $_POST['copyright'],
        'number_propery_fetched' => $_POST['limit'],
        'primary_color' => $_POST['primary_color'],
        'secondary_color' => $_POST['secondary_color'],
    );
    
    $updateStyle = UpdateDealSiteData($data2,$u_id);
    
    if($updateStyle){
        
        $data = array(
            'status' => 200,
            'message' => 'Succesfully updated Deal site Setting',
        );
        
    }else{
        
        $data = array(
            'status' => 400,
            'message' => 'Sorry could not process your request.',
        );
        
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
    
    
}


// 
// 
// OTHER FUNCTIONS
// 
// 

// Validate if its real url or not
function validate_url($url) {
    $path = parse_url($url, PHP_URL_PATH);
    $encoded_path = array_map('urlencode', explode('/', $path));
    $url = str_replace($path, implode('/', $encoded_path), $url);

    return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
}
// extract the first text in the domain
function extract_domain($domain){
    
    $parsedUrl = parse_url($domain);
    
    $host = explode('.', $parsedUrl['host']);
    
    $subdomain = $host[0];
    return $subdomain;
}