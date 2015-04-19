<?php
# Example of Streaming Plots with PHPlot
# This simply plots sin(x) and cos(x), updating at the rate given below.
# Replace the function next_row() to plot something else.
# This must run using a web server, not CLI.

require_once 'phplot/phplot.php';

# Configuration:
# This is the fixed number of points along the X axis:
$n_rows = 40;
# Data range for Y:
$max_y = 1;
$min_y = -1;
# Frames per second:
$frame_rate = 10;
# Total runtime in seconds. Use 0 to run 'forever':
$run_for = 0;

# Derived:
$run_forever = $run_for == 0;
$frame_time = 1 / $frame_rate;
$n_frames = $frame_rate * $run_for;

# Return the next data row (per PHPlot text-data data type):
function next_row($x)
{
    global $frame_rate;
    # Map 8 seconds of frames into 360 degrees (360/8 = 45 degrees/second)
    $theta = deg2rad(45 * $x / $frame_rate);
    return array('', sin($theta), cos($theta));
}

# Create an initial data array with no values. New values will be
# shifted in to the end. This is text-data format; the X values
# are implicit and ignored (not plotted).
for ($i = 0; $i < $n_rows; $i++) $data[$i] = array('', '', '');

# Create and configure the PHPlot object:
$plot = new PHPlot(640, 480);
$plot->SetDataType('text-data');
$plot->SetPlotType('lines');
$plot->SetFileFormat('jpg');
$plot->SetXTickLabelPos('none');
$plot->SetXTickPos('none');
$plot->SetXDataLabelPos('none');
# Don't draw the initial, empty values:
$plot->SetDrawBrokenLines(True);
# Force the Y range, or it will use the first frame to calculate:
$plot->SetPlotAreaWorld(NULL, $min_y, NULL, $max_y);
$plot->SetPrintImage(False);

# Main loop:
$plot->StartStream();
$timestamp = microtime(TRUE);
for ($frame = 0; $run_forever || $frame < $n_frames; $frame++) {
    # Set PHP timeout so it won't terminate the script early.
    set_time_limit(60);
    # Discard the oldest data row, and shift in the new row:
    array_shift($data);
    $data[] = next_row($frame);
    # Set a plot title that includes the frame number:
    $plot->SetTitle(sprintf("Moving Plot Test (Frame %4d)", $frame));
    # Reload the data array:
    $plot->SetDataValues($data);
    # Draw and output the plot:
    $plot->DrawGraph();
    $plot->PrintImageFrame();
    # Sleep until it is time to start the next frame:
    time_sleep_until($timestamp += $frame_time);
}
# End the stream:
$plot->EndStream();