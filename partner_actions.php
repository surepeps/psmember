<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 

global $wo, $sqlConnect;

$action = filter('action');
$status = 0;
 

if($action == 'savePartner') {
    
    
    $user_id = filter('user_id');
    $name = filter('name');
    $email = filter('email');
    $phone_number = filter('phone_number');
    $company = filter('company');
    $property_per_month = filter('property_per_month');
    $notes = filter('notes');
    $social = filter('social');
    

    if(!$user_id){
        $message = "Please login to add partner.";
    }else if(!$name){
        $message = "Please enter a valid name.";
    }else if(!$email){
        $message = "Please enter a valid email.";
    }else if(!$phone_number){
        $message = "Please enter a valid phone number.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to add partner";
        }else{

            $social = serialize($social);
            $password = filter('password');

            // Making password hashed so that no one can see this.
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $partner = [
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone_number,
                'password' => $hashed,
                'company' => $company,
                'property_per_month' => $property_per_month,
                'social_urls' => $social,
                'notes' => $notes
            ];

            // This is partner
            $query = insertRow('wo_user_partner', $partner);
            if($sqlConnect->query($query)) {
                $message = "Partner added successfully";
                $status = 1;
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}else if($action == 'updatePartner') {
    
    
    $partner_id = filter('partner_id');
    $user_id = filter('user_id');
    $name = filter('name');
    $email = filter('email');
    $phone_number = filter('phone_number');
    $company = filter('company');
    $property_per_month = filter('property_per_month');
    $notes = filter('notes');
    $social = filter('social');
    

    if(!$user_id){
        $message = "Please login to add partner.";
    }else if(!$partner_id){
        $message = "This partner is not found. Please try again with different partner, Thanks";
    }else if(!$name){
        $message = "Please enter a valid name.";
    }else if(!$email){
        $message = "Please enter a valid email.";
    }else if(!$phone_number){
        $message = "Please enter a valid phone number.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to add partner";
        }else{

            $where = [
                'id' => $partner_id
            ];
            $partner = getTableData('wo_user_partner', $where, 1);
            if(!$partner) {
                $message = "This partner is already deleted. Please try again with different";
            }else{
                $social = serialize($social);
                $password = filter('password');

                // Making password hashed so that no one can see this.
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $partner = [
                    'user_id' => $user_id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone_number,
                    'password' => $hashed,
                    'company' => $company,
                    'property_per_month' => $property_per_month,
                    'social_urls' => $social,
                    'notes' => $notes
                ];

                // This is partner
                $query = updateRow('wo_user_partner', $partner, $where);
                if($sqlConnect->query($query)) {
                    $message = "Partner updated successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }

            
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}else if($action == 'deletePartner') {
    
    
    $partner_id = filter('partner_id');
    
    if(!$user_id){
        $message = "Please login to add partner to delete.";
    }else if(!$partner_id){
        $message = "Please select a valid partner to delete.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to delete partner";
        }else{

            $where = [
                'id' => $partner_id
            ];
            $partner = getTableData('wo_user_partner', $where, 1);
            if(!$partner) {
                $message = "This partner is already deleted. Please try again with different";
            }else{

                // This is partner
                $query = deleteRow('wo_user_partner', $where);
                if($sqlConnect->query($query)) {
                    $message = "Partner is deleted successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'addCategory') {
    
    $user_id = filter('user_id');
    $category = filter('category');

    if(!$user_id){
        $message = "Please login to add category.";
    }else if(!$category){
        $message = "Please enter a valid category name.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to add category";
        }else{

            $where = [
                'id' => $partner_id
            ];
            
            // This is partner
            $category = [
                'category' => $category,
                'user_id' => $user_id
            ];
            $query = insertRow('wo_partner_categories', $category);
            if($sqlConnect->query($query)) {
                $message = "Category is added successfully";
                $status = 1;
            }else{
                $message = mysqli_error($sqlConnect);
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'deleteCategory') {
    
    
    $category_id = filter('id');
    $user_id = filter('user_id');
    
    if(!$user_id){
        $message = "Please login to add delete to delete.";
    }else if(!$category_id){
        $message = "Please select a valid category to delete.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to delete category";
        }else{

            $where = [
                'id' => $category_id
            ];
            $partner = getTableData('wo_partner_categories', $where, 1);
            if(!$partner) {
                $message = "This partner is already deleted. Please try again with different";
            }else{

                // This is partner
                $query = deleteRow('wo_partner_categories', $where);
                if($sqlConnect->query($query)) {
                    $message = "Category is deleted successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'addTag') {
    
    $user_id = filter('user_id');
    $tag_id = filter('tag');
    $category_id = filter('category');

    if(!$user_id){
        $message = "Please login to add category.";
    }else if(!$tag_id){
        $message = "Please enter a valid tag name.";
    }else if(!$category_id){
        $message = "Please select a valid category.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to add category";
        }else{

            $category = getTableData('wo_partner_categories', [
                'id' => $category_id
            ], 1);
            
            if(!$category){
                $message = "This category is deleted. Please try again with different";
            }else{
                $tag = [
                    'tag' => $tag_id,
                    'category_id' => $category_id,
                    'user_id' => $user_id
                ];

                $query = insertRow('wo_partner_tags', $tag);
                
                if($sqlConnect->query($query)) {
                    $message = "Tag is added successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }

            
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'deleteTag') {
    
    
    $category_id = filter('id');
    $user_id = filter('user_id');
    
    if(!$user_id){
        $message = "Please login to add delete to delete.";
    }else if(!$category_id){
        $message = "Please select a valid category to delete.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to delete category";
        }else{

            $where = [
                'id' => $category_id
            ];
            $partner = getTableData('wo_partner_categories', $where, 1);
            if(!$partner) {
                $message = "This partner is already deleted. Please try again with different";
            }else{

                // This is partner
                $query = deleteRow('wo_partner_categories', $where);
                if($sqlConnect->query($query)) {
                    $message = "Category is deleted successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'addPartnerTag') {
    
    $user_id = filter('user_id');
    $partner_id = filter('partner_id');
    $tag_ids = filter('tags');

    
    if(!$user_id){
        $message = "Please login to add category.";
    }else if(!$partner_id){
        $message = "Please select a valid partner.";
    }else if(!count($tag_ids)){
        $message = "Please select atleast one tag.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to add category";
        }else{

            $partner = getTableData('wo_user_partner', ['id' => $partner_id], 1);

            if(!$partner){
                $message = "This partner is already deleted.";
            }else{

                foreach($tag_ids as $tag_id){
                    $params = [
                        'user_id' => $user_id,
                        'tag_id' => $tag_id,
                        'partner_id' => $partner_id,
                    ];
                    
                    $tag = getTableData('wo_partner_link_tags', $params);
                    if(!$tag){
                        if(!$sqlConnect->query(insertRow('wo_partner_link_tags', $params))){
                            $message = mysqli_error($sqlConnect);
                            $status = 2;
                            break;
                        }
                    }
                }

                if($status != 2) {
                    $message = "Tags are added successfully";
                    $status = 1;
                }else{
                    $status = 0;
                }
                
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'deletePartnerTag') {
    
    
    $link_id = filter('link_id');
    $user_id = filter('user_id');
    
    if(!$user_id){
        $message = "Please login to add delete to delete.";
    }else if(!$link_id){
        $message = "Please select a valid partner tag to delete.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to delete category";
        }else{

            $where = [
                'id' => $link_id
            ];

            $tag = getTableData('wo_partner_link_tags', $where, 1);
            if(!$tag) {
                $message = "This partner tag is already deleted. Please try again with different";
            }else{

                // This is partner
                $query = deleteRow('wo_partner_link_tags', $where);
                if($sqlConnect->query($query)) {
                    $message = "Partner tag is deleted successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
        'icon' => 1
    ];
}else if($action == 'updatePassword') {
    
    $user_id = filter('user_id');
    $partner_id = filter('partner_id');
    $password = filter('password');
    
    

    if(!$user_id){
        $message = "Please login to add partner.";
    }else if(!$partner_id){
        $message = "Please select a valid partner.";
    }else if(!$password){
        $message = "Please enter a valid password.";
    }else{
        
        $user = getTableData(T_USERS, ['user_id' => $user_id], 1);
        if(!$user) {
            $message = "Please login to add partner";
        }else{

            $where = ['id' => $partner_id];

            $partner = getTableData('wo_user_partner', $where, 1);
            if(!$partner) {
                $message = "This partner is already deleted.";
            }else{  
                // Making password hashed so that no one can see this.
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $update = [
                    'password' => $hashed
                ];
                // This is partner
                $query = updateRow('wo_user_partner', $update, $where);
                if($sqlConnect->query($query)) {
                    $message = "Password added successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }
        }

    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}else if($action == 'approveProperty') {
    
    $property_id = filter('property_id');
    $partner_id = filter('partner_id');
    $password = filter('password');
    $send_contract = filter('send_contract');
    

    if(!$partner_id){
        $message = "Please login to add partner.";
    }else if(!$property_id){
        $message = "Please select a valid property.";
    }else{
        
        $partner = getTableData('wo_user_partner', $where, 1);
        if(!$partner) {
            $message = "This partner is already deleted.";
        }else{  

            $where = ['id' => $property_id];
            $property = getTableData('wo_partner_properties', $where, 1);
            if(!$property){
                $message = "This property is already deleted.";
            }else{
                
                $update = [
                    'status' => 1,
                    'contract_status' => $send_contract
                ];
                // This is partner
                $query = updateRow('wo_partner_properties', $update, $where);
                if($sqlConnect->query($query)) {

                    if($send_contract && sendPropertyContract($property_id, $user_id)){
                        $message = "Property approved and Contract sent successfully";
                    }else{
                        $message = "Property approved successfully";
                    }
                    
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
            }
        }
    }
    
    $data = [
        'message' => $message,
        'status' => $status,
    ];
}else if($action == 'updateContractDetails') {
    

    $folderPath = "upload/signature/";

    $member_id = filter('user_id');
    $name = filter('name');
    $company = filter('company');
    $county = filter('county');
    $state = filter('state');
    $pad = filter('signature');

    if(!$member_id){
        $message = "Please login to update contract details.";
    } else if(!$name){
        $message = "Please enter a valid name.";
    } else if(!$company){
        $message = "Please enter a valid company.";
    } else if(!$county){
        $message = "Please enter a valid county.";
    } else if(!$state){
        $message = "Please enter a valid state.";
    } else{
        
        $where = [
            'user_id' => $member_id
        ];
        $partner = getTableData(T_USERS, $where, 1);
        
        if(!$partner) {
            $message = "Please login first";
        }else{

            $where = [
                'member_id' => $member_id,
                'member_type' => 'user'
            ];
            $contract = getTableData('wo_contract_members', $where, 1);

            if(!$contract && !$pad) {
                $message = "Please draw a valid signature.";
            }else{

                if($contract) {
                    $path = $contract['signature_path'];
                }
                if($pad) {
                    $image_parts = explode(";base64,", $pad);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $path = "{$name}-" . uniqid() . '.'.$image_type;
                    $file = $folderPath . $path ;

                    file_put_contents($file, $image_base64);
                }
                
                $signature = [
                    'member_id' => $member_id,
                    'name' => $name,
                    'company' => $company,
                    'state' => $state,
                    'county' => $county,
                    'signature_path' => $path,
                    'member_type' => 'user'
                ];

                if($contract) {
                    $query = updateRow('wo_contract_members', $signature, $where);
                }else{
                    $query = insertRow('wo_contract_members', $signature);
                }
                
                if($sqlConnect->query($query)) {
                    $message = "Contract details are updated successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }
                
            }

        }

    }

    
    $data = [
        'status' => $status,
        'message' => $message
    ];
}else if($action == 'viewProperty') {
    $id = filter('id');

    $html = "";
    if($id) {

        $property = getTableData('wo_partner_properties', ['id' => $id], 1);
        $wo['property'] = $property;
        $html = Wo_LoadPage('partners/view-property');
        
    }

    $data = [
        'status' => $status,
        'html' => $html
    ];
}else if($action == 'pushToListing') {
    $property_id = filter('property_id');
    $uploadFiles = filterUpload('images');
    $user_id = filter('user_id');

    if(!$user_id){
        $message = "You are not logged in, please login first";
    }else if(!$property_id) {
        $message = "Please select a valid property to push listing";
    }else if(!count($uploadFiles['name'])){
        $message = "Please select atleast on image for property";
    }else{

        $property = getTableData('wo_partner_properties', ['id' => $property_id], 1);
        if(!$property){
            $message = "This property is already deleted, please select atleast one property";
        }else{
            $files = [];
            if(count($uploadFiles['name'])){
                foreach($uploadFiles['name'] as $key => $file){
                    $files[] = [
                        'name' => $uploadFiles['name'][$key],
                        'type' => $uploadFiles['type'][$key],
                        'tmp_name' => $uploadFiles['tmp_name'][$key],
                        'error' => $uploadFiles['error'][$key],
                        'size' => $uploadFiles['size'][$key],
                    ];
                }
            }

            if(count($files)){

                $dealType = [
                    'fix_and_flip' => 'Fix and Flip',
                    'buy_and_hold' => 'Buy and Hold',
                    'wholesale' => 'Wholesale',
                ];

                $allow_promotion = filter('allow_promotion');
                $desctiption = $property['notes'];
                $propertycode = uniqid("pro_");
                $tab1 = [
                    'user_id' => $user_id,
                    'propid' => 'form_tab1',
                    'listing_title' => filter('title'),
                    'entered_address' => $property['address'],
                    'city' => filter('city'),
                    'postal_code' => "",
                    'state' => filter('state'),
                    'country' => "",
                    'city_r' => filter('city'),
                    'path' => 'partner-property',
                    'access_remain' => "",
                    'access_used' => "",
                    'prop_type' => filter('prop_type'),
                    'deal_type' => $dealType[$property['deal_type']],
                    'propertycode' => $propertycode,
                    'beds' => $property['beds'],
                    'property_size' => $property['sqft'],
                    'baths' => $property['baths'],
                    'constructions_year' => "",
                    'flip_price' => 0,
                    'flip_arv' => $property['arv'],
                    'flip_ext_repair' => $property['repairs'],
                    'rental_price' => $property['purchase_price'],
                    'rental_arv' => $property['rent'],
                    'rental_ext_rent' => "",
                    'allow_promotion' => $allow_promotion,
                    'gift_price' => filter('gift_price'),
                    'promotion_note' => $allow_promotion,
                    'video_link' => "",
                    'visibility' => "",
                    'buy_nowP' => "",
                ];

                $json_tab1 = json_encode($tab1);
                $extra = json_decode($property['extra'], 1);
                $listingData = [
                    'tab1' => $json_tab1,
                    'propertycode' => $propertycode,
                    'allow_promotion' => $allow_promotion,
                    'user_id' => $user_id,
                    'description' => $property['notes'],
                    'lang' => $extra['lng'],
                    'lat' => $extra['lat'],
                    'status' => 0,
                    'partner_property' => $property['id']
                ];

                $query = insertRow('Wo_Listing', $listingData);
                // $query = "INSERT INTO Wo_Listing (`tab1`,`propertycode`,`allow_promotion`,`user_id`,`description`,`status`) VALUES ('$json_tab1','$propertycode','$allow_promotion','$user_id', '$description','0')";

                if($sqlConnect->query($query)){
                    uploadPartnerFiles($user_id, $propertycode, $files);
                    $message = "Property is pushed to listing successfully";
                    $status = 1;
                }else{
                    $message = mysqli_error($sqlConnect);
                }

            }
        }
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
        
}




header("Content-type: application/json");
echo json_encode($data);
die();   