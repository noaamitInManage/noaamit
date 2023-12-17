<?php

/**
 * Created by PhpStorm.
 * User: Galevy
 * Date: 13/04/2016
 * Time: 12:00
 */
class pushClient extends BaseManager
{

    private $push_system_url = "http://push.inmanage.com";
    private $client_id = 0;
    private $token = "";
    private $code = 0;
    private $send_ts = 0;
    private $testers_mails = '';

    private static $tb_push_queue = "push__tb_push_queue";
    private static $tb_files_to_run = "push__tb_files_to_run";
    private static $tb_black_list = "push__tb_black_list";

    public $query = "";
    public $local_db = false;

    /*----------------------------------------------------------------------------------*/

    function __construct($client_id, $token, $query)
    {
        parent::__construct();

        $this->client_id = $client_id;
        $this->token = $token;
        $this->query = $query;
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Method Name: sendPush
     *
     * Description: send request to push system to send push notifications
     *  1. run the sql query and insert the data into a csv file.
     *  2. add the csv file to zip archive
     *  3. the zip archive send to the api
     *
     * @return mixed
     */
    public function sendPush($code = 0)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(600);

        $this->code = $code;

        if ($this->local_db) {
            $this->sendToLocalServer();
        } else {
            $this->sendToExternalServer();
        }
    }

    /*----------------------------------------------------------------------------------*/

    private function sendToExternalServer()
    {
        $result = $this->db->query($this->query);

        $path = "{$_SERVER['DOCUMENT_ROOT']}/salat2/_static/push";
        if (!file_exists($path)) {
            mkdir($path . "/", 0777); //create push directory
            mkdir($path . "/csv/", 0777); //create csv directory
            mkdir($path . "/zip/", 0777); //create zip directory
        }

        $file_name = "push-" . time();
        $csv_path = "{$path}/csv/{$file_name}.csv";
        $zip_path = "{$path}/zip/{$file_name}.zip";

        //create csv filw with all the data
        $csv_handler = fopen($csv_path, "w");
        while ($row = $this->db->get_stream($result)) {
            fputcsv(
                $csv_handler,
                array(
                    $row['platform'],
                    $row['message'],
                    $row['token'],
                    $row['params'],
                    $row['send_from_ts'],
                    $row['send_until_ts']
                )
            );
        }

        fclose($csv_handler);
        if ($this->testers_mails != '') {
            mail($this->testers_mails, 'MPNS - ' . __FILE__, print_r("csv file created", true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }

        //add the csv file to zip
        $zip = new ZipArchive();
        $zip->open($zip_path, ZipArchive::CREATE);
        $zip->addFile($csv_path, basename($csv_path));
        $zip->close();
        if ($this->testers_mails != '') {
            mail($this->testers_mails, 'MPNS - ' . __FILE__, print_r("zip file created", true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }

        //send request to push system
        if (function_exists('curl_file_create')) { // php 5.6+
            $cFile = curl_file_create($zip_path);
        } else {
            $cFile = '@' . realpath($zip_path);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->push_system_url . "/api/iphone/1.0/sendPush/");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            array(
                'client_id' => $this->client_id,
                'token' => $this->token,
                'code' => $this->code,
                'type' => $cFile,
                'send_ts' => $this->send_ts
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        $return = curl_exec($ch);
        if ($this->testers_mails != '') {
            mail($this->testers_mails, 'MPNS - ' . __FILE__, print_r("file sent to push.dominos", true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }

        curl_close($ch);
        unlink($csv_path); //delete data file
        unlink($zip_path); //delete zip file

        //truncate temp table
        $this->db->query("DELETE FROM `tb_push__tmp`");

        return $return;
    }

    /*----------------------------------------------------------------------------------*/

    private function sendToLocalServer()
    {
        $this->add_file();

        $result = $this->db->query($this->query);

        $line = 0;
        while ($row = $this->db->get_stream($result)) {
            $line++;

            $db_fields = array(
                "platform" => $row['platform'],
                "content" => $row['message'],
                "code" => $this->code,
                "unique_id" => $line,
                "token" => $row['token'],
                "json" => $row['params'],
                "send" => 0,
                "send_from_ts" => $row['send_from_ts'],
                "send_until_ts" => $row['send_until_ts'],
                "last_update" => time()
            );

            $res = $this->db->insert(self::$tb_push_queue, $db_fields);
        }

        $this->remove_tokens();
    }

    /**
     * Method Name: add_file
     *
     * Description: add a file to DB
     */
    private function add_file()
    {
    	$Db = Database::getInstance();
        $db_fields = array(
            "client_id" => $this->client_id,
            "unique_id" => $this->code,
            "extension" => "local server",
            "comment" => "added by local server",
            "parse" => 1,
            "convert" => 1,
            "last_update" => time()
        );
        $query = "INSERT INTO `" . self::$tb_files_to_run . "` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
        $res = $Db->query($query);
        $this->id = $this->db->insert(self::$tb_files_to_run, $db_fields);
        $this->db->delete('push__tb_push_queue', 'code', '<', $this->code - 2);
    }

    /**
     * Method Name: remove_tokens
     *
     * Description: remove broken tokens
     */
    private function remove_tokens()
    {
        $query = "
			DELETE push FROM `" . self::$tb_push_queue . "` AS push
				LEFT JOIN `" . self::$tb_black_list . "` AS black
					ON push.token = black.token AND push.platform = black.platform
			WHERE black.token IS NOT NULL AND push.send = 0
		";
        $result = $this->db->query($query);
    }

    /*****************************************************************************************/

    public function sendPushTest($token, $platform, $message)
    {
        //send request to push system
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->push_system_url . "/api/{$platform}/1.0/sendPushTest/");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            array(
                'token' => $token,
                'message' => $message
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        $return = curl_exec($ch);

        curl_close($ch);
    }

    public function sendPushToTesters($message)
    {
        $query = "
		SELECT Device.device_type AS 'platform', Device.token_device AS 'token' FROM `tb_push__testers` AS Tester
			LEFT JOIN `tb_push__devices` AS Device
				ON Tester.user_id = Device.user_id
		";

        $result = $this->db->query($query);

        $testers_mailsArr = array();

        if ($result->num_rows) {
            while ($row = $this->db->get_stream($result)) {
                $this->sendPushTest($row['token'], $row['platform'], "מערכת פוש - " . $message);
                $testers_mailsArr[] = $row['email'];
            }
        }

        $this->testers_mails = impload(",", $testers_mailsArr);
        if ($this->testers_mails != '') {
            $mail_message = 'נשלחה בקשה לשליחת התראות פוש. מתחיל ביצירת רשימת היעדים לפוש...';
            if ($local_db) {
                $mail_message .= "<br /><strong>הכנסה לDatabase ישירות</strong>";
            }
            mail($this->testers_mails, 'MPNS - ' . __FILE__, print_r($mail_message, true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }
    }
}

?>