<?php

if ( !isset($_SESSION['user']) ) $_SESSION['user'] = 'DEMO';
$user = $_SESSION['user'];

//Konstanten
$success = 'Success!';

//Array mit den Usern
$users = array();
$users['ALEX'] = 'Alex';
$users['DEMO'] = 'H.Muster';

//Array mit den vorhandenen Jobs inkl Tages-Soll
$jobs = array();
$should = array();

switch($user) {
        case "ALEX";
                $jobs['GdeEI'] = 'Gde EI';
                $jobs['GrtEI'] = 'Gde EI Sitzung';
                $jobs['VRSG'] = 'VRSG';
                $jobs['PPS'] = 'PPS';

                $should['GdeEI'] = 510 * 0.5;
                $should['GrtEI'] = 0;
                $should['VRSG'] = 504 * 0.4;
                $should['PPS'] = 0;

                break;
        case "DEMO";
                $jobs['J1'] = 'Job 1';
                $jobs['J2'] = 'Job 2';
                $jobs['J3'] = 'Hobby';
                $jobs['J4'] = 'Family';

                $should['J1'] = 0;
                $should['J2'] = 0;
                $should['J3'] = 0;
                $should['J4'] = 0;
}

//Array mit den Feiertagen (ohne Jahreszahl = jährlich)
$holidays100 = array('-01-01','01-02','-08-01','-11-01','-12-25','-12-26');
$holidays50  = array('-12-24','-12-31');
//2013
$holidays100[] = '2013-03-29'; //Karfreitag
$holidays100[] = '2013-04-01'; //Ostermontag
$holidays100[] = '2013-05-09'; //Pfingstmontag
$holidays100[] = '2013-05-20'; //Auffahrt
//2014
$holidays100[] = '2014-04-18'; //Karfreitag
$holidays100[] = '2014-04-21'; //Ostermontag
$holidays100[] = '2014-05-29'; //Auffahrt
$holidays100[] = '2014-06-09'; //Pfingstmontag
//2015
$holidays100[] = '2015-04-03'; //Karfreitag
$holidays100[] = '2015-04-06'; //Ostermontag
$holidays100[] = '2015-05-14'; //Auffahrt
$holidays100[] = '2015-05-25'; //Pfingstmontag

?>
