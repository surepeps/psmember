<?php
$root=__DIR__;

require_once($root.'/config.php');
require_once('assets/init.php'); 


global $wo, $sqlConnect;

$action = filter('action');

$error = "";

if($action == 'addContact') {
    $listingIds = filter('listing_ids');
    
    $added = $updated = 0;    
    $contactId = filter('contact_id');
    if(count($listingIds) && $contactId) {

        foreach($listingIds as $id) {
            if(!isNumeric($id)) continue;
            $where = [
                'listing_id' => $id
            ];

            $listingContact = getTableData('listing_contact', $where, 1);
            if($listingContact) {
                $query = updateRow('listing_contact', ['contact_id' => $contactId], $where);
            }else{
                $where += [
                    'contact_id' => $contactId
                ];
                $query = insertRow('listing_contact', $where);
            }


            if(!$sqlConnect->query($query)) {
                $error = mysqli_error($sqlConnect);
                break;
            }
            $added ++;
        }
    }

    if($error) {
        $string = "<div class='alert alert-danger'>". $error . "</div>";
    }else{
        $string = "<div class='alert alert-success'>". $added . " listing(s) are updated successfully. </div>";
    }
    
    $data = [
        'message' => $string
    ];

}else if($action == 'removeContact') {
    $listingIds = filter('listing_ids');
    
    $deleted = 0;    
    $contactId = filter('contact_id');
    if(count($listingIds) && $contactId) {

        foreach($listingIds as $id) {
            if(!isNumeric($id)) continue;
            $where = [
                'listing_id' => $id,
                'contact_id' => $contactId
            ];

            $listingContact = getTableData('listing_contact', $where, 1);
            
            if($listingContact) {
                $query = deleteRow('listing_contact', $where);
                if(!$sqlConnect->query($query)) {
                    $error = mysqli_error($sqlConnect);
                    break;
                }
                $deleted ++;
            }
        }
    }

    if($error) {
        $string = "<div class='alert alert-danger'>". $error . "</div>";
    }else{
        $string = "<div class='alert alert-success'>". $deleted . " listing(s) contact is deleted successfully. </div>";
    }
    
    $data = [
        'message' => $string
    ];

}else if($action == 'removeListing') {

    $deleted = 0;    
    $listingIds = filter('listing_ids');
    
    if(count($listingIds)) {

        foreach($listingIds as $id) {
            if(!isNumeric($id)) continue;
            $where = [
                'id' => $id,
            ];

            $listing = getTableData('Wo_Listing', $where, 1);
            
            if($listing) {
                $query = deleteRow('Wo_Listing', $where);
                if(!$sqlConnect->query($query)) {
                    $error = mysqli_error($sqlConnect);
                    break;
                }
                $deleted ++;
            }
        }
    }

    if($error) {
        $string = "<div class='alert alert-danger'>". $error . "</div>";
    }else{
        $string = "<div class='alert alert-success'>". $deleted . " listing(s) are deleted successfully. </div>";
    }
    
    $data = [
        'message' => $string
    ];

}else if($action == 'addTagCategory') {
    $category=filter("category");
    $user_id=filter("user_id");

    
    $content = $message = "Nothing happend";
    $status = 0;
      
    $categoryData = [
        'name' => $category,
        'created_by' => $user_id
    ];

    if(!$category){
        $message = "Please enter the category name first";
    }else {
        $status = 1;
        if($id = filter('id')){
            $where = ['id' => $id];
            $rowQuery = updateRow('listing_tag_category', $categoryData, $where);
            $message = "Category updated";
        }else{
            $rowQuery = insertRow('listing_tag_category', $categoryData);
            $message = "Category added";
        }
        
        if (!$sqlConnect->query($rowQuery))  {
            $message = mysqli_error($sqlConnect);
            $status = 0;
        }
    }
    if($status) {
        $message = "<div class='alert alert-success'>" . $message . "</div>";
    }else{
        $message = "<div class='alert alert-danger'>" . $message . "</div>";
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == 'addTag') {
    $category = filter('category');
    $user_id = filter('user_id');
    $name = filter('tag');

    $response = [
        'message' => "Please fill all the fields",
        'status' => 0
    ];

    if($category && $name){
        $options = [
            'tag' => $name,
            'category' => $category,
            'created_by' => $user_id
        ];
        
        $id = filter('id');
        if($id) {
            $tag = getTableData('listing_tag', ['id' => $id],1);
            if($tag){
                $where = ['id' => $id];
                $query = updateRow('listing_tag',$options, $where);
                $message = "Tag updated";
            }
        }
        else{
            $query = insertRow('listing_tag',$options);
            $message = "Tag added";
        }
        
        if($sqlConnect->query($query)){
            $message = "<div class='alert alert-success'>" . $message . "</div>";
            $status = 1;
        }else{
            $message = "<div class='alert alert-danger'>" .mysqli_error($sqlConnect). "</div>";
            $status = 0;
        }
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];

}else if($action == 'deleteTag') {
    $id = filter('id');
    $status = 0;
    if(!$id) {
        $message = "<div class='alert alert-danger'>Please select the valid tag</div>";
    }else {

        $where = ['id' => $id];
        $tag = getTableData('listing_tag', $where, 1);
        if($tag) {
            $query = deleteRow('listing_tag', $where);
            if($sqlConnect->query($query)){
                $status = 1;
                $message = "<div class='alert alert-success'>" .$tag['tag']. " delete successfully</div>";
            }else{
                $message = "<div class='alert alert-danger'>" .mysqli_error($sqlConnect). "</div>";
            }
        }else{
            $message = "<div class='alert alert-danger'>Tag not found, Pleas select the valid tag</div>";
        }
        
    }
    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == "deleteTagCategory" ){
    
    $id = filter('id');
    $status = 0;
    if(!$id) {
        $message = "<div class='alert alert-danger'>Tag category not found, pleas select the valid tag</div>";
    }else {

        $where = ['id' => $id];
        $tag = getTableData('listing_tag_category', $where, 1);
        if($tag) {
            $query = deleteRow('listing_tag_category', $where);
            if($sqlConnect->query($query)){
                $status = 1;
                $message = "<div class='alert alert-success'>" .$tag['name']. " delete successfully</div>";
            }else{
                $message = "<div class='alert alert-danger'>" .mysqli_error($sqlConnect). "</div>";
            }
        }else{
            $message = "<div class='alert alert-danger'>Tag category not found, pleas select the valid tag</div>";
        }
        
    }
    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == 'closeListing') {

    $listing_id = filter('listing_id');
    $profit = filter('actual_profit');
    $uniqueID = filter('unique_id');
    $closeDate = filter('close_date');

    
    $status = 0;
    if(!$listing_id) {
        $message = "Please select a valid listing";
    }else if(!$profit) {
        $message = "Please enter a valid profit";
    }else if(!$closeDate) {
        $message = "Please select a valid close date";
    }else if(!$uniqueID) {
        $message = "Please please enter a valid Unique ID for property referrels";
    }else{
        $listingClosed = getTableData('close_listing', [
            'unique_id' => $uniqueID
        ]);
        if($listingClosed) {
            $message = "Listing is alread closed with this code, please closed another listing that is not closed. Thanks.";
        }else {
            $closeDate = date('Y-m-d H:i:s', strtotime($closeDate));
            $closeData = [
                'listing_id' => $listing_id,
                'unique_id' => $uniqueID,
                'actual_profit' => $profit,
                'close_date' => $closeDate,
                'closed_by' => $wo['user']['user_id']
            ];

            $query = insertRow('close_listing', $closeData);
            if($sqlConnect->query($query))  {
                /**
                 * Once the listing is closed let's pay to parents
                 */
                payToPropertyReferrels($uniqueID);
                
                $message = "Listing is closed successfully";
                $status = 1;
            }
        }

    }
    
    if($status) {
        $message = "<div class='alert alert-success'>". $message. "</div>";
    }else{
        $message = "<div class='alert alert-danger'>". $message. "</div>";
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == 'getClosedListingData') {

    $date = $amount = $uniqueID =''; 
    $listing_id = filter('listing_id');

    if($listing_id) {
        $closedListing = getTableData('close_listing', ['listing_id' => $listing_id], 1);
        if($closedListing) {
            $date = $closedListing['close_date'];
            $amount = $closedListing['actual_profit'];
            $uniqueID = $closedListing['unique_id'];
        }

    }
    
    $data = [
        'amount' => $amount,
        'unique_id' => $uniqueID,
        'date' => $date,
        'status' => $closedListing ? 1 : 0
    ];
}else if($action == 'getContractListingData') {

    $date = $amount = $uniqueID =''; 
    $listing_id = filter('listing_id');

    if($listing_id) {
        $closedListing = getTableData('close_listing', ['listing_id' => $listing_id], 1);
        $contractListing = getTableData('contract_listing', ['listing_id' => $listing_id], 1);
        if($contractListing) {
            $date = $contractListing['expected_close_date'];
            $amount = $contractListing['expected_profit'];
            $uniqueID = $contractListing['unique_id'];
        }

    }
    
    $data = [
        'expected_profit' => $amount,
        'unique_id' => $uniqueID,
        'expected_close_date' => $date,
        'is_closed' => $closedListing ? 1 : 0,
    ];
}else if($action == 'contractListing') {

    $listing_id = filter('listing_id');
    $expectedProfit = filter('expected_profit');
    $uniqueID = filter('unique_id');
    $expectedDate = filter('expected_close_date');
    $promotionListing = getTableData('Wo_list_promotion', ['promote_code' => $uniqueID], 1); 
    $closedListing = getTableData('close_listing', [
        'unique_id' => $uniqueID,
        'closed_by' => $wo['user']['user_id']
    ], 1); 


    $status = 0;
    if(!$listing_id) {
        $message = "Please select a valid listing";
    }else if(!$expectedProfit) {
        $message = "Please enter a valid expected profit";
    }else if(!$expectedDate) {
        $message = "Please select a valid expected close date";
    }else if(!$uniqueID) {
        $message = "Please please enter a valid Unique ID for property referrels";
    }else if(!$promotionListing) {
        $message = "This Unique ID is not attached with any promoted listing. Please change the Unique ID and try again.";
    }else if($closedListing) {
        $message = "A listing is already closed with this Unique ID. Please change the Unique ID and try again.";
    }else{
        $contractListing = getTableData('contract_listing', [
            'listing_id' => $listing_id
        ], 1);

        $expectedDate = date('Y-m-d H:i:s', strtotime($expectedDate));
        $contractData = [
            'listing_id' => $listing_id,
            'unique_id' => $uniqueID,
            'expected_profit' => $expectedProfit,
            'expected_close_date' => $expectedDate,
            'contract_by' => $wo['user']['user_id']
        ];

        $saved = 0;
        if($contractListing) {
            $query = updateRow('contract_listing', $contractData, ['listing_id' => $listing_id]);
            $message = "Contract is updated successfully";
        }else {
            $query = insertRow('contract_listing', $contractData);
            $message = "Contract is added successfully";
            $saved = 1;
        }

        if($sqlConnect->query($query))  {
            $status = 1;

            /** Creating the logs based on this Unique ID */
            if($saved) {
                payToContractReferrels($uniqueID);
            }else{

                /** Updating the logs based on this Unique ID */
                updateContractReferrels($contractListing['unique_id'], $uniqueID);
            }
        }

    }
    
    if($status) {
        $message = "<div class='alert alert-success'>". $message. "</div>";
    }else{
        $message = "<div class='alert alert-danger'>". $message. "</div>";
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == "listingAddTag" ){
    $listing_ids = filter('listing_ids');
    $tag_id = filter('tag_id');
    $user_id = filter('user_id');
    
    $status = 0;
    if(count($listing_ids) && $tag_id && $user_id){
        $added = 0;
        foreach($listing_ids as $listing_id){
            $options = [
                'listing_id' => $listing_id,
                'tag_id' => $tag_id,
                'added_by' => $user_id
            ];

            $listingTag = getTableData('link_listing_tag', $options, 1);
            
            if(!$listingTag){
                $insertQuery = insertRow('link_listing_tag',$options);
             
                if($sqlConnect->query($insertQuery)){
                    $added ++;
                }else{
                    pre(mysqli_error($sqlConnect)); 
                }
            }
            
        }

        if(!$added){
            $message = "Tag is already add to the selected listings. Please try again with different tag.";
        }else{
            $message = "Tag is added to " . $added . " listings successfully";
            $status = 1;
        }
       
    }else{
        $message = "Please select atleast one listing";
    }

    if($status){
        $message = "<div class='alert alert-success'>" . $message . "</div>";
    }else{
        $message = "<div class='alert alert-danger'>" . $message . "</div>";
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == "listingRemoveTag" ){
    $listing_ids = filter('listing_ids');
    $tag_id = filter('tag_id');
    $user_id = filter('user_id');
    
    $status = 0;
    if(count($listing_ids) && $tag_id && $user_id){
        $deleted = 0;
        foreach($listing_ids as $listing_id){
            $options = [
                'listing_id' => $listing_id,
                'tag_id' => $tag_id,
                'added_by' => $user_id
            ];

            $listingTag = getTableData('link_listing_tag', $options, 1);
            
            if($listingTag){
                $deleteQuery = deleteRow('link_listing_tag',$options, $options);
                if($sqlConnect->query($deleteQuery)){
                    $deleted ++;
                }else{
                    pre(mysqli_error($sqlConnect)); 
                }
            }
        }

        if(!$deleted){
            $message = "This tag is not added to the selected listings.";
        }else{
            $message = "Tag is removed to " . $deleted . " listings successfully";
            $status = 1;
        }
       
    }else{
        $message = "Please select atleast one listing";
    }

    if($status){
        $message = "<div class='alert alert-success'>" . $message . "</div>";
    }else{
        $message = "<div class='alert alert-danger'>" . $message . "</div>";
    }

    $data = [
        'message' => $message,
        'status' => $status
    ];
}else if($action == "SendSMSBulk" ){

    $listing_ids = filter('listing_ids');
    $message = filter('message');
    $from_phone = filter('from_phone');
    $status = $count = 0;


    if(!$message) {
        $resp = "Please enter a valid message";
    }else if(!$from_phone) {
        $resp = "Please select a valid from phone number.";
    }else if(count($listing_ids) == 0) {
        $resp = "Please select at least one listing to send sms.";
    }else{
        foreach($listing_ids as $id)  {

            $query = "
                SELECT U.* FROM contact U
                LEFT JOIN listing_contact C ON C.contact_id = U.id
                WHERE C.listing_id = '{$id}'
            ";
            
            $contact = getRow($query);
            if($contact && !empty($contact['mobile'])) {
                $to_phone = $contact['mobile'];
                
                /** Send SMS */
                $sendresponse = send_bulk_sms_broadcast($to_phone, $from_phone, $message);
                if($sendresponse) {
                    $count ++;
                }
            }
        }
        if($count) {
            $resp = "Sms has been sent to {$count} items successfully";
            $status = 1;
        }else{
            $resp = "The selected items doesn't contain the phone.";
        }
        
    }

    if($status) {
        $message = "<div class='alert alert-success'>{$resp}</div>";
    }else {
        $message = "<div class='alert alert-danger'>{$resp}</div>";
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];
}else if($action == "SendEMAILBulk" ){

    $listing_ids = filter('listing_ids');
    $message = filter('message');
    $from_email = filter('from_email');
    $from_name = filter('from_name');
    $subject = filter('subject');
    $status = $count = 0;

    if(!$from_email) {
        $resp = "Please enter a valid from email.";
    }else if(!$subject) {
        $resp = "Please enter a valid subject.";
    }else if(!$message) {
        $resp = "Please enter a valid message.";
    }else if(count($listing_ids) == 0) {
        $resp = "Please select at least one listing to send sms.";
    }else{
        foreach($listing_ids as $id)  {
            $query = "
                SELECT U.* FROM contact U
                LEFT JOIN listing_contact C ON C.contact_id = U.id
                WHERE C.listing_id = '{$id}'
            ";
            
            $contact = getRow($query);
            if($contact && !empty($contact['email'])) {
                $to_email = $contact['email'];
                
                $send_message_data = array(
                    'from_email' => $from_email,
                    'from_name' => $from_name,
                    'to_email' => $to_email,
                    'to_name' => $contact['firstname'] . ' ' . $contact['lastname'],
                    'subject' => $subject,
                    'is_html' => false,
                    'message' => $message
                );
                
                
                if(sendSandGridEmail($send_message_data)) {
                    $count ++;
                }
            }
        }
        if($count) {
            $resp = "Email has been sent to {$count} items successfully";
            $status = 1;
        }else{
            $resp = "The selected items doesn't contain the email.";
        }
    }

    if($status) {
        $message = "<div class='alert alert-success'>{$resp}</div>";
    }else {
        $message = "<div class='alert alert-danger'>{$resp}</div>";
    }

    $data = [
        'status' => $status,
        'message' => $message
    ];
}else if($action == "getTemplateData") {
    $id = filter('id');
    $type = filter('template_type');
    $listing_type = filter('listing_type');
    
    $status = $template = 0;

    $promoted = getTableData('Wo_list_promotion', ['listing_id' => $id], 1);
    $listing =  getTableData('Wo_Listing', ['id' => $id], 1);

    if($promoted && $listing) {

        $thumbnail = unserialize($listing['tab6']);

        $title = $promoted['title'];
        $url = $wo['site_url'] . "/promoted-property/" . $promoted['promote_code'];
        $description = $promoted['description'];
        $price = $promoted['price'];

        
        if($listing_type == 'landing') {

            $title = get_listing_by_specific_colm("tab1","listing_title",$id);
            $url = $wo['site_url'] . "/listing/" . $promoted['slug'];
            $description = $listing['description'];
            $price = get_listing_by_specific_colm("tab1","rental_price",$id);

        }

        $tags = [
            "Property_Title" => $title,
            "Property_Description" => $description,
            "Property_Thumbnail" => "<img style='width: 280.622px;' src='" . $wo['site_url'] . "/themes/wondertag/uploads_images/" . $thumbnail[0] .  "' />",
            "Property_Address" => get_listing_by_specific_colm("tab1","entered_address",$id),
            "Property_Url" => $url,
            "Property_City" => get_listing_by_specific_colm("tab1","city",$id),
            "Property_State" => get_listing_by_specific_colm("tab1","state",$id),
            "Property_Zipcode" => get_listing_by_specific_colm("tab1","postal_code",$id),
            "Property_Price" => $price,
            "Property_Repairs" => get_listing_by_specific_colm("tab1","flip_ext_repair",$id),
            "Property_Bathrooms" => get_listing_by_specific_colm("tab1","baths",$id),
            "Property_Bedrooms" => get_listing_by_specific_colm("tab1","beds",$id),
            "Property_Sqft" => get_listing_by_specific_colm("tab1","property_size",$id),
            "Property_Year_Built" => get_listing_by_specific_colm("tab1","constructions_year",$id),
            "Property_ARV" => get_listing_by_specific_colm("tab1","rental_arv",$id),
        ];
        

        $templateType = $type . '_template';
        if(isset($wo['config'][$templateType])) {
            $template = $wo['config'][$templateType];
            foreach($tags as $key => $value) {
                $replace = "~{$key}~";
                $template = str_replace($replace, $value, $template);
            }
            $status = 1;
            $message = "Template is copied";
        }else{
            $message = "Please contact administrator";
        }
    }else{
        $message = "Listing not found";
    }

    $data = [
        'template' => $template,
        'status' => $status,
        'message' => $message,
        'thumbnail' => $wo['site_url'] . "/themes/wondertag/uploads_images/" . $thumbnail[0],
        'type' => $type
    ];
}else if($action == "saveBankDetails") {

    $user_id = filter('user_id');
    $bank_name = filter("bank_name");
    $account_number = filter("account_number");
    $bank_holder = filter("bank_holder");
    $bank_address = filter("bank_address");
    $bank_swift = filter("bank_swift");

    $status = 0;
    if(!$user_id) {
        $message = "You are not logged in. Please login and try again.";
    }else if(!$bank_name) {
        $message = "Please type Bank Name and try again.";
    }else if(!$account_number) {
        $message = "Please type Account Number and try again.";
    }else if(!$bank_holder) {
        $message = "Please type Bank Holder Name and try again.";
    }else if(!$bank_address) {
        $message = "Please type Bank Address and try again.";
    }else if(!$bank_swift) {
        $message = "Please type Routing/IBAN/Swift Code and try again.";
    }else{

        $where = ['user_id' => $user_id];
        $bankDetails = getTableData('wo_bank_details', $where, 1);
        $messageTitle = "Added";
        $detailData = [
            'bank_name' => $bank_name,
            'account_number' => $account_number,
            'bank_holder' => $bank_holder,
            'bank_address' => $bank_address,
            'bank_swift' => $bank_swift,
            'user_id' => $user_id,
        ];

        if($bankDetails) {
            $messageTitle = "Updated";
            $query = updateRow('wo_bank_details', $detailData, $where);
        }else{
            $query = insertRow('wo_bank_details', $detailData);
        }
        
        if($sqlConnect->query($query)){
            $status = 1;
            $message = "Bank details are {$messageTitle}";
        }else{
            $message = mysqli_error($sqlConnect);
        }
    }


    $data = [
        'status' => $status,
        'message' => $message
    ];
}else if($action == 'addWithdraw') {

    $amount = filter('amount');
    $user_id = filter('user_id');

    $where = ['user_id' => $user_id];
    $user = getTableData('Wo_Users', $where, 1);
    $bankDetails = getTableData('wo_bank_details', $where, 1);

    $userBalance = $status = 0;

    if(!$amount) {
        $message = "Please enter a valid amount";
    }else if(!$user){
        $message = "You have not logged in. Please login to proceed.";
    }else if(!$bankDetails){
        $message = "Please setup your bank details first to withdraw to proceed.";
    }else if($amount > getUserReferralBalance($user_id)){
        $message = "You have insufficient balance to withdraw this amount.";
    }else{
        $withdrawData = [
            'amount' => $amount,
            'via' => 'Bank',
            'status' => 'pending',
            'user_id' => $user['user_id']
        ];

        $query = insertRow('wo_user_withdraw', $withdrawData);
        if($sqlConnect->query($query)) {

            updateUserReferralBalance($user_id, $amount, '-');
            $userBalance = getUserReferralBalance($user_id);
            $status = 1;
            $message = "This amount has been added for withdraw, we will let you know when this will be approved. Thanks";
        }
    }

    $data  = [
        'status' => $status,
        'message' => $message,
        'user_balance' => number_format($userBalance)
    ];
}
else if($action == 'verifyBuyer') {

    $email = filter('email');
    $pin_code = filter('pin_code');
    $listing_id = filter('listing_id');
    
    $address = $status = 0;

    if(!$email) {
        $message = "Please enter a valid email.";
    }else if(!$pin_code){
        $message = "Please enter a valid Customer Number.";
    }else if(!$listing_id){
        $message = "Listing is not found. Please try again with another lisitng.";
    }else{

        $emailContact = getBuyerPinByEmail($email);

        
        if(!$emailContact || $emailContact['pin_code'] != $pin_code){
            $message = "Invalid cradentials";
        }else{

            $listing = getTableData('Wo_Listing', ['id' => $listing_id], 1);
            if(!$listing) {
                $message = "Listing is deleted. Please try again with another listing.";
            } else { 
                $address = get_listing_by_specific_colm("tab1","entered_address",$listing_id);
                $message = "Cradentials verified. You can see the address.";
                $status = 1;
            }
            
        }
        
        
    }

    $data  = [
        'status' => $status,
        'message' => $message,
        'address' => $address
    ];
} else if($action == 'simpleTemplateData') {
    $id = filter('id');
    $type = filter('template_type');
    $listing_type = filter('listing_type');
    
    $status = $template = 0;

    $promoted = getTableData('Wo_list_promotion', ['listing_id' => $id], 1);
    $listing =  getTableData('Wo_Listing', ['id' => $id], 1);

    if($listing) {

        $tab1 = json_decode($listing['tab1'], 1);
        $thumbnail = unserialize($listing['tab6']);

        $title = $tab1['listing_title'];
        $url = $wo['site_url'] . "/property/" . $listing['id'];
        $description = $listing['description'];
        $price = $tab1['flip_price'];

        $tags = [
            "Property_Title" => $title,
            "Property_Description" => $description,
            "Property_Thumbnail" => "<img style='width: 280.622px;' src='" . $wo['site_url'] . "/themes/wondertag/uploads_images/" . $thumbnail[0] .  "' />",
            "Property_Address" => get_listing_by_specific_colm("tab1","entered_address",$id),
            "Property_Url" => $url,
            "Property_City" => get_listing_by_specific_colm("tab1","city",$id),
            "Property_State" => get_listing_by_specific_colm("tab1","state",$id),
            "Property_Zipcode" => get_listing_by_specific_colm("tab1","postal_code",$id),
            "Property_Price" => $price,
            "Property_Repairs" => get_listing_by_specific_colm("tab1","flip_ext_repair",$id),
            "Property_Bathrooms" => get_listing_by_specific_colm("tab1","baths",$id),
            "Property_Bedrooms" => get_listing_by_specific_colm("tab1","beds",$id),
            "Property_Sqft" => get_listing_by_specific_colm("tab1","property_size",$id),
            "Property_Year_Built" => get_listing_by_specific_colm("tab1","constructions_year",$id),
            "Property_ARV" => get_listing_by_specific_colm("tab1","rental_arv",$id),
        ];

        $templateType = $type . '_template';
        if(isset($wo['config'][$templateType])) {
            $template = $wo['config'][$templateType];
            foreach($tags as $key => $value) {
                $replace = "~{$key}~";
                $template = str_replace($replace, $value, $template);
            }
            $status = 1;
            $message = "Template is copied";
        }else{
            $message = "Please contact administrator";
        }
    }else{
        $message = "Listing not found";
    }


    $data = [
        'template' => $template,
        'status' => $status,
        'message' => $message,
        'thumbnail' => $wo['site_url'] . "/themes/wondertag/uploads_images/" . $thumbnail[0],
        'type' => $type
    ];
} else if($action == 'findLatLng') {

    $user_id = filter('user_id');

    if($user_id){

        
        $listItems = getFilteredPromoted($user_id);
        $unMap = [];
        foreach($listItems as $prop){
            $tab1 = json_decode($prop['tab1'], 1);
            if($prop['lat'] && $prop['lang']){}else{

                $unMap[] = $prop;
            }
        }
        
        
        if(count($unMap)){
            foreach($unMap as $item){
                $tab = json_decode($item['tab1'], 1);
                
                $address = $tab['address'] . " " . $tab['city']. " " . $tab['state'];
                
                if(!empty(trim($address))){

                    $address = str_replace(" ", "+", ltrim(rtrim($address)));

                    $url ="https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyAOCpcRVN4KuruP6zFRbbNt0y3b4w8nSCE";
                    $json = file_get_contents($url);
                    $r = json_decode($json);

                    if($r){
                        if ($r->status == 'OK') {
                            $update = [
                                'lat' => $r->results[0]->geometry->location->lat,
                                'lang' => $r->results[0]->geometry->location->lng,
                            ]; 
                        
                        } else {
                            $update = [
                                'lat' => "WRONG",
                                'lang' => "WRONG",
                            ]; 
                        }

                        $query = updateRow(T_LISTINGS, $update, ['id' => $item['id']]);
                        $sqlConnect->query($query);

                        if($error = mysqli_error($sqlConnect)){
                            pre($error, $update, $item, $r); exit; 
                        }
                    
                    } 

                    
                }
            }
        }

    }

    $data = [
        'message' => "Yes latitude & longitudes are updated",
        'status' => 1
    ];

} else if ($action == 'getListingData') {
    $id = filter('id');
    $status = $html = $listing = 0;
    if (!$id) {
        $message = "Please select a valid listing";
    } else {

        $where = ['id' => $id];
        $listing = getTableData(T_LISTINGS, $where, 1);
        if ($listing) {
            $wo['first'] = $listing;
            $html = Wo_LoadPromotedPage('store/single-content');
            $status = 1;
        } else {
            $message = "<div class='alert alert-danger'>Tag not found, Pleas select the valid tag</div>";
        }
    }
    $listing['tab1'] = json_decode($listing['tab1'], 1);
    $data = [
        'message' => $message,
        'status' => $status,
        'html' => $html,
        'listing' => $listing
    ];
}

header("Content-type: application/json");
echo json_encode($data);
die();   

