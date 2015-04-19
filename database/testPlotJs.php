<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

	<?php 
	//session_start();

	if( $db->connect_errno )
	    die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

	if( !$db->set_charset( "utf8mb4" ) ) {
	    printf("Error loading character set utf8mb4: %s\n", $db->error);
	} 

	// arrays to hold results
	$data = array();
	$activityLabels = array();
	$activityDetails = array();

	$patid = $_SESSION['patid'];

	// table names for queries
	$dataTable = $_SESSION['table'];
	$tablePrefix = "";
	$userTable = $tablePrefix . "patientdata";
	$datasetTable = $tablePrefix . "dataset";
	$activityDatasetTable = $tablePrefix . "activitydataset";
	$activityTable = $tablePrefix . "activity";

	// data
	$query = "SELECT * from {$dataTable} WHERE patientId={$patid}";
	$result = $db->query($query);

	// label names
	$query2 = "SELECT * FROM {$activityDatasetTable} WHERE datasetID IN (SELECT ID FROM {$datasetTable} WHERE patientId={$patid})";
	$result2 = $db->query( $query2 );
	if( !$result2 ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
	}
	
	// label info
	$query3 = "SELECT activity FROM {$activityDatasetTable} WHERE datasetID IN (SELECT ID FROM {$datasetTable} WHERE patientId={$patid})";
	$result3 = $db->query( $query3 );
	if( !$result3) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
	}
	

	// get the data -- hard coded for accelerometer
	while( $row = $result->fetch_assoc() ) {
		$data[] = array( $row['timestamp'], $row['accelerometer_x_CAL'], $row['accelerometer_y_CAL'], $row['accelerometer_z_CAL'],);
	}

	// get the label names
	while($row2 = $result2->fetch_assoc()){
		//echo "row2";
		//echo json_encode($row2);
		$activityDetails[] = array($row2['startTime'],$row2['endTime']);
	}

	// get the label times
	while($row3 = $result3->fetch_assoc()){
		//echo "row3";
		//echo json_encode($row3);
		$activityLabels[] = array($row3['activity']);
	}

	?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>PAC Lab</title>
	<link href="flot/examples/examples.css" rel="stylesheet" type="text/css">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="../../excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="flot/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="flot/jquery.flot.selection.js"></script>
	<script language="javascript" type="text/javascript" src="flot/jquery.flot.time.js"></script>
	<script type="text/javascript">

	
	$(function() {


		var d1 = <?php echo json_encode($data); ?>; // the data
		var d2 = <?php echo json_encode($activityDetails); ?>; // the activity times
		var d3 = <?php echo json_encode($activityLabels); ?>; // the activity labels

		function getData(x1, x2) {
	
			var d = [];
			var dx = []; // acceleration in x
			var dy = []; // acceleration in y
			var dz = []; // acceleration in z

			for (var x = 0;  x< d1.length; x++) {
				
				if(Date(d1[x][0])>=Date(x1) && Date(d1[x][0])<=Date(x2)){
					dx.push([new Date(d1[x][0]).getTime(),d1[x][1]]);
					dy.push([new Date(d1[x][0]).getTime(),d1[x][2]]);
					dz.push([new Date(d1[x][0]).getTime(),d1[x][3]]);
				}
				
			}

			d.push({label:'x', color:1, data: dx});
			d.push({label:'y', color:2, data: dy});
			d.push({label:'z', color:3, data: dz});
		
			return d;
		}

		function getLabels(x1, x2){

			var dactive = [];
			var dinactive = [];
			var dfinal = [];


			for (var x = 0;  x< d2.length; x++) {
				
				var d = [];

				if(Date(d2[x][0])>=Date(x1) && Date(d2[x][1])<=Date(x2)){
					//d.push([new Date(d2[x][0]).getTime(),0]); // straight line
					//d.push([new Date(d2[x][1]).getTime(),0]); // straight line
					if(d3[x]=='active'){
						dactive.push([new Date(d2[x][0]).getTime(),0]);
						dactive.push([new Date(d2[x][1]).getTime(),0]);
					}
					else{
						dinactive.push([new Date(d2[x][0]).getTime(),0]);
						dinactive.push([new Date(d2[x][1]).getTime(),0]);
					}
					
				}
				
			}

			dfinal.push({label:'active', color:4, data: dactive});
			dfinal.push({label: 'inactive', color:5, data:dinactive});
			

			return dfinal;
		}
		
		
		var startData = getData(d1[0][0],d1[d1.length-1][0]);

		var startLabels = getLabels(d2[0][0],d2[d2.length-1][1]);

		var options = {
			legend: {
				show: true
			},
			series: {
				lines: {
					show: true
				},
				points: {
					show: true
				}
			},
			xaxis: {
				mode: "time"
			},
			selection: {
				mode: "xy"
			}
		};

			 
		var plot = $.plot("#placeholder", startData, options);

		var overview = $.plot("#overview", startData, {
				legend: {
					show: false
				},
				series: {
					lines: {
						show: true,
						lineWidth: 1
					},
					shadowSize: 0
				},
				xaxis: {
					mode: "time",
					ticks: false
				},
				grid: {
					color: "#999"
				},
				selection: {
					mode: "xy"
				}
			});

		
		var labelPlot = $.plot("#labels", startLabels , 
		{
			legend:{
				show: true,
				noColumns:0
			},
			series:{
				lines: {
					show:true
				},
				points: {
					show: false	
				}
			},
			xaxis: {
				mode: "time",
			},
			yaxis: {
				show:false
			},
			selection:{
				mode:'xy'
			}
		}
			);


		$("#placeholder").bind("plotselected", function (event, ranges) {

			// clamp the zooming to prevent eternal zoom

			if (ranges.xaxis.to - ranges.xaxis.from < 0.00001) {
				ranges.xaxis.to = ranges.xaxis.from + 0.00001;
			}

			if (ranges.yaxis.to - ranges.yaxis.from < 0.00001) {
				ranges.yaxis.to = ranges.yaxis.from + 0.00001;
			}

			// add a little space for spacing
			ranges.xaxis.to = ranges.xaxis.to + 0.10;
			ranges.xaxis.from = ranges.xaxis.from + 0.10;
			ranges.yaxis.to = ranges.yaxis.to + 0.10;
			ranges.yaxis.from = ranges.yaxis.from + 0.10;

			// do the zooming

			plot = $.plot("#placeholder", getData(ranges.xaxis.from, ranges.xaxis.to),
				$.extend(true, {}, options, {
					xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to},
					yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to}
				})
			);

			// don't fire event on the overview to prevent eternal loop

			overview.setSelection(ranges, true);
			labelPlot.setSelection(ranges, true);
		});

		$("#overview").bind("plotselected", function (event, ranges) {
			plot.setSelection(ranges);
			labelPlot.setSelection(ranges);
		});

		$("#labels").bind("plotselected", function (event, ranges) {

			// don't fire event on the overview to prevent eternal loop

			overview.setSelection(ranges, true);
			plot.setSelection(ranges), true;
		});

		// Add the Flot version string to the footer

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});

	

	if(typeof recording=='undefined'){
		var recording=false;
	}
	if(recording!=null && recording){
		echo('<meta http-equiv="refresh" content="5">');
	}

	var reloading;

// function checkReloading() {
//     if (window.location.hash=="#autoreload") {
//         reloading=setTimeout("window.location.reload();", 5000);
//         document.getElementById("reloadCB").checked=true;
//     }
// }

// function toggleAutoRefresh(cb) {
//     if (cb.checked) {
//         window.location.replace("#autoreload");
//         reloading=setTimeout("window.location.reload();", 5000);
//     } else {
//         window.location.replace("#");
//         clearTimeout(reloading);
//     }
// }

var updateInterval = 3;
		$("#updateInterval").val(updateInterval).change(function () {
			var v = $(this).val();
			if (v && !isNaN(+v)) {
				updateInterval = +v;
				if (updateInterval < 1) {
					updateInterval = 1;
				} else if (updateInterval > 2000) {
					updateInterval = 2000;
				}
				$(this).val("" + updateInterval);
			}
		});

function update() {

			plot.setData([getRandomData()]);

			// Since the axes don't change, we don't need to call plot.setupGrid()

			plot.draw();
			setTimeout(update, updateInterval);
		}

//window.onload=checkReloading;

	</script>
	<?php session_write_close() ?>
	
</head>
<body>

	<div id="header">
		<h2>Accelerometer</h2>
		<input type="checkbox" onclick="update();" id="refresh">Recording</input>
	</div>

	<div id="content">

		
		<div class="container">
			<div id="placeholder" class="placeholder" style="float:left; width:650px;"></div>
			<div id="overview" class="placeholder" style="float:right;width:160px; height:125px;"></div>
		</div>
		<div class="container" style="margin-top: -30px; height:130px">
			<div id="labels" class="placeholder" style="margin-top: -20px; float:left;width:650px;height:125px;"></div>
		</div>

	</div>


</body>
</html>
