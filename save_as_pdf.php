<?php ob_start("callback");?>
<html>
<head><title>Print</title>

<style>
th,td {
    width: 13%;
	text-align: center;
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
<?php if($_REQUEST['print_type']=="fix_and_flip"){?>



<?php }else{ ?>
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
					
					
					
			 
	   
	?>

		var markers = [
		<?php echo $markers;?>    
		];
		window.onload = function () {
			LoadMap();
		}
		var map, mapOptions;
		function LoadMap() {
			mapOptions = {
				center: new google.maps.LatLng(<?php echo $_REQUEST['property_ip'];?>),
				zoom: 15,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
	 
			for (var i = 0; i < markers.length; i++) {
				var data = markers[i];
				var myLatlng = new google.maps.LatLng(data.lat, data.lng);		
				
			if(data.title=="Main"){ //main house info
				var iconpath = new google.maps.MarkerImage("https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|356bde");
			}else{
				var iconpath = new google.maps.MarkerImage("https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|2196f3");
			}

				var marker = new google.maps.Marker({
					position: myLatlng,
					map: map,
					icon: iconpath,
					title: data.title
				});
			}
		};
	</script>
	<h1><?php echo $_REQUEST['data_str_main_address'];?></h1>
	<p><?php echo str_replace("<br>","",$_REQUEST['data_str_main_info']);?></p>

	<div id="dvMap">
				</div>

	<?php 
	$data_arr = strip_tags($_REQUEST["data_str"],"<table><tr><td><th><h1><h4><br><div>");
	$data_arr = str_replace("<td></td>","",$data_arr);
	echo $data_arr;
	echo "<hr>";

	$data_str_2 = strip_tags($_REQUEST["data_str_2"],"<table><tr><td><th><h1><h4><br><div>");
	echo $data_str_2;
	echo "<hr>";

	$data_str_3 = strip_tags($_REQUEST["data_str_3"],"<table><tr><td><th><h1><h4><br><div>");
	echo $data_str_3;
	//echo "<hr>";

	//print_r($data_arr);

	//echo "<hr style='margin-top:100px;'>";
	//print_r($_REQUEST);
	?>

	</div>
<?php } //end of else ?>
</body>
</html>
<?php 
  $page = ob_get_contents();
   ob_end_clean();
 ?>