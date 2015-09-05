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
		var dat = new Date(d1[0][0]).getFullYear() + "-" + ('0' + (new Date(d1[0][0]).getMonth()+1)).slice(-2) + "-" + ('0' + new Date(d1[0][0]).getDate()).slice(-2); // the date
		document.getElementById('date').value = dat;

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
		
		if(d1.length>0){
			var startData = getData(d1[0][0],d1[d1.length-1][0]);
		}
		else{
			var startData = [];
		}

		if (d2.length>0){
			var startLabels = getLabels(d2[0][0],d2[d2.length-1][1]);
		}
		else{
			var startLabels = [];
		}

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
				mode: "time",
				timezone: "browser"
			},
			selection: {
				mode: "x"
			}
		};

			 
		var plot = $.plot("#placeholder", startData, options);

		
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
				timezone: "browser"
			},
			yaxis: {
				show:false
			},
			selection:{
				mode:"x"
			}
		}
			);

		
		// Select to create label
		$("#placeholder").bind("plotselected", function (event, ranges) {
			var start = new Date(ranges.xaxis.from);
			var end = new Date (ranges.xaxis.to);

			var sHours = "0"+start.getHours();
			
			var sMinutes = "0"+start.getMinutes();
			
			var sSeconds = "0"+start.getSeconds();
			
			var eHours = "0"+end.getHours();
			
			var eMinutes = "0"+end.getMinutes();
			
			var eSeconds = "0"+end.getSeconds();
			
			var startTime = sHours.substr(-2) + ":" + sMinutes.substr(-2) + ":" + sSeconds.substr(-2);
			var endTime = eHours.substr(-2) +  ":" + eMinutes.substr(-2) + ":" + eSeconds.substr(-2);
			

			document.getElementById('start').value = startTime;
			document.getElementById('end').value = endTime;
			
		});
		
		

		$("#labels").bind("plotselected", function (event, ranges) {

			// don't fire event on the overview to prevent eternal loop

			//overview.setSelection(ranges, true);
			plot.setSelection(ranges), true;
		});

		// Add the Flot version string to the footer

		$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
	});

	

var reloading;

function checkReloading() {
    if (window.location.hash=="#autoreload") {
        reloading=setTimeout("window.location.reload();", 5000);
        document.getElementById("refresh").checked=true;
        activity.disabled=true;
        start.disabled=true;
        end.disabled=true;
        save.disabled=true;
    }
}

function toggleAutoRefresh(cb) {
    if (cb.checked) {
        window.location.replace("#autoreload");
        reloading=setTimeout("window.location.reload();", 1000);

    } else {
        window.location.replace("#");
        clearTimeout(reloading);
        activity.disabled=false;
        start.disabled=false;
        end.disabled=false;
        save.disabled=false;
    }
}

function predictLabels(){
	// get all data
	$.get("backend.php",{param:value},callbackFunction);
		callbackFunction(data)
	{
		alert("No more predictions");
	}
	 
}



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

		
		<div class="container" position="relative">
			<div id="placeholder" class="placeholder" style="float:left; width:650px;"></div>
			<!--//div id="overview" class="placeholder" style="float:right;width:160px; height:125px;"></div-->
			<div id="label_select" style="float:right;width:160px; height:125px;">
			
			<form action="saveData.php" method="post" class="login" id='form'>
			<p>Label:
			<select name="activity" id="activity">
				<option>inactive</option>
				<option>low activity</option>
				<option>medium activity</option>
				<option>high activity</option>
		</select>
		<p>
			<label for="date">Date: </label>
			<input id="date" name="date">
			<label for="x-start">Start Time:</label>
			<input type="time" name="x-start" id="start" size="15" step=1 >
			<label for="x-end">End Time:</label>
			<input type="time" name="x-end" id="end" size="15" step=1>
		<p class="select-submit">
      <button type="save" class="save-button" id="save">Save</button>

			</form>
			<p class="select-submit" >
      <button type="predict" class="save-button" id="predict" onclick="predictLabels()">Predict Labels</button>      
			</div>
		</div>
		
    </p>
		<div class="container" style="margin-top: -30px; height:130px">
			<div id="labels" class="placeholder" style="margin-top: -20px; float:left;width:650px;height:125px;"></div>

		</div>


	</div>


</body>
</html>
