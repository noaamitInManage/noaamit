<?php

/**
 * Created by PhpStorm.
 * User: Galevy
 * Date: 21/06/2016
 * Time: 10:58
 *
 * Update:
 *    By: David
 *    Date: 09.08.16
 *    Description: Add backup each table by date, fix sql error bugs, add comments
 */
class RecordDBLogs extends BaseManager
{

	public static $save_log = true;
	private $sourceLink; // From (Main) DB mysql connection
	private $logLink; // To (Backup) DB mysql connection
	private $sourceConfig; // From (Main) DB connection details
	private $logConfig; // To (Main) DB connection details
	private $logTableName; // Table backup name
	public $logTablesArr = array(); // Arrays with tables name to backup
	public $ts_backup_from = 0; // In constructor we define this to 3 month
	public $where_ts_backup_from = ''; // where condition to query, from what time to backup

	/**
	 * key - table name
	 * value - how much time to backup - values can be(half_year, 3_month, month, week, day)
	 *
	 * if table not exist here the default time is 3 month.
	 * The time calc in buildFromWhereTime method, so if we want to add more values, need to calc the new value in this method.
	 *
	 * @var array
	 */
	public $tables_archive_backup_fromArr = array(
	    'tb_api_log' => 'month',
	    'tb_activetrail_log' => 'month',
	    'tb_area_change_log' => 'week',
	    'tb_cg__order_faild_log' => 'month',
	    'tb_coupons__user_log' => 'month',
	    'tb_data_log' => 'week',
	    'tb_error_report' => 'week',
	    'tb_lock_api_by_ip' => 'month',
	    'tb_newsletter_api_log' => 'month',
	    'tb_opportunity__editing_log' => 'month',
	    'tb_search_log' => 'month',
	    'tb_secure_tokens_log' => 'week',
	    'tb_sql_error' => 'week',
	    'tb_subscribers_log' => 'month',
	    'tb_suppliers__user_login_information' => 'week',
	    'tb_threads_log' => 'day',
	    'tb_user_login_information' => 'month',
	    'tb_crons_log' => 'week',
	    'tb_tracking' => 'week',
	    'tb_boosttrack_log' => 'week',
	    'tb_user_item_visits' => 'week',
	);

	function __construct($db_informationArr)
	{
		$this->sourceConfig = $db_informationArr['source'];
		$this->logConfig = $db_informationArr['log'];
		$this->sourceLink = mysqli_connect($this->sourceConfig['host'], $this->sourceConfig['user'], $this->sourceConfig['pass']);
		$this->logLink = mysqli_connect($this->logConfig['host'], $this->logConfig['user'], $this->logConfig['pass']);

		mysqli_select_db($this->sourceLink, $this->sourceConfig['name']);
		mysqli_select_db($this->logLink, $this->logConfig['name']);

		$this->logTablesArr = self::getLogTables();
		$this->ts_backup_from = strtotime("-3 month"); //default time to backup, we use this if table not exist in tables_archive_backup_fromArr array
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function deleteDuplicateRowsFromBackupAndMain()
	{

		$query = "SELECT `id` FROM `tb_api_log`";
		$res = $this->db->query($query, $this->logLink);
		if ($res->num_rows) {
			while ($line = $this->db->get_stream($res)) {
				$query = "DELETE FROM `tb_api_log` WHERE `id` = {$line['id']}";
				$this->db->query($query, $this->sourceLink);
			}
		}
	}
	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: getTableFields
	 *
	 * This method return array with all table fields
	 *
	 * @param $table_name
	 * @return array
	 */
	public static function getTableFields($table_name)
	{
		$Db = Database::getInstance();

		$query = "SHOW FIELDS FROM `{$table_name}`";
		$resultField = $Db->query($query) or db_showError(__FILE__, __LINE__, $query);
		$table_fieldsArr = array();

		while ($field = $Db->get_stream($resultField)) {
			$table_fieldsArr[$field['Field']] = "`" . $field['Field'] . "`";
		}

		return $table_fieldsArr;
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: getLogTables
	 *
	 * In this method we write the tables name that we want to backup
	 *
	 * @return array
	 */
	private function getLogTables()
	{
		$log_tablesArr = array(
		    'tb_api_log',
		    'tb_tracking',
            'tb_activetrail_log',
            'tb_area_change_log',
            'tb_cg__log',
            'tb_cg__order_faild_log',
            'tb_coupons__user_log',
            'tb_data_log',
            'tb_error_report',
            'tb_lock_api_by_ip',
			'tb_newsletter_api_log',
			'tb_opportunity__editing_log',
			'tb_orders_status_log',
			'tb_paypal__error_log',
			'tb_paypal__log',
            'tb_search_log',
            'tb_secure_tokens_log',
			'tb_sql_error',
			'tb_subscribers_log',
			'tb_suppliers__login_as_supplier_tokens',
			'tb_suppliers__user_login_information',
			'tb_threads_log',
            'tb_user_login_information',
            'tb_user_item_visits',
            'tb_users_old_system_passwords_logs',
            'tb_boosttrack_log',
            'tb_vouchers_queue',
		);
		
		return $log_tablesArr;
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: syncData
	 *
	 * This Method copy the data from original table to backup table
	 *
	 * @param $table_name
	 */
	public function syncData($table_name)
	{
		$query = "SHOW FIELDS FROM `{$table_name}`";
		$resultField = $this->db->query($query, $this->sourceLink);
		$table_fieldsArr = array();

		while ($field = $this->db->get_stream($resultField)) {
			$table_fieldsArr[] = $field['Field'];
		}

		$this->buildFromWhereTime($table_name, $table_fieldsArr);
		$fields_listArr = '`'.implode("`,`", $table_fieldsArr).'`';

		$query = "
		INSERT INTO {$this->logConfig['name']}.`{$table_name}` ({$fields_listArr})
			SELECT {$fields_listArr} FROM {$this->sourceConfig['name']}.`{$table_name}` {$this->where_ts_backup_from}
		";


		$result = $this->db->query($query, $this->logLink);
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: buildFromWhereTime
	 *
	 * This method build where condition to query, from what time backup data
	 *
	 * @param $table_name
	 * @param $table_fieldsArr
	 */
	public function buildFromWhereTime($table_name, $table_fieldsArr)
	{
		if (array_key_exists($table_name, $this->tables_archive_backup_fromArr)) {
			switch ($this->tables_archive_backup_fromArr[$table_name]) {
				case 'half_year';
					$this->ts_backup_from = strtotime("-6 month");
					break;
				case '3_month';
					$this->ts_backup_from = strtotime("-3 month");
					break;
				case '2_month';
					$this->ts_backup_from = strtotime("-2 month");
					break;
				case 'month';
					$this->ts_backup_from = strtotime("-1 month");
					break;
				case 'week';
					$this->ts_backup_from = strtotime("-1 week");
					break;
				case 'day';
					$this->ts_backup_from = strtotime("-1 days");
					break;
				default:
					$this->ts_backup_from = strtotime("-3 month");
					break;
			}
		}

		if (in_array('last_update', $table_fieldsArr)) {
			$this->where_ts_backup_from = 'WHERE last_update <= ' . $this->ts_backup_from;
		} else if (in_array('insert_ts', $table_fieldsArr)) {
			$this->where_ts_backup_from = 'WHERE insert_ts <= ' . $this->ts_backup_from;
		} else if (in_array('inset_ts', $table_fieldsArr)) {
            $this->where_ts_backup_from = 'WHERE inset_ts <= ' . $this->ts_backup_from;
        } else if (in_array('created_ts', $table_fieldsArr)) {
            $this->where_ts_backup_from = 'WHERE created_ts <= ' . $this->ts_backup_from;
        }
	}

	//----------------------------------------------------------------------------------------------------------------

	public static function recordLog($table_name, $action, $id, $id_field = 'id')
	{
		$table_fieldsArr = self::getTableFields("logs__" . $table_name);
		$table_fieldsArr['log_record_ts'] = "'" . time() . "'";
		$table_fieldsArr['log_action'] = "'" . $action . "'";
		$table_fieldsArr['log_id'] = "'" . $id . "'";

		$query = "
			INSERT INTO `logs__{$table_name}` (`" . implode("`,`", array_keys($table_fieldsArr)) . "`)
				SELECT " . implode(",", array_values($table_fieldsArr)) . " FROM `{$table_name}` WHERE `{$id_field}` = {$id}
		";
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: truncateLogTable
	 *
	 * This Method delete the data from original table
	 *
	 * @param $table_name
	 */
	public function truncateLogTable($table_name)
	{
		$query = "DELETE FROM {$this->sourceConfig['name']}.`{$table_name}` {$this->where_ts_backup_from}";
		$result = $this->db->query($query, $this->sourceLink);
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: syncTables
	 *
	 * This method check if need to create new table or alter changes
	 *
	 * @param $table_name
	 * @param bool|true $localDB
	 */
	public function syncTables($table_name, $localDB = true)
	{
		$this->logTableName = $table_name;
		if ($this->isTableExists($table_name)) {
			$query = "SHOW FIELDS FROM `{$this->logTableName}`";
			$resultField = $this->db->query($query, $this->logLink);
			$table_filedsArr = array();
			while ($field = $this->db->get_stream($resultField)) {
				$table_filedsArr[] = $field['Field'];
			}

			$query = "SHOW FIELDS FROM `{$table_name}`";
			$resultField = $this->db->query($query, $this->sourceLink);
			while ($field = $this->db->get_stream($resultField)) {
				$this->alterTable($table_name, $field, ((in_array($field['Field'], $table_filedsArr)) ? "CHANGE" : "ADD"));
			}
		} else {
			// create the table
			$this->createTable($table_name);
		}
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: isTableExists
	 *
	 * This method check if table exist in DB backup
	 *
	 * @param $table_name
	 * @return bool
	 */
	private function isTableExists($table_name)
	{
		$query = "
			SELECT COUNT(*) as cnt
				FROM information_schema.`TABLES`
			WHERE `TABLE_SCHEMA` = '{$this->logConfig['name']}' AND `TABLE_NAME` = '{$this->logTableName}'
		";
		$resultCheck = $this->db->query($query, $this->logLink);

		return mysqli_result($resultCheck, 0, 'cnt') > 0;
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: createTable
	 *
	 * This method create the table in backup DB
	 *
	 * @param $table_name
	 */
	private function createTable($table_name)
	{
		$query = "SHOW CREATE TABLE `{$table_name}`";
		$resultShowCreateTable = $this->db->query($query, $this->sourceLink);
		$query = mysqli_result($resultShowCreateTable, 0, 'Create Table');
		$query = str_replace("CREATE TABLE `{$table_name}`", "CREATE TABLE `{$this->logTableName}`", $query);
		$this->db->query($query, $this->logLink);
		self::dropTableKeys($table_name);
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: alterTable
	 *
	 * If table exist in DB backup, we make alter changes on this table from origin table
	 *
	 * @param $table_name
	 * @param $field
	 * @param $action
	 */
	private function alterTable($table_name, $field, $action)
	{
		$isNull = $field['Null'] == 'Yes' ? 'Null' : 'Not Null';
		$defaultValue = $field['Default'] == '' ? '' : "DEFAULT '{$field['Default']}'";

		switch ($action) {
			case 'CHANGE':
				$action = "CHANGE `{$field['Field']}`";
				break;
			case 'ADD':
				$action = "ADD";
				break;
			case 'DROP':
				$action = "DROP COLUMN";
				break;
		}

		$queryAlterTable = "ALTER TABLE `{$table_name}` {$action} `{$field['Field']}` {$field['Type']} {$isNull} {$defaultValue}";
		$this->db->query($queryAlterTable, $this->logLink);
		$queryIndexAlter = "ALTER TABLE `{$table_name}` Drop INDEX `{$field['Field']}`";
		$this->db->query($queryIndexAlter, $this->logLink);
	}

	//----------------------------------------------------------------------------------------------------------------

	/**
	 * Method Name: dropTableKeys
	 *
	 * This method remove all keys from table in backup
	 *
	 * @param $table_name
	 */
	private function dropTableKeys($table_name)
	{
		//drop PRIMARY KEY
		$query = "ALTER TABLE `{$this->logTableName}` DROP PRIMARY KEY";
		//$result = mysql_query($query) or db_showError(__FILE__, __LINE__, $query);

		//drop INDEXs
		$query = "SHOW INDEX FROM `{$table_name}`";
		$resultIndex = $this->db->query($query, $this->sourceLink);

		while ($field = $this->db->get_stream($resultIndex)) {
			$queryIndexAlter = "ALTER TABLE `{$this->logTableName}` DROP INDEX `{$field['Key_name']}`";
			$this->db->query($queryIndexAlter, $this->logLink);
		}
	}
}

?>