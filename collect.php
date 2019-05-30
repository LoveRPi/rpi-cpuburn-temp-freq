#!/usr/bin/php
<?php

$stable_time = 30;
$stable_dev = 0.375;
$minimum_iterator = 200;

$finished = false;

$started = false;

$iterator = 0;
$temps = [];
$clocks = [];

function stddev(&$array,$size){
	$array_size = count($array);
	if ($array_size < $size) $size = $array_size;
	$array_slice = array_slice($array,$array_size - $size);
	$sum = array_sum($array_slice);
	$mean = $sum / $size;
	$squarediff = 0;
	foreach ($array_slice as $value) $squarediff += pow($value - $mean,2);
	return sqrt($squarediff / $size);
}

while (!$finished || $iterator < $minimum_iterator) {
	$temp = floatval(strstr(substr(strstr(`vcgencmd measure_temp`,'='),1),'\'',true));
	$clock = intval(trim(substr(strstr(`vcgencmd measure_clock arm`,'='),1))) / 1000000;
	echo $temp.' '.$clock.' ';
	$temps[] = $temp;
	$clocks[] = $clock;
	$stddev = stddev($temps,$stable_time);
	echo ' '.$stddev.PHP_EOL;
	if ($started) {
		if ($stddev < $stable_dev){
			$finished = true;
		}
	} else {
		if ($iterator >= $stable_time && $stddev < $stable_dev){
			echo "Starting Burn".PHP_EOL;
			shell_exec('nice -n 19 ./cpuburn-arm/cpuburn-a53 > /dev/null 2>&1 &');
			$started = true;
		}
	} 
	$iterator++;
	sleep(1);
}

echo "Stopping Burn".PHP_EOL;
shell_exec('pkill -9 cpuburn-a53');

$output = '';
for ($i = 0; $i < $iterator; $i++){
	$output .= $temps[$i].','.$clocks[$i].PHP_EOL;
}
file_put_contents($argv[1],$output,FILE_APPEND);

#file_put_contents($argv[1],implode(',',$temps).PHP_EOL,FILE_APPEND);
#file_put_contents($argv[1],implode(',',$clocks).PHP_EOL,FILE_APPEND);

?>
