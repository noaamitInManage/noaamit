<?php

/**
 * Created by PhpStorm.
 * User: Gal Levy
 * Date: 03/12/2015
 * Time: 10:07
 */


class cronsManager extends BaseManager
{

    private $id = 0;
    private $name;
    private $tb_name = "tb_crons_log";

    /*----------------------------------------------------------------------------------*/
    function __construct($name = '')
    {
        parent::__construct();

        $this->name = $name;
    }

    /**
     * Method Name: start_proccess
     *
     * Description: add a record to DB when CronJob is starting
     */
    function start_proccess()
    {
        $db_fieldsArr = array(
            'name' => $this->name,
            'dt_start' => time(),
        );

        $this->id = $this->db->insert($this->tb_name, $db_fieldsArr);
    }

    /**
     * Method Name: end_proccess
     *
     * Description: add a record to DB when CronJob is ending.
     *
     * @param string $status (1=done,0=error)
     */
    function end_proccess($status = '')
    {
        $db_fieldsArr = array(
            'dt_end' => time(),
            'status' => $status,
        );
        $this->db->update($this->tb_name, $db_fieldsArr, 'id', $this->id);
    }

    /**
     * Method Name: is_running
     *
     * Description: check on DB if there is a CronJon that run
     */
    function is_running($tries = 3)
    {
        $query = "
			SELECT * FROM `{$this->tb_name}`
			WHERE `name` = '{$this->name}' AND `status` = 0 AND `try` <= {$tries}
		";
        $result = $this->db->query($query);

        if ($result->num_rows) {
            $row = $this->db->get_stream($result);

            if ($row['try'] >= $tries) {
                $db_fieldsArr = array(
                    'status' => 1,
                );
                $this->db->update($this->tb_name, $db_fieldsArr, 'id', $row['id']);

                $return = false;
            } else { // is running
                $query = "UPDATE `{$this->tb_name}` SET `try` = `try` + 1 WHERE `id` = '{$row['id']}'";
                $this->db->query($query);

                $return = true;

                mail(
                    implode(",", configManager::$developersEmailsArr),
                    'CronManager - CRON IS DOWN! - ' . $this->name,
                    print_r(
                        array(
                            date('d-m-Y H:i:s'),
                            $row
                        )
                        , true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
            }
        } else {
            $return = false;
        }

        return $return;
    }

    /***************************************************************************/
}