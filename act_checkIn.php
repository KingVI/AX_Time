<?php
session_start();
include("./glob_db.php");
include("./initials.php");
include("./functions.php");

$DB_LNK = mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

if (!checkTime($_POST["checkInTime"])) {
  die("Input-Wert(e) ungültig!");
}

// ===Check In===
//Vars:
//- checkInSelect
//- checkInTime

//Aktuellen Job ermitteln
$sql = "SELECT id, time_start FROM zeit WHERE user = '".$user."' AND time_end ='00:00:00';";
$qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (0)");
if ( mysql_num_rows($qry) == 1 ) {
	//Aktuellen Job abschliessen
	$row = mysql_fetch_array($qry);
	updateCheckOut($row["id"], $row["time_start"], $_POST["checkInTime"]);
}

//Insert durchführen
$sql = "INSERT INTO zeit (user, job_id, day_date, time_start)
               VALUES ('".$user."', '".$_POST["checkInSelect"]."', '".date("Y-m-d")."', '".$_POST["checkInTime"]."');";
mysql_query($sql);

// fetig
echo($success);

?>