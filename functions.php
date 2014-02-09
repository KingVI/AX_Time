<?php

function updateCheckOut($id, $rec_start, $time) {
        if ( $time == $rec_start ) {
                // Startzeit = Endzeit --> Buchung lschen
                $sql = "DELETE FROM zeit WHERE id = ".$id.";";
        } else {
                // Endzeit ergnzen (Update)
                $sql = "UPDATE zeit SET time_end = '".$time."' WHERE id = ".$id.";";
        }
        mysql_query($sql) or die("Update failed! -> ".$sql);
}

function minutes2Time($min) {
        $hour = floor($min / 60);
        $minutes = $min - floor($min / 60) * 60;
        if ( $minutes < 10 ) $minutes = "0".$minutes;
        return $hour.":".$minutes;
}

function time2Minutes($time) {
        $values = explode (':', $time);
        $min = $values[0] * 60 + $values[1];
        return $min;
}

function checkTime($time) {
        // Zeit-Ausdruck auf gltigkeit testen
        if (preg_match('/^(?:2[0-4]|[01][0-9]):[0-5][0-9]$/', $time) == 1) {
                return true;
        }
        return false;
}

function createSqlDate($date) {
  if (strpos ($date, '.')) {
    $values = explode ('.', $date);
    $day = $values[0];
    $month = $values[1];
    $year  = $values[2];

      if ($check= checkdate($month, $day ,$year)) {
              if ($month < 10) $month = "0".$month;
              if ($day   < 10) $day   = "0".$day  ;
        $result = $year."-".$month."-".$day;
      } else {
        $result = false;
      }
  } else {
    $result = false;
  }
  return $result;
}

function calcDuration($start, $end) {
    if ( $end == '00:00:00' ) {
        $dur = 0;
    } else {
        $dur = (substr($end, 0, 2) - substr($start, 0, 2) ) * 60;
        $dur = $dur + substr($end, 3, 2)   - substr($start, 3, 2);
    }
        return $dur;
}

// Arbeitszeit-Soll berechnen
function calcMonthShould($job, $date) {
  global $should;
  global $holidays100;
  global $holidays50;

  if ( $should[$job] == 0 ) return '';

  // Arbeitstage Array
  $workDays = array('1','2','3','4','5');

  // Anfangszeit: erster des Monats
  $month = substr($date,5,2);
  $year  = substr($date,0,4);
  $theDate = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));

  // Feiertage
  foreach($holidays100 as $key => $value) {
    if ( strlen($holidays100[$key]) == 6 ) $holidays100[$key] = $year.$value;
  }
  foreach($holidays50 as $key => $value) {
    if ( strlen($holidays50[$key]) == 6 )  $holidays50[$key]  = $year.$value;
  }

  $dayCounter = 1;
  $daysSoFar = 0;
  $holidays  = 0;

  while( $month == date("n", mktime(0, 0, 0, $month, $dayCounter, $year ))) {
    $workingDate = mktime(0, 0, 0, $month, $dayCounter, $year );
    if( in_array(date("w",$workingDate),$workDays) ){
      if( (in_array(date("Y-m-d", $workingDate), $holidays100))) {
        $holidays++;
      } else {
        if (in_array(date("Y-m-d", $workingDate), $holidays50)) {
          $daysSoFar = $daysSoFar + 0.5;
          $holidays = $holidays + 0.5;
        } else {
          $daysSoFar++;
        }
      }
    }
    $dayCounter++;
  }

  if ( $holidays > 0 ) {
    return $daysSoFar * $should[$job].' ('.$holidays.')';
  } else {
    return $daysSoFar * $should[$job];
  }
}

?>