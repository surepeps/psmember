<?php 
namespace Dompdf;
require_once 'assets/libraries/dompdf/autoload.inc.php';

if($_REQUEST['out_type']==2){

	
ob_start();
}
?>
<?php if($_REQUEST['print_type']=="fix_and_flip"){ 

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
// echo "<hr>";
//print_r($data);
 //echo "<hr>";
//echo "<pre>";
//print_r($_REQUEST);
if($_REQUEST['out_type']==2){ require_once('assets/includes/data_in_tables.php');}
else{ //let use this just for the print page. ?>
		<html>
		<head><title>Print</title>
		<link rel="stylesheet" href="/themes/wowonder/stylesheet/fix-and-flip-style-print.css?v=<?php echo date("gis");?>">		
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
		<div class="field"><span class="label">Purchase Date</span><span class="value"><?php echo $data['purchase_date'];?></span></div>
		<div class="field"><span class="label">Purchase Price</span><span class="value"><?php echo $data['purchase_price'];?></span></div>
		<div class="field"><span class="label">Fix Up Costs</span><span class="value"><?php echo $data['fix_up_costs_price'];?></span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Closing Costs</span><span class="value"><?php echo $data['purchase_closing_costs_price'];?></span></div>
		<div class="field"><span class="label">Property Taxes</span><span class="value"><?php echo $data['property_taxes_price'];?> / year</span></div>
		<div class="field"><span class="label">Insurance</span><span class="value"><?php echo $data['insurance_costs_price'];?></span></div>
		</div>
		</section>
		<section>
		<div class="section-header">Misc. Holding Costs</div>
		<div class="column">
		<div class="field"><span class="label">Utilities</span><span class="value"><?php echo $data['utility_costs_price'];?>  / month</span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Other</span><span class="value"><?php echo $data['other_costs_price'];?> / month</span></div>
		</div>
		</section>
		<section class="totals">
		<div class="field"><span class="label">Total Project Cost</span><span class="value"><?php echo $_REQUEST['total_project_costs_hid'];?></span></div>
		</section>
		<section>
		<div class="section-header">First Loan Financing</div>
		<div class="column">
		<div class="field"><span class="label">Loan Amount</span><span class="value"><?php echo $_REQUEST['first_loan_amount_hid'];?></span></div>
		<div class="field"><span class="label">Down Payment</span><span class="value"><?php echo $data2['down_payment_price'];?> <?php echo $data2['down_payment_unit'];?></span></div>
		<div class="field"><span class="label">Interest Rate</span><span class="value"><?php echo $data2['interest_rate'];?> <?php echo $data2['interest_rate_unit'];?></span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Points</span><span class="value"><?php echo $data2['points'];?></span></div>
		<div class="field"><span class="label">Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['first_loan_amount_finance_hid'];?></span></div>
		</div>
		</section>
		<section>
		<div class="section-header">Second Loan Financing</div>
		<div class="column">
		<div class="field"><span class="label">Loan Amount</span><span class="value"><?php echo $_REQUEST['second_loan_amount_hid'];?></span></div>
		<div class="field"><span class="label">Down Payment</span><span class="value"><?php echo $data3['down_payment_price'];?> <?php echo $data3['down_payment_unit'];?></span></div>
		<div class="field"><span class="label">Interest Rate</span><span class="value"><?php echo $data3['interest_rate'];?> <?php echo $data3['interest_rate_unit'];?></span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Points</span><span class="value"><?php echo $data3['points'];?> <?php echo $data3['points_unit'];?></span></div>
		<div class="field"><span class="label">Loan Amount Financed</span><span class="value"><?php echo $_REQUEST['second_loan_amount_finance_hid'];?></span></div>
		</div>
		</section>
		<section class="totals">
		<div class="field"><span class="label">Total Amount Financed</span><span class="value"><?php echo $_REQUEST['total_amount_finance_hid'];?></span></div>
		</section>
		<section>
		<div class="section-header">Sale Items</div>
		<div class="column">
		<div class="field"><span class="label">Estimated Sale Date</span><span class="value"><?php echo $data['estimated_sale_date'];?></span></div>
		<div class="field"><span class="label">After Repaired Value (ARV)</span><span class="value"><?php echo $data['after_repair_value_price'];?></span></div>
		<div class="field"><span class="label">Agent Commissions</span><span class="value"><?php echo $data['agent_commission_percent'];?> %</span></div>
		</div>
		<div class="column">
		<div class="field"><span class="label">Closing Costs</span><span class="value"><?php echo $data['sale_closing_costs_price'];?></span></div>
		<div class="field"><span class="label">Seller Concessions</span><span class="value"><?php echo $data['seller_concession_percent'];?> %</span></div>
		<div class="field"><span class="label">Deferred Interest Expense</span><span class="value"><?php echo $_REQUEST['deferred_interest_expense_hid'];?></span></div>
		</div>
		</section>
		<section class="totals">
		<div class="field"><span class="label">Total Cost of Sale</span><span class="value"><?php echo $_REQUEST['total_cost_of_sale_hid'];?></span></div>
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
		<section class="analysis-summary for-faf">
		<div class="analysis-column is-red" style="<?php echo $box1_bg;?>">
		<div class="value">$<?php echo $net_profit_hid;?></div>
		<div class="label">Net Profit</div>
		</div>  
		<div class="analysis-column is-red" style="<?php echo $box2_bg;?>">
		<div class="value"><?php echo $_REQUEST['cash_on_hand_return_hid'];?>%</div>
		<div class="label">Cash On Cash</div>
		</div>
		<div class="analysis-column is-green" style="<?php echo $box3_bg;?>">
		<div class="value"><?php echo $_REQUEST['annual_roi_hid'];?>%</div>
		<div class="label">Annual ROI</div>
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
<?php }else{ //comps print and PDF ?>
<html>
<head><title>Print</title>

<style>
th,td {
    width: 13%;
	text-align: center;
	    padding-bottom: 20px;
}

.main_wrap {
    margin: auto;
    width: 800px;
}

#dvMap{
    width: 100%;
    height: 350px;
    position: relative;
    overflow: hidden;
}

.percent_block {
    float: left;
    width: 30%;
}
.percent_figure_block {
    float: right;
    width: 70%;
}
</style>
</head>
<body>

		<div class="main_wrap">
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaKk9lOoS2QXFpxovEoX3lxWwT-poxTz0"></script>
		<script type="text/javascript">
		<?php 
		  $comps_lng_lat = json_decode($_REQUEST['comps_lng_lat']);
		  $comps_lng_lat = (array)$comps_lng_lat;
		  $selected_comps_arr = explode(",",$_REQUEST['selected_comps_print']); 
		 // print_r($comps_lng_lat);
		  foreach($selected_comps_arr as $comps_lnglat_id){
				//echo 
				if(!empty($comps_lnglat_id)){
					
				//echo "[".$comps_lnglat_id."]";
				//echo "---".$comps_lng_lat[$comps_lnglat_id];
				$lnglat_arr = explode(", ",$comps_lng_lat[$comps_lnglat_id]);
				$lng = $lnglat_arr[0];
				$lat = $lnglat_arr[1];
				//echo $lng." ".$lat;
				
				$markers.='
				{
				"title": "",
				"lat": "'.$lat.'",
				"lng": "'.$lng.'",
				"description": " "
				},
				';
				
				$image_markers.="&markers=color:blue%7C$lat,$lng";
				}
		  }
				
				$lnglat_arr2 = explode(",",$_REQUEST['property_ip']);
				$lng2 = $lnglat_arr2[1];
				$lat2 = $lnglat_arr2[0];
					$markers.='
						{
						"title": "Main",
						"lat": "'.$lat2.'",
						"lng": "'.$lng2.'",
						"description": " "
						},
						';
						
					$image_main_markers="&markers=color:purple%7C$lat2,$lng2";	
						
				 
		$map_url="https://maps.googleapis.com/maps/api/staticmap?center=".str_replace(" ","+",$_REQUEST['data_str_main_address'])."&zoom=15&size=800x380&maptype=map";
		$map_url.="$image_main_markers"."$image_markers";
		$map_url.="&key=AIzaSyCey3pPT-jLhjUtHfo6m26976mFvYBZeNs";
		//echo $map_url;



		?>


		</script>
		<h1><?php echo $_REQUEST['data_str_main_address'];?></h1>
		<p><?php echo str_replace("<br>","",$_REQUEST['data_str_main_info']);?></p>

		<img src="<?php echo $map_url;?>" width="800" height="420">
		<!--<div id="dvMap">            </div>-->

		<?php 
		$_REQUEST_arr = strip_tags($_REQUEST["data_str"],"<table><tr><td><th><h1><h4><br><div>");
		$_REQUEST_arr = str_replace("<td></td>","",$_REQUEST_arr);
		$_REQUEST_arr = str_replace("<th>&nbsp;</th>","",$_REQUEST_arr);
		$_REQUEST_arr = str_replace("<th>selected</th>","<th></th>",$_REQUEST_arr);
		$_REQUEST_arr = str_replace('class="table gs-table"','class="table gs-table" width="700" ',$_REQUEST_arr);
		if($_REQUEST['out_type']==2){
		echo "<br clear='all'><br clear='all'><p>&nbsp;</p><p>&nbsp;</p>";
		}
		echo $_REQUEST_arr;
		echo "<hr>";

		$_REQUEST_str_2 = strip_tags($_REQUEST["data_str_2"],"<table><tr><td><th><h1><h4><br><div>");
		echo $_REQUEST_str_2;
		echo "<hr>";

		$_REQUEST_str_3 = strip_tags($_REQUEST["data_str_3"],"<table><tr><td><th><h1><h4><br><div>");
		//$_REQUEST_str_3 = str_replace('<div class="percent_block">',"</tr><tr><td>",$_REQUEST_str_3);
		//$_REQUEST_str_3 = str_replace('<div class="percent_figure_block">',"<td>",$_REQUEST_str_3);
		//$_REQUEST_str_3 = str_replace('</div>',"</td>",$_REQUEST_str_3);
		$_REQUEST_str_3 = str_replace('<div class="percent_block">','<br clear="all"><div class="percent_block">',$_REQUEST_str_3);
		$_REQUEST_str_3 = str_replace('<h4>Offer Chart</h4>',"",$_REQUEST_str_3);

		echo '<h4>Offer Chart</h4><div id="current_comps_table_div">			
			<table class="table gs-table" width="700">
						<tbody>';
		echo "<tr>";
		echo $_REQUEST_str_3;
		echo "</tbody></tr></table>";
		echo "<br><br><br></div>";
		//echo "<hr>";

		//print_r($_REQUEST_arr);

		//echo "<hr style='margin-top:100px;'>";
		//print_r($_REQUEST);
		?>
				<?php if($_REQUEST['out_type']==1){?>
				<script>
				 setTimeout(function(){print(); }, 3000);
				</script>
				<?php } ?>
		</div>
</body>
</html>		
<?php } //end of else ?>

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