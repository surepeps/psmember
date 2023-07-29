<?php 
namespace Dompdf;
require_once 'assets/libraries/dompdf/autoload.inc.php';

if($_REQUEST['out_type']==2){

	
ob_start();
}
?>
<?php 

 foreach($_REQUEST as $key => $val){
	     if($key=="analysis"){
			foreach($val as $key2 => $val2){
				$data[$key2]=$val2;
				
				if($key2=="first_loan"){
					foreach($val2 as $key3 => $val3){
						$data2[$key3]=$val3;
					}
				 }
				 
				if($key2=="second_loan"){
					foreach($val2 as $key3 => $val3){
						$data3[$key3]=$val3;
					}
				 }				 
			}
		 }
		 
		 
 }
/*echo "<hr>";
print_r($data);
echo "<hr>";
echo "<pre>";
print_r($_REQUEST);*/
if($_REQUEST['out_type']==2){ require_once('assets/includes/data_in_tables_bh.php');}
else{ //let use this just for the print page. ?>
		<html>
		<head><title>Print</title>
		<link rel="stylesheet" href="/themes/wowonder/stylesheet/buy-and-hold-style-print.css?v=<?php echo date("gis");?>">		

		</head>
		<body>	
		<div class="page for-analysis first">
		<div class="header" style="display:none;">
		<h1>john test</h1>
		<div class="contact-details">
		<div class="field">j.jackson@gmail.com</div>
		</div>
		</div>
		<div class="header" style="border-top: solid 1px #eee;padding-top:10px">
		<h1><?php echo $_REQUEST['street_number'];?> <?php echo $_REQUEST['street_name'];?></h1>
		<h2><?php echo $_REQUEST['city'];?>, <?php echo $_REQUEST['state'];?> <?php echo $_REQUEST['postal_code'];?></h2>
		<div class="address-details" style="display:none">
		<div class="field">32708</div>
		<div class="field">Seminole</div>
		<div class="field">United States</div>
		</div>
		</div>

		<section>
		<div class="section-header">Purchase Items</div>
		<div class="column">

		<div class="field"><span class="label">Number of Units</span><span class="value"><?php echo $_REQUEST['number_of_units'];?></span></div>
		<div class="field"><span class="label">Purchase Price</span><span class="value"><?php echo $data['purchase_price'];?></span></div>
        <div class="field"><span class="label">&nbsp;</span><span class="value">&nbsp;</span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Fix Up Costs</span><span class="value"><?php echo $data['fix_up_costs_price'];?></span></div>
		<div class="field"><span class="label">Closing Costs</span><span class="value"><?php echo $data['purchase_closing_costs_price'];?></span></div>
		<div class="field"><span class="label">Property Taxes</span><span class="value"><?php echo $data['property_taxes_price'];?> / year</span></div>

		</div>
		</section>


		
		
		<section>
		<div class="section-header">First Loan Financing</div>
		<div class="column">
		<div class="field"><span class="label">Loan Amount</span><span class="value"><?php echo $_REQUEST['first_loan_amount_hid'];?></span></div>
		<div class="field"><span class="label">Down Payment</span><span class="value"><?php echo $data2['down_payment_percent'];?> <?php echo $data2['down_payment_unit'];?></span></div>
		<div class="field"><span class="label">Interest Rate</span><span class="value"><?php echo $data2['interest_rate'];?>% <?php echo $data2['interest_rate_unit'];?></span></div>
		<div class="field"><span class="label">Points</span><span class="value"><?php echo $data2['points'];?> pt(s) <?php echo $data2['points_unit'];?></span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Loan Type</span><span class="value"><?php echo $_REQUEST['first_amortization_interest_unit'];?></span></div>
		<div class="field"><span class="label">Term</span><span class="value"><?php echo $_REQUEST['first_loan_term'];?> years</span></div>
		<div class="field"><span class="label">Principal and Interest Payment</span><span class="value"><?php echo $_REQUEST['first_principal_interest_payment_hid'];?></span></div>
		<div class="field"><span class="label">Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['first_loan_amount_finance_hid'];?></span></div>
		</div>
		</section>
		
		<section>
		<div class="section-header">Second Loan Financing</div>
		<div class="column">
		<div class="field"><span class="label">Loan Amount</span><span class="value"><?php echo $_REQUEST['second_loan_amount_hid'];?></span></div>
		<div class="field"><span class="label">Down Payment</span><span class="value"><?php echo $data3['down_payment_percent'];?> <?php echo $data3['down_payment_unit'];?></span></div>
		<div class="field"><span class="label">Interest Rate</span><span class="value"><?php echo $data3['interest_rate'];?> % <?php echo $data3['interest_rate_unit'];?></span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Points</span><span class="value"><?php echo $data3['points'];?> pt(s) <?php echo $data3['points_unit'];?></span></div>
		<div class="field"><span class="label">Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['second_loan_amount_finance_hid'];?></span></div>
		</div>
		</section>
		
		<section>
		<div class="section-header">Second Loan Financing</div>
		<div class="column">
		<div class="field"><span class="label">Loan Amount</span><span class="value"><?php echo $_REQUEST['second_loan_amount_hid'];?></span></div>
		<div class="field"><span class="label">Down Payment</span><span class="value"><?php echo $data3['down_payment_price'];?> <?php echo $data3['down_payment_unit'];?></span></div>
		<div class="field"><span class="label">Interest Rate</span><span class="value"><?php echo $data3['interest_rate'];?> <?php echo $data3['interest_rate_unit'];?></span></div>
		<div class="field"><span class="label">Points</span><span class="value"><?php echo $data3['points'];?> <?php echo $data3['points_unit'];?></span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Loan Type</span><span class="value"><?php echo $_REQUEST['second_amortization_interest_unit'];?></span></div>
		<div class="field"><span class="label">Term</span><span class="value"><?php echo $_REQUEST['second_loan_term'];?> years</span></div>
		<div class="field"><span class="label">Principal and Interest Payment</span><span class="value"><?php echo $_REQUEST['second_principal_interest_payment_hid'];?></span></div>
		<div class="field"><span class="label">Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['second_loan_amount_finance_hid'];?></span></div>
		</div>
		</section>		
		
		
		
		
		<section class="totals">
		<div class="field"><span class="label">Total Amount Financed</span><span class="value"><?php echo $_REQUEST['estimated-monthly-rent'];?></span></div>
		</section>
		
<section>
<div class="section-header">Monthly Expenses</div>
<div class="column">
<div class="field"><span class="label">Property Taxes</span><span class="value"><?php echo $_REQUEST['property_taxes_hid'];?></span></div>
<div class="field"><span class="label">Insurance</span><span class="value"><?php echo $_REQUEST['insurance'];?> / month</span></div>
<div class="field"><span class="label">Property Management</span><span class="value"><?php echo $_REQUEST['property_management'];?> % / month</span></div>
<div class="field"><span class="label">Total Monthly Financing Payment(s)</span><span class="value"><?php echo $_REQUEST['monthly_financing_hid'];?></span></div>
</div>
<div class="column">
<div class="field"><span class="label">HOA</span><span class="value">$<?php echo $_REQUEST['hoa'];?> / month</span></div>
<div class="field"><span class="label">Vacancy</span><span class="value"><?php echo $_REQUEST['vacancy'];?> %</span></div>
<div class="field"><span class="label">Repairs</span><span class="value"><?php echo $_REQUEST['repairs'];?> %</span></div>
</div>
</section>

		</div>



		<div class="page break for-analysis">
		<div class="header" style="display:block">


		<section class="results">
		<?php 
		$block_post = $_REQUEST['to_print_block_str'];
		$block_post = str_replace('"results"','"value"',$block_post);
		$block_post = str_replace('"results net_profit_span"','"value"',$block_post);
		$block_post = str_replace('"results roi_span"','"value"',$block_post);
		$block_post = str_replace('"results coc_span"','"value"',$block_post);
		$block_post = str_replace('"results roi_span"','"value"',$block_post);
		$block_post = str_replace('<span class="label">','</div><div class="field"><span class="label">',$block_post);
		$block_post = str_replace('<div class="analysis_box">','<div class="analysis_box" style="display:none">',$block_post);
		$block_post = str_replace('class="settings','style="display:none;" class="settings',$block_post);

		echo '<div class="field">'.$block_post;
		?>
		<hr><?php /*
		<div class="field"><span class="label">Total Project Cost</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">First Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Second Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Total Amount Financed</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Total Cost of Sale</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Project Duration</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Cash Required</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">First Loan, Monthly Interest Cost</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Second Loan, Monthly Interest Cost</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Estimated Net Profit</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Cash On Cash Return</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>
		<div class="field"><span class="label">Annual Return on Investment (ROI)</span><span class="value"><?php echo $_REQUEST['state'];?></span></div>*/?>
		</section>

		<?php

		$slider_1 = $_REQUEST['slider_1'];   
		$slider_1_arr = explode(",",$slider_1);
		$low_1 = ($slider_1_arr[0]*1000);
		$high_1 = ($slider_1_arr[1]*1000);
		$net_profit_hid = str_replace("$","",$_REQUEST['net_profit_hid']);
		if($low_1>$net_profit_hid){
		   $box1_bg = "background: #e62929 !important;";
		}elseif($net_profit_hid>$low_1 AND $net_profit_hid<$high_1){
		   $box1_bg = "background: #eabe0f !important;";
		}else{
		   $box1_bg = "background: #1ab91a !important;";	
		}

		$slider_2 = $_REQUEST['slider_2'];  
		$slider_2_arr = explode(",",$slider_2);
		$low_2 = $slider_2_arr[0];
		$high_2 = $slider_2_arr[1];
		$cash_on_hand_return_hid = $_REQUEST['cash_on_hand_return_hid'];
		if($low_2>$cash_on_hand_return_hid){
		   $box2_bg = "background: #e62929 !important;";
		}elseif($cash_on_hand_return_hid>$low_2 AND $cash_on_hand_return_hid<$high_2){
		   $box2_bg = "background: #eabe0f !important;";
		}else{
		   $box2_bg = "background: #1ab91a !important;";	
		}

		$slider_3 = $_REQUEST['slider_3'];  
		$slider_3_arr = explode(",",$slider_3);
		$low_3 = $slider_3_arr[0];
		$high_3 = $slider_3_arr[1];
		$annual_roi_hid = $_REQUEST['annual_roi_hid'];
		if($low_3>$annual_roi_hid){
		   $box3_bg = "background: #e62929 !important;";
		}elseif($annual_roi_hid>$low_3 AND $annual_roi_hid<$high_3){
		   $box3_bg = "background: #eabe0f !important;";
		}else{
		   $box3_bg = "background: #1ab91a !important;";	
		}
		//echo "$low_3 $high_3 $annual_roi_hid";

		?>
				<section class="analysis-summary for-bah">
				<div class="analysis-column is-green">
				<div class="value"><?php echo $_REQUEST['Annual_Return_Investment_ROI_hid'];?>%</div>
				<div class="label">Annual ROI</div>
				</div>
				<div class="analysis-column is-green">
				<div class="value"><?php echo $_REQUEST['Cap_Rate_hid'];?></div>
				<div class="label">Cap Rate</div>
				</div>
				<div class="analysis-column is-green">
				<div class="value"><?php echo $_REQUEST['Net_Cash_Flow_Project_hid'];?> /m</div>
				<div class="label">Net Cash Flow</div>
				</div>
				<div class="analysis-column is-green">
				<div class="value"><?php echo $_REQUEST['Return_of_Cash_Invested_hid'];?> Years</div>
				<div class="label">Return of Cash</div>
				</div>
				</section>
		</body>
		</html>	
<?php } //end of if($_REQUEST['out_type']==2) else ?>

<?php if($_REQUEST['out_type']==1){?>
<script>
setTimeout(function(){print(); }, 3000);
</script>
<?php } ?>


</div>



<?php 
if($_REQUEST['out_type']==2){
  $page = ob_get_contents();
   ob_end_clean();

	$dompdf = new Dompdf(); 
	$dompdf->set_option('isRemoteEnabled', TRUE);
	$dompdf->loadHtml($page);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$dompdf->stream("",array("Attachment" => false));
	exit(0);
}
?>