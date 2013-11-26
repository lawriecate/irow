<h1>test calculations</h1>
<?

$split = '00:22:29.5';
echo $split . '<br>';

function simple_seconds($time) {
$seconds = substr($split,(strpos($split,":")+1));
$minutes = substr($split,0,strpos($split, ":"));

echo "$minutes <br>$seconds<br><br>";
$total_ms = ($minutes * 60) + ($seconds);
echo $total_ms;
}

function time_to_seconds($time) {
	$parts = explode(":",$time);
	$parts = array_reverse($parts);
	$raise60 = 0;
	$total_seconds = 0;
	foreach($parts as $part) {
		//echo "$part x 60 ^ $raise60<br>";
	
		$seconds = $part * pow(60,$raise60);
		//echo $seconds . "<br>";
		$total_seconds += $seconds;
		$raise60++;
	}
	return $total_seconds;
}

function format_time($t,$f=':') // t = seconds, f = separator 
{
  return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}


function seconds_to_time($init) {
	// for i = 1 to 3
		// for j = 0 to (60 * i)
	//return gmdate("H:i:s",$seconds);
	$hours = floor($init / 3600);
	$minutes = floor(($init / 60) % 60);
	$seconds = $init % 60;
	$fractional =  strstr($init,".");
	$combined_seconds = $seconds . $fractional;
	if($seconds < 10) {
		$second_pad = "0";
	} else {
		$second_pad = NULL;
	}

	return str_pad($hours,2,"0",STR_PAD_LEFT) . ":" . str_pad($minutes,2,"0",STR_PAD_LEFT) . ":" . $second_pad . $combined_seconds;
}

echo time_to_seconds($split);echo "<br>";
echo "-------------------<br>";
echo seconds_to_time(time_to_seconds($split));
?>
<script type="text/javascript">
function time_to_seconds(time) {
			parts = time.split(':');
		parts.reverse();
		raise60 = 0;
		total_seconds = 0;
		for(var key in parts) {
			var part = parts[key];
			//echo "$part x 60 ^ $raise60<br>";
		
			seconds = part * Math.pow(60,raise60);
			
			total_seconds += seconds;
			raise60++;
		}
		return total_seconds;
	}

function outputSplit(init,longOutput) {

		hours = Math.floor(init / 3600);
		minutes = Math.floor((init / 60) % 60);
		seconds = init % 60;

		var pad=function(num,field){
		    var n = '' + num;
		    var w = n.length;
		    var l = field.length;
		    var pad = w < l ? l-w : 0;
		    return field.substr(0,pad) + n;
		};

		if(init.toString().indexOf(".") != -1) {
			fractional =  init.toString().substr(init.toString().indexOf("."));
		} else {
			fractional = ".0";
		}
		
		seconds = seconds.toString().substr(0,seconds.toString().indexOf("."));
		combined_seconds =  pad(seconds,"0") + fractional.substr(0,2);
		
		if(seconds < 10) {
			second_pad = "0";
		} else {
			second_pad = "";
		}

		pretty = minutes + ":" + second_pad + combined_seconds;

		

		if(longOutput == true) {
			pretty = pad(hours,"00") + ":"  + pad(minutes,"00") + ":" + second_pad + combined_seconds;
		}

		return pretty;
	}


  	function distanceCalc(time,split) {
  		return 500 * (time_to_seconds(time) / time_to_seconds(split));
  	}

  	function timeCalc(distance,split) {
  		seconds = (distance / 500) * time_to_seconds(split);
  		return outputSplit(seconds);
  	}
document.write('<br>');
	document.write(time_to_seconds('00:01:50.4'));

	document.write('<br>');
	document.write(outputSplit('1440'));


	document.write('<br>');
	document.write(outputSplit('2.53221'));
	document.write('<br>');
	document.write(timeCalc(2000,'1:50.4'));

	document.write('<br>');
	document.write(timeCalc(2000,'1:50.4'));
	</script>