<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

	<?php include 'dataGrabber.php'; ?>		

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

			var dactive_low = [];
			var dactive_med = [];
			var dactive_high = [];
			var dinactive = [];
			var dfinal = [];


			for (var x = 0;  x< d2.length; x++) {
				
				var d = [];

				if(Date(d2[x][0])>=Date(x1) && Date(d2[x][1])<=Date(x2)){
					//d.push([new Date(d2[x][0]).getTime(),0]); // straight line
					//d.push([new Date(d2[x][1]).getTime(),0]); // straight line
					if(d3[x]=='low activity'){
						dactive_low.push([new Date(d2[x][0]).getTime(),0]);
						dactive_low.push([new Date(d2[x][1]).getTime(),0]);
					}
					if(d3[x]=='medium activity'){
						dactive_med.push([new Date(d2[x][0]).getTime(),0]);
						dactive_med.push([new Date(d2[x][1]).getTime(),0]);
					}if(d3[x]=='high activity'){
						dactive_high.push([new Date(d2[x][0]).getTime(),0]);
						dactive_high.push([new Date(d2[x][1]).getTime(),0]);
					}
					else{
						dinactive.push([new Date(d2[x][0]).getTime(),0]);
						dinactive.push([new Date(d2[x][1]).getTime(),0]);
					}
					
				}
				
			}

			dfinal.push({label:'low activity', color:4, data: dactive_low});
			dfinal.push({label:'medium activity', color:5, data: dactive_med});
			dfinal.push({label:'high activity', color:6, data: dactive_high});
			dfinal.push({label: 'inactive', color:7, data:dinactive});
			

			return dfinal;
		};
		
		
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
					show:false
				},
				points: {
					show: true	
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

	

	// if(typeof recording=='undefined'){
	// 	var recording=false;
	// }
	// if(recording!=null && recording){
	// 	echo('<meta http-equiv="refresh" content="5">');
	// }

var reloading;

function checkReloading() {
    if (window.location.hash=="#autoreload") {
        reloading=setTimeout("window.location.reload();", 5000);
        document.getElementById("refresh").checked=true;
    }
}

function toggleAutoRefresh(cb) {
    if (cb.checked) {
        window.location.replace("#autoreload");
        reloading=setTimeout("window.location.reload();", 1000);
    } else {
        window.location.replace("#");
        clearTimeout(reloading);
    }
}

// var updateInterval = 3;
// 		$("#updateInterval").val(updateInterval).change(function () {
// 			var v = $(this).val();
// 			if (v && !isNaN(+v)) {
// 				updateInterval = +v;
// 				if (updateInterval < 1) {
// 					updateInterval = 1;
// 				} else if (updateInterval > 2000) {
// 					updateInterval = 2000;
// 				}
// 				$(this).val("" + updateInterval);
// 			}
// 		});

// function getOutput() {
//   getRequest(
//       'dataGrabber.php', // URL for the PHP file
//        update,  // handle successful request
//        drawError    // handle error
//   );
//   return false;
// } ;

// function drawError() {
//     var container = document.getElementById('output');
//     container.innerHTML = 'Bummer: there was an error!';
// };

// function update() {

// 			var startData = getData(d1[0][0],d1[d1.length-1][0]);
// 			var startLabels = getLabels(d2[0][0],d2[d2.length-1][1]);
			
// 			$plot.setData(startData);
// 			$plot.setupGrid();
// 			$plot.draw();
// 			setTimeout(getOutput, updateInterval);

// 			$labelPlot.setData(startLabels);
// 			$labelPlot.setupGrid();
// 			$labelPlot.draw();

// 			$overview.setData(startData);
// 			$overview.setupGrid();
// 			$overview.draw();
// 		};

window.onload=checkReloading;

	</script>
	<?php session_write_close() ?>
	
</head>
<body>

	<div id="header">
		<h2>Accelerometer</h2>
		<input type="checkbox" onclick="toggleAutoRefresh(this);" id="refresh">Recording</input>
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
