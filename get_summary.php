<?php
/*
* Gibt eine Übersicht aus:
* > für jeden Job
*    \_ Job-Name
*    \_ Arbeitszeit heute
*    \_ Arbeitszeit aktueller Monat
*    \_ Arbeitszeit vergangener Monat
*    \_ Arbeitszeit aktuelles Jahr
*/

session_start();
include("./glob_db.php");
include("./initials.php");
include("./functions.php");

$DB_LNK   =mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

//INIT
$dur_day = 0;
$dur_mon = 0;
$dur_lmon= 0;

echo "<table id='tbl_overview' border='2'>
      <tr><th>Job</th><th>day</th><th>month</th><th>l. month</th></tr>";

foreach ($jobs as $key => $value) {
	//Tageszeit ermitteln
	$sql = "SELECT day_date, time_start, time_end FROM zeit 
	        WHERE user = '".$user."' AND job_id ='".$key."' AND time_end <> '00:00:00' AND 
	              day_date = '".date("Y-m-d")."';";
	$qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (1)");
	
	$dur_day = 0;
	while ($row = mysql_fetch_array($qry)) {
		$dur_day = $dur_day + calcDuration($row["time_start"], $row["time_end"]);
	}
	
	//Zeit Monat ermitteln
	$til_dat = date("Y-m");
	$sql = "SELECT day_date, time_start, time_end FROM zeit 
	       WHERE ( user = '".$user."' AND job_id='".$key."' AND time_end <> '00:00:00' AND 
	               day_date >= '".$til_dat."-01' AND day_date <= '".$til_dat."-31' );";
	$qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (2)");
	
	$dur_mon = 0;
	while ($row = mysql_fetch_array($qry)) {
		$dur_mon = $dur_mon + calcDuration($row["time_start"], $row["time_end"]);
	}
	
	//Zeit Vormonat ermitteln
	if ( date("m") == '01' ) {
		$til_dat = (date("Y")-1)."-12";
	} else {
		$til_dat = date("Y")."-".(date("m")-1);
	}
	$sql = "SELECT day_date, time_start, time_end FROM zeit 
	       WHERE ( user = '".$user."' AND job_id='".$key."' AND time_end <> '00:00:00' AND 
	               day_date >= '".$til_dat."-01' AND day_date <= '".$til_dat."-31' );";
	$qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (2)");
	
	$dur_lmon = 0;
	while ($row = mysql_fetch_array($qry)) {
		$dur_lmon = $dur_lmon + calcDuration($row["time_start"], $row["time_end"]);
	}	
	
	//Zeit Jahr ermitteln
	$sql = "SELECT day_date, time_start, time_end FROM zeit 
	       WHERE ( user = '".$user."' AND job_id='".$key."' AND time_end <> '00:00:00' AND 
	               day_date >= '".date("Y")."-01-01' AND day_date <= '".date("Y")."-12-31' );";
	
	//Infos ausgeben
	echo "<tr><td>".$value."</td><td>".$dur_day." min<br />(".minutes2Time($dur_day)." h)</td>
	                             <td>".$dur_mon." min<br />(".minutes2Time($dur_mon)." h)</td>
	                             <td>".$dur_lmon." min<br />(".minutes2Time($dur_lmon)." h)</td>
	                        </tr>";
}

echo "</table>";

?>