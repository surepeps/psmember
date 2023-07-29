
<style>
    .card-container {
  margin-bottom: 40px;
}

.card-container-header {
  margin-bottom: 10px;
}
@media (min-width: 40em) {
  .card-container-header {
    align-items: center;
    display: flex;
    justify-content: space-between;
  }
}

.card-container-title {
  margin-bottom: 0;
}

.card-container-actions > a:not(:last-child) {
  margin-right: 10px;
}

.card-container-styled > .card-container-body {
  background: #eee;
  border: 1px solid #ddd;
  border-radius: 3px;
  box-shadow: 0 1px 5px 0 rgba(0, 0, 0, 0.1);
  padding: 20px 20px 0;
}

.card-group > .card-container-body {
  display: flex;
  flex-wrap: wrap;
}

.card-columns > .card-container-body {
  -moz-column-count: 1;
       column-count: 1;
}
@media (min-width: 40em) {
  .card-columns > .card-container-body {
    -moz-column-count: 3;
         column-count: 3;
  }
}
@media (min-width: 60em) {
  .card-columns > .card-container-body {
    -moz-column-count: 4;
         column-count: 4;
  }
}
@media (min-width: 80em) {
  .card-columns > .card-container-body {
    -moz-column-count: 5;
         column-count: 5;
  }
}
.card-columns > .card-container-body > .card {
  max-width: 100%;
}

.card {
  display: inline-flex;
  margin-bottom: 20px;
  margin-right: 10px;
  max-width: 320px;
  position: relative;
  vertical-align: top;
  width: 100%;
}

.card-back,
.card-front {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 3px;
  position: relative;
  width: 100%;
}
@media (min-width: 30em) {
  .card-back[data-image-position=left], .card-back[data-image-position=right],
.card-front[data-image-position=left],
.card-front[data-image-position=right] {
    flex-direction: row;
  }
}

.card-front {
  display: flex;
  flex-direction: column;
}

.card-image {
  min-height: 1px;
}
.card-image > img {
  border-radius: 3px;
  display: block;
  margin: auto;
  max-width: 100%;
}
.card-image:not(:first-child):not(:last-child) {
  border-radius: 0;
}

.card-image-right, .card-image-left {
  width: 100%;
}
@media (min-width: 30em) {
  .card-image-right, .card-image-left {
    max-width: 55%;
  }
}
.card-image-right > img, .card-image-left > img {
  height: 100%;
}

.card-image-top > img {
  border-radius: 3px 3px 0 0;
}

.card-image-bottom {
  order: 1;
}
.card-image-bottom > img {
  border-radius: 0 0 3px 3px;
}

.card-image-right {
  order: 1;
}

.card-content {
  padding: 15px;
  flex: 1 1 auto;
}

.card-actions {
  padding: 15px;
}
.card-actions > a {
  color: #2ea0eb;
  font-size: 0.8571rem;
  text-decoration: none;
}
.card-actions > a:not(:last-child) {
  margin-right: 10px;
}
.card-actions > a:hover {
  color: #2580bc;
}

.card-flip-container {
  perspective: 1000px;
  width: 100%;
}
.card-flip-container > .card-back,
.card-flip-container > .card-front {
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  transition: opacity 0.35s cubic-bezier(0.23, 1, 0.32, 1), transform 0.65s cubic-bezier(0.23, 1, 0.32, 1);
  transform-style: preserve-3d;
}
.card-flip-container > .card-front {
  transform: rotateY(0) translate3d(0, 0, 2px);
}
.card-flip-container > .card-back {
  height: 0;
  margin-top: -2px;
  opacity: 0;
  overflow: hidden;
  transform: rotateY(-180deg);
  width: 0;
}

.card-flipped > .card-back {
  height: 100%;
  opacity: 1;
  overflow: visible;
  transform: rotateY(0);
  width: 100%;
}

.card-flipped > .card-front {
  height: 0;
  overflow: hidden;
  transform: rotateY(180deg) translate3d(0, 0, 2px);
  width: 0;
}

.card-flip-button {
  bottom: 2px;
  color: rgba(255, 255, 255, 0.7);
  display: block;
  font-size: 1.25rem;
  position: absolute;
  right: 5px;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
}

.card-selected > .card-front {
  background: #2ea0eb;
  border-color: #2788c8;
  box-shadow: inset 0 -4px 0 0 #2788c8;
  color: #fff;
}

.card-selected .card-actions > a {
  color: #fff;
}

.text-left {
  text-align: left !important;
}

.text-center {
  text-align: center !important;
}

.text-right {
  text-align: right !important;
}

.card-inverse {
  color: #fff;
}

.card-inverse .card-actions > a {
  color: #fff;
}

.card-primary > .card-back,
.card-primary > .card-front {
  background: #2ea0eb;
  border-color: #2788c8;
  box-shadow: inset 0 -4px 0 0 #2788c8;
}

.card-alert > .card-back,
.card-alert > .card-front {
  background: #ffdc73;
  border-color: #d9bb62;
  box-shadow: inset 0 -4px 0 0 #d9bb62;
}

.card-success > .card-back,
.card-success > .card-front {
  background: #89d085;
  border-color: #74b171;
  box-shadow: inset 0 -4px 0 0 #74b171;
}

.card-warning > .card-back,
.card-warning > .card-front {
  background: #f29a4e;
  border-color: #ce8342;
  box-shadow: inset 0 -4px 0 0 #ce8342;
}

.card-danger > .card-back,
.card-danger > .card-front {
  background: #fa5757;
  border-color: #d54a4a;
  box-shadow: inset 0 -4px 0 0 #d54a4a;
}

*, *::before, *::after {
  box-sizing: border-box;
}

body {
  font-family: "Open Sans", sans-serif;
  font-size: 87.5%;
}

p {
  margin-top: 0;
}

h1 {
  font-weight: 700;
  margin-top: 0;
}

h2 {
  font-weight: 700;
  margin-top: 0;
}

h3 {
  font-weight: 700;
  margin-top: 0;
}

h4 {
  font-weight: 700;
  margin-top: 0;
}

h5 {
  font-weight: 700;
  margin-top: 0;
}

h6 {
  font-weight: 700;
  margin-top: 0;
}

.section {
  background: #f7f7f7;
  border-bottom: 2px solid #ddd;
  padding: 75px 50px;
}
.section:nth-child(even) {
  background: #fff;
}

.section-info {
  margin-bottom: 50px;
  text-align: center;
}

@media (min-width: 40em) {
  .section-text {
    margin: auto;
    max-width: 700px;
  }
}

.subtitle {
  color: #999;
}

.btn {
  background: #2ea0eb;
  border: 1px solid #2990d4;
  box-shadow: 0 3px 0 0 #2788c8;
  border-radius: 3px;
  color: #fff;
  padding: 5px 8px;
  text-align: center;
}
</style>
<?php
// UnDocumemnted API Key
$api_id = "2b1e86b638620bf2404521e6e9e1b19e";

// General Api key
// $api_id = "c886d0392167da7542e9f5215a2bf361";

// Sales Comparables API Key
// $api_id = "ef737a57791b5d58694769dcbc2e6070";

function SalesComparables($api_id,$params = ""){
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.gateway.attomdata.com/property/v2/salescomparables/address/'.$params.'?searchType=Radius&minComps=1&maxComps=10&miles=5&bedroomsRange=2&bathroomRange=2&sqFeetRange=600&lotSizeRange=2000&saleDateRange=6&yearBuiltRange=10&ownerOccupied=Both&distressed=IncludeDistressed');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    
    $headers = array();
    $headers[] = 'Accept: application/json';
    $headers[] = 'Apikey: '.$api_id;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    
    
    return $result;
    
}

$address = "313 Pleasant Gardens Drive";
$city = "Apopka";
$county = "US";
$state = "FL";
$zip = "32703";

$param = $address."/".$city."/".$county."/".$state."/".$zip;
// $param = "11235%20S%20STEWART%20AVE/Chicago/US/IL/60628";

$result = SalesComparables($api_id,$param);

// $json = json_decode($result);
print_r($result);
// $d = $json['RESPONSE_GROUP']['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE_ext']['SUBJECT_PROPERTY_ext']['PROPERTY']['COMPARABLE_PROPERTY_ext']['@DistanceFromSubjectPropertyMilesCount'];
// $d = $json['RESPONSE_GROUP']['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE_ext']['SUBJECT_PROPERTY_ext']['PROPERTY'];
// foreach($d as $u){
//     if($u['COMPARABLE_PROPERTY_ext']){
//         $r = $u['COMPARABLE_PROPERTY_ext'];
//         $n = $r['_OWNER'];
//         echo '<div class="card">
//         <div class="card-front">
//           <div class="card-content">
//             <h3>'.$r['@_StreetAddress'].'</h3>
//             <p>
//               '.$r['@StandardUseDescription_ext'].'
//             </p>
//           </div>
//           <div class="card-actions">
//             BUYER\'S NAME: <a href="">'.$n['@_Name'].'</a>
//           </div>
//         </div>
//       </div>';
//         // echo "<li>".$u['COMPARABLE_PROPERTY_ext']['@DistanceFromSubjectPropertyMilesCount']."</li>";
//     }
    
// }
// echo $d;




?>