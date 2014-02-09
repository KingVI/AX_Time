<?php
session_start();
include("./glob_db.php");

//User setzen -> via GET-Var
if ( isset($_GET['setUser']) ) $_SESSION['user'] = $_GET['setUser'];
include("./initials.php");

$DB_LNK   =mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

//HTML-Code für Jobs-Optionlist
$html_job_options = "";
foreach ($jobs as $key => $value)
        $html_job_options = $html_job_options.'<option value="'.$key.'">'.$value.'</option>';

//Ermittelt die aktive Arbeit (inkl. seit wann) aus oder eine Info dass aktuell nicht gearbeitet wird.
$sql = "SELECT job_id, time_start FROM zeit WHERE user = '".$user."' AND time_end ='00:00:00';";
$qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (0)");
if ( mysql_num_rows($qry) == 0 ) {
        $working = false;
        $working_job = "";
} else {
        $working = true;
        $row = mysql_fetch_array($qry);
        $working_job = $jobs[$row['job_id']];
        //$working_from = $row['time_start'];
}
mysql_free_result($qry);

?>
<!DOCTYPE html>
<html>
<head>
  <title>AX Time</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
  <link rel="apple-touch-icon" href="./flagge.png"/>

        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
        <link rel="stylesheet" href="./myCSS.css" />
        <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
        <script src="./myScripts.js"></script>
</head>
<body onload="initData()">

<div data-role="page" id="main">

        <div data-role="header">
                <a onclick="initData();" data-icon="refresh" data-iconpos="notext" data-direction="reverse" class="ui-btn-left jqm-home">Refresh</a>
                <h1>AX Time | User:<?php echo($users[$user]); ?></h1>
                <a href="#view" data-icon="grid" data-iconpos="notext">Overview</a>
                <!--<a href="#info" data-icon="info" data-iconpos="notext">Info</a>-->
        </div><!-- /header -->

        <div data-role="content">

                <div data-role="collapsible-set">


                        <div id="block_cOut" data-role="collapsible" data-mini="true" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-theme="e" data-content-theme="c" <?php if ($working) echo(" data-collapsed='false'"); ?>>
                                <h3>Check Out</h3>
                                <form id="form_cOut">
                                        <div data-role="fieldcontain">
                                                <label for="checkOutTime">Zeit</label>
                                                <input type="Time" name="checkOutTime" id="checkOutTime" value=""  />
                                        </div>
                                        <button type="submit_cOut" name="submit_cOut" id="submit_cOut" value="cOut">Check out</button>
                                </form>
                        </div>

                        <div id="block_cIn" data-role="collapsible" data-mini="true" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-theme="e" data-content-theme="c" <?php if (!$working) echo(" data-collapsed='false'"); ?> >
                                <h3>Check In</h3>
                                <form id="form_cIn">
                                        <select name="checkInSelect" id="checkInSelect">
                                                <?php echo($html_job_options); ?>
                                        </select>
                                        <div data-role="fieldcontain">
                                                <label for="checkInTime">Zeit</label>
                                                <input type="Time" name="checkInTime" id="checkInTime" value=""  />
                                        </div>
                                        <button type="submit" name="submit_cIn" id="submit_cIn" value="cIn">Check in</button>
                                </form>
                        </div>

                        <div id="block_save" data-role="collapsible" data-mini="true" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-theme="e" data-content-theme="c">
                                <h3>Save</h3>
                                <form id="form_save">
                                        <select name="saveSelect" id="checkInSelect">
                                                <?php echo($html_job_options); ?>
                                        </select>
                                        <div data-role="fieldcontain">
                                                <label for="saveDate">Datum</label>  <input type="Date" name="saveDate" id="saveDate" value=""  />
                                                <label for="saveTimeFrom">Von</label><input type="Time" name="saveTimeFrom" id="saveTimeFrom" value=""  />
                                                <label for="saveTimeTo">Bis</label>  <input type="Time" name="saveTimeTo" id="saveTimeTo" value=""  />
                                        </div>
                                        <button type="submit_save" name="submit_save" id="submit_save" value="save">Save</button>
                                </form>
                        </div>
                </div>

                <hr />

                <div id="summary"> </div>

  </div><!-- /content -->

  <div data-role="footer" class="footer-docs" data-position="fixed">
          <div data-role="navbar">
          <ul>
                  <!--<li><a onclick="myTest();">TEST</a></li>-->
                  <li id="notification"></li>
                  <li id="working">
<?php
if ( $working ) {
        echo "working for ".$working_job;
} else {
        echo "not working";
}
?>
                  </li>
          </ul>
          </div>
  </div>

</div><!-- /page -->

<div data-role="page" id="view" data-add-back-btn="true">
        <div data-role="header">
          <a href="#main" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-left jqm-home">Home</a>
          <h1>Overview | User:<?php echo($users[$user]); ?></h1>
        </div><!-- /header -->

        <div data-role="content">
                <input type="date" name="viewSelDate"  id="viewSelDate" />
                <div id="view_content">
                        <div align='center'><img src='./img/ajax-loader.gif' border='0' /></div>
                </div>
        </div><!-- /content -->

  <div data-role="footer" class="footer-docs" data-position="fixed">
          <div data-role="navbar">
                  <ul>
                          <li><a id="view_nav_prev">prev</a></li>
                          <li><a id="view_nav_next">next</a></li>
                  </ul>
          </div>
          <div data-role="navbar">
                  <ul>
                          <li><a id="view_day">day</a></li>
                          <li><a id="view_month">month</a></li>
                          <li><a id="view_year">year</a></li>
                  </ul>
          </div>
  </div>

</div>

<div data-role="page" id="info" data-add-back-btn="true">

        <div data-role="header">
          <a href="#main" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-left jqm-home">Home</a>
          <h1>Info</h1>
          <a href="#main" data-icon="arrow-l" data-iconpos="notext" data-rel="back">zur&uuml;ck</a>
        </div><!-- /header -->

        <div data-role="content">

          <h1>Online</h1>
          <ul data-role="listview">
            <li><a href="mailto:alex.arnold@red-queen.ch">E-Mail</a></li>
            <li><a href="http://www.alexarnold.ch/fb">Facebook</a></li>
            <li><a href="http://www.alexarnold.ch/twitter">Twitter</a></li>
          </ul>

          <h1>Adresse</h1>
          Alex Arnold<br />Härdlistrasse 21<br />9453 Eichberg

        </div><!-- /content -->

  <div data-role="footer" class="footer-docs" data-position="fixed"><p align=center>2012 <a href="http://www.red-queen.ch">ALeX</a></p></div>

</div><!-- /page -->

</body>
</html>