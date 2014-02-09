<?php
session_start();
include("./glob_db.php");
include("./initials.php");
include("./functions.php");

$DB_LNK = mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

if (!checkTime($_POST["checkOutTime"])) {
  die("Zeit-Angabe ungültig!");
}

// ===Check Out===
//Vars:
// - checkOutTime

//Aktuellen Job ermitteln
$sql = "SELECT id, time_start FROM zeit WHERE user = '".$user."' AND time_end ='00:00:00';";
$qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (0)");
if ( mysql_num_rows($qry) == 0 ) die("Kein Satz gefunden!");

$row = mysql_fetch_array($qry);

//Zeit prüfen
if ( time2Minutes($row["time_start"]) > time2Minutes($_POST["checkOutTime"]) )
	die("End-Zeit muss nach Start-Zeit sein!");

//Update durchführen
updateCheckOut($row["id"], $row["time_start"], $_POST["checkOutTime"]);

// fetig
echo($success);
?>