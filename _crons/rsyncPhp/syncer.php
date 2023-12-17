<?php
include('Net/SSH2.php');
$fileArr = parse_ini_file('/var/www/html/file.ini'); // this can be changed to any path.

set_time_limit(86400);
$sourcePassword = $fileArr["source.pass"];
$sourceUserName = $fileArr['source.user'];
$sourceHost = $fileArr['source.host'];
$sourcePath = $fileArr['source.path'];
$sourceFilename = $fileArr['source.filename'];
$sourceExclude = $fileArr['source.exclude'];

$excludeString = parseExclude($sourceExclude);



foreach ($fileArr['rep.host'] as $key => $value) {

	$targetPassword = $fileArr['rep.pass'][$key];
	$targetHost = $value;
	$targetUser = $fileArr['rep.user'][$key];
	$targetPath = $fileArr['rep.path'][$key];

	
	echo "\n";
	$sftp = new Net_SSH2($sourceHost);
		if (!$sftp->login($sourceUserName, $sourcePassword)) {
		    exit('Login Failed');
		}
	if($sourceHost != $targetHost){
		echo "\n";
		$targetSshConnection = '--rsh="sshpass -p '.$targetPassword.' ssh -l '.$targetUser.'" '.$targetHost.':'.$targetPath;
		echo $cmd = 'rsync -r '.$excludeString.' '.$sourcePath.$sourceFilename.' '.$targetSshConnection;
		echo $sftp->exec($cmd);
		echo "Process completed.";
        echo $sftp->exec('exit');
        unset($sftp);
		//ssh2_exec($sftp , 'exit');
	}else{
		echo "\n";
		echo $cmd = 'rsync -r '.$excludeString.' '.$sourcePath.$sourceFilename.' '.$targetPath.' 2>&1';
		echo $sftp->exec($cmd);
		echo "Process completed.";
        echo $sftp->exec('exit');
        unset($sftp);
		//ssh2_exec($sftp , 'exit');
	}

	echo "\n";
	

}
echo "\n";
die($command);


function parseExclude($array){
	$stringToReturn = '';
	foreach ($array as $key => $value) {
		if($value != NULL && strlen($value) > 0){
			$stringToReturn .= ' --exclude '.$value;
		}
	}
	return $stringToReturn;
}


?>