<?php

global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];

require_once($root.'/config.php');
require_once('assets/init.php');

$sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);


$fileId = filter('file_id');
$result = $sqlConnect->query("SELECT * FROM files WHERE file_id=" . $fileId);
pre($result); exit; 
$file = $result->fetch_assoc();
pre($file); exit; 

if(!$file || $file['extension'] != "csv"){
    echo "File not supported"; exit; 
}


$contacts = [];
$fileUrl = "./upload/" . $file['filename'];
if (($handle = fopen($fileUrl, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if(count($data)){
            $contacts[] = $data;
        }
    }

    
    fclose($handle);
}

$headings = $contacts[0];
unset($contacts[0]);
    
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>

    <link href="assets/css/bootstrap.min.css" />
</head>
<body>

    <div class="container">
        <h2><?= $file['filename']  ?></h2>

        <a href="<?= url('import?file_id='. $file['file_id']); ?>">Import Contacts</a>

        <div class="input-group">
            <label>Select first name</label>
            <select id="first_name">
                <?php foreach($headings as $heading) {?>
                    <option value="<?= $heading ?>"><?= $heading ?> </option>
                <?php } ?>
            </select>
        </div>
        
        <div class="input-group">
            <label>Select last name</label>
            <select id="last_name">
                <?php foreach($headings as $heading) {?>
                    <option value="<?= $heading ?>"><?= $heading ?> </option>
                <?php } ?>
            </select>
        </div>
        
        <div class="input-group">
            <label>Phone number</label>
            <select id="phone">
                <?php foreach($headings as $heading) {?>
                    <option value="<?= $heading ?>"><?= $heading ?> </option>
                <?php } ?>
            </select>
        </div>
        <button id="apply_mapping">Apply mapping</button>


        <div id="contacts">
                
        </div>
        
    </div>
    

    <script src="jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <script>

            $("#apply_mapping").on('click', (e) => {
                let firstName = $('#first_name').val(),
                     lastName = $('#last_name').val(),
                     phone = $('#phone').val();

                let sort = {
                    firstname: firstName,
                    lastname: lastName,
                    phone: phone
                };
                $.ajax({
                    url: "map-content.php",
                    data: {
                        sorting: sort,
                        file_id: '<?= $file["file_id"] ?>'
                    },
                    type: "post",
                    success: (response) => {
                        response = JSON.parse(response);
                        if(response.status == 1 && response.content){
                            $('#contacts').empty();
                            $('#contacts').append(response.content);
                        }
                    }
                })
            });

    </script>
</body>
</html>

