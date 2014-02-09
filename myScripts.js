
$view_level = 1;
$success = 'Success!';

//Test-Funktion (für EN-Tests)
function myTest() {
        alert($("#checkInSelect").val());
}

//Felder init (nur beim Bildstart)
function initData() {
        var now = new Date();
        var nowDate = now.getFullYear()+"-";
        if (now.getMonth() < 9) nowDate = nowDate+"0";
        nowDate = nowDate+(now.getMonth()+1)+"-";
        if (now.getDate() < 10) nowDate = nowDate+"0";
        nowDate = nowDate+now.getDate();
        $("#saveDate").val(nowDate);
        $("#viewSelDate").val(nowDate);

        refreshData();
}

// Daten auf dem Dialog aktualisieren
//  - Zeit-/Datum-Felder
//  - Buchungsübersicht
function refreshData () {
        var now = new Date();
        var nowTime = now.getHours();
        if ( now.getHours() < 10 ) nowTime = "0"+nowTime;
        if ( now.getMinutes() < 10 ) nowTime = nowTime+":0"+now.getMinutes();
        if ( now.getMinutes() > 9 ) nowTime = nowTime+":"+now.getMinutes();
        $("#checkInTime").val(nowTime);
        $("#checkOutTime").val(nowTime);

        //Summary anzeigen
        $.ajax({
                type: "POST",
          url: "get_summary.php",
                cache: false,
                success: onSuccess_summary,
                error: onError
        });

        //Übersicht anzeigen
        $.ajax({
          type: "POST",
    url: "get_overview.php",
                cache: false,
                data: "viewLevel="+$view_level+"&viewDate="+document.getElementById("viewSelDate").value,
                success: onSuccess_overview
        });
}

// Success-Funktion für AJAX-Call summary
function onSuccess_summary(data, status) {
        $("#summary").html(data);
}

// Success-Funktion für AJAX-Call checkIn
function onSuccess_cIn(data, status) {
        if (data = $success) {
                $("#working").text("working for "+$("#checkInSelect").val());
                $("#block_cIn").trigger("collapse");
                $("#block_cOut").trigger("expand");
                refreshData();
        }
        $("#notification").text(data);
}

// Success-Funktion für AJAX-Call checkOut
function onSuccess_cOut(data, status) {
        if (data = $success) {
                $("#working").text("not working");
                $("#block_cOut").trigger("collapse");
                $("#block_cIn").trigger("expand");
                refreshData();
        }
        $("#notification").text(data);
}

// Success-Funktion für AJAX-Call save
function onSuccess_save(data, status) {
        if (data = $success) refreshData();
        $("#notification").text(data);
}

// Error-Funktion für AJAX-Calls
function onError(data, status) {
        alert("AJAX Error?!?");
}

// Ansicht neu einlesen
function readOverview() {
        $("#view_content").html("<div align='center'><img src='./img/ajax-loader.gif' border='0' /></div>");

        $.ajax({
                        type: "POST",
                        url: "get_overview.php",
                        cache: false,
                        data: "viewLevel="+$view_level+"&viewDate="+document.getElementById("viewSelDate").value,
                        success: onSuccess_overview
        });
}

function onSuccess_overview(data, status) {
        $("#view_content").html(data);
}

// Funktionen instanzieren
// - Submit Form checkIn
// - Submit Form checkOut
// - Submit Form save
$(document).ready(function() {

        // Funktion für Submit checkIn
        $("#submit_cIn").click(function(){
                var formData = $("#form_cIn").serialize();

    $.ajax({
            type: "POST",
      url: "act_checkIn.php",
                        cache: false,
                        data: formData,
                        success: onSuccess_cIn,
                        error: onError
                });
                return false;
        });

        // Funktion für Submit checkOut
        $("#submit_cOut").click(function(){
                var formData = $("#form_cOut").serialize();

    $.ajax({
            type: "POST",
      url: "act_checkOut.php",
                        cache: false,
                        data: formData,
                        success: onSuccess_cOut,
                        error: onError
                });
                return false;
        });

        // Funktion für Submit save
        $("#submit_save").click(function(){
                var formData = $("#form_save").serialize();

    $.ajax({
            type: "POST",
      url: "act_save.php",
                        cache: false,
                        data: formData,
                        success: onSuccess_save,
                        error: onError
                });
                return false;
        });

        // Funktion für View_DAY
        $("#view_day").click(function(){
                $view_level = 1;
                $("#view_nav_next").removeClass("ui-btn-active");
                $("#view_nav_prev").removeClass("ui-btn-active");
                readOverview();
        });

        // Funktion für View_MONTH
        $("#view_month").click(function(){
                $view_level = 2;
                $("#view_nav_next").removeClass("ui-btn-active");
                $("#view_nav_prev").removeClass("ui-btn-active");
                readOverview();
        });

        // Funktion für View_YEAR
        $("#view_year").click(function(){
                $view_level = 3;
                $("#view_nav_next").removeClass("ui-btn-active");
                $("#view_nav_prev").removeClass("ui-btn-active");
                readOverview();
        });

        // Funktion für View navigate previous
        $("#view_nav_prev").click(function(){
                $d = $("#viewSelDate").val();
                $d = new Date($d.substring(0,4), $d.substring(5,7)-1, $d.substring(8,10));
                if ($view_level == 1) $d.setDate($d.getDate()-1);
                if ($view_level == 2) $d.setMonth($d.getMonth()-1);
                if ($view_level == 3) $d.setFullYear($d.getFullYear()-1);
                $("#viewSelDate").val(createStringDate($d));
                readOverview();
        });

        // Funktion für View navigat next
        $("#view_nav_next").click(function(){
                $d = $("#viewSelDate").val();
                $d = new Date($d.substring(0,4), $d.substring(5,7)-1, $d.substring(8,10));
                if ($view_level == 1) $d.setDate($d.getDate()+1);
                if ($view_level == 2) $d.setMonth($d.getMonth()+1);
                if ($view_level == 3) $d.setFullYear($d.getFullYear()+1);
                $("#viewSelDate").val(createStringDate($d));
                readOverview();
        });

        // Neu einlesen, wenn Datum verändert (bei Fokusverlust)
         $("#viewSelDate").focusout(function() {
                  readOverview();
         });
});

function deleteTime($id) {
        $.ajax({
            type: "POST",
      url: "del_time.php",
                        cache: false,
                        data: "timeId="+$id,
                        success: onSuccess_delete
        });
}
function onSuccess_delete(data, status) {
        if ( !isNaN(parseFloat(data)) && isFinite(data) ) {
                document.getElementById("time"+data).style.display = "none";
        } else {
                alert("Fehler: "+data);
        }
}

function showDay($day) {
        $d = $("#viewSelDate").val();
        $d = new Date($d.substring(0,4), $d.substring(5,7)-1, $d.substring(8,10));
        $d.setDate($day);
        $("#viewSelDate").val(createStringDate($d));
        $view_level = 1;
        $("#view_nav_next").removeClass("ui-btn-active");
        $("#view_nav_prev").removeClass("ui-btn-active");
        $("#view_month").removeClass("ui-btn-active");
        $("#view_day").addClass("ui-btn-active");
        readOverview();
}

function showMonth($mon) {
        $d = $("#viewSelDate").val();
        $d = new Date($d.substring(0,4), $d.substring(5,7)-1, $d.substring(8,10));
        $d.setMonth($mon-1);
        $("#viewSelDate").val(createStringDate($d));
        $view_level = 2;
        $("#view_nav_next").removeClass("ui-btn-active");
        $("#view_nav_prev").removeClass("ui-btn-active");
        $("#view_year").removeClass("ui-btn-active");
        $("#view_month").addClass("ui-btn-active");
        readOverview();
}

function createStringDate($d) {
        $s = $d.getFullYear()+"-";
        if ($d.getMonth() < 9) $s = $s+"0";
        $s = $s+($d.getMonth()+1)+"-";
        if ($d.getDate() < 10) $s = $s+"0";
        $s = $s+$d.getDate();
        return $s;
}