<?php

$root  = __DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;


$file_id = filter('id');

if(!$file_id) {
    header("location:" . $wo['site_url']);
}else{
    $where = [
        'id' => $file_id
    ];

    $file = getTableData('wo_contact_files', $where, 1);
    if(!$file) {
        header("location:" . $wo['site_url']);
    }else{


        $filename = $file['filename'];
        $info = pathinfo($filename);
        $extension = $info['extension'];

        $ds = DIRECTORY_SEPARATOR;
        $filepath = "upload{$ds}files{$ds}contact{$ds}" . $filename;

        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"".$filename."\""); 
        echo readfile($filepath);
    }
}

die();   