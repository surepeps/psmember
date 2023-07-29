<?php


global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);
$user_id = $_POST['user_id'];


 $user_id = $wo['user']['user_id'];

if(isPost()){
    $save = filter('save');
    
    $file = filterUpload('file');
    $data = [];
    if($save && $file){
        moveFile($file);

        $extension = getExtension($file['name']);
        $time = time();
        $query = " INSERT INTO contact_list (user_id,filename,extension,created_at) VALUES  ('" . $user_id ."', '" . $file['name'] ."' , '" . $extension . "', " . $time . ")";
        
        /** @var MySql $db */
        $result = $sqlConnect->query($query);
        
        
        
        $content = '
            <tr>
                <td class="align-middle">'. $file['name'] .'</td>
                <td class="align-middle">'. $extension .'</td>
                <td class="align-middle">'. 35 .'</td>
                <td class="align-middle">'. getTime($time) .'</td>
                <td class="align-middle">
                    <a class="btn bg-orange" href="https://dev.propertysalers.com/?link1=map&file_id=">Apply Mapping and Import</a> 
                    <button type="submit" class="bg-danger text-white rounded-circle text-center dles">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        ';
                                        
        $data = [
            'message' => "Successfuly Save",
            'status' => 1,
            'content' => $content
        ];
    }

    echo json_encode($data);  exit; 
 
}

function moveFile($file){
    $name = basename($file["name"]);
    $path = './upload/contacts/' . $name;
    return move_uploaded_file($file['tmp_name'], $path);
}

