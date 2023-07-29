<?php


$root=__DIR__;
require_once($root .'/config.php');
require_once($root .'/assets/init.php'); 
// showPhpErrors();
$action = filter('action');

$package_table = "wo_organization_package";
$package_user = "wo_package_users";
$attach_user = "wo_attach_users";
$status = 0;


if ($action == "addPackage") {
    $title = filter('title');
    $num_of_users = filter('num_of_users');

    $status = 0;

    if(!$title) {
        $message = "Please enter a valid title";
    }else if(!$num_of_users) {
        $message = "Please enter a valid number of users";
    }else{

        $data = [
            'title' => $title,
            'num_of_members' => $num_of_users,
        ];
        if($sqlConnect->query(insertRow($package_table, $data))){
            $status = 1;
            $message = "Package is created successfully";
        }else{
            $message = mysqli_error($sqlConnect);
        }

    }

    $data = [
        'message' => $message,
        'status' => $status
    ];

} else if ($action == "editPackage") {

    $package_id = filter('package_id');
    $title = filter('title');
    $num_of_users = filter('num_of_users');

    $status = 0;

    if(!$title) {
        $message = "Please enter a valid title";
    }else if(!$num_of_users) {
        $message = "Please enter a valid number of users";
    }else{

        $where = [
            'id' => $package_id
        ];

        $package = getTableData($package_table, $where, 1);
        if(!$package){
            $message = "Package is deleted, please try again with different package.";
        }else{
            $data = [
                'title' => $title,
                'num_of_members' => $num_of_users,
            ];
            if($sqlConnect->query(updateRow($package_table, $data, $where))){
                $status = 1;
                $message = "Package is updated successfully";
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
} else if ($action == 'deletePackage'){

    $status = 0;
    $id = filter('package_id');
    
    $where = ['id' => $id];
    $package = getTableData($package_table, $where, 1);


    if(!$id || !$package) {
        $message = 'Package not found, please try again with different Package';
    }else{

        $query = deleteRow($package_table, $where);
        if($sqlConnect->query($query)) {

            $sqlConnect->query(deleteRow($package_user, ['package_id' => $id]));
            $status = 1;
            $message = "Package and users attached with it is Deleted Successfully";

        }else {
            $message = mysqli_error($sqlConnect);
        }
 
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
} else if ($action == 'findUsers'){
    $data['data'] = [
        'Qasim',
        'Ashir',
        'Naveed',
        'Rahat',
        'Hafeez'
    ];
} else if ($action == 'savePackageUsers') {
    $package_id = filter('package_id');
    $users = filter('package_users');

    if(!$package_id){
        $message = "Please select a valid package";
    }else if(!$users){
        $message = 'Please select atleast one user to attach with package';
    }else{
        $where = ['id' => $package_id];
        $package = getTableData($package_table, $where, 1);
        if(!$package){
            $message = 'This is package is deleted, please try again with different';
        }else{
            $usernames = explode(',', $users);
            $already = getPackageUsers($package_id);

            $alreadyUserIds = array_column($already, 'user_id');
            $userIds = [];

            if(count($users)) {
                foreach($usernames as $username):

                    // Getting user based on the username
                    $user = getTableData(T_USERS, ['username' => $username], 1);

                    // If user doesn't exists continue the further loop.
                    if(!$user) continue;
                    
                    $userData = [
                        'package_id' => $package_id,
                        'user_id' => $user['user_id']
                    ];

                    // Removing that this is added
                    $userIds[] = $user['user_id'];

                    // Check if this user is already attached with package
                    if(in_array($user['user_id'], $alreadyUserIds)) continue;

                    // Save package user data in the database.
                    $sqlConnect->query(insertRow($package_user, $userData));

               endforeach;
            }

            $delete = array_diff($alreadyUserIds, $userIds);

            // Deleting the users that are deleted 
            $sqlConnect->query("DELETE FROM {$package_user} WHERE user_id IN (" . implode(',', $delete) . ")");

            $status = 1;
            $message = "Users has been attached with \"" . $package['title'] . "\" package successfully.";

        }
    }
    

    $data = [
        'status' => $status,
        'message' => $message
    ];
} else if ($action == 'getPackageUsers') {

    $package_id = filter('package_id');
    $users = filter('package_users');

    $html = '';
    if(!$package_id){
        $message = "Please select a valid package";
    }else{
        $where = ['id' => $package_id];
        $package = getTableData($package_table, $where, 1);
        if($package){
            
            $packageUsers = getPackageUsers($package_id);

            foreach($packageUsers as $user):
                $html .= '<li >' . $user['username'] . '</li>';
            endforeach;
            
        }
    }

    $data = [
        'html' => $html,
        'status' => '',
        'message' => '',
        'users' => $packageUsers
    ];

}else if ($action == 'getUserAdded') {

    $parent_id = filter('user_id');

    $html = '';
    if(!$parent_id){
        $message = "Please select a valid user";
    }else{
        $where = ['user_id' => $parent_id];
        $parent = getTableData(T_USERS, $where, 1);
        if($parent){
            
            $userUsers = getUserUsers($parent_id);
            foreach($userUsers as $user):
                $html .= '<li >' . $user['username'] . '</li>';
            endforeach;
            
        }
    }

    $data = [
        'html' => $html,
        'status' => '',
        'message' => '',
        'users' => $packageUsers
    ];

} else if ($action == 'deleteUser'){

    $status = 0;
    $id = filter('attach_id');
    
    $where = ['id' => $id];
    $attach = getTableData($attach_user, $where, 1);


    if(!$id || !$attach) {
        $message = 'User not found, please try again with different User';
    }else{

        $query = deleteRow($attach_user, $where);
        if($sqlConnect->query($query)) {

            $status = 1;
            $message = "User attached with it is Deleted Successfully";

        }else {
            $message = mysqli_error($sqlConnect);
        }
 
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
} else if ($action == 'saveUsersOnly1') {



    $parent_id = filter('user_id');
    $users = filter('package_users');

    if(!$parent_id){
        $message = "Please select a valid user";
    }else if(!$users){
        $message = 'Please select atleast one user to attach with user';
    }else{
        $where = ['user_id' => $parent_id];
        $parent = getTableData(T_USERS, $where, 1);

        if(!$parent){
            $message = 'This user is deleted, please try again with different';
        }else{
            $usernames = explode(',', $users);
            $already = getUserUsers($parent_id);

            $alreadyUserIds = array_column($already, 'user_id');
            $userIds = [];

            if(count($users)) {
                foreach($usernames as $username):

                    if(!$username) continue; 
                    // Getting user based on the username
                    $user = getTableData(T_USERS, ['username' => $username], 1);

                    // If user doesn't exists continue the further loop.
                    if(!$user || $user['user_id'] == $parent_id) continue;
                    

                    // Removing that this is added
                    $userIds[] = $user['user_id'];

                    // Check if this user is already attached with package
                    if(in_array($user['user_id'], $alreadyUserIds)) continue;

                    $userData = [
                        'parent_id' => $parent_id,
                        'user_id' => $user['user_id']
                    ];

                    // Save package user data in the database.
                    $sqlConnect->query(insertRow($attach_user, $userData));
               endforeach;
            }

            $delete = array_diff($alreadyUserIds, $userIds);

            // Deleting the users that are deleted 
            $sqlConnect->query("DELETE FROM {$attach_user} WHERE user_id IN (" . implode(',', $delete) . ")");

            $status = 1;
            $message = "Users has been attached with \"" . $parent['username'] . "\" user successfully.";

        }
    }
    

    $data = [
        'status' => $status,
        'message' => $message
    ];
} else if($action == 'saveUsersOnly') {
    
    $userParams = filter('user');
    $parent_id = filter('user_id');
    $permissions = filter('permission');
    $message = "";


    if(count($userParams)) {
        if($user_id = createUserAndConfirm($userParams, $message)){
            
            // Add Permissions
            addPermissionsToUser($user_id, $permissions);
            
            

            $userData = [
                'parent_id' => $parent_id,
                'user_id' => $user_id
            ];
            
            // Save package user data in the database.
            $sqlConnect->query(insertRow($attach_user, $userData));

            $message = "Team member is added and permissions are set";
            $status = 1;
        } else if(empty($message)) {
            $message = "Error while creating account or sending email";
        }  
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];

}else if($action == 'getUserPermissions') {

    $user_id = filter('user_id');

    $html = "";
    if($user_id){
        
        $proFeatures = getAllProFeatures();
        $userPermissions = getUserTeamPermissions($user_id);
        foreach($proFeatures as $feature ) : 

            $id = $feature['id'];

            $allow = $deny = '';
            if($userPermissions[$id]['permission_value'] == 'allow') {
                $allow = 'active';
            }else{
                $deny = 'active';
            }
            
            $html .= '
                <div class="col-md-6"> 
                    <div class="form-group">
                        <div class="permission clearfix">
                            <label class="float-left" for="first_name"> ' . $feature['feature_name'] . ' </label>
                            <div class="allow-deny btn-group float-right">
                                <span data-value="allow" class="allow ' . $allow . '">Allow</span>
                                <span data-value="deny" class="deny ' . $deny . '">Deny</span>

                                <input type="hidden" name="permission[' . $id  . ']" value="' . $userPermissions[$id]['permission_value'] . '">
                            </div>
                        </div>
                    </div>
                </div>
            ';
        
        endforeach;
    }
     

    $data = [
        'status' => $status,
        'message' => $message,
        'html' => $html
    ];
}else if($action == 'updatePermissions'){
    $user_id = filter('user_id');
    $permissions = filter('permission');

    if($user_id) {
        if(updatePermissionsToUser($user_id, $permissions)){
            $status = 1;
            $message = "User permissions are updated";
        }else{
            $message = "Error while updating the user permissions";
        }
    }else{
        $message = "Please select a valid user to update its permissions";
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];
} else if($action == 'updateUserTrial'){
    
    $user_id = filter('user_id', 0);
    $starts = filter('trial_starts');
    $ends = filter('trial_ends');
    $is_on_trial = filter('is_on_trial', 0);
    
    if(!$user_id) {
        $message = "Please select a valid user to update trial";
    }else if($is_on_trial && (!$starts || !$ends)){
        $message = "Please select a valid Trial Start and End date";
    }else if($starts >  $ends){
        $message = "Starting date can never be greater then ending date";
    }else {

        $where = ['user_id' => $user_id];
        $user = getTableData('wo_attach_users', $where, 1);

        
        if(!$user){
            $message = "This user is deleted";
        } else {
            
            $query = "UPDATE wo_attach_users SET is_trial='{$is_on_trial}', trial_starts='{$starts}', trial_ends='{$ends}' WHERE user_id='{$user_id}'";
            
            if($sqlConnect->query($query)){

                $trial = getTrialEndings($starts, $ends);
                
                if($is_on_trial && !$trial){
                    changeUserStatus($user_id, 2);
                }else{
                    changeUserStatus($user_id, 1);
                }

                $message = "Trial is updated successfully";
                $status = 1;
            }
        }

    }

    $data = [
        'status' => $status,
        'message' => $message
    ];

    
}

if($data['status'] == 0) {
    $data['message'] = '<div class="alert alert-danger"><i class="fa fa-times"></i> ' .$data['message']. '</div>';
}else{
    $data['message'] = '<div class="alert alert-success"><i class="fa fa-check"></i> ' .$data['message']. '</div>';
}
header("Content-type: application/json");
echo json_encode($data);
die();   
