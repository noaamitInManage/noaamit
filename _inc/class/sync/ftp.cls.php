<?php

/**
 * Simple FTP Class
 * @author Albert Ahronian
 * @since May 15, 2011
 * @since Oct 06, 2011
 *
 */
class PHP_FTP{

	var $server='';
	var $username='';
	var $password='';
	var $port=21;
	public $connectionID=0;

	function __construct($server, $username='', $password='', $port=21){
		$this->server=$server;
		$this->username=$username;
		$this->password=$password;
		$this->port=$port;
		$this->connectionID = $this->connect();
		return $this->connectionID;
	}

	function kill(){
		if($this->connectionID){
			$this->disconnect();
		}
		unset($this);
	}

	private function connect(){
		//$connectionID = @ftp_connect($this->server, $this->port, 3) or die("Could not connect to FTP: [{$this->server}:{$this->port}]");
		$connectionID = @ftp_connect($this->server, $this->port, 3) or mail('gal@inmanage.co.il','subject',print_r(array('ftp error'),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
		ftp_set_option($connectionID,FTP_TIMEOUT_SEC,180);
	//	$result = @ftp_login($connectionID, $this->username, $this->password) or die("Could not login to FTP");
		$result = ftp_login($connectionID, $this->username, $this->password) or mail('gal@inmanage.co.il','subject',print_r(array('ftp login error'),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
		
		return $connectionID;
	}

	private function disconnect(){
		ftp_close($this->connectionID);
		$this->connectionID = 0;
	}
	
	function isDirExists($dirPath){
		$pwd = ftp_pwd($this->connectionID);
		if (@ftp_chdir($this->connectionID, $dirPath)){
			ftp_chdir($this->connectionID, $pwd);   
			return true;
		}
		return false; 
	}
	
	function getPWD(){
		$pwd = ftp_pwd($this->connectionID);
		return $pwd; 
	}
	
	function setPWD($dirPath){
		if ($dirPath=='') {
			return false;
		}
		if (@ftp_chdir($this->connectionID, $dirPath)){
			return true;
		}
		return false;
	}
	
	function createDir($dirPath, $chmod=0777){
		if($dirPath==''){
			return false;
		}
		if(!$this->isDirExists($dirPath)){
			if(@ftp_mkdir($this->connectionID, $dirPath)){
				ftp_chmod($this->connectionID, $chmod, $dirPath);
				return true;
			}else{
				$i = strlen($dirPath)-2;
				while($dirPath[$i]!='/') {
					$i--;
				}
				if($this->createDir(substr($dirPath, 0, $i+1))){
					if(ftp_mkdir($this->connectionID, $dirPath)){
						ftp_chmod($this->connectionID, $chmod, $dirPath);
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}else{
			return true;
		}
	}
}

?>