<?php
session_start();
include("./glob_db.php");
include("./initials.php");
include("./functions.php");

$DB_LNK   =mysql_connect($DB_HOST, $DB_USER, $DB_PWD) or die("Error on mysql_connect!");
mysql_select_db($DB_NAME, $DB_LNK) or die("Error on SELECT_DB");

if ( trim($_POST['viewDate']) != '' ) {
        $viewDate = $_POST['viewDate'];
} else {
        $viewDate = date("Y-m-d");
}

switch($_POST['viewLevel']) {

        case 1;
                //Sicht: Tag

                // Buchungen des Tages ermitteln
                $sql = "SELECT id, job_id, day_date, time_start, time_end FROM zeit
                        WHERE user = '".$user."' AND day_date = '".$viewDate."' ORDER BY time_start ASC;";
                $qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (1)");

                if ( mysql_num_rows($qry) == 0 ) {
                        echo "keine Daten im Zeitbereich";
                } else {
                        echo "<table id='tbl_overview' border='2'>
              <tr><th>start</th><th>end</th><th>job</th><th>del</td></tr>";

                        while ($row = mysql_fetch_array($qry)) {
                                echo "<tr id='time".$row['id']."'><td>".substr($row['time_start'],0,5)."</td>
                                          <td>".substr($row['time_end'],0,5)."</td>
                                          <td>".$jobs[$row['job_id']]."</td>
                                          <td><a href='#' onClick='deleteTime(".$row['id'].");'><img src='./img/delete.png' border='0' /></a></td></tr>";
                        }

                        echo "</table>";
                }

                break;

        case 2;
                //Sicht: Monat

                // Buchungen ermitteln
                $sql = "SELECT job_id, day_date, time_start, time_end FROM zeit
                        WHERE user = '".$user."' AND day_date >= '".substr($viewDate,0,8)."01' AND day_date <= '".substr($viewDate,0,8)."31'
                        ORDER BY day_date ASC;";
                $qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (1)");

                if ( mysql_num_rows($qry) == 0 ) {
                        echo "keine Daten im Zeitbereich";
                } else {

                        $tbl = array();
                        $sum = array();
                        foreach ($jobs as $key => $value) $sum[$key] = 0;

                        while ($row = mysql_fetch_array($qry)) {
                                $ix_j = $row["job_id"];
                                $ix_d = substr($row["day_date"],-2);

                                if ( !isset($tbl[$ix_d][$ix_j]) ) $tbl[$ix_d][$ix_j] = 0;

                                $tbl[$ix_d][$ix_j] += calcDuration($row["time_start"], $row["time_end"]);
                                $sum[$ix_j]        += calcDuration($row["time_start"], $row["time_end"]);
                        }

                        echo "<table id='tbl_overview' border='2'>";

                        // ROW: Title
                        echo "<tr bgcolor='FFFA78'><th>day</td>";
                        foreach ($jobs as $key => $value)
                                echo "<th>".$value."</th>";
                        echo "<th><i>SUM</i></th>";
                        echo "</tr>";

                        // ROW: soll
                        $row_sum = 0;
                        echo "<tr bgcolor='FFFCA8'><td>SOLL</td>";
                        foreach ($jobs as $key => $value) {
                        	echo "<td align='right'>".calcMonthShould($key, $viewDate)."</td>";
                        	$row_sum += calcMonthShould($key, $viewDate);
                        }
                        echo "<td align='right'><i>".$row_sum."</i></td>";
                        echo "</tr>";
                        
                        // ROW: sum
                        $row_sum = 0;
                        echo "<tr bgcolor='FFFCA8'><td>SUM</td>";
                        foreach ($jobs as $key => $value) {
                        	echo "<td align='right'>".$sum[$key]."</td>";
                        	$row_sum += $sum[$key];
                        }
                        echo "<td align='right'><i>".$row_sum."</i></td>";
                        echo "</tr>";

                        // value rows 
                        foreach ($tbl as $key => $row) {
                                //Montage farblich markieren
                                if ( date("N", mktime(0, 0, 0, substr($viewDate,5,2), $key, substr($viewDate,0,4))) == 1 ) {
                                       echo "<tr style='background-color: #CEF6CE;'>";
                                } else {
                                       echo "<tr>";
                                }
                                echo "<td><a href='#' onClick='showDay(".$key.");'>".$key."</a></td>";
                                $row_sum = 0;
                                foreach ($jobs as $key => $value) {
                                        echo "<td align='right'>";
                                        if ( isset($row[$key])) {
                                                echo $row[$key];
                                                $row_sum += $row[$key];
                                        } else {
                                                echo "&nbsp;";
                                        }
                                        echo "</td>";
                                }
                                echo "<td align='right'><i>".$row_sum."</i></td>";
                                echo "</tr>";
                        }
                        echo "</table>";
                }

                break;

        case 3;
                //Sicht: Jahr

                // Buchungen ermitteln
                $sql = "SELECT job_id, day_date, time_start, time_end FROM zeit
                        WHERE user = '".$user."' AND day_date >= '".substr($viewDate,0,4)."-01-01' AND day_date <= '".substr($viewDate,0,4)."-12-31'
                        ORDER BY day_date ASC;";
                $qry = mysql_query($sql) or die("Anfrage nicht erfolgreich (1)");

                if ( mysql_num_rows($qry) == 0 ) {
                        echo "keine Daten im Zeitbereich";
                } else {

                        $tbl = array();
                        $sum = array();
                        foreach ($jobs as $key => $value) $sum[$key] = 0;

                        while ($row = mysql_fetch_array($qry)) {
                                $ix_j = $row["job_id"];
                                $ix_d = substr($row["day_date"],5,2);

                                if ( !isset($tbl[$ix_d][$ix_j]) ) $tbl[$ix_d][$ix_j] = 0;
//
                                $tbl[$ix_d][$ix_j] += calcDuration($row["time_start"], $row["time_end"]);
                                $sum[$ix_j]        += calcDuration($row["time_start"], $row["time_end"]);
                        }

                        echo "<table id='tbl_overview' border='2'>";

                        echo "<tr bgcolor='FFFA78'><th>month</td>";
                        foreach ($jobs as $key => $value)
                                echo "<th>".$value."</th>";
                        echo "<th><i>SUM</i></th>";
                        echo "</tr>";

                        $row_sum = 0;
                        echo "<tr bgcolor='FFFCA8'><td>SUM</td>";
                        foreach ($jobs as $key => $value) {
                        	echo "<td align='right'>".minutes2Time($sum[$key])."</td>";
                        	$row_sum += $sum[$key];
                        }
                        echo "<td align='right'><i>".minutes2Time($row_sum)."</i></td>";
                        echo "</tr>";

                        foreach ($tbl as $key => $row) {
                                echo "<tr><td><a href='#' onClick='showMonth(".$key.");'>".$key."</a></td>";
                                $row_sum = 0;
                                foreach ($jobs as $key => $value) {
                                        echo "<td align='right'>";
                                        if ( isset($row[$key])) {
                                                echo minutes2Time($row[$key]);
                                                $row_sum += $row[$key];
                                        } else {
                                                echo "&nbsp;";
                                        }
                                        echo "</td>";
                                }
                                echo "<td align='right'><i>".minutes2Time($row_sum)."</i></td>";
                                echo "</tr>";
                        }
                        echo "</table>";
                }

                break;

        default;
    die("Aktion ungültig?!?");
    break;
}

?>
