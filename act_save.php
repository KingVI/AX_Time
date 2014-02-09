<?php
session_start();
include("./glob_db.php");
include("./initials.php");
include("./functions.php");

$DB_LNK = mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

if ( !checkTime($_POST["saveTimeFrom"]) || !checkTime($_POST["saveTimeTo"]) ) {
  die("Zeit ungültig! (hh:mm)");
}

//Zeit prüfen
if (time2Minutes($_POST["saveTimeFrom"]) > time2Minutes($_POST["saveTimeTo"]) ) 
	die("End-Zeit muss nach Start-Zeit sein!");

// ===Save===
//Vars:
//- saveSelect
//- saveDate
//- saveTimeFrom
//- saveTimeTo

//Insert durchführen
$sql = "INSERT INTO zeit (user, job_id, day_date, time_start, time_end)
               VALUES ('".$user."', '".$_POST["saveSelect"]."', '".$_POST["saveDate"]."', '".$_POST["saveTimeFrom"]."', '".$_POST["saveTimeTo"]."');";
mysql_query($sql);

// fetig
echo($success);

?>