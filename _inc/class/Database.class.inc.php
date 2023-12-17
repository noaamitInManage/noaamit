<?php
include_once($_SERVER['DOCUMENT_ROOT'] ."/_inc/class/configManager.class.inc.php");

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Gal dell
 * Date: 1/9/2017
 * Time: 3:22 PM
 */
class Database
{

    private static $instnace = null;

    private $error_emailsArr = array(
        "mysql.manager@inmanage.net",
       // "daniel@inmanage.net"
    );

    private $dev_mode = NULL;
    private $backtrace = true;


    private $db_login = array(
        'host' => 'localhost',
        'user' => 'noaainmanage',
        'pass' => 'cWzELzo-Ya4WA@(C',
        'db' => 'noaainma_db',
    );
    private $db_login_dev = array(
        'host' => 'localhost',
        'user' => 'noaainmanage',
        'pass' => 'cWzELzo-Ya4WA@(C',
        'db' => 'noaainma_db',
    );

    private $db_logs_informationArr = array(
        'local' => array(
            'source' => array(
                'host' => '',
                'user' => '',
                'pass' => '',
                'name' => '',
            ),
            'log' => array(
                'host' => '',
                'user' => '',
                'pass' => '',
                'name' => '',
            )
        ),
    );

    private $connection = null;

    /*----------------------------------------------------------------------------------*/

    /**
     * Database constructor.
     * make the first connection
     * @param array $db_loginArr
     */
    function __construct($db_loginArr = array())
    {
        $this->dev_mode = configManager::is_dev_mode();

        // make connection
        if (count($db_loginArr)) {
            $db_login = $db_loginArr;
        } else {
            $db_login = ($this->dev_mode) ? $this->db_login_dev : $this->db_login;
        }
        $this->db_connection($db_login);


		// set utf-8
        $this->query("SET NAMES 'utf8'");
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @return Database|null
     */
    public static function getInstance()
    {
        if (null === self::$instnace) {
            self::$instnace = new Database();
        }
        return self::$instnace;
    }
    /*----------------------------------------------------------------------------------*/


    /**----------------------------------------------------------------------------------*/
    /**
     *
     * @name db_connection - save the connection on private var: $this->connection
     *
     * @param $db_login array() {
     * 'host'=>'*',
     * 'user'=>'*',
     * 'pass'=>'*',
     * 'db'=>'*',
     * }
     * @return bool
     */
    private function db_connection($db_login)
    {

        $link = mysqli_connect($db_login['host'], $db_login['user'], $db_login['pass'], $db_login['db']);

        if (!$link) {
            $this->error_handler(mysqli_connect_error(), 'DB Connect');
            return false;
        } else {
            $this->connection = $link;
            return true;
        }
    }


    /*----------------------------------------------------------------------------------*/
    /**
     *
     * @name db_close - turn of the connection on $this->connection
     *  * must to run each end of running file(index.php, _ajax/ajax.index.php)
     * @return true
     */
    private function db_close()
    {
        mysqli_close($this->connection);

        return true;
    }


    /*----------------------------------------------------------------------------------*/
    /**
     * @name query - Simple query with no manipulation
     * @param $query - (string)
     * @param $connection
     * @return bool|mysqli_result
     */
    public function query($query, $connection = null)
    {
        $connection = $connection === null ? $this->connection : $connection;

        $response = mysqli_query($connection, $query)
        or $this->error_handler(mysqli_error($connection), $query);

        return $response;
    }

    /*----------------------------------------------------------------------------------*/
    /*----------------------------------------------------------------------------------*/
    /**
     * @name unbuffered_query - Simple unbuffered query with no manipulation
     * @param $query - (string)
     * @param $connection
     * @return bool|mysqli_result
     */
    public function unbuffered_query($query, $connection = null)
    {
        $connection = $connection === null ? $this->connection : $connection;

        $response = mysqli_query($connection, $query, MYSQLI_USE_RESULT)
        or $this->error_handler(mysqli_error($connection), $query);

        return $response;
    }

    /*----------------------------------------------------------------------------------*/
	/**
	 * @name get_num_rows - get $result num of rows
	 * @param $result - (stram)
	 * @param
	 * @return int|mysqli_fetch_assoc
	 */

    public function get_num_rows($result){
        return mysqli_num_rows($result);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name insert - insert rows to table
     * * important! - this function escape strings, so do not do that to values before you call him
     * @param $tb_name
     * @param $db_fields - fields to insert: array(
     *  key => value,
     *  key2 => value2,
     * )
     * @param $buffered
     * @example $Db->insert('tb_data_log',$fields);
     * @return bool|int
     */
    public function insert($tb_name, $db_fields, $buffered = true)
    {
        foreach ($db_fields AS &$value) {
            $value = $this->make_escape($value);
        }

        $query_method = $buffered ? 'query' : 'unbuffered_query';

        $query = "INSERT INTO `{$tb_name}` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
        $response = $this->{$query_method}($query);

        if ($response) {
            return $buffered ? mysqli_insert_id($this->connection) : true;
        }
        return false;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * INSERT ON DUPLICATE KEY UPDATE query
     *
     * @param $tb_name
     * @param $db_fields
     * @param $update_fieldsArr
     * @param bool $buffered
     * @return bool|int|string
     */
    public function insert_on_duplicate_key_update($tb_name, $db_fields, $update_fieldsArr, $buffered = true)
    {
        foreach ($db_fields AS &$value) {
            $value = $this->make_escape($value);
        }

        $query_method = $buffered ? 'query' : 'unbuffered_query';

        $updateArr = array();
        foreach ($update_fieldsArr as $key) {
            $updateArr[] = "`$key` = '{$db_fields[$key]}'";
        }

        $query = "
            INSERT INTO `{$tb_name}`
                (`" . implode("`,`", array_keys($db_fields)) . "`)
            VALUES
                ('" . implode("','", array_values($db_fields)) . "')
            ON DUPLICATE KEY UPDATE
                " . implode(', ', $updateArr) . "
        ";

        $response = $this->{$query_method}($query);

        if ($response) {
            return $buffered ? mysqli_insert_id($this->connection) : true;
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name replace
     * * important! - this function escape strings, so do not do that to values before you call him
     * @param $tb_name
     * @param $db_fields - fields to insert: array(
     *  key => value,
     *  key2 => value2,
     * )
     * @param $buffered
     * @example $Db->replace('tb_data_log',$fields);
     * @return bool|int
     */
    public function replace($tb_name, $db_fields, $buffered = true)
    {
        foreach ($db_fields AS &$value) {
            $value = $this->make_escape($value);
        }

        $query_method = $buffered ? 'query' : 'unbuffered_query';

        $query = "REPLACE INTO `{$tb_name}` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
        $response = $this->{$query_method}($query);

        if ($response) {
            return $buffered ? mysqli_insert_id($this->connection) : true;
        }
        return false;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_insert_id - get the last insert id
     * @param null
     * @example $Db->get_insert_id();
     * @return bool|int
     */
    public function get_insert_id()
    {
        return mysqli_insert_id($this->connection);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_affected_rows
     * @param null
     * @example $Db->get_affected_rows();
     * @return bool|int
     */
    public function get_affected_rows()
    {
        return mysqli_affected_rows($this->connection);
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @name get_single_where_string
     * @description returns where string for given parameters
     * @param $field
     * @param $operator - operator - only if 3rd parameter is value / value
     * @param null $value - only if 2nd parameter is operator
     * @return string
     */
    private function get_single_where_string($field, $operator, $value = null)
    {
        // If no operator was sent, use '=' as default
        if ($value == null) {
            $value = $operator;
            $operator = '=';
        }

        // Build the where string
        $str = "`" . $field . "` " . $operator . " '" . $this->make_escape($value) . "'";

        return $str;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @name map_whereArr
     * @description returns an array of where conditions
     * @param $whereArr
     * @return array|bool
     */
    private function map_whereArr($whereArr)
    {
        $final_whereArr = array();
        foreach ($whereArr as $conditionArr) {
            if (count($conditionArr) == 2) {
                $final_whereArr[] = $this->get_single_where_string($conditionArr[0], $conditionArr[1]);
            } elseif (count($conditionArr) == 3) {
                $final_whereArr[] = $this->get_single_where_string($conditionArr[0], $conditionArr[1], $conditionArr[2]);
            } else {
                return false;
            }
        }

        return $final_whereArr;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @name get_where_string
     * @description Returns the complete where string
     * @param null $where_field
     * @param null $where_operator
     * @param null $where_value
     * @return bool|string
     */
    public function get_where_string($where_field = null, $where_operator = null, $where_value = null)
    {
        // Set the where variables
        $where = "";
        $whereArr = array();

        // Determine the where condition
        if ($where_field != null) {
            if (!is_array($where_field)) { // Single where condition
                if ($where_operator == null) {
                    return false;
                }

                $where = " WHERE " . $this->get_single_where_string($where_field, $where_operator, $where_value);
            } else { // Array of where conditions
                $whereArr = $this->map_whereArr($where_field);
                if ($whereArr === false) {
                    return false;
                }

                $where = count($whereArr) ? " WHERE " . implode(' AND ', $whereArr) : "";
            }
        }

        return $where;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name update
     * @description executes an update query
     * @param $tb_name
     * @param $db_fieldsArr
     * @param null $where_field
     * @param null $where_operator
     * @param null $where_value
     * @return bool|mysqli_result
     */
    public function update($tb_name, $db_fieldsArr, $where_field = null, $where_operator = null, $where_value = null)
    {
        // Get the where string
        $where = $this->get_where_string($where_field, $where_operator, $where_value);

        $updateArr = array();
        foreach ($db_fieldsArr as $key => $value) {
            $value = $this->make_escape($value);
            $updateArr[] = "`$key` = '{$value}'";
        }

        $query = "UPDATE `" . $tb_name . "` SET  " . implode(', ', $updateArr) . $where;
        $response = $this->query($query);

        return $response;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * @name delete
     * @description executes a delete query
     * @param $tb_name
     * @param null $where_field
     * @param null $where_operator
     * @param null $where_value
     * @return bool|mysqli_result
     */
    public function delete($tb_name, $where_field = null, $where_operator = null, $where_value = null)
    {
        // Get the where string
        $where = $this->get_where_string($where_field, $where_operator, $where_value);

        $query = "DELETE FROM `" . $tb_name . "`" . $where;
        $response = $this->query($query);

        return $response;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $tb_name
     * @param (array) $selectFieldsArr - fields to get in results array(leave null to get all(like '*')
     * @param null $fieldIndex - name of key from table that her values will be the keys of array
     * @param (string) $whereString - mysql string to find specific rows(like 'type=2 AND (status=7 OR status=4)')
     * @return array|bool|null
     */
    public function get_rows($tb_name, $selectFieldsArr = array(), $fieldIndex = null, $whereString = '')
    {

        $select = (($selectFieldsArr && count($selectFieldsArr) > 0) ? "`" . implode("`,`", $selectFieldsArr) . "`" : "*");

        if ($whereString) {
            $whereString = " WHERE " . $whereString;
        }

        $query = "SELECT {$select} FROM `{$tb_name}` {$whereString}";

        return $this->get_query($this->query($query), $fieldIndex);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_query - get rows from table by mysql query
     * @param $queryResponse - response from $db->query()
     * @param null $fieldIndex - name of key from table that her values will be the keys of array(* must to be in selected fields)
     * @return array|bool
     */
    public function get_query($queryResponse, $fieldIndex = null)
    {
        if (!$queryResponse) {
            return false;
        }

        $resultsArr = array();
        $i = 0;
        while ($row = mysqli_fetch_assoc($queryResponse)) {
            $index = $fieldIndex ? $row[$fieldIndex] : $i;
            $resultsArr[$index] = $row;

            $i++;
        }
        mysqli_free_result($queryResponse);

        return $resultsArr;
    }


    /**
     * @name get_stream -when we need to get results as a stream
     * @param $queryResponse - response from $db->query()
     * @return array|bool|null
     */
    public function get_stream($queryResponse)
    {
        if (!$queryResponse) {
            return false;
        }

        return mysqli_fetch_assoc($queryResponse);
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_stream_array -when we need to get results as a stream
     * @param $queryResponse - response from $db->query()
     * @return array|bool|null
     */
    public function get_stream_array($queryResponse)
    {
        if (!$queryResponse) {
            return false;
        }

        return mysqli_fetch_array($queryResponse);
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * @name make_escape - add slashes and remove problem strings. (for sql injection)
     * @param $value (string)
     * @return string
     */
    public function make_escape($value)
    {
        return mysqli_real_escape_string($this->connection, trim($value));
    }
    /*----------------------------------------------------------------------------------*/

    /**
     *
     * error_handler - when query fails, this function catch the error
     *
     * @param $error (string) results of mysqli_error() / mysqli_connect_error()
     * @param $query (string) the query that fell
     * @return bool
     */
    public function error_handler($error, $query = '')
    {

        if ($this->backtrace) {
            $debugArr = debug_backtrace(false);
        } else {
            $debugArr = '';
        }

        $error_fields = array(
            'File' => __FILE__,
            'Line' => __LINE__,
            'Query' => $query,
            'Error' => $error,
            'Date' => date("d-m-Y H:i:s"),
            'Debug' => $debugArr
        );

        if ($this->dev_mode && _officeIP) {
        } else {
            mail(implode(',', $this->error_emailsArr), 'Mysql error:', print_r(array($error_fields), true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }

        // return false because is error, so query need to fall in checking
        return false;

    }

    /*----------------------------------------------------------------------------------*/

    public static function get_time_field($value, $format = 'H:i')
    {
        return Carbon::parse('01/01/1970 ' . $value)->format($format);
    }

    /*----------------------------------------------------------------------------------*/

}

?>
