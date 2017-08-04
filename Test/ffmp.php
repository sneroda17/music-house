<?php
error_reporting(E_ALL);
convert();
function convert(){
try{

    $file = dirname(__FILE__) ."/Agent.mp3";
echo $file;
    $prelisten_audio =dirname(__FILE__) ."/terrrAgent.mp3";
     $wavefile =    dirname(__FILE__) ."/output.png";
  //$command = '`ffmpeg -i "' . $file . '"  -b:a 128k  -map_metadata 0 -id3v2_version 3 -write_id3v1 1 -max_muxing_queue_size 16000 "' . $prelisten_audio . '"`';
 // $command = '`ffmpeg -i "' . $file . '" -ab 128k  -c:v copy "' . $prelisten_audio . '"`';
 //$command =      '`ffmpeg -i "' . $file . '"  -b:a 128k  -c:v copy "' . $prelisten_audio . '"`';  
   $command =      '`ffmpeg -i "' . $file . '"  -b:a 128k   -map_metadata 0 -id3v2_version 3 -write_id3v1 1 -c:v copy  "' . $prelisten_audio . '"`';  

  //$command = '`ffmpeg -i "' . $file . '" -filter_complex showwavespic=s=640x120 -frames:v 1 "' . $wavefile ."`';
  $command = '`ffmpeg -i "' . $file . '" -filter_complex showwavespic=s=640x120 -frames:v 1 "' . $wavefile .'"`';
    $command = '`ffmpeg -i "' . $file . '" -filter_complex "[0:a]aformat=channel_layouts=mono, compand=gain=-6, showwavespic=s=600x120:colors=#9cf42f[fg]; color=s=600x120:color=#44582c, drawgrid=width=iw/10:height=ih/5:color=#9cf42f@0.1[bg]; [bg][fg]overlay=format=rgb,drawbox=x=(iw-w)/2:y=(ih-h)/2:w=iw:h=1:color=#9cf42f"  -vframes 1 "' . $wavefile . '"`';


            //echo PHP_EOL . $command . PHP_EOL;
 //@exec($command);


            runCmd($command);
 }catch(Exception $ex){
        print_r($ex);
     }
}


function runCmd($cmd){

$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("file", "error-output.txt", "a") // stderr is a file to write to
);

$cwd = '/tmp';
$env = array();

$process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);

if (is_resource($process)) {
    // $pipes now looks like this:
    // 0 => writeable handle connected to child stdin
    // 1 => readable handle connected to child stdout
    // Any error output will be appended to /tmp/error-output.txt

    fwrite($pipes[0], '<?php print_r($_ENV); ?>');
    fclose($pipes[0]);

    echo stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    // It is important that you close any pipes before calling
    // proc_close in order to avoid a deadlock
    $return_value = proc_close($process);

    echo "command returned $return_value\n";
}


}


?>