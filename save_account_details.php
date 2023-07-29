<?php

$root  = __DIR__;


require_once($root.'/config.php');
require_once($root . '/assets/init.php');

require_once($root . '/stripe_class.php');

global $wo, $sqlConnect;

$status = 0;
$stripeClass = "";



$action = filter('action');
$user_id = filter('user_id');
$where = ['user_id' => $user_id];
$filters = filter('account');


if(!$user_id) {
    $message = "User is not found, please login first";
}else{
    $user = getTableData(T_USERS, $where, 1);
    if(!$user) {
        $message = "User is deleted, please try again with different user";
    }
}


if(empty($message)){

    try{

        /** New Stripe Class instentiating */
        $stripeClass = new StripeAccount($user_id, $filters);
        
    
    } catch (Exception $e) {
        $message = $e->getMessage();
    }


    if($action == 'createAccount') {

        $email = $filters['email'];
        $account_type = $filters['account_type'];


        if(!$email) {
            $message = "Please enter a valid email address";
        }else {

            $account = getTableData('wo_bank_details', ['user_id' => $user_id], 1);
            
            
            if($account) {
                $message = "You have already created an account. You can not change the email or account type. Please update the other details.";
            }else{
                $filters += [
                    'user_id' => $user_id
                ];
    
                $stripeClass->setInput($filters);
                
                
                if($stripeClass->create()) {
                    $status = 1;
                    $message = "Account is created successfully";
                }else{
                    $message = $stripeClass->getError();
                }
            }

        }
    }else if($action == 'businessProfile'){

        $account = getTableData('wo_bank_details', [
            'user_id' => $user_id
        ], 1);

        if(!$account) {
            $message = "Please create account first from first (Account) tab";
        }else{

            $input = $filters;

            $filters = [
                'business_profile' => [
                    'mcc' => $input['mcc_number'],
                    'name' => $input['business_name'],
                    'support_email' => $input['support_email'],
                    'support_phone' => $input['support_phone'],
                    'support_url' => $input['support_url'],
                    'url' => $input['support_url'],
                    'support_address' => [
                        'line1' => $input['business_line1'],
                        'city' => $input['business_city'],
                        'state' => $input['business_state'],
                        'postal_code' => $input['business_postal_code'],
                    ]
                ]
            ];

            $stripeClass->setInput($filters);

            try{
                $stripeClass->update();
                $status = 1;
                $message = "Business details are added.";
            }catch(Exception $e) {
                $message = $e->getMessage();
            }
        }
    }else if($action == 'externalAccount'){

        $account = getTableData('wo_bank_details', [
            'user_id' => $user_id
        ], 1);

        if(!$account) {
            $message = "Please create account first from first (Account) tab";
        }else{

            $input = $filters;
            $routingNumber = $input['routing_number'];
            $accountNumber = $input['account_number'];
            $filters = [
                'external_account' => [
                    'object' => 'bank_account',
                    'country' => 'US',
                    'currency' => 'USD',
                    'account_holder_name' => $input['account_holder_name'],
                    'routing_number' => "{$routingNumber}",
                    'account_number' => "{$accountNumber}",
                ],
            ];

            $stripeClass->setInput($filters);

            try{
                $stripeClass->update();
                $status = 1;
                $message = "External account details are added.";
            }catch(Exception $e) {
                $message = $e->getMessage();
            }
        }
    }else if($action == 'personalProfile'){

        $account = getTableData('wo_bank_details', [
            'user_id' => $user_id
        ], 1);

        if(!$account) {
            $message = "Please create account first from first (Account) tab";
        }else{

            $input = $filters;
            $personal = [
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'id_number' => $input['id_number'],
                'email' => $input['individual_email'],
                'phone' => $input['phone_number'],
                'dob' => [
                    'day' => $input['birth_day'],
                    'month' => $input['birth_month'],
                    'year' => $input['birth_year']
                ],
                'address' => [
                    'line1' => $input['line1'],
                    'postal_code' => $input['postal_code'],
                    'city' => $input['city'],
                    'state' => $input['state']
                ]
            ];

            if($stripeClass->isIndividualAccount()) {
                $filters = [
                    'individual' => $personal
                ];
                $stripeClass->setInput($filters);
                $stripeClass->update();
                $message = "Personal profile details are added.";

            }else if(!$account['person_id']){
                
                $stripeClass->setInput($personal);
                $person = $stripeClass->createPerson($account['stripe_account_id']);
                $message = "Company details are added.";
                
            }

            if($error = $stripeClass->getError()) {
                $message = $error;
            }else{
                $status = 1;
            }
            
        }
    }else if($action == 'updateVerification'){

        $file = filterUpload('document');
        $account = getTableData('wo_bank_details', [
            'user_id' => $user_id
        ], 1);

        if(!$account) {
            $message = "Please create account first from first (Account) tab";
        }elseif($account['file_id']) {
            $message = "Verification file can not be updated. Thanks.";
        }else{

            $filters = [
                'verification_file' => $file
            ];

            $stripeClass->setInput($filters);
            try{
                if($stripeClass->file() ){

                    $file_id = $stripeClass->params['file_id'];
                    
                    unset($stripeClass->params['verification_file']);
                    if($stripeClass->isIndividualAccount()){

                        // Setting the params for the Individual or Person File
                        $stripeClass->setInput([
                            'individual' => [
                                'verification' => [
                                    'document' => [
                                        'front' => $file_id
                                    ]
                                ]
                            ]
                        ]);

                        if($stripeClass->update()){
                            $status = 1;
                            $message = "Personal profile verification file is updated.";
                        }
                    }else{
                        if($stripeClass->updateRepresentativeFile($file_id)){
                            $message = "Company representative verification file is updated";
                            $status = 1;
                        }
                    }
                    
                }
                
                if($error = $stripeClass->getError()){
                    $message = $error;
                }

            }catch(Exception $e) {
                $message = $e->getMessage();
            }
        }
    }else if($action == 'updateCompany') {

        $account = getTableData('wo_bank_details', [
            'user_id' => $user_id
        ], 1);

        if(!$account) {
            $message = "Please create account first from first (Account) tab";
        }else{

            $input = $filters;
            $filters = [
                'company' => [
                    'name' => $input['company_name'],
                    'phone' => $input['company_phone'],
                    'tax_id' => $input['tax_id'],
                    'address' => [
                        'line1' => $input['company_line1'],
                        'city' => $input['company_city'],
                        'state' => $input['company_state'],
                        'postal_code' => $input['company_postal_code'],
                    ]
                ]
            ];

            $stripeClass->setInput($filters);
            try{
                $stripeClass->update();
                $status = 1;
                $message = "Company details are added.";
            }catch(Exception $e) {
                $message = $e->getMessage();
            }
        }
    }
}



$data = [
    'status' => $status,
    'message' => $message,
    'action' => $action == 'createAccount' ? 'create' : 0
];


header("Content-type: application/json");
echo json_encode($data);
die();   