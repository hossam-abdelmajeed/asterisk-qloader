<?php
include('config.php');
ini_set('memory_limit', MEM_LIMIT);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING );
$errors = []; // Store all foreseen and unforseen errors here
function array_key_isset($k, $a){
    return isset($a[$k]) || array_key_exists($k, $a);
}
function log_error($errors){
	$err_log = fopen(ERR_LOG,'a');
	$err = json_encode(array('res'=>'error','time'=>date("Y-m-d H:i:s"),'err-code'=>$errors));
	fwrite($err_log, $err."\n");
	fclose($err_log);
}
function get_lookup(){
	$file = fopen(LOOKUP,'a');
	if(!$file){$errors[] = "Error: Can not open file GET LOOKUP"; return false;}
	$line = fgets($file);
	if($line == ''){fputs($file,1);$line=1;}
	fclose($file);
	return $line;
}
function set_lookup($val){
	$file = fopen(LOOKUP,'w');
	if(!$file){$errors[] = "Error: Can not open file SET LOOKUP"; return false;}
	fputs($file,$val);
	fclose($file);
}
function parse_file($filename,$startFromLine,$fetched_at){
	try {
        $file = new SplFileObject($filename);
		$file->seek($startFromLine-1);
		$iteratoration = $startFromLine-1;
    } catch (LogicException $exception) {
        die(log_error('SplFileObject : '.$exception->getMessage()));
    }
	$conn = mysqli_connect(SRV,USR,PWD) or die(log_error(json_encode(array('res'=>'error', 'err-code'=>'error in connection: '.mysqli_connect_error()))));
	@mysqli_select_db($conn,DB) or die (log_error(json_encode(array('res'=>'error', 'err-code'=>'Error select DB: ' . mysqli_error($conn)))));
	while (!$file->eof()) {
		$line = $file->fgets();
			$row = explode(DLMTR, preg_replace("[\'\"]", "", $line));
			$timestamp = trim($row[0]);
			$uniqueid = trim($row[1]);
			$queue = trim($row[2]);
			$agent = explode('/', trim($row[3]))[1];
			$event = trim($row[4]);
			if(array_key_isset(5,$row)){$arg1 = trim($row[5]);} else {$arg1 = NULL;}
			if(array_key_isset(6,$row)){$arg2 = trim($row[6]);} else {$arg2 = NULL;}
			if(array_key_isset(7,$row)){$arg3 = trim($row[7]);} else {$arg3 = NULL;}
			if(array_key_isset(8,$row)){$arg4 = trim($row[8]);} else {$arg4 = NULL;}
			if(array_key_isset(9,$row)){$arg5 = trim($row[9]);} else {$arg5 = NULL;}
				if(in_array($event,unserialize(NEEDED_EVENETS))){
					$result = mysqli_query($conn,"select uniqueid from ".TB." where timestamp='$timestamp' and uniqueid='$uniqueid' and event='$event'; ");
					if(mysqli_num_rows($result) == 0) {
					  $result = mysqli_query($conn,"insert into ".TB." (`id`,`fetched_at`,`timestamp`,`uniqueid`,`queue`,`agent`,`event`,`arg1`,`arg2`,`arg3`,`arg4`,`arg5`)
						values (NULL, '$fetched_at', '$timestamp','$uniqueid','$queue','$agent','$event','$arg1','$arg2','$arg3','$arg4','$arg5'); ");
					  if(!$result){$errors[] = "Error insert data ".mysqli_error($conn);}
					} else {
						$errors[] = 'data found b4 '.$check_log;
					}
				}
		$iteratoration++;
		set_lookup($iteratoration);
		$file->next();
    }
    $file = null;
	mysqli_close($conn);
}



//grep on last EXACT timestamp in DB
$conn = mysqli_connect(SRV,USR,PWD) or die(log_error(json_encode(array('res'=>'error', 'err-code'=>'error in connection: '.mysqli_connect_error()))));
@mysqli_select_db($conn,DB) or die (log_error(json_encode(array('res'=>'error', 'err-code'=>'Error select DB: ' . mysqli_error($conn)))));
$result = mysqli_query($conn,"select timestamp from ".TB." order by id DESC limit 1;");
if(!$result){die (log_error(json_encode(array('res'=>'error', 'err-code'=>'Error retrieve last timestamp from DB: ' . mysqli_error($conn)))));}
//know the line will start from
if(mysqli_num_rows($result)== 0){
	$line_num = '1';
} else {
	$last_timestamp=mysqli_fetch_row($result)[0];
	$line_num = shell_exec('grep -n -w "'.$last_timestamp.'" '.QUEUE_LOG);
	$line_num = explode(':',explode(PHP_EOL,$line_num)[0])[0];
}
mysqli_close($conn);
//set the lookup with last_num as starting point
set_lookup($line_num);

//initiate the loop
while(true){
	$lookup = get_lookup();
	parse_file(QUEUE_LOG,$lookup,date("Y-m-d H:i:s"));
	if(!empty($errors)){log_error($errors);}
	usleep(FETCH_EVERY);
}
?>

