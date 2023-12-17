<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 22/06/2017
 * Time: 10:51
 *
 *  Table structure
 *
    CREATE TABLE IF NOT EXISTS `tb_queue` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
      `class` varchar(255) NOT NULL,
      `method` varchar(255) NOT NULL,
      `paramsArr` longtext NOT NULL,
      `return_type` varchar(255) NOT NULL,
      `priority` int(11) NOT NULL DEFAULT '1',
      `tries` int(11) NOT NULL DEFAULT '0',
      `done` int(11) NOT NULL DEFAULT '0',
      `status` int(11) NOT NULL DEFAULT '0',
      `last_update` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;
 *
 * @example
 *
 * QueueManager::add('siteFunctions', 'send_mail', ['daniel@inmanag.net', 'subject', 'content'], 5, true);
 *
 * $Queue = new QueueManager();
 * $Queue->run();
 *
 */

class QueueManager extends BaseManager
{
    /**
     * The queue table in the database
     * @var string
     */
    public static $tb_name = 'tb_queue';

    /**
     * Maximum tries for each queue item
     * @var int
     */
    public static $max_tries = 3;

    /**
     * Seconds between each try
     * @var int
     */
    public static $interval = 300;

    /**
     * Array of tasks to be exeucted in the current queue
     * @var array
     */
    public $tasksArr = array();

    /*----------------------------------------------------------------------------------*/
    /**
     * Queueable constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Get the current tasks
        $this->tasksArr = $this->get_ready_tasks();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name add
     * @description adds a task to the queue
     * @param $class
     * @param $method
     * @param array $paramsArr
     * @param int $priority
     * @param null $return_type
     * @return array
     */
    public static function add($class, $method, $paramsArr = array(), $priority = 1, $return_type = null)
    {
        // Get database instance
        $Db = Database::getInstance();

        // Prepare the fields
        $db_fieldsArr = array(
            'class' => $class,
            'method' => $method,
            'return_type' => $return_type,
            'paramsArr' => serialize($paramsArr),
            'priority' => $priority,
            'last_update' => time(),
        );

        // Insert the row the the db
        $id = $Db->insert(self::$tb_name, $db_fieldsArr);
        $db_fieldsArr['id'] = $id;

        return $db_fieldsArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_ready_tasks
     * @description Returns an array with all the tasks that are ready to be executed
     * @return array
     */
    private function get_ready_tasks()
    {
        // Get database instance
        $Db = Database::getInstance();

        // Set the valid minimum ts
        $ts = time();
        $min_ts = $ts - self::$interval;

        // Get the tasks from the database
        $sql = "
            SELECT * FROM `" . self::$tb_name . "`
            WHERE `done` = 0 AND `tries` < " . self::$max_tries . " AND `last_update` <= " . $min_ts . "
        ";
        $result = $Db->query($sql);

        // Get all tasks to array
        $tasksArr = array();
        while ($rowArr = $Db->get_stream($result)) {
            $tasksArr[] = $rowArr;
        }

        return $tasksArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name execute_task
     * @description Executes a method from the queue
     * @param $taskArr
     * @return bool
     */
    public function execute_task($taskArr)
    {
        // Check if any required parameters are missing
        if (!$taskArr['class'] || !$taskArr['method']) {
            return false;
        }

        // Call the method
        $result = call_user_func_array($taskArr['class'] . '::' . $taskArr['method'], unserialize($taskArr['paramsArr']));

        // If we know the return type, check it
        if ($taskArr['return_type'] && (gettype($result) != $taskArr['return_type'])) {
            return false;
        }

        return true;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name run
     * @description runs the current queue
     * @return array
     */
    public function run()
    {
        // Prepare the arrays
        $successful_tasksArr = array();
        $failed_tasksArr = array();

        // Run on the tasks queue
        foreach ($this->tasksArr as $taskArr) {
            // Execute the task
            $result = $this->execute_task($taskArr);

            // Determine to which array to add the task
            if ($result) {
                $successful_tasksArr[] = $taskArr;
            } else {
                $failed_tasksArr[] = $taskArr;
            }

            // Set status
            $status = $result ? 1 : 0;
            $done = ($status == 1) || ($taskArr['tries'] + 1) >= self::$max_tries ? 1 : 0;

            // Update the database
            $db_fieldsArr = array(
                'tries' => $taskArr['tries'] + 1,
                'status' => $status,
                'done' => $done,
                'last_update' => time(),
            );
            $this->db->update(self::$tb_name, $db_fieldsArr, 'id', $taskArr['id']);
        }

        // Return the arrays
        return array(
            'successful_tasksArr' => $successful_tasksArr,
            'failed_tasksArr' => $failed_tasksArr,
        );
    }
}