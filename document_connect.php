<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


$user_id = $wo['user']['user_id'];
if(isset($_POST['action']) && $_POST['action'] == 'enter_mydocD'){
    
    
	$title = $_POST['title'];
	
	if (isset($_FILES['uploadFile']) && !empty($_FILES['uploadFile'])) {

		if (!empty($_FILES['uploadFile']["tmp_name"])) {
			$orignalname = $_FILES['uploadFile']["name"];
			$filename = "";
			$fileInfo = array(
				'file' => $_FILES["uploadFile"]["tmp_name"],
				'name' => $_FILES['uploadFile']['name'],
				'size' => $_FILES["uploadFile"]["size"],
				'type' => $_FILES["uploadFile"]["type"],
				'types' => 'jpg,png,gif,jpeg,doc,docx,pdf,xls,csv,xlsx,pptx,ppt',
			);

			$media = Wo_ShareFile($fileInfo, 0, false);
			if (!empty($media)) {
				$filename = $media['filename'];

			}
			
            // check if the file extension is .pdf?
            $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
            
            if($fileExt == 'pdf'){
                
                $fileN = pathinfo($filename, PATHINFO_FILENAME);
                // $fileNOEXT = substr($file, 0, strrpos($file, "."));
                $y = date("Y");
                $m = date("m");
                $pth = "upload/files/".$y."/".$m."/";
                $im = new Imagick();
                
                $im->setResolution(300,300);
                $im->readimage($filename."[0]"); 
                $im->setImageFormat('png');    
                $im->writeImage($pth.$fileN.'.png'); 
                $im->clear(); 
                $im->destroy();
            
            }

			

			$query_insert = "INSERT INTO `Wo_contracts` (user_id,title,file,fileName) VALUES ($user_id,'$title','$filename','$orignalname')";
			$offerquery = mysqli_query($sqlConnect, $query_insert);
			
			if ($offerquery) {
				$data = array(
					'status' => 200,
					'message' => 'Document Uploaded Successfully',

				);
			} else {
				$data = array(
					'status' => 400,
					'message' => 'System Error while Processing your request',

				);

			}
			
			
		}
	}else{
	    $data = array(
			'status' => 400,
			'message' => "Sorry File field can't be Empty",
		);
	    
	}

	
	header("Content-type: application/json");
    echo json_encode($data);
    die;
	
    
}

if(isset($_POST['action']) && $_POST['action'] == 'enter_SYSdocD'){
    
   $title = $_POST['title'];
	
	if (isset($_FILES['uploadFile']) && !empty($_FILES['uploadFile'])) {

		if (!empty($_FILES['uploadFile']["tmp_name"])) {
			$orignalname = $_FILES['uploadFile']["name"];
			$filename = "";
			$fileInfo = array(
				'file' => $_FILES["uploadFile"]["tmp_name"],
				'name' => $_FILES['uploadFile']['name'],
				'size' => $_FILES["uploadFile"]["size"],
				'type' => $_FILES["uploadFile"]["type"],
				'types' => 'jpg,png,gif,jpeg,doc,docx,pdf,xls,csv,xlsx,pptx,ppt',
			);

			$media = Wo_ShareFile($fileInfo, 0, false);
			if (!empty($media)) {
				$filename = $media['filename'];

			}
			
            // check if the file extension is .pdf?
            $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
            
            if($fileExt == 'pdf'){
                
                $fileN = pathinfo($filename, PATHINFO_FILENAME);
                // $fileNOEXT = substr($file, 0, strrpos($file, "."));
                $y = date("Y");
                $m = date("m");
                $pth = "upload/files/".$y."/".$m."/";
                $im = new Imagick();
                
                $im->setResolution(300,300);
                $im->readimage($filename."[0]"); 
                $im->setImageFormat('png');    
                $im->writeImage($pth.$fileN.'.png'); 
                $im->clear(); 
                $im->destroy();
            
            }

			

			$query_insert = "INSERT INTO `other_systemFiles` (user_id,title,file,fileName) VALUES ($user_id,'$title','$filename','$orignalname')";
			$offerquery = mysqli_query($sqlConnect, $query_insert);
			
			if ($offerquery) {
				$data = array(
					'status' => 200,
					'message' => 'Document Uploaded Successfully',

				);
			} else {
				$data = array(
					'status' => 400,
					'message' => 'System Error while Processing your request',

				);

			}
			
			
		}
	}else{
	    $data = array(
			'status' => 400,
			'message' => "Sorry File field can't be Empty",
		);
	    
	}
   
   header("Content-type: application/json");
   echo json_encode($data);
   die; 
}


if(isset($_POST['action']) && $_POST['action'] == 'deleteMyDoc'){
    
    if($_POST['doc_id'] > 0){
        
        $id = $_POST['doc_id'];
        
        $ds  = DIRECTORY_SEPARATOR;
        
        $queryfilter = mysqli_query($sqlConnect,"SELECT * FROM `Wo_contracts` WHERE `id` = $id AND `user_id` = $user_id");
        $p_query = mysqli_fetch_assoc($queryfilter);
        
        // FileName
        $fName = $p_query['file'];
        
        // Get File Extension
        $fileD = get_document_fileExt($fName);
        $fExt = $fileD['fileExt'];
        $fwitoutExt = $fileD['fileNoExt'];
        
        // check if file is a pdf?
        if($fExt == 'pdf'){
            $targetPath = dirname( __FILE__ ) . $ds. $fName;
            $targetPathPng = dirname( __FILE__ ) . $ds. $fwitoutExt.'.png';
            
            unlink($targetPath);
            unlink($targetPathPng); 
        }else{
            $targetPath = dirname( __FILE__ ) . $ds. $fName;
            unlink($targetPath);
        }
        // closedir($targetPath);
        
        $deleQuery = mysqli_query($sqlConnect,"DELETE FROM `Wo_contracts` WHERE `id` = $id AND `user_id` = $user_id");
        if($deleQuery){
            
            $data = array(
                
				'status' => 200,
				'message' => 'Document Deleted Successfully',

			);
			
        }else{
            
            $data = array(
                
				'status' => 400,
				'message' => 'System Error while Processing your request',

			);
				
            
        }
    
    }else{
        
        $data = array(
			'status' => 400,
			'message' => "Sorry document field can't be Empty",
		);
		
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}


if(isset($_POST['action']) && $_POST['action'] == 'deleteSysDoc'){
    
    if($_POST['doc_id'] > 0){
        
        $id = $_POST['doc_id'];
        
        $ds  = DIRECTORY_SEPARATOR;
        
        $queryfilter = mysqli_query($sqlConnect,"SELECT * FROM `other_systemFiles` WHERE `id` = $id");
        $p_query = mysqli_fetch_assoc($queryfilter);
        
        // FileName
        $fName = $p_query['file'];
        
        // Get File Extension
        $fileD = get_document_fileExt($fName);
        $fExt = $fileD['fileExt'];
        $fwitoutExt = $fileD['fileNoExt'];
        
        // check if file is a pdf?
        if($fExt == 'pdf'){
            $targetPath = dirname( __FILE__ ) . $ds. $fName;
            $targetPathPng = dirname( __FILE__ ) . $ds. $fwitoutExt.'.png';
            
            unlink($targetPath);
            unlink($targetPathPng); 
        }else{
            $targetPath = dirname( __FILE__ ) . $ds. $fName;
            unlink($targetPath);
        }
        // closedir($targetPath);
        
        $deleQuery = mysqli_query($sqlConnect,"DELETE FROM `other_systemFiles` WHERE `id` = $id");
        if($deleQuery){
            
            $data = array(
                
				'status' => 200,
				'message' => 'Document Deleted Successfully',

			);
			
        }else{
            
            $data = array(
                
				'status' => 400,
				'message' => 'System Error while Processing your request',

			);
				
            
        }
    
    }else{
        
        $data = array(
			'status' => 400,
			'message' => "Sorry document field can't be Empty",
		);
		
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    die;
}



if(isset($_POST['action']) && $_POST['action'] == 'loadMyMoreDoc'){
    
    if( isset($_POST['row']) && isset($_POST['limit']) ){
        
        $start = $_POST['row'];
        $limit = $_POST['limit'];
        
        $mycontract = get_mydocumentsby_cat("Wo_contracts",$user_id,$start,$limit);
        if ($mycontract->num_rows > 0) {
            
			while($docDetails = mysqli_fetch_assoc($mycontract)){
			    
			    $MyDocfileN = $docDetails['file'];
			    $mydocTitle = $docDetails['fileName'];
			    $mydocid = $docDetails['id'];
			    
                $fileDetatails = extractFileDetails($MyDocfileN);
                $mfilePrevImg = $fileDetatails['fileImg'];
                $mfileSize = $fileDetatails['fileSize'];
                $nfileExt = $fileDetatails['fileExt']; ?>
			    

    
                    <div class="col-lg-3 col-xl-2 mydoc_<?= $mydocid ?>" id="mydoc_<?= $mydocid ?>">
                        <div class="file-man-box">
                            <a href="javascript:void();" onClick="deleteMyDocFile(<?= $mydocid ?>, '<?= $mydocTitle ?>')" class="file-close">
                                <i class="fa fa-times-circle"></i>
                            </a>
                            <a href="javascript:void();" onClick="PreviewDocx('<?= $MyDocfileN ?>','<?= $nfileExt ?>');" class="file-preview">
                                <i class="fas fa-eye"></i>
                            </a>
                            <div class="file-img-box"><img src="<?= $mfilePrevImg ?>" alt="icon"></div>
                            <a href="javascript:void();" onClick="DownLoadFile(<?= $mydocid ?>, '<?= $MyDocfileN ?>', '<?= $mydocTitle ?>')" class="file-download">
                                <i class="fa fa-download"></i>
                            </a>
                            <div class="file-man-title">
                                <h5 class="mb-0 text-overflow"><?= $mydocTitle ?></h5>
                                <p class="mb-0"><small><?= $mfileSize ?></small></p>
                            </div>
                        </div>
                    </div>
			    
		<?php	}
			

        }
        
    }
    
    
    
}



// New Function....
if(isset($_POST['action']) && $_POST['action'] == 'loadSysMoreDoc'){
    
    if( isset($_POST['row']) && isset($_POST['limit']) ){
        
        $Sysstart = $_POST['row'];
        $Syslimit = $_POST['limit'];
     
        								
		$Syscontract = get_Sysdocumentsby_cat("other_systemFiles",$Sysstart,$Syslimit);
        if ($Syscontract->num_rows > 0) {
			while($SysdocDetails = mysqli_fetch_assoc($Syscontract)){
			    $SysDocfileN = $SysdocDetails['file'];
			    $SysdocTitle = $SysdocDetails['fileName'];
			    $Sysdocid = $SysdocDetails['id'];
			    
                $SysfileDetatails = extractFileDetails($SysDocfileN);
                $sfilePrevImg = $SysfileDetatails['fileImg'];
                $sfileSize = $SysfileDetatails['fileSize'];
                $sfileExt = $SysfileDetatails['fileExt'];
                
				?>
				
				<div class="col-lg-3 col-xl-2 sysdoc_<?= $Sysdocid ?>" id="sysdoc_<?= $Sysdocid ?>">
                    <div class="file-man-box">
                        <a href="javascript:void();" onClick="PreviewDocx('<?= $SysDocfileN ?>','<?= $sfileExt ?>');" class="file-preview">
                            <i class="fas fa-eye"></i>
                        </a>
                        <div class="file-img-box"><img src="<?= $sfilePrevImg ?>" alt="icon"></div>
                        <a href="javascript:void();" onClick="DownLoadFile(<?= $Sysdocid ?>, '<?= $SysDocfileN ?>', '<?= $SysdocTitle ?>')" class="file-download">
                            <i class="fa fa-download"></i>
                        </a>
                        <div class="file-man-title">
                            <h5 class="mb-0 text-overflow"><?= $SysdocTitle ?></h5>
                            <p class="mb-0"><small><?= $sfileSize ?></small></p>
                        </div>
                    </div>
                </div>
			    
		<?php	}
			

        }
        
    }
    
    
    
}





?>