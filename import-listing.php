<?php

global $wo, $sqlConnect;
$root=__DIR__;
require_once('config.php');
require_once('assets/init.php');


global $wo, $sqlConnect;

$action = filter('action');

if($action == 'getFilters') {

    

    $file = filterUpload('upload');

    if(!$file) {
        $message = "Please select a valid file";
    }else{

        $html="";
        
        $wo['file'] = $file;
        $html = Wo_LoadPage('admin-crm/map-filters');

        $message = "Found";
        $status = 1;
    }
    
    $data = [
        'status' => $status,
        'message' => $message,
        'html' => $html
    ];

} else if($action == 'filterMap'){

    $file = filterUpload('file');
    $filters = getListingFilters();
    $request = filter('data');
    extract($_REQUEST);
    $html="";
    $active = $sold = $properties = 0;
    $sl_no=1;
    

    if(!$file) {
        $message = "Please select a valid file";
    } else { 
        
        $location = $file['tmp_name'];
        if (($handle = fopen($location, "r")) !== FALSE) {
            $i=1; 
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if($i>1){ 
                    

                    $html.='<tr><td>'.$sl_no.'</td>';
                    foreach($filters as $key => $filter){
                        if(!isset($data[$request[$key]])){
                            $html .= '<td><input type="hidden" name="data[' . $key . '][]" value=""></td>';
                        }else{
                            $html .= '<td><input type="hidden" name="data[' . $key . '][]" value="'.$data[$request[$key]].'">'.$data[$request[$key]].'</td>';
                        }
                    }

                    $html .= '</tr>';
                    $sl_no=$sl_no+1;;
                }
                $i++;
            }  
            fclose($handle);
        }


        $status = 1;
        $message = "Found";
    }

    $data = [
        'html' => $html,
        'status' => $status,
        'message' => $message,
    ];

} else if($action == 'saveFilters'){

    $name = filter('name');
    $data = filter('data');
    $user_id = filter('user_id');
    $filters = getListingFilters();
    

    if(!$data || !count($data)) {
        $message = "Please map the fields first then click save";
    } else {
        
        
        foreach($data as $key => $value) {
			$saveData = [];

            
			$keys = array_keys($data);
			foreach($data['title'] as $key => $value ){
				foreach($filters as $filterKey => $filterValue){
					$filterData[$filterKey] = $data[$filterKey][$key];
				}
				$saveData[] = $filterData;
			}
		}


        if(count($saveData)){

            createListingsFromMap($user_id, $saveData);

            $status = 1;
            $message = "Imported successfully!";
        }else{
            $message = "There is no record found. Please try again.";
        }
        
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];

} else if($action == 'uploadImages') {
    $file = filterUpload('file');
    $folder = filter('folder');
    
    
    $status = 0;
    $html = "";
    if($file){

        $detectedType = exif_imagetype($file['tmp_name']);
        $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
        

        if(in_array($detectedType, $allowedTypes)){
            $dir = "import";
            if(!file_exists($dir)){
                mkdir($dir);
            }

            if(file_exists($dir)){

                $dir = $dir. "/{$folder}";
                // New folder creation;
                if(!file_exists($dir)){
                    mkdir($dir);
                }

                $name = $file['name'] = str_replace(' ', '-', $file['name']);
                $to = $dir . "/{$name}";

                if(move_uploaded_file($file['tmp_name'], $to)){
                    $wo['url'] = $wo['site_url'] . "/{$to}";
                    $wo['file'] = $file;
                    $html = Wo_LoadPage('import-files/uploaded-template');
                    $status = 1;
                }

            }
        }
        
    }


    $data = [
        'html' => $html,
        'status' => $status
    ];

}

header("Content-type: application/json");
echo json_encode($data);
die;

?>