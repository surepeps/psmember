<?php 
  
  global $wo, $sqlConnect;
  $root=$_SERVER['DOCUMENT_ROOT'];
  require_once($root.'/config.php');
  require_once('assets/init.php');

  $sqlConnect   = $wo['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, 3306);

  $q = mysqli_query($sqlConnect, "SELECT `contest_no` FROM `strastic_winners` WHERE `winner_type` = 'weekly'");
  
  $newcontestno = 1;
  if(mysqli_num_rows($q) > 0) {
    $contestdata = mysqli_fetch_assoc($q);
    $newcontestno = $contestdata['contest_no'] + 1;
  }
  $where .= 'MONTH(wo_user_points.datetime)= MONTH(CURRENT_DATE())';
  $query   = mysqli_query($con, " SELECT Wo_Users.first_name,Wo_Users.last_name,Wo_Users.user_id,SUM(wo_user_points.points) AS totalpoints,wo_user_points.datetime FROM Wo_Users INNER JOIN wo_user_points ON wo_user_points.userid = Wo_Users.user_id WHERE  ".$where." GROUP BY wo_user_points.userid ORDER BY totalpoints DESC LIMIT 1"); 

  $fetched_data = mysqli_fetch_assoc($query);

  mysqli_query($sqlConnect, "UPDATE `strastic_winners` SET status=0 WHERE winner_type='weekly'");

  $query_one = mysqli_query($sqlConnect, "INSERT INTO `strastic_winners` (`points`,`datetime`,`userid`,`winner_type`,`contest_no`,`rank`,`status`) VALUES(".$fetched_data['totalpoints'].",".date("Y-m-d H:i:s").",".$fetched_data['userid'].",'weekly',".$newcontestno.",1,1) ");
  
  if ($query_one) {
    mail("test1212@mailinator.com",'Cron weekly','winner '.$fetched_data['userid']);
  }

?>