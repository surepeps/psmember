<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');


$user_id = $wo['user']['user_id'];

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