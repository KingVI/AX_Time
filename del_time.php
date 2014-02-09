<?php
// Buchung löschen
session_start();
include("./glob_db.php");
include("./initials.php");

if ( !isset($_POST["timeId"]) ) die ("no Parameter!");

$DB_LNK = mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

//Delete durchführen
$sql = "DELETE FROM zeit WHERE id = ".$_POST['timeId'].";";
mysql_query($sql);

// fetig
echo($_POST['timeId']);

?>