<?php
require_once('require/class.Connection.php');
require_once('require/class.Spotter.php');
require_once('require/class.Language.php');
$Spotter = new Spotter();
$sort = filter_input(INPUT_GET,'sort',FILTER_SANITIZE_STRING);
$date = filter_input(INPUT_GET,'date',FILTER_SANITIZE_STRING);
$spotter_array = $Spotter->getSpotterDataByDate($date,"0,1", $sort);

if (!empty($spotter_array))
{
	$title = sprintf(_("Most Common Time of Day on %s"),date("l F j, Y", strtotime($spotter_array[0]['date_iso_8601'])));
	require_once('header.php');
	print '<div class="select-item">';
	print '<form action="'.$globalURL.'/date" method="post" class="form-inline">';
	print '<div class="form-group">';
	print '<label for="datepickeri">'._("Select a Date").'</label>';
	print '<div class="input-group date" id="datepicker">';
	print '<input type="text" class="form-control" id="datepickeri" name="date" value="" />';
	print '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
	print '</div>';
	print '<button type="submit"><i class="fa fa-angle-double-right"></i></button>';
	print '</div>';
	print '</form>';
	print '</div>';
	print '<script type="text/javascript">$(function () { $("#datepicker").datetimepicker({ format: "YYYY-MM-DD", defaultDate: "'.$date.'" }); }); </script>';
	print '<br />';
	print '<div class="info column">';
	print '<h1>'.sprintf(_("Flights from %s"),date("l F j, Y", strtotime($spotter_array[0]['date_iso_8601']))).'</h1>';
	print '</div>';

	include('date-sub-menu.php');
	print '<div class="column">';
	print '<h2>'._("Most Common Time of Day").'</h2>';
	print '<p>'.sprintf(_("The statistic below shows the most common time of day on <strong>%s</strong>."),date("l F j, Y", strtotime($spotter_array[0]['date_iso_8601']))).'</p>';

	$hour_array = $Spotter->countAllHoursByDate($date);
	print ' <script type="text/javascript" src="https://www.google.com/jsapi"></script>';
	print '<div id="chartHour" class="chart" width="100%"></div>
      	<script> 
      		google.load("visualization", "1", {packages:["corechart"]});
          google.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
            	["'._("Hour").'", "'._("# of Flights").'"], ';

	$hour_data = '';
	foreach($hour_array as $hour_item)
	{
		$hour_data .= '[ "'.date("ga", strtotime($hour_item['hour_name'].":00")).'",'.$hour_item['hour_count'].'],';
	}
	$hour_data = substr($hour_data, 0, -1);
	print $hour_data;
	print ']);
    
            var options = {
            	legend: {position: "none"},
            	chartArea: {"width": "80%", "height": "60%"},
            	vAxis: {title: "'._("# of Flights").'"},
            	hAxis: {showTextEvery: 2},
            	height:300,
            	colors: ["#1a3151"]
            };
    
            var chart = new google.visualization.AreaChart(document.getElementById("chartHour"));
            chart.draw(data, options);
          }
          $(window).resize(function(){
    			  drawChart();
    			});
      </script>';
	print '</div>';
} else {
	$title = _("Unknown Date");
	require_once('header.php');
	print '<h1>'._("Error").'</h1>';
	print '<p>'._("Sorry, this date does not exist in this database. :(").'</p>'; 
}

require_once('footer.php');
?>