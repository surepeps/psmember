<?php
include("assets/init.php");

$sqlConnect = $wo['sqlConnect'];

$step = filter('step');

if($step && is_numeric($step) && $step > 0){

    $stepRunner = new StepRunner();
    $stepMethod = "step{$step}";
    if(method_exists($stepRunner, $stepMethod)){
        $stepRunner->{$stepMethod}();
    }else{
        echo("Please code first to run that step"); exit; 
    }

}else{
    echo "Not a valid step number. Please enter a numeric value for step";
}

/**
 * Step runner is for database to create table or insert data in it.
 */
class StepRunner {

    protected $sqlConnect;

    public function __construct() {
        global $sqlConnect;
        $this->sqlConnect = $sqlConnect;
    }

    public function runQuery($query){
        
        $result = mysqli_query($this->sqlConnect, $query);
        if($result){
            pre("Operation Performed");
        }else{
            echo mysqli_error($this->sqlConnect); exit; 
        }
    }

    public function step1(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_lead_contracts` (
                `contract_id` int(11) DEFAULT NULL auto_increment,   
                `contact_id` int(11) DEFAULT NULL default '0', 
                `contact_type` VARCHAR(10) DEFAULT NULL DEFAULT 'contact',
                `pipeline_id` int(11) DEFAULT NULL default '0', 
                `stage_id` int(11) DEFAULT NULL default '0', 
                `step_id` int(11) DEFAULT NULL default '0', 
                `created_at` int(11) DEFAULT NULL default '9',
                PRIMARY KEY  (`contract_id`)
            );
        ";

        $this->runQuery($query);
        
    }

    public function step2(){
        $query = "DROP TABLE IF EXISTS `wo_pipeline_offer` ";
        $this->runQuery($query);

        $query = "
            CREATE TABLE IF NOT EXISTS `wo_pipeline_offer` (
                `offer_id` int(11) DEFAULT NULL auto_increment,   
                `contact_id` int(11) DEFAULT NULL default '0', 
                `contact_type` VARCHAR(10) DEFAULT NULL DEFAULT 'contact',
                `date` int(11) DEFAULT NULL default '0',    
                `price` int(11) DEFAULT NULL default '0',  
                `created_by` int(11) DEFAULT NULL default '0',       
                `created_at` int(11) DEFAULT NULL default '9',
                PRIMARY KEY  (`offer_id`)
            );
        ";
        $this->runQuery($query);
        
    }

    public function step3(){
        $query = "DROP TABLE IF EXISTS `wo_pipeline_appointment` ";
        $this->runQuery($query);

        $query = "
            CREATE TABLE IF NOT EXISTS `wo_pipeline_appointment` (
                `appt_id` int(11) DEFAULT NULL auto_increment,   
                `contact_id` int(11) DEFAULT NULL default '0',  
                `contact_type` VARCHAR(10) DEFAULT NULL DEFAULT 'contact',
                `date` int(11) DEFAULT NULL default '0',  
                `created_by` int(11) DEFAULT NULL default '0',       
                `created_at` int(11) DEFAULT NULL default '9',
                PRIMARY KEY  (`appt_id`)
            );
        ";

        $this->runQuery($query);
        
    }

    public function step4(){

        $query = " DROP TABLE IF EXISTS contact_to_pipeline ";
        $this->runQuery($query);

        $query = "
            CREATE TABLE IF NOT EXISTS `contact_to_pipeline` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `contact_id` int(11) DEFAULT NULL,
                `contact_type` varchar(30) DEFAULT NULL DEFAULT 'contact',
                `added_by` int(11) DEFAULT NULL,
                `pipe_id` int(11) DEFAULT NULL,
                `stage_id` int(11) DEFAULT NULL,
                `step_id` int(11) DEFAULT NULL,
                `action_id` int(11) DEFAULT NULL,
                `duration_b_stage` varchar(100) DEFAULT NULL,
                `duration_b_step` varchar(100) DEFAULT NULL,
                `duration_b_action` varchar(100) DEFAULT NULL,
                `duration` varchar(100) DEFAULT NULL,
                `date_created` timestamp DEFAULT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                `date_updated` timestamp DEFAULT NULL DEFAULT current_timestamp()
            );
        ";
        $this->runQuery($query);
    }

    public function step5(){
        $query = "
            CREATE TABLE IF NOT EXISTS `contact_notes` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `contact_id` int(11) DEFAULT NULL,
                `contact_type` varchar(30) DEFAULT NULL DEFAULT 'contact',
                `added_by` int(11) DEFAULT NULL,
                `notes` TEXT NULL DEFAULT NULL,
                `name` TEXT NULL DEFAULT NULL,
                `created_at` timestamp DEFAULT NULL DEFAULT current_timestamp()
            );
        ";
        $this->runQuery($query);
    }

    public function step6(){
        $query = "
            ALTER TABLE `link_tag` ADD `contact_type` VARCHAR(15) DEFAULT 'contact' AFTER `added_by`; 
        ";
        $this->runQuery($query);
    }

    public function step7(){
        $query = " CREATE TABLE IF NOT EXISTS deal_pipelines AS SELECT * FROM campaign_pipeline_t"; $this->runQuery($query);
        $query = " CREATE TABLE IF NOT EXISTS deal_pipeline_stages AS SELECT * FROM pipeline_campaign_stages"; $this->runQuery($query);
        $query = " CREATE TABLE IF NOT EXISTS deal_pipeline_steps AS SELECT * FROM pipeline_campaign_steps"; $this->runQuery($query);
    }

    public function step9(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_properties` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY, 
                `owner_name` VARCHAR(100) DEFAULT NULL,
                `email` VARCHAR(50) DEFAULT NULL,   
                `phone` int(50) DEFAULT NULL,   
                `address` TEXT NULL DEFAULT NULL,
                `city` VARCHAR(50) DEFAULT NULL, 
                `state` VARCHAR(50) DEFAULT NULL, 
                `zipcode` int(5) DEFAULT NULL,    
                `bedrooms` TEXT DEFAULT NULL,   
                `bathrooms` TEXT DEFAULT NULL,  
                `sqft` int(11) DEFAULT NULL, 
                `garage` int(11) DEFAULT NULL,   
                `year_built` int(11) DEFAULT NULL,  
                `property_type` TEXT NULL DEFAULT NULL, 
                `occupancy_type` TEXT NULL DEFAULT NULL, 
                `exit_strategy` TEXT NULL DEFAULT NULL, 
                `estimated_arv` int(11) DEFAULT NULL,   
                `estimated_repairs` TEXT NULL DEFAULT NULL, 
                `offer_amount` int(20) DEFAULT NULL,  
                `compos_3` TEXT DEFAULT NULL,  
                `lockbox_code` VARCHAR(50) DEFAULT NULL,   
                `contact_id` int(11) DEFAULT NULL default '0',   
                `buyer_id` int(11) DEFAULT NULL default '0', 
                `deal_notes` TEXT DEFAULT NULL, 
                `tex_mailing_address` TEXT DEFAULT NULL, 
                `created_by` int(11) DEFAULT NULL default '0',       
                `created_at` int(11) DEFAULT NULL default '9'
            );
        ";
        $this->runQuery($query); 
    }

    public function step11(){
        $query = "
            CREATE TABLE IF NOT EXISTS `contact` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `csv_id` int(11)  DEFAULT 0,
                `firstname` varchar(100) DEFAULT NULL,
                `lastname` varchar(100) DEFAULT NULL,
                `mobile` varchar(12) DEFAULT NULL,
                `email` varchar(100) DEFAULT NULL,
                `prstreetadd` text DEFAULT NULL,
                `prcity` varchar(100) DEFAULT NULL,
                `prstate` varchar(50) DEFAULT NULL,
                `przip` varchar(20) DEFAULT NULL,
                `taxmailstreetadd` varchar(200) DEFAULT NULL,
                `taxmailcity` varchar(100) DEFAULT NULL,
                `taxmailstate` varchar(50) DEFAULT NULL,
                `taxmailzip` varchar(20) DEFAULT NULL,
                `otherstreetadd` text DEFAULT NULL,
                `othercity` varchar(40) DEFAULT NULL,
                `otherstate` varchar(30) DEFAULT NULL,
                `otherzip` varchar(20) DEFAULT NULL,
                `min_price` varchar(255)  DEFAULT NULL,
                `max_price` varchar(255)  DEFAULT NULL,
                `is_multiple_match` int(11)  DEFAULT NULL,
                `beds` varchar(255)  DEFAULT NULL,
                `bath` varchar(255)  DEFAULT NULL,
                `zip_code` text  DEFAULT NULL,
                `property_type` text  DEFAULT NULL,
                `createddate` datetime  DEFAULT NULL,
                `updateddate` datetime  DEFAULT NULL,
                `contactinsertedby` varchar(30) DEFAULT NULL,
                `tag` int(11)  DEFAULT NULL,
                `Status` varchar(10) DEFAULT NULL,
                `first_time_home_buyer` varchar(255)  DEFAULT NULL,
                `send_all_properties` varchar(255)  DEFAULT NULL,
                `city` text  DEFAULT NULL,
                `how_will_you_purchasing_home` varchar(255)  DEFAULT NULL,
                `fund_available` varchar(255)  DEFAULT NULL,
                `can_you_provide_proof_of_fund` varchar(255)  DEFAULT NULL,
                `proof_image` text  DEFAULT NULL,
                `buying_strategy` varchar(255)  DEFAULT NULL,
                `time` varchar(255)  DEFAULT NULL,
                `send_to_buyer` int(11)  DEFAULT NULL,
                `contact_id` int(11)  DEFAULT NULL,
                `buyer_id` int(11)  DEFAULT NULL,
                `deal_notes` text  DEFAULT NULL,
                `occupancy_type` varchar(255)  DEFAULT NULL,
                `estimated_arv` varchar(100)  DEFAULT NULL,
                `estimated_repairs` varchar(100)  DEFAULT NULL,
                `offer_amount` varchar(100)  DEFAULT NULL,
                `compos_3` text  DEFAULT NULL,
                `lockbox_code` varchar(255)  DEFAULT NULL,
                `year_built` int(11)  DEFAULT NULL,
                `garage` int(11)  DEFAULT NULL,
                `sqft` int(11)  DEFAULT NULL,
                `motivation` varchar(100)  DEFAULT NULL,
                `type` int(11)  DEFAULT NULL
            ) ;
          
        ";
        $this->runQuery($query);
    }

	public function step12(){
        $query = "
		CREATE TABLE IF NOT EXISTS `pipeline_stage_goal` (
		  `id` int(11) DEFAULT NULL AUTO_INCREMENT PRIMARY KEY,
		  `stage_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `stage_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `status` int(11) DEFAULT NULL,
		  `date_updated` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
		) ;          
        ";
        $this->runQuery($query);
    }

	public function step13(){
        $query = "
		CREATE TABLE IF NOT EXISTS `pipeline_cron_logs` (
		  `id` int(11) DEFAULT NULL AUTO_INCREMENT PRIMARY KEY,
		  `pipe_id` int(11) DEFAULT NULL,
		  `stage_id` int(11) DEFAULT NULL,
		  `step_id` int(11) DEFAULT NULL,
		  `action_id` int(11) DEFAULT NULL,
		  `contact_in_ques` int(11) DEFAULT NULL,
		  `start_time` datetime DEFAULT NULL,
		  `end_time` datetime DEFAULT NULL,
		  `is_active` int(11) DEFAULT NULL,
		  `created_at` datetime DEFAULT NULL
		) ;          
        ";
        $this->runQuery($query);
    }

    public function step14(){
        $query = " 
            ALTER TABLE `contactdynamic_csv` ADD `content_type` VARCHAR(20) DEFAULT 'contact'; 
        ";
        $this->runQuery($query);
    } 

    public function step15(){
        $query = "
            CREATE TABLE IF NOT EXISTS `closed_deal` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY, 
                `contact_id` INT(11) DEFAULT '0',
                `contact_type` VARCHAR(100) DEFAULT NULL,
                `profit` INT(11) DEFAULT '0',
                `date` INT(11) DEFAULT '0',
                `created_by` int(11) DEFAULT '0',       
                `created_at` int(11) DEFAULT '0'
            );
        ";
        $this->runQuery($query); 
    }

    public function step16(){
        $query = "
            ALTER TABLE `wo_properties` 
            ADD `motivation` VARCHAR(15) NULL,
            ADD `tex_mailing_city` VARCHAR(64) NULL,
            ADD `tex_mailing_state` VARCHAR(64) NULL,
            ADD `tex_mailing_zipcode` VARCHAR(32) NULL;
        ";
        $this->runQuery($query);
    }

    public function step17() {
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_contract_content` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY, 
                `as_title` INT(11) DEFAULT '0',
                `contract_id` INT(11) DEFAULT '0',
                `title` VARCHAR(100) NULL,
                `purchase_price` INT(11) DEFAULT '0',
                `rehab_amount` INT(11) DEFAULT '0',
                `estimated_amount` INT(11) DEFAULT '0',
                `date` INT(11) DEFAULT '0',
                `buyer_id` INT(11) DEFAULT '0',
                `contact_id` INT(11) DEFAULT '0',
                `street` VARCHAR(100) NULL,
                `city` VARCHAR(100) NULL,
                `state` VARCHAR(100) NULL,
                `zip` INT(11) DEFAULT '0',
                `has` VARCHAR(500) NULL,
                `square_feet` INT(11) DEFAULT '0',
                `year_built` INT DEFAULT '0',
                `stories` INT DEFAULT '0',
                `offer_amount` INT(11) DEFAULT '0',
                `bedrooms` INT(11) DEFAULT '0',
                `bathrooms` INT(11) DEFAULT '0',
                `property_type` VARCHAR(100) DEFAULT NULL,
                `actual_profit` INT(11) DEFAULT '0',
                `lockbox` INT(11) DEFAULT '0',
                `created_by` int(11) DEFAULT '0',       
                `created_at` int(11) DEFAULT '0'
            );
        ";
        $this->runQuery($query);
    }

    /** Adding a new column that makes difference between the deal and lead pipeline */
    public function step18() {
        $query = "
            ALTER TABLE `campaign_pipeline_T` ADD `type` VARCHAR(20) DEFAULT 'lead';     
        ";
        $this->runQuery($query);
    }


    /** Clonnig all the rows and making them deal instead of lead pipeline */
    public function step19() {
        $query = "
            INSERT INTO `campaign_pipeline_T` 
                (`pipe_code`, `user_id`, `pipeName`, `pipeDesc`, `pipeLCN`, `pipeTimeZone`, `pipeType`, `pipeTarget`, `status`, `date`, `type`) 
            SELECT `pipe_code`, `user_id`, `pipeName`, `pipeDesc`, `pipeLCN`, `pipeTimeZone`, `pipeType`, `pipeTarget`, `status`, `date`, 'deal' as `type`
                FROM `campaign_pipeline_T` WHERE type = 'lead'
        ";
        $this->runQuery($query);
    }

    public function step20(){
        $query = "
            CREATE TABLE IF NOT EXISTS `equity_deal_list` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY,
                `list_id` int(11) DEFAULT '0',
                `mls` VARCHAR(100) DEFAULT NULL,
                `address` VARCHAR(100) DEFAULT NULL,
                `city` VARCHAR(100) DEFAULT NULL,
                `zipcode` VARCHAR(100) DEFAULT NULL,
                `status` VARCHAR(100) DEFAULT NULL,
                `close_price` VARCHAR(100) DEFAULT NULL,
                `list_price` VARCHAR(100) DEFAULT NULL,
                `sp_sqft` VARCHAR(100) DEFAULT NULL,
                `lp_sqft` VARCHAR(100) DEFAULT NULL,
                `beds` VARCHAR(100) DEFAULT NULL,
                `half_baths` VARCHAR(100) DEFAULT NULL,
                `full_baths` VARCHAR(100) DEFAULT NULL,
                `heated_area` VARCHAR(100) DEFAULT NULL,
                `year_built` VARCHAR(100) DEFAULT NULL,
                `school_district` VARCHAR(100) DEFAULT NULL,
                `subdivision` VARCHAR(100) DEFAULT NULL,
                `garages` VARCHAR(100) DEFAULT NULL,
                `lot_size` FLOAT DEFAULT '0',
                `swimming_pool` VARCHAR(100) DEFAULT NULL,
                `property_type` VARCHAR(100) DEFAULT NULL,
                `c_dome` VARCHAR(100) DEFAULT NULL,
                `lat` VARCHAR(20) DEFAULT 0,
                `lng` VARCHAR(20) DEFAULT 0,
                `created_by` int(11) DEFAULT '0',       
                `created_at` int(11) DEFAULT '0'
            );
        ";
        $this->runQuery($query); 
    }

    public function step21(){
        $query = "
            CREATE TABLE IF NOT EXISTS `equity_deal` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY,  
                `list_name` VARCHAR(100) DEFAULT NULL,
                `active` int(11) DEFAULT '0',    
                `sold` int(11) DEFAULT '0',    
                `created_by` int(11) DEFAULT '0',       
                `created_at` int(11) DEFAULT '0'
            );
        ";
        $this->runQuery($query); 
    }

    public function step22(){
        $query = " 
            ALTER TABLE `equity_deal_list` 
                ADD COLUMN `lat` VARCHAR(20) DEFAULT '0',
                ADD COLUMN `lng` VARCHAR(20) DEFAULT '0'
            ; 
        ";
        
        $this->runQuery($query);
    } 

    /** Adding a new column in the equity deal for properties count */
    public function step23() {
        $query = "
            ALTER TABLE `equity_deal` ADD `properties` INT DEFAULT 0;     
        ";
        $this->runQuery($query);
    }

    public function step24(){
        $query = "
            ALTER TABLE `equity_deal_list` 
            ADD `first_name` VARCHAR(50) NULL,
            ADD `last_name` VARCHAR(50) NULL,
            ADD `state` VARCHAR(50) NULL,
            ADD `mailing_address` VARCHAR(100) NULL,
            ADD `mailing_city` VARCHAR(50) NULL,
            ADD `mailing_state` VARCHAR(50) NULL,
            ADD `mailing_zipcode` INT DEFAULT 0;
        ";

        $this->runQuery($query);
    }

    public function step25(){
        $query = "
            CREATE TABLE IF NOT EXISTS `buyer_finder_list` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `list_name` varchar(100) DEFAULT NULL,
                `created_by` int(11)  DEFAULT 0,
                `created_at` int(11)  DEFAULT 0
            );
        ";
        $this->runQuery($query);
    }

    public function step26(){
        $query = "
            CREATE TABLE IF NOT EXISTS `buyer_finder` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `list_id` int(11)  DEFAULT 0,
                `buyer_full_name` varchar(100) DEFAULT NULL,
                `buyer_company` varchar(1000) DEFAULT NULL,
                `phone_number` varchar(1000) DEFAULT NULL,
                `email` varchar(100) DEFAULT NULL,
                `property_type` VARCHAR(100) DEFAULT NULL,
                `area_of_interest` VARCHAR(500) DEFAULT NULL,
                `additional_contact` VARCHAR(500) DEFAULT NULL,
                `mailing_address` VARCHAR(100) DEFAULT NULL,
                `created_by` int(11)  DEFAULT 0,
                `created_at` int(11)  DEFAULT 0
            ) ;
          
        ";
        $this->runQuery($query);
    }

    public function step28(){
        $query = "
            ALTER TABLE `Wo_Blog` 
            ADD `video_link` VARCHAR(100) DEFAULT NULL
        ";

        $this->runQuery($query);
    }

    public function step29(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_leads` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `first_name` varchar(100) DEFAULT NULL, 
                `last_name` varchar(100) DEFAULT NULL, 
                `phone` varchar(100) DEFAULT NULL, 
                `email` varchar(100) DEFAULT NULL, 
                `address` varchar(100) DEFAULT NULL, 
                `city` varchar(100) DEFAULT NULL, 
                `state` varchar(100) DEFAULT NULL, 
                `garage` varchar(100) DEFAULT NULL, 
                `bedrooms` varchar(100) DEFAULT NULL, 
                `roof` varchar(100) DEFAULT NULL, 
                `ac` varchar(100) DEFAULT NULL, 
                `owned_years` varchar(100) DEFAULT NULL, 
                `proprety_conditions` varchar(100) DEFAULT NULL, 
                `repaired` text DEFAULT NULL, 
                `occupied` text DEFAULT NULL, 
                `realtor` varchar(100) DEFAULT NULL, 
                `sell_fast` int(11)  DEFAULT 0,
                `goal` varchar(100) DEFAULT NULL, 
                `call_time` varchar(100) DEFAULT NULL,
                `seller_motivation` varchar(500) DEFAULT NULL,
                `bought` INT(1) DEFAULT 1,
                `price` INT(11) DEFAULT 0,
                `source` varchar(100) DEFAULT NULL,
                `assign_date` INT(11) DEFAULT 0,
                `can_buy` INT(11) DEFAULT 0,
                `created_by` int(11)  DEFAULT 0,
                `created_at` int(11)  DEFAULT 0
            ) ;
          
        ";
        $this->runQuery($query);
    }

    public function step30(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_assgin_lead` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `lead_id` int(11) DEFAULT 0,
                `user_id` int(11) DEFAULT 0,
                `price` INT(11) DEFAULT 0,
                `created_at` INT(11) DEFAULT 0
            );
        ";
        $this->runQuery($query);
    }

    /**
     * Saving the states in the state table
     */
    public function step31() {
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_states` (
                `abb` varchar(5) PRIMARY KEY,
                `full` varchar(50) DEFAULT NULL
            );
        ";
        $this->runQuery($query);
    }

    public function step32() {

        $file = "upload/states.csv";
        
        if(file_exists($file)){
            $query = "INSERT INTO wo_states (abb,full) VALUES ";
            if (($handle = fopen($file, "r")) !== FALSE) {
                $i=1; 
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    
                if($i>1){ 
                    $short = $data[1];
                    $full = $data[0];

                    if(empty($short)) continue;
                        $query .= "('{$short}','{$full}'),";
                }
                $i++;
                }  
                fclose($handle);
            } 

            $query = rtrim($query, ',');
            $this->runQuery($query);
        }
        
    }

    public function step34(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_user_tiers` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` int(11) DEFAULT 0,
                `parent_id` int(11) DEFAULT 0,
                `package` VARCHAR(20) DEFAULT NULLs,
                `amount` int(11) DEFAULT 0,
                `created_at` INT(11) DEFAULT 0
            );
        ";

        $this->runQuery($query);
    }

    public function step35(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_refferal_payment_log` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` int(11) DEFAULT 0,
                `payer_id` int(11) DEFAULT 0,
                `amount` FLOAT DEFAULT 0,
                `currency` VARCHAR(10) DEFAULT NULL,
                `percentage` int(11) DEFAULT 0,
                `package` VARCHAR(20) DEFAULT NULL,
                `is_paid` INT(11) DEFAULT 0,
                `created_at` INT(11) DEFAULT 0,
                `log_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step36(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_refferal_sales_log` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` int(11) DEFAULT 0,
                `type` VARCHAR(10) NULL DEFAULT 'user',
                `amount` FLOAT DEFAULT 0,
                `currency` VARCHAR(10) DEFAULT NULL,
                `package` VARCHAR(20) DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step37(){
        $query = "
            CREATE TABLE IF NOT EXISTS `listing_contact` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `listing_id` int(11) DEFAULT 0,
                `contact_id` int(11) DEFAULT 0,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step38(){
        $query = "
            CREATE TABLE IF NOT EXISTS `listing_tag_category` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(20) DEFAULT NULL,
                `created_by` int(11) DEFAULT 0,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step39(){
        $query = "
            CREATE TABLE IF NOT EXISTS `listing_tag` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `tag` VARCHAR(20) DEFAULT NULL,
                `category` int(11) DEFAULT 0,
                `created_by` int(11) DEFAULT 0,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step40(){
        $query = "
            CREATE TABLE IF NOT EXISTS `close_listing` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `listing_id` int(11) DEFAULT 0,
                `unique_id` int(11) DEFAULT 0,
                `actual_profit` int(11) DEFAULT 0,
                `close_date` DATETIME DEFAULT NULL,
                `closed_by` int(11) DEFAULT 0,
                `closed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step41(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_property_referrel_logs` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` int(11) DEFAULT 0,
                `payer_id` int(11) DEFAULT 0,
                `amount` FLOAT DEFAULT 0,
                `currency` VARCHAR(10) DEFAULT NULL,
                `percentage` FLOAT(11) DEFAULT 0,
                `unique_code` int(11) DEFAULT 0,
                `expected` int(11) DEFAULT 0,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step42(){
        $query = "
            CREATE TABLE IF NOT EXISTS `contract_listing` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `listing_id` int(11) DEFAULT 0,
                `unique_id` int(11) DEFAULT 0,
                `expected_profit` int(11) DEFAULT 0,
                `expected_close_date` DATETIME DEFAULT NULL,
                `contract_by` int(11) DEFAULT 0,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }

    public function step43(){
        $query = "
            CREATE TABLE IF NOT EXISTS `cron_jobs` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) DEFAULT NULL,
                `file_name` VARCHAR(50) DEFAULT NULL,
                `run_type` VARCHAR(20) DEFAULT NULL,
                `last_run_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `next_run_time` DATETIME DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }
    
    public function step44(){
        $query = "
            CREATE TABLE IF NOT EXISTS `link_listing_tag` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `listing_id` VARCHAR(100) DEFAULT NULL,
                `tag_id` VARCHAR(50) DEFAULT NULL,
                `added_by` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }
    
    public function step45(){
        $query = "
            ALTER TABLE `Wo_Users` ADD `referral_balance` INT(11) DEFAULT 0; 
        ";

        $this->runQuery($query);
    }

    public function step46(){
        $query = "
            CREATE TABLE IF NOT EXISTS `reverse_search_list` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `list_name` varchar(100) DEFAULT NULL,
                `records` int(11)  DEFAULT 0,
                `status` int(11)  DEFAULT 0,
                `created_by` int(11)  DEFAULT 0,
                `created_at` int(11)  DEFAULT 0
            );
        ";
        $this->runQuery($query);
    }

    public function step47(){
        $query = "
            CREATE TABLE IF NOT EXISTS `reverse_search_list_items` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `list_id` int(11)  DEFAULT 0,
                `first_name` varchar(100) DEFAULT NULL,
                `last_name` varchar(100) DEFAULT NULL,
                `email` varchar(500) DEFAULT NULL,
                `phone` varchar(500) DEFAULT NULL,
                `address` VARCHAR(100) DEFAULT NULL,
                `city` varchar(100) DEFAULT NULL,
                `state` varchar(100) DEFAULT NULL,
                `zip_code` varchar(5)  DEFAULT NULL,
                `created_by` int(11)  DEFAULT 0,
                `created_at` int(11)  DEFAULT 0
            ) ;
          
        ";
        $this->runQuery($query);
    }
    
    public function step48(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_bank_details` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(40)  DEFAULT NULL,
                `account_type` VARCHAR(20)  DEFAULT NULL,
                `user_id` int(11)  DEFAULT 0,
                `stripe_account_id` VARCHAR(30)  DEFAULT NULL,
                `file_id` VARCHAR(30)  DEFAULT NULL,
                `person_id` VARCHAR(30)  DEFAULT NULL,
                `account_holder_name` varchar(30) DEFAULT NULL,
                `routing_number` varchar(20) DEFAULT NULL,
                `account_number` varchar(20) DEFAULT NULL,
                `title` varchar(30) DEFAULT NULL,
                `first_name` varchar(20) DEFAULT NULL,
                `last_name` varchar(20) DEFAULT NULL,
                `phone_number` varchar(20) DEFAULT NULL,
                `individual_email` varchar(50) DEFAULT NULL,
                `id_number` varchar(30) DEFAULT NULL, 
                `birth_day` INT(2) DEFAULT 0,
                `birth_month` INT(2) DEFAULT 0,
                `birth_year` INT(4) DEFAULT 0,
                `line1` varchar(100) DEFAULT NULL,
                `postal_code` INT(5) DEFAULT 0,
                `city` varchar(20) DEFAULT NULL,
                `state` varchar(20) DEFAULT NULL,
                `business_name` varchar(60) DEFAULT NULL,
                `business_address` varchar(1000) DEFAULT NULL,
                `support_phone` varchar(20) DEFAULT NULL,
                `support_email` varchar(30) DEFAULT NULL,
                `support_url` varchar(100) DEFAULT NULL,
                `product_description` varchar(3000) DEFAULT NULL,
                `business_line1` varchar(200) DEFAULT NULL,
                `business_city` varchar(30) DEFAULT NULL,
                `business_state` varchar(20) DEFAULT NULL,
                `business_postal_code` INT(5) DEFAULT NULL,
                `company_name` varchar(30) DEFAULT NULL,
                `company_phone` varchar(15) DEFAULT NULL,
                `tax_id` varchar(20) DEFAULT NULL,
                `company_line1` varchar(100) DEFAULT NULL,
                `company_postal_code` INT(5) DEFAULT NULL,
                `company_city` varchar(20) DEFAULT NULL,
                `company_state` varchar(20) DEFAULT NULL,
                `login_url` varchar(100) DEFAULT NULL,
                `mcc_number` INT(6) DEFAULT 0,
                `is_file_uploaded` INT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ;
        ";

        $this->runQuery($query);
    }

    /** Table for saving the withdraw amount */
    public function step49(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_user_withdraw` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` float(11) DEFAULT 0,
                `amount` varchar(100) DEFAULT NULL,
                `via` varchar(100) DEFAULT NULL,
                `status` varchar(100) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    /** Tables for actions and plans */
    public function step50(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_action_plans` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `plan_title` varchar(100) DEFAULT NULL,
                `plan_description` varchar(1000) DEFAULT NULL,
                `parent_plan` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step51(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_plan_actions` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `plan_id` INT(11) DEFAULT 0,
                `action_id` INT(11) DEFAULT 0,
                `action_title` varchar(100) DEFAULT NULL,
                `action_description` varchar(5000) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step33(){
        $query = "
            ALTER TABLE `Wo_Users` 
                ADD `clicks` int(11) DEFAULT 0,
                ADD `unique_clicks` INT(11) DEFAULT 0;
        ";

        $this->runQuery($query);
    }

    public function step52(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_unique_clicks` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `ip_address` VARCHAR(20) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    /** Table for Buyer Dashboard */
    

    /** Tables for actions and plans */
    public function step53(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_buyer_pincode` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `contact_id` INT(11) DEFAULT 0,
                `pin_code` INT(5) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }
    

    /** Tables for Offers and Schedule Visit */
    public function step54(){
        $query = "
            ALTER TABLE `Wo_offers` 
                ADD `customer_id` INT(15) DEFAULT 0; 
        ";
        $this->runQuery($query);
    }


    public function step55(){
        $query = "
            ALTER TABLE `Wo_Schedule_Visits` 
                ADD `customer_id` INT(15) DEFAULT 0; 
        ";
        $this->runQuery($query);
    }

    /** Table for Buyer Notification */
    public function step56(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_buyer_notification` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `contact_id` INT(11) DEFAULT 0,
                `notification` VARCHAR(100) DEFAULT NULL,
                `value` INT(5) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }
    
    /* Payment Approval Table and Alter Tables */
    public function step57(){
        $query = "
            ALTER TABLE `Wo_Users` 
                ADD `stripe_account_id` VARCHAR(30) DEFAULT NULL,
                ADD `referral_approval` INT(1) DEFAULT 0; 
        ";

        $this->runQuery($query);
    }
    
    public function step58(){
        $query = "
            ALTER TABLE `Wo_Users` 
                ADD `user_current_balance` VARCHAR(30) DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }

    public function step59(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_payment_approvals` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `amount` INT(5) DEFAULT 0,
                `month` VARCHAR(10) DEFAULT 0,
                `is_closed` VARCHAR(10) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step60(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_transections_month` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `month` VARCHAR(10) DEFAULT 0
            );
        ";
        $this->runQuery($query);
    }

    public function step61(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_buyer_finder_access` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `package_id` INT(11) DEFAULT 0,
                `num_of_cities` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step62(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_buyer_access_cities` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `package_id` INT(11) DEFAULT 0,
                `cities` TEXT DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step63(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_user_tasks` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `title` VARCHAR(100) DEFAULT NULL,
                `description` VARCHAR(1000) DEFAULT NULL,
                `category` VARCHAR(1000) DEFAULT NULL,
                `start_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `end_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `alert_before` INT(2) DEFAULT 0,
                `alert_on` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `status` INT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step64(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_organization_package` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(100) DEFAULT NULL,
                `num_of_members` INT(5) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step65(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_package_users` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `package_id` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step66(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_attach_users` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `parent_id` INT(11) DEFAULT 0,
                `user_id` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step67(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_org_permissions` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `feature_id` INT(11) DEFAULT 0,
                `permission_value` VARCHAR(50) DEFAULT 'deny',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step68(){
        $query = "
            ALTER TABLE `Wo_Users` 
                ADD `welcome_pipeline` INT(11) DEFAULT 0; 
        ";

        $this->runQuery($query);
    }

    public function step69(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_scheduled_emails` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `prop_id` INT(11) DEFAULT 0,
                `subject` VARCHAR(100) DEFAULT 'NULL',
                `email_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `from_email` VARCHAR(50) DEFAULT 'NULL',
                `tags` VARCHAR(1000) DEFAULT 'NULL',
                `email_text` VARCHAR(10000) DEFAULT 'NULL',
                `type` INT(1) DEFAULT 0,
                `status` INT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step70(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_scheduled_sms` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `prop_id` INT(11) DEFAULT 0,
                `sms_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `from_phone` VARCHAR(50) DEFAULT 'NULL',
                `tags` VARCHAR(1000) DEFAULT 'NULL',
                `sms_text` VARCHAR(1000) DEFAULT 'NULL',
                `type` INT(1) DEFAULT 0,
                `status` INT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step71(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_contact_files` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `contact_id` INT(11) DEFAULT 0,
                `filename` VARCHAR(100) DEFAULT NULL,
                `type` VARCHAR(20) DEFAULT NULL,
                `is_proof` INT(1) DEFAULT 0,
                `can_delete` INT(1) DEFAULT 1,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step72(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_user_partner` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `name` VARCHAR(100) DEFAULT NULL,
                `email` VARCHAR(50) DEFAULT NULL,
                `password` VARCHAR(100) DEFAULT NULL,
                `phone` VARCHAR(50) DEFAULT NULL,
                `company` VARCHAR(50) DEFAULT NULL,
                `property_per_month` INT(11) DEFAULT 0,
                `social_urls` VARCHAR(500) DEFAULT NULL,
                `notes` VARCHAR(5000) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step73(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_partner_categories` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `category` VARCHAR(100) DEFAULT NULL,
                `user_id` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step74(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_partner_tags` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `tag` VARCHAR(100) DEFAULT NULL,
                `category_id` INT(11) DEFAULT 0,
                `user_id` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step75(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_partner_link_tags` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `partner_id` INT(11) DEFAULT 0,
                `tag_id` INT(11) DEFAULT 0,
                `user_id` INT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step76(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_partner_properties` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `partner_id` INT(11) DEFAULT 0,
                `address` VARCHAR(200) DEFAULT NULL,
                `occupancy` VARCHAR(50) DEFAULT NULL,
                `beds` INT(3) DEFAULT 0,
                `baths` INT(3) DEFAULT 0,
                `three_comp` VARCHAR(200) DEFAULT NULL,
                `sqft` INT(11) DEFAULT 0,
                `stage` VARCHAR(200) DEFAULT NULL,
                `deal_type` VARCHAR(200) DEFAULT NULL,
                `arv` INT(11) DEFAULT 0,
                `repairs` INT(11) DEFAULT 0,
                `rent` INT(11) DEFAULT 0,
                `wholesale_fee` INT(11) DEFAULT 0,
                `split_percentage` FLOAT(11) DEFAULT 0,
                `marketed` INT(1) DEFAULT 0,
                `notes` VARCHAR(10000) DEFAULT NULL,
                `picture` VARCHAR(200) DEFAULT NULL,
                `closing_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `purchase_price` FLOAT(11) DEFAULT 0,
                `option_term` INT(11) DEFAULT 0,
                `status` INT(1) DEFAULT 0,
                `contract_status` INT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step77(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_contract_members` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `member_id` INT(11) DEFAULT 0,
                `name` VARCHAR(50) DEFAULT NULL,
                `company` VARCHAR(100) DEFAULT NULL,
                `state` VARCHAR(100) DEFAULT NULL,
                `county` VARCHAR(100) DEFAULT NULL,
                `signature_path` VARCHAR(200) DEFAULT NULL,
                `member_type` VARCHAR(20) DEFAULT 'partner',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step78(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_partner_contracts` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `partner_id` INT(11) DEFAULT 0,
                `property_id` INT(11) DEFAULT 0,
                `user_info` TEXT DEFAULT NULL,
                `partner_info` TEXT DEFAULT NULL,
                `tax_id` VARCHAR(50) DEFAULT NULL,
                `send_date` DATETIME DEFAULT NULL,
                `signed_date` DATETIME DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step79(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_counter_offer_price` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `offer_id` INT(11) DEFAULT 0,
                `counter_price` INT(11) DEFAULT 0,
                `counterer_id` INT(11) DEFAULT 0,
                `type` VARCHAR(100) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step80(){
        $query = "
            ALTER TABLE `contact` 
            ADD `terms_agreed` INT(1) DEFAULT 0; 
        ";
        $this->runQuery($query);
    }

    public function step81(){
        $query = "
            ALTER TABLE `Wo_Listing` 
                ADD `partner_property` INT(11) DEFAULT 0; 
        ";

        $this->runQuery($query);
    }

    public function step82(){
        $query = "
            ALTER TABLE `wo_partner_properties` 
                ADD `extra` MEDIUMTEXT DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }

    public function step83(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_users` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `name` VARCHAR(100) DEFAULT 0,
                `email` VARCHAR(100) DEFAULT 0,
                `phone` VARCHAR(20) DEFAULT NULL,
                `password` VARCHAR(200) DEFAULT NULL,
                `wallet` FLOAT(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step84(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_properties` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `address` VARCHAR(100) DEFAULT 0,
                `city` VARCHAR(100) DEFAULT 0,
                `state` VARCHAR(100) DEFAULT 0,
                `zipcode` VARCHAR(100) DEFAULT 0,
                `occupancy` VARCHAR(100) DEFAULT 0,
                `prop_type` VARCHAR(100) DEFAULT 0,
                `beds` FLOAT(3) DEFAULT 0.00,
                `baths` FLOAT(3) DEFAULT 0.00,
                `sqft` INT(5) DEFAULT 0,
                `status` VARCHAR(20) DEFAULT NULL,
                `stage` VARCHAR(20) DEFAULT NULL,
                `min_offer_amount` VARCHAR(20) DEFAULT NULL,
                `max_offer_amount` VARCHAR(20) DEFAULT NULL,
                `offer_code` VARCHAR(50) DEFAULT NULL,
                `detail` MEDIUMTEXT DEFAULT NULL,
                `accepted_amount` VARCHAR(20) DEFAULT NULL,
                `wholesale_fee` VARCHAR(20) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step85(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_accepted` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(30) DEFAULT NULL,
                `email` VARCHAR(30) DEFAULT NULL,
                `phone` VARCHAR(20) DEFAULT NULL,
                `offer_code` VARCHAR(50) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }

    public function step86(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_numbers` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `account_id` VARCHAR(50) DEFAULT NULL,
                `number` VARCHAR(20) DEFAULT NULL,
                `status` INT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }
    
    public function step87(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_list_detail` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY,
                `list_id` int(11) DEFAULT '0',
                `first_name` VARCHAR(100) DEFAULT NULL,
                `last_name` VARCHAR(100) DEFAULT NULL,
                `address` VARCHAR(100) DEFAULT NULL,
                `city` VARCHAR(100) DEFAULT NULL,
                `state` VARCHAR(100) DEFAULT NULL,
                `zipcode` VARCHAR(100) DEFAULT NULL,
                `status` VARCHAR(100) DEFAULT NULL,
                `close_price` VARCHAR(100) DEFAULT NULL,
                `list_price` VARCHAR(100) DEFAULT NULL,
                `sp_sqft` VARCHAR(100) DEFAULT NULL,
                `lp_sqft` VARCHAR(100) DEFAULT NULL,
                `beds` VARCHAR(100) DEFAULT NULL,
                `half_baths` VARCHAR(100) DEFAULT NULL,
                `full_baths` VARCHAR(100) DEFAULT NULL,
                `heated_area` VARCHAR(100) DEFAULT NULL,
                `year_built` VARCHAR(100) DEFAULT NULL,
                `school_district` VARCHAR(100) DEFAULT NULL,
                `subdivision` VARCHAR(100) DEFAULT NULL,
                `garages` VARCHAR(100) DEFAULT NULL,
                `lot_size` FLOAT DEFAULT '0',
                `swimming_pool` VARCHAR(100) DEFAULT NULL,
                `property_type` VARCHAR(100) DEFAULT NULL,
                `c_dome` VARCHAR(100) DEFAULT NULL,
                `mailing_address` VARCHAR(100) DEFAULT NULL,
                `mailing_city` VARCHAR(50) DEFAULT NULL,
                `mailing_state` VARCHAR(50) DEFAULT NULL,
                `mailing_zipcode` VARCHAR(10) DEFAULT NULL,
                `lat` VARCHAR(20) DEFAULT 0,
                `lng` VARCHAR(20) DEFAULT 0,
                `user_id` int(11) DEFAULT '0', 
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query); 
    }

    public function step88(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_list` (
                `id` int(11) AUTO_INCREMENT PRIMARY KEY,  
                `list_name` VARCHAR(100) DEFAULT NULL,
                `active` int(11) DEFAULT 0,    
                `sold` int(11) DEFAULT 0,    
                `properties` int(11) DEFAULT 0,    
                `user_id` int(11) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query); 
    }

    public function step89(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_offer_onspot_transections` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `amount` FLOAT(11) DEFAULT 0,
                `type` VARCHAR(30) DEFAULT null,
                `extra` TEXT DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $this->runQuery($query);
    }
    
    

    public function step90(){
        $query = "
            ALTER TABLE `wo_attach_users` 
                ADD `is_trial` TINYINT DEFAULT 0,
                ADD `trial_starts` DATETIME DEFAULT NULL,
                ADD `trial_ends` DATETIME DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }

    public function step91(){
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_zoom_meetings` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `event_id` INT(11) DEFAULT 0,
                `zoom_id` INT(11) DEFAULT 0,
                `topic` VARCHAR(100) DEFAULT NULL,
                `start_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `duration` INT(11) DEFAULT 0,
                `password` VARCHAR(100) DEFAULT NULL,
                `type` INT(2) DEFAULT 0,
                `join_url` VARCHAR(300) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        
        $this->runQuery($query);
    }
    

    public function step92(){
        $query = "
            ALTER TABLE `Wo_Events` 
                ADD `allowed_package_id` INT(11) DEFAULT 0
        ";

        $this->runQuery($query);
    }
    

    public function step93(){
        $query = "
            ALTER TABLE `wo_leads` 
            
                ADD `bathrooms` VARCHAR(100) DEFAULT NULL,
                ADD `funds` FLOAT(11) DEFAULT 0,
                ADD `property_type` VARCHAR(300) DEFAULT NULL,
                ADD `buying_strategy` VARCHAR(300) DEFAULT NULL,
                ADD `purchase_strategy` VARCHAR(300) DEFAULT NULL,
                ADD `area_of_interest` TEXT DEFAULT NULL,
                ADD `min_price` INT(11) DEFAULT 0,
                ADD `max_price` INT(11) DEFAULT 0;
        ";

        $this->runQuery($query);
    }

    public function step94(){
        $query = "
            ALTER TABLE `Wo_Users` 
                ADD `trial_backup` INT(1) DEFAULT 0,
                ADD `is_member_trial` TINYINT DEFAULT 0,
                ADD `member_trial_starts` DATETIME DEFAULT NULL,
                ADD `member_trial_ends` DATETIME DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }

    public function step95(){
        $query = "
            CREATE TABLE IF NOT EXISTS `trial_already_permissions` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) DEFAULT 0,
                `permissions` VARCHAR(1000) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        
        
        $this->runQuery($query);
    }

    public function step96()
    {
        $query = "
            ALTER TABLE `wo_action_plans` 
                ADD `plan_thumb` VARCHAR(255) DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }
    

    public function step97()
    {
        $query = "
            ALTER TABLE `wo_plan_actions` 
                ADD `video_link` VARCHAR(100) DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }

    public function step98()
    {
        $query = "
            ALTER TABLE `Wo_Listing` 
                ADD `is_private` tinyint(1) DEFAULT 0; 
        ";

        $this->runQuery($query);
    }
    


    public function step99()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `wo_listing_contracts` (
                `id` int(20) AUTO_INCREMENT PRIMARY KEY,
                `seller_id` INT(11) DEFAULT 0,
                `buyer_id` INT(11) DEFAULT 0,
                `listing_id` INT(11) DEFAULT 0,
                `contract_type` VARCHAR(100) DEFAULT NULL,
                `data` TEXT DEFAULT NULL,
                `status` VARCHAR(20) DEFAULT 'sent',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->runQuery($query);
    }



    public function step100()
    {
        $query = "
            ALTER TABLE `Wo_Users` 
                ADD `member_type` INT(1) DEFAULT 0,
                ADD `member_type_data` VARCHAR(100) DEFAULT NULL,
                ADD `experience_level` INT(1) DEFAULT 0,
                ADD `experience_level_data` VARCHAR(100) DEFAULT NULL,
                ADD `property_strategy` INT(1) DEFAULT 0,
                ADD `property_strategy_data` VARCHAR(100) DEFAULT NULL,
                ADD `what_you_need` INT(1) DEFAULT 0,
                ADD `what_you_need_data` VARCHAR(100) DEFAULT NULL; 
        ";

        $this->runQuery($query);
    }
}   




