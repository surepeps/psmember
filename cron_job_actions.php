<?php


$root=__DIR__;
require_once($root .'/config.php');
require_once($root .'/assets/init.php'); 
// showPhpErrors();
$action = filter('action');

if($action == "addCronJob") {
    $name = filter('name');
    $type = filter('type');
    $file_name = filter('file_name');


    $status = 0;
    if(!$name) {
        $message = "Please enter a valid name";
    }else if(!$file_name) {
        $message = "Please enter a valid filename";
    }else if(!$type) {
        $message = "Please select a valid run time";
    }else{

        $job = getTableData('cron_jobs', ['file_name' => $file_name], 1);
        if($job) {
            $message = "Cron job already exists with this filename, please try with another filename";
        }else{
            $data = [
                'name' => $name,
                'run_type' => $type,
                'file_name' => $file_name
            ];
            if($sqlConnect->query(insertRow('cron_jobs', $data))){
                $status = 1;
                $message = "Cron job is created successfully";
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }

    if($status == 0) {
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == "editCronJob") {
    $name = filter('name');
    $type = filter('type');
    $id = filter('id');

    $status = 0;
    if(!$name) {
        $message = "Please enter a valid name";
    }else if(!$type) {
        $message = "Please select a valid run time";
    }else{

        $where = ['id' => $id];
        $job = getTableData('cron_jobs', $where, 1);
        if(!$job) {
            $message = "Cron job not found, please try again with another cron job.";
        }else{
            $data = [
                'name' => $name,
                'run_type' => $type,
            ];
            if($sqlConnect->query(updateRow('cron_jobs', $data, $where))){
                $status = 1;
                $message = "Cron job is updated successfully";
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }

    if($status == 0) {
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == 'deleteCronJob'){

    $status = 0;
    $id = filter('cron_id');
    
    $where = ['id' => $id];
    $cron = getRow("SELECT * FROM cron_jobs WHERE ". getWhere($where));
    if(!$id || !$cron) {
        $message = 'Cron job not found, please try again with different cron job';
    }else{

        $query = deleteRow('cron_jobs', $where);
        if($sqlConnect->query($query)) {
            $status = 1;
            $message = "Cron Job Deleted Successfully";
        }else {
            $message = mysqli_error($sqlConnect);
        }
        
    }

    if($status) {
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}else if($action == 'getCronJobData') {
    $id = filter('id');
    $cron = getRow("SELECT * FROM cron_jobs WHERE id={$id}");
    if(!$id || !$cron) {
        $status = 0;
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i>Cron job not found, please try again with different cron job</div>';
    }else{
        $status = 1;
        $message = "";
    }

    $data = [
        'message' => $message,
        'status' => $status,
        'cron' => $cron
    ];


}else if($action == 'getCronModalData') {
    $id = filter('id');
    $cron = getRow("SELECT * FROM cron_jobs WHERE id={$id}");

    $html = "";
    if($cron) {
        $status = 1;
        $html = '
            <div>
                <div class="form-group">
                    <label for="name">Job Name</label>
                    <input type="text" id="name" name="name" value="' . $cron['name'] .'" class="form-control">
                </div>
                <div class="form-group">
                    <label for="file_name">File name</label>
                    <input disabled type="text" id="file_name" name="file_name" value="' . $cron['file_name'] . '" class="form-control">
                </div>
                <div class="form-group">
                    <label for="type">Run Time</label>
                    <select id="type" name="type" class="form-control">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <input type="hidden" name="action" value="editCronJob">
                <input type="hidden" name="id" value="' . $cron['id'] . '">
                <input type="hidden" name="hash_id" value="' .Wo_CreateSession(). '">
            </div>
        ';
    }

    $data = [
        'html' => $html,
        'status' => $status
    ];


}else if($action == 'runCronJob'){

    $status = 0;
    $id = filter('cron_id');
    
    $cron = getRow("SELECT * FROM cron_jobs WHERE id={$id}");
    if(!$id || !$cron) {
        $message = 'Cron job not found, please try again with different cron job';
    }else{


        $ds = DIRECTORY_SEPARATOR;
        $fileLocation = 'cron' . $ds . "run.php";
        
        
        if(!file_exists($fileLocation))  {
            $message = "Can not run this crob job, because its not developed yet. Please contact your developer. Thanks";
        }else{

            require($fileLocation);
            
            /** @var CronJob $job */   
            $job = new CronJob($cron);
            
            if(!$job->validate()){
                $message = $job->getMessage();
            }else{

                $job->run();
                $status = 1;
                $message = "Cron Job Run Successfully";
            }
            
        }

        
    }

    if($status) {
        $message = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$message. '</div>';
    }else{
        $message = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$message. '</div>';
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}

header("Content-type: application/json");
echo json_encode($data);
die();   
