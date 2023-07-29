<?php

showPhpErrors();
$root=__DIR__;


require_once($root .'/vendor/autoload.php');
require_once($root .'/vendor/stripe/stripe-php/init.php');
use Stripe\StripeClient;


global $wo; 

/**
     * PROPERTIES OF THE CLASS
     * 
     * @property StripeClient $stripe 
     * @property String $stripe_account_id 
     * @property Boolean $is_update 
     * @property Array $input 
     * @property Array $default 
     * @property String $error 
     * @property Int $file_id 
     * @property Int $user_id 
     * @property Int $person_id 
     * @property Boolean $file_new_updated 
     * @property Array $account 
     * 
*/ 


class StripeAccount {

    private $stripe_account_id = null;
    private $is_update = 0;
    private $file_new_updated = 0;
    private $input = [];
    private $default = [];
    private $stripe_secret = null;
    private $error = null;
    private $file_id = null;
    private $user_id = null;
    private $person_id = null;
    private $account = [];
    private $stripe = [];

    /**
     * @return null
     */
    public function __construct($user_id = 0, $default = []) {
        $this->user_id = $user_id;
        $this->input = $default;
        $this->setDefaultData();


        // $account = $this->stripe->accounts->retrieve($this->stripe_account_id);
        // $account = $this->stripe->accounts->retrievePerson($this->stripe_account_id, $this->person_id);
        // pre($account); exit; 
    }

    public function setInput($params = []) {
        $this->params = $params;
    } 

    /** 
     * validating the information
     * @return true|false
     */
    public function _validate() {
        $input = $this->input;
         
        if(!$this->isCreate()){
            
            $required = $this->getRequiredFields();
            foreach($required as $key => $value) {
                if(!isset($input[$key]) || empty($input[$key])) {
                    $errorMessage = $required[$key];
                    $this->error = "Please enter a valid {$errorMessage}";

                    return false; 
                }
            }
        }else{
            if(!isset($input['email']) || empty($input['email'])) {
                $this->error = "Please enter a valid email";
            }
        }

        if(empty($input['verification_file']) && !$this->file_id){
            $this->error = "Please select a valid verification file";
        }

        if(empty($input['user_id'])){
            $this->error = "You are not logged in, please login first to update the details.";
        }

        return true; 
    }

    /**
     * Setting the default data for the stripe user
     * @return null;
     */
    private function setDefaultData() {

        global $wo;
        $input = $this->input;

        $account = getTableData('wo_bank_details', [
            'user_id' => $this->user_id
        ], 1);

        /** 
         * Getting the Account Data In the Database
         */
        if($account) {

            $this->account = $account;
            $this->stripe_account_id = $account['stripe_account_id'];
            $this->is_update = 1;

            $this->file_id = $account['file_id'];
            $this->person_id = $account['person_id'];
        }
            

        if($this->file_id) {
            $this->file_new_updated = 0;
        }
        $this->stripe_secret = $wo['config']['stripe_secret'];
        $this->stripe = new StripeClient($this->stripe_secret);

    }

    /**
     * @return true|false
     */
    public function create(){

        $stripe = $this->stripe;
        
        if(isset($this->input['email']) && $this->input['email']) {

            try {

                $email = $this->input['email'];
                $params = [
                    'type' => 'custom',
                    'country' => 'US',
                    'email' => $email,
                    'tos_acceptance' => [
                        'date' => time(), 
                        'ip' => get_ip_address()
                    ],
                    'business_type' => $this->input['account_type'],
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ]
                ];
                

                $response = $stripe->accounts->create($params);
                

                $this->input['stripe_account_id'] = $response->id;
                return 
                    $this->updateStripeId($response->id) && 
                    $this->createAccountDetails($response->id);

            } catch(Exception $e) {
                $this->error = $e->getMessage();
            }
            
        }else{
            $this->error = "Please enter a valid email address";
        }

        return false; 
        
    }

    /**
     * @return true|false
     */
    public function update(){

        $stripe = $this->stripe;
        $stripe_id = $this->stripe_account_id;

        if(!$stripe) {
            $this->error = "Stripe is not developed. Please contact administrator.";
        }else{
            try{

                $params = $this->params;

                $stripe->accounts->update($stripe_id, $params);
                return $this->updateAccountDetails();

            }catch(Exception $e){
                $this->error = $e->getMessage(); 
            }
        }
        

        return false; 

    }

    /**
     * Create Company Representative
     * @return true|false
     */
    public function createPerson($stripe_account_id) {


        $params = $this->params;

        if(!$this->isIndividualAccount()) {

            try {

                $params['relationship'] = [
                    'owner' => true,
                    'executive' => true,
                    // "percent_ownership" => 0,
                    'representative' => true,
                    'title' => $this->input['title']
                ];


                // 03215900162 Engineer Mohammad Ali Mirza Number
                if(!empty($this->account['person_id'])){
                    $this->stripe->accounts->updatePerson($stripe_account_id, $this->person_id, $params);
                    return true;
                }else{
                    $response = $this->stripe->accounts->createPerson($stripe_account_id, $params);
                    return
                        $this->updateAccountDetails() && 
                        $this->updateColumnOnly('person_id', $response->id);
                }
                
            } catch(Exception $e) {
                $this->error = $e->getMessage();
            }
            
        }else{
            $this->error = "Please enter a valid representative information.";
        }

        return false; 
    }

    /**
     * @return string|false
     */
    public function file(){

        if(isset($this->params['verification_file']['tmp_name']) && !empty($this->params['verification_file']['tmp_name'])) {
            try {

                $stripe = $this->stripe;
                $file = $this->params['verification_file']['tmp_name'];
                $fp = fopen($file, 'r');
            
                $createdFile = $stripe->files->create([
                    'purpose' => 'identity_document',
                    'file' => $fp
                ]);

                $file_id = $createdFile->id;
                $this->params['file_id'] = $file_id;
                $this->file_new_updated = 1;

                return $this->updateColumnOnly('file_id', $file_id);

            } catch(Exception $e) {
                $this->error = $e->getMessage();
            }
            
        }else{
            $this->error = "Please select a valid verification file.";
        }

        return false; 
        
    } 

    /**
     * @return string|false
     */
    public function createAccountLogin($stripe_account_id){

        if($stripe_account_id) {
            try {

                $loginLink = $this->stripe->accounts->createLoginLink($stripe_account_id);

                $url = $loginLink->url;
                return $this->updateColumnOnly('login_url', $url);

            } catch(Exception $e) {
                $this->error = $e->getMessage();
            }
            
        }else{
            $this->error = "Stripe Account ID is not found.";
        }

        return false; 
        
    }

    function updateRepresentativeFile($file_id){
        $stripe = $this->stripe;
        $account = $this->account;

        $params = [
            'verification' => [
                'document' => [
                    'front' => $file_id
                ]
            ]
        ];

        try{
            $stripe->accounts->updatePerson($account['stripe_account_id'], $account['person_id'], $params);
            return true; 
        }catch(Exception $e) {
            $this->error = $e->getMessage();
        }

        return false; 
    }

    /**
     * Updating the stripe account details
     * @return true
     */
    private function updateStripeId($stripe_id) {
        global $wo, $sqlConnect;
        
        $user_id = $this->user_id;
        if($stripe_id && $user_id){

            $where = ['user_id' => $user_id];

            //Getting user based on the user_id
            $user = getTableData(T_USERS, $where, 1);

            // Checking if user found based on the user_id
            if($user) {
                
                // If found updating the stripe_accound_id
                return $sqlConnect->query(updateRow(T_USERS, [
                    'stripe_account_id' => $stripe_id
                ], $where));

            }else{
                $this->error = "User not found.";
            }
        }
    }

    /**
     * Insert data in the database
     * @return true|false
     */
    private function createAccountDetails($stripe_account_id) {
        global $sqlConnect;

        $input = $this->input;
        $params = [
            'stripe_account_id' => $stripe_account_id,
            'user_id' => $this->user_id,
            'email' => $input['email'],
            'account_type' => $input['account_type']
        ];
        
        $query = insertRow('wo_bank_details', $params);
        

        if($sqlConnect->query($query)){
            return true;
        }else{
            return mysqli_error($sqlConnect);
        }
    }

    /**
     * @return true|false
     */
    private function updateAccountDetails() {
        global $sqlConnect;

        $input = $this->input;

        $where = ['user_id' => $this->user_id];
        $query = updateRow('wo_bank_details', $input, $where);

        if($sqlConnect->query($query)) {
            return true;
        }else{
            $this->error = mysqli_error($sqlConnect);
            return false; 
        }
    }

    /** 
     * @return Array 
     * */
    private function getRequiredFields() {

        $required =  [
            'account_holder_name' => "Account Holder Name",
            'routing_number' => "Routing Number",
            'first_name' => "First Name",
            'last_name' => "Last Name",
            'phone_number' => "Phone Number",
            'individual_email' => "Individual Email",
            'id_number' => "ID Number",
            'birth_day' => "Birth Day",
            'birth_month' => "Birth Month",
            'birth_year' => "Birth Year",
            'line1' => "Line Address",
            'postal_code' => "Postal Code",
            'city' => "City",
            'state' => "State",
            'mcc_number' => "MCC",
        ];

        if(!$this->isIndividualAccount()){
            $required += [
                "company_name" => "Company Name",
                "tax_id" => "Tax ID",
            ];
        }

        return $required;
    }

    /**
     * @return Array
     */
    private function getPreparedParams() {

        $input = $this->input;
        $routingNumber = $input['routing_number'];
        $accountNumber = $input['account_number'];

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

        if(!$this->isIndividualAccount()) {
            $mainPersonal = [
                'representative' => $personal,
                'company' => [
                    'name' => $input['company_name'],
                    'tax_id' => $input['tax_id']
                ]
            ];
        }else{
            $mainPersonal ['individual'] =  $personal;
        }

        $params = [
            
            'external_account' => [
                'object' => 'bank_account',
                'country' => 'US',
                'currency' => 'USD',
                'account_holder_name' => $input['account_holder_name'],
                'routing_number' => "{$routingNumber}",
                'account_number' => "{$accountNumber}",
            ],
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'business_type' => $input['account_type'],
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
            ],
            'tos_acceptance' => [
                'date' => time(), 
                'ip' => get_ip_address()
            ],
        ];

        $params += $mainPersonal;
        

        if($this->file_new_updated && $this->isIndividualAccount()) {
            $params['individual']['verification']  = [
                'document' => [
                    'front' => $this->file_id
                ]
            ];
        }

        return $params;
    }

    /**
     * @return true|false
     */
    public function isCreate() {
        
        if($this->is_update) {
            return false;
        }else{
            return true; 
        }
    }

    /**
     * @return true|false
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Check if the object is Individual or Company
     * @return true|false
     */
    public function isIndividualAccount() {
        return $this->account['account_type'] == 'individual';
    }

    /**
     * Update only one column witha specific valud and column key
     * @return true|false
     */
    private function updateColumnOnly($column, $value) {
        global $sqlConnect;

        $where = ['user_id' => $this->user_id];
        $query = updateRow('wo_bank_details', [$column => $value], $where);

        return $sqlConnect->query($query);
    }

    
    public function retreive($stripe_account_id){
        return $this->stripe->accounts->retrieve($stripe_account_id);
    }

}