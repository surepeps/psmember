<?php
global $wo, $sqlConnect;
$root=$_SERVER['DOCUMENT_ROOT'];
require_once($root.'/config.php');
require_once('assets/init.php');
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
<?php
$user_id = $wo['user']['user_id'];

$userrole = Wo_UserRole($user_id);

if(isset($_POST['action']) && $_POST['action']=="get_offer_graphs" ) {

		$yearvalue = $_POST['yearval'];
		if($userrole=="buyer" || $userrole=="investor")
			$columnname = "last_updated_by_user";
		else
			$columnname = "seller_id";
		$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_offers` WHERE $columnname=".$user_id." AND YEAR(offer_start_date)='$yearvalue'");

		while($row = mysqli_fetch_array($query)) {

			$status = $row['offer_status'];
			$monthofoffer = date("F",strtotime($row['offer_start_date']));
			$offersarray[$status][$monthofoffer][] = $row['offer_start_date'];

		}

		$montharray = array(
						    'January',
						    'February',
						    'March',
						    'April',
						    'May',
						    'June',
						    'July ',
						    'August',
						    'September',
						    'October',
						    'November',
						    'December',
						);

		$statusarr = array("pending","accepted","rejected","counter");

		$newdataarray = array();
		//print_r($offersarray);
		foreach ($montharray as $key => $value) {
			foreach ($statusarr as $key => $status) {
			
				$count = 0;
				if(isset($offersarray[$status][$value]))
					$count = count($offersarray[$status][$value]);
				$newdataarray [$status] [$value] = $count;
			}
		}


?>
<canvas id="offersGraph"></canvas>
<script type="text/javascript">
	var ctx = document.getElementById("offersGraph").getContext('2d');
	offergraph(ctx,[<?php echo implode(",",$newdataarray['pending']); ?>],[<?php echo implode(",", $newdataarray['accepted']); ?>],[<?php echo implode(",", $newdataarray['rejected']); ?>],[<?php echo implode(",", $newdataarray['counter']); ?>]);
	function offergraph(ctx,pendingdata,accepteddata,rejecteddata,counterdata) {

	var myChart = new Chart(ctx, {
	    type: 'line',
	    data: {
	        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sep", "Oct", "Nov", "Dec"],
	        datasets: [{
	            label: 'Pending',
	            data: pendingdata,
	            backgroundColor: 'rgba(150,188,51,1)',
	            borderColor: 'rgba(255,99,132,1)',
	            borderWidth: 1
	        },
	        {
	            label: 'Accepted',
	            data: accepteddata,
	            backgroundColor: 'rgba(245,130,32,1)',
	            borderColor: 'rgba(245,130,32,1)',
	            borderWidth: 1
	        },
	         {
	            label: 'Rejected',
	            data: rejecteddata,
	            backgroundColor: 'rgba(52,152,219,1)',
	            borderColor: 'rgba(52,152,219,1)',
	            borderWidth: 2
	        }, 
	        {
	            label: 'Counter',
	            data: counterdata,
	            backgroundColor: 'rgba(2,209,191,1)',
	            borderColor: 'rgba(2,209,191,1)',
	            borderWidth: 2
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:false
	                }
	            }]
	        },
					legend: {
						position: 'bottom'
					}
	    }
	});


}
</script>

<?php
die;
} else if(isset($_POST['action']) && $_POST['action']=="get_visit_graphs" ) {

		$yearvalue = $_POST['yearval'];

		if($userrole=="buyer" || $userrole=="investor")
			$columnname = "user_id";
		else
			$columnname = "property_author";
		$query_visit = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Schedule_Visits` WHERE $columnname=".$user_id." AND  visit_date LIKE '%$yearvalue%'");

		//echo "SELECT * FROM `Wo_Schedule_Visits` WHERE property_author=".$user_id." AND  visit_date LIKE '%$yearvalue%'";
			//echo "SELECT * FROM `Wo_Schedule_Visits` WHERE property_author=".$user_id." AND  Year(CAST(visit_date as datetime) as DateField)='2019'";
		while($rowvisit = mysqli_fetch_array($query_visit)) {

			//print_r($rowvisit);
			$statusvisit = $rowvisit['visits_status'];
			$monthofvisit = date("F",strtotime($rowvisit['visit_date']));
			$visitarray[$statusvisit][$monthofvisit][] = $rowvisit['visit_date'];

		}
		//print($visitarray);
		$montharray = array(
						    'January',
						    'February',
						    'March',
						    'April',
						    'May',
						    'June',
						    'July ',
						    'August',
						    'September',
						    'October',
						    'November',
						    'December',
						);

		$statusarrvisits = array("pending","accepted","rejected","rescheduled");

		$newdataarrayvisits = array();

		foreach ($montharray as $key => $value) {
			foreach ($statusarrvisits as $key => $statusvisit) {
			
				$count = 0;
				if(isset($visitarray[$statusvisit][$value]))
					$count = count($visitarray[$statusvisit][$value]);
				$newdataarrayvisits [$statusvisit] [$value] = $count;

			}

		}

?>
<canvas id="schedVisitsGraph"></canvas>
<script type="text/javascript">
	var ctx = document.getElementById("schedVisitsGraph").getContext('2d');


	var svGraph = new Chart(ctx, {
	    type: 'line',
	    data: {
	        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sep", "Oct", "Nov", "Dec"],
	        datasets: [{
	            label: 'Pending',
	            data: [<?php echo implode(",",$newdataarrayvisits['pending']); ?>],
	            backgroundColor: 'rgba(150,188,51,1)',
	            borderColor: 'rgba(255,99,132,1)',
	            borderWidth: 1
	        },
	        {
	            label: 'Accepted',
	            data: [<?php echo implode(",",$newdataarrayvisits['accepted']); ?>],
	            backgroundColor: 'rgba(245,130,32)',
	            borderColor: 'rgba(245,130,32)',
	            borderWidth: 1
	        },
	         {
	            label: 'Rejected',
	            data: [<?php echo implode(",",$newdataarrayvisits['rejected']); ?>],
	            backgroundColor: 'rgba(52,152,219,1)',
	            borderColor: 'rgba(52,152,219,1)',
	            borderWidth: 2
	        },
	        {
	            label: 'Reschedule',
	            data: [<?php echo implode(",",$newdataarrayvisits['rescheduled']); ?>],
	            backgroundColor: 'rgba(2,209,191,1)',
	            borderColor: 'rgba(2,209,191,1)',
	            borderWidth: 2
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:false
	                }
	            }]
	        },
					legend: {
						position: 'bottom'
					}
	    }
	});
</script>

<?php
die;
} else if(isset($_POST['action']) && $_POST['action']=="get_listings_graphs" ) {

		$yearvalue = $_POST['yearval'];
		$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Listing` WHERE user_id=".$user_id." AND YEAR(dtae_time)='$yearvalue'");

		while($row = mysqli_fetch_array($query)) {

			$monthoflisting = date("F",strtotime($row['dtae_time']));
			$listingarray[$monthoflisting][] = $row['dtae_time'];

		}

		$montharray = array(
						    'January',
						    'February',
						    'March',
						    'April',
						    'May',
						    'June',
						    'July ',
						    'August',
						    'September',
						    'October',
						    'November',
						    'December',
						);

		$newdataarraylisting = array();

		foreach ($montharray as $key => $value) {
			
				$count = 0;
				if(isset($listingarray[$value]))
					$count = count($listingarray[$value]);
				$newdataarraylisting[$value] = $count;
		}

?>
<canvas id="propertiesGraph1"></canvas>
<script type="text/javascript">
	var ctx = document.getElementById("propertiesGraph1").getContext('2d');
	var pG1 = new Chart(ctx, {
	    type: 'line',
	    data: {
	        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sep", "Oct", "Nov", "Dec"],
	        datasets: [{
	            label: 'Listings',
	            data: [<?php echo implode(",",$newdataarraylisting); ?>],
	            backgroundColor: 'rgba(90,178,145,1)',
	            borderColor: 'rgba(101,107,139,1)',
	            borderWidth: 1
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:false
	                }
	            }]
	        },
					legend: {
						position: 'bottom'
					}
	    }
	});
</script>

<?php
die;
} else if(isset($_POST['action']) && $_POST['action']=="get_single_property_graph" ) {

			$proid = $_POST['proid'];
			/***********************************Graph data for Offers *************************************/
				$query = mysqli_query($sqlConnect,"SELECT * FROM `Wo_offers` WHERE seller_id=".$user_id." AND YEAR(offer_start_date)='2019' AND property_id=".$proid);

				while($row = mysqli_fetch_array($query)) {

					/*$status = $row['offer_status'];*/
					$monthofoffer = date("F",strtotime($row['offer_start_date']));
					$offersarray[$monthofoffer][] = $row['offer_start_date'];

				}

				$montharray = array(
								    'January',
								    'February',
								    'March',
								    'April',
								    'May',
								    'June',
								    'July ',
								    'August',
								    'September',
								    'October',
								    'November',
								    'December',
								);

				$statusarr = array("pending","accepted","rejected","counter");

				$newdataarray = array();

				foreach ($montharray as $key => $value) {
					/*foreach ($statusarr as $key => $status) {*/
					
						$count = 0;
						if(isset($offersarray[$value]))
							$count = count($offersarray[$value]);
						$newdataarray [$value] = $count;

					/*}*/

				}


		/***********************************Graph data for Schedule Visits *************************************/

			$query_visit = mysqli_query($sqlConnect,"SELECT * FROM `Wo_Schedule_Visits` WHERE property_author=".$user_id." AND  visit_date LIKE '%2019%' AND property_id=".$proid);
			//echo "SELECT * FROM `Wo_Schedule_Visits` WHERE property_author=".$user_id." AND  Year(CAST(visit_date as datetime) as DateField)='2019'";
			while($rowvisit = mysqli_fetch_array($query_visit)) {

				$monthofvisit = date("F",strtotime($rowvisit['visit_date']));
				$visitarray[$monthofvisit][] = $rowvisit['visit_date'];

			}

			$montharray = array(
							    'January',
							    'February',
							    'March',
							    'April',
							    'May',
							    'June',
							    'July ',
							    'August',
							    'September',
							    'October',
							    'November',
							    'December',
							);

			$newdataarrayvisits = array();

			foreach ($montharray as $key => $value) {
				
					$count = 0;
					if(isset($offersarray[$value]))
						$count = count($visitarray[$value]);
					$newdataarrayvisits [$value] = $count;

			}


?>
<canvas id="mReportsGraph1"></canvas>
<script type="text/javascript">
var ctx = document.getElementById("mReportsGraph1").getContext('2d');
	var pG1 = new Chart(ctx, {
	    type: 'line',
	    data: {
	        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sep", "Oct", "Nov", "Dec"],
	        datasets: [
	         {
	            label: 'Schedule Visits',
	            data: [<?php echo implode(",",$newdataarrayvisits); ?>],
	            backgroundColor: 'rgba(52,152,219,1)',
	            borderColor: 'rgba(101,107,139,1)',
	            borderWidth: 2
	        },
					{
						 label: 'Offers',
						 data: [<?php echo implode(",",$newdataarray); ?>],
						 backgroundColor: 'rgba(245,130,32,1)',
						 borderColor: 'rgba(101,107,139,1)',
						 borderWidth: 2
				 }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:false
	                }
	            }]
	        },
					legend: {
						position: 'bottom'
					}
	    }
	});
</script>

<?php
die;
} else if(isset($_POST['action']) && $_POST['action']=="update_spent_time" ) {

		$proid = $_POST['property_id'];
	
		$user_id = $wo['user']['user_id'];

		$diff = $_POST['spenttime'];

		$currentmonth = date("n");
		$query_view = mysqli_query($sqlConnect,"SELECT * FROM `Wo_property_views_time` WHERE property_id = $proid AND  user_id= $user_id AND MONTH(created_date)='$currentmonth'");

		if(mysqli_num_rows($query_view ) > 0) {
			
			$row_views = mysqli_fetch_array($query_view);
			
			$time_spent = $row_views['time_spent'];

			$newtime = $time_spent + $diff;

			$query_one = mysqli_query($sqlConnect, "UPDATE `Wo_property_views_time` SET `time_spent`= '$newtime' , modified_date = '".date("Y-m-d")."' WHERE id=".$row_views['id']);

		}else{

			$query_insert   = "INSERT INTO `Wo_property_views_time`(`property_id`, `user_id`,`time_spent`,`created_date`,`modified_date`) VALUES ({$proid},'{$user_id}','{$diff}','".date("Y-m-d")."','".date("Y-m-d")."')";
			$sql_query = mysqli_query($sqlConnect, $query_insert);
		}
} else if(isset($_POST['action']) && $_POST['action']=="get_single_property_time_graph" ) {

			$proid = $_POST['proid'];
			/***********************************Graph data for View Time Visits *************************************/

			$query_time = mysqli_query($con,"SELECT * FROM `Wo_property_views_time` WHERE  YEAR(created_date)='2019' AND property_id=".$proid);
			//echo "SELECT * FROM `Wo_Schedule_Visits` WHERE property_author=".$user_id." AND  Year(CAST(visit_date as datetime) as DateField)='2019'";
			while($rowtime = mysqli_fetch_array($query_time)) {

				$monthoftime = date("F",strtotime($rowtime['created_date']));
				$timearray[$monthoftime][] = $rowtime['time_spent'];
				$userarray[$monthoftime][] = $rowtime['user_id'];

				
			}

			$montharray = array(
							    'January',
							    'February',
							    'March',
							    'April',
							    'May',
							    'June',
							    'July ',
							    'August',
							    'September',
							    'October',
							    'November',
							    'December',
							);

				$newdataarraytime = array();
				$newdataarrayavgtime = array();

				foreach ($montharray as $key => $value) {
					
						$count = 0;
						$avgcount = 0;
						if(isset($timearray[$value]))
							$count = array_sum($timearray[$value]);
						$newdataarraytime [$value] = date("H.i.s",strtotime($count));
					if(isset($userarray[$value])) {
						$usercount = count($userarray[$value]);
						$avgcount = $count / $usercount;

					}
					$newdataarrayavgtime[$value] = date("H.i.s",strtotime($avgcount));

				}

?>

<canvas id="aReportsGraph1"></canvas>

<script>
var ctx = document.getElementById("aReportsGraph1").getContext('2d');

var pG1 = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: 'Total Time',
            data: [<?php echo implode(",",$newdataarraytime); ?>],
            backgroundColor: 'rgba(90,178,145,1)',
            borderColor: 'rgba(101,107,139,1)',
            borderWidth: 1
        },
        {
            label: 'Avg. Time',
            data: [<?php echo implode(",",$newdataarrayavgtime); ?>],
            backgroundColor: 'rgba(173,106,191,1)',
            borderColor: 'rgba(101,107,139,1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:false
                }
            }]
        },
				legend: {
					position: 'bottom'
				}
    }
});
</script>
<?php
die;
}



die;

?>