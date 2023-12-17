<?php
/**
 * Utility for syncing database structure between two servers
 * @note: the utility does NOT support field name change. it will create new field instead.
 * @author Albert Ahronian
 * @since Oct 03, 2012
 *
 */

class SyncDBStructure{
	private $sourceLink;
	private $destinationLink;
	private $sourceConfig;
	private $destinationConfig;
	
	function __construct($sourceConfig, $destinationConfig){
		$this->sourceConfig = $sourceConfig;
		$this->destinationConfig = $destinationConfig;
		
		$this->sourceLink = mysql_connect($this->sourceConfig['host'], $this->sourceConfig['user'], $this->sourceConfig['password'], true);
		mysql_select_db($this->sourceConfig['name'], $this->sourceLink);
		
		$this->destinationLink = mysql_connect($this->destinationConfig['host'], $this->destinationConfig['user'], $this->destinationConfig['password'],true);
		mysql_select_db($this->destinationConfig['name'], $this->destinationLink);
	}
	
	public function Sync(){
		if($this->sourceLink == null || $this->destinationLink == null){
			return false;
		}
		$Db =Database::getInstance();
		$query = "SHOW TABLES";
		$resultSource = $Db->query($query, $this->sourceLink);
		
		while($table = mysqli_fetch_array($resultSource)) {
			$tableName = $table[0];
			
			if($this->isTableExists($tableName)){
				$query = "SHOW FIELDS FROM `{$tableName}`";
				$resultField = $Db->query($query, $this->destinationLink) or db_showError(__FILE__, __LINE__, $query);
				$tblDestFields = array();
				while ($field = $Db->get_stream($resultField)) {
					$tblDestFields[] = $field['Field'];
				}
				
				$query = "SHOW FIELDS FROM `{$tableName}`";
				$resultField = $Db->query($query, $this->sourceLink) or db_showError(__FILE__, __LINE__, $query);
				
				while($field = $Db->get_stream($resultField)) {
					$this->alterTable($tableName, $field, in_array($field['Field'], $tblDestFields));
				}
			}
			else{
				// create the table
				$this->createTable($tableName);
			}
		}
	}
	
	private function isTableExists($tableName){
		$Db = Database::getInstance();
		$query = "SELECT COUNT(*) as cnt
					FROM information_schema.tables 
					WHERE table_schema = '{$this->destinationConfig['name']}' 
					AND table_name = '{$tableName}'
					";
		$resultCheck = $Db->query($query, $this->destinationLink) or db_showError(__FILE__, __LINE__, $query);
		
		return mysqli_result($resultCheck, 0, 'cnt') > 0;
	}
	
	private function createTable($tableName){
		$Db = Database::getInstance();

		$query = "SHOW CREATE TABLE `{$tableName}`";
		$resultShowCreateTable = $Db->query($query, $this->sourceLink) or db_showError(__FILE__, __LINE__, $query);
		$query = mysqli_result($resultShowCreateTable, 0, 'Create Table');
		$Db->query($query, $this->destinationLink);
	}
	
	private function alterTable($tableName, $field, $isExists){
		$Db =Database::getInstance();
		$isNull = $field['Null'] == 'Yes' ? 'Null' : 'Not Null';
		$defaultValue = $field['Default'] == '' ? '' : "DEFAULT '{$field['Default']}'";
		$action = $isExists ? "CHANGE `{$field['Field']}`" : 'ADD';
		
		$queryAlterTable = "ALTER TABLE `{$tableName}` {$action} `{$field['Field']}` {$field['Type']} {$isNull} {$defaultValue} {$field['Extra']}";

		$Db->unbuffered_query($queryAlterTable, $this->destinationLink);
		
		$queryIndexAlter = "ALTER TABLE `{$tableName}` Drop INDEX `{$field['Field']}`";
		$Db->unbuffered_query($queryIndexAlter, $this->destinationLink);
		
		switch ($field['Key']) {
			case 'PRI':
				$queryIndexAlter = "ALTER TABLE `{$tableName}` DROP PRIMARY KEY, ADD PRIMARY KEY(`{$field['Field']}`)";
				$Db->unbuffered_query($queryIndexAlter, $this->destinationLink);
				break;
				
			case 'MUL':
				$queryIndexAlter = "ALTER TABLE `{$tableName}` ADD INDEX (`{$field['Field']}`)";
				$Db->unbuffered_query($queryIndexAlter, $this->destinationLink);
				break;
				
			case 'UNI':
				$queryIndexAlter = "ALTER TABLE `{$tableName}` ADD UNIQUE (`{$field['Field']}`)";
				$Db->unbuffered_query($queryIndexAlter, $this->destinationLink);
				break;
		}
	}
}

?>