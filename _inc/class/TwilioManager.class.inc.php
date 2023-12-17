<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 25/06/2017
 * Time: 13:27
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/vendors/Twilio/autoload.php');

class TwilioManager extends BaseManager
{
    /**
     * Class instance
     * @var null
     */
    private static $instance = null;

    /**
     * Configuration array
     * @var array
     */
    private $configArr = array(
        'sid' => 'AC67623fbbc6ea9a594e3464edca165c15',
        'token' => '5a6cfe40f457eb41f4f194f11f6e67f4',
    );

    /**
     * Services SIDs array
     * @var array
     */
    private $service_sidArr = array(
        'Auth' => 'MG32e18d402cbf3254c833fa42034e63ed',
    );

    /**
     * Callback url for logs
     * @var string
     */
    private $status_callback_url = '';

    /**
     * The number sms will be sent from - DO NOT CHANGE HERE - set from configManager
     * @var string
     */
    private $from = null;

    /**
     * Twilio client (set int he constructor)
     * @var \Twilio\Rest\Client
     */
    private $client = null;

    /**
     * Log tables
     */
    private $tb_log = 'tb_sms__log';
    private $tb_status_log = 'tb_sms__status_log';
    private $tb_error_log = 'tb_sms__twilio_error_log';

    /*----------------------------------------------------------------------------------*/
    /**
     * TwilioManager constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->from = param('sms_from');
        $this->client = new Twilio\Rest\Client($this->configArr['sid'], $this->configArr['token']);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @return null|TwilioManager
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name send_from_sender
     * @description Returns
     * @param $to
     * @param $text
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     */
    private function send_from_sender($to, $text)
    {
        $message = $this->client->messages->create($to, [
            'from' => $this->from,
            'body' => $text,
            'StatusCallback' => $this->status_callback_url,
        ]);

        return $message;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $to
     * @param $text
     * @param $service
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     * @throws Exception
     */
    private function send_from_service($to, $text, $service)
    {
        if (!array_key_exists($service, $this->service_sidArr)) {
            throw new Exception('Service now found.');
        }

        $message = $this->client->messages->create($to, [
            'messagingServiceSid' => $this->service_sidArr[$service],
            'body' => $text,
        ]);

        return $message;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name send
     * @description Sends an sms
     * @param $to
     * @param $text
     * @param null $service
     * @return bool
     */
    public function send($to, $text, $service = null)
    {
        $ts = time();

        try {
            if ($service !== null) {
                $message = $this->send_from_service($to, $text, $service);
            } else {
                $message = $this->send_from_sender($to, $text);
            }

            $this->write_log($message->sid, $this->from, $to, $text, $message->status, $ts);
            $this->write_status_log($message->sid, $this->from, $to, $message->status, $ts);

            return true;
        } catch (Exception $e) {
            $this->write_error_log($this->from, $to, $text, $e->getStatusCode(), $e->getMessage(), $ts);
            
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name write_log
     * @description Writes an sms log
     * @param $sid
     * @param $from
     * @param $to
     * @param $message
     * @param $status
     * @param $last_update
     */
    private function write_log($sid, $from, $to, $message, $status, $last_update)
    {
        $db_fieldsArr = array(
            'sid' => $sid,
            'from' => $from,
            'to' => $to,
            'message' => $message,
            'status' => $status,
            'last_update' => $last_update,
        );

        $this->db->insert($this->tb_log, $db_fieldsArr);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name write_status_log
     * @description Writes a status change log
     * @param $sid
     * @param $from
     * @param $to
     * @param $status
     * @param $last_update
     */
    public function write_status_log($sid, $from, $to, $status, $last_update)
    {
        $db_fieldsArr = array(
            'sid' => $sid,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'last_update' => $last_update,
        );

        $this->db->insert($this->tb_status_log, $db_fieldsArr);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name write_error_log
     * @description Writes an error log
     * @param $from
     * @param $to
     * @param $message
     * @param $error_code
     * @param $error_message
     * @param $last_update
     */
    public function write_error_log($from, $to, $message, $error_code, $error_message, $last_update)
    {
        $db_fieldsArr = array(
            'from' => $from,
            'to' => $to,
            'message' => $message,
            'error_code' => $error_code,
            'error_message' => $error_message,
            'last_update' => $last_update,
        );

        $this->db->insert($this->tb_error_log, $db_fieldsArr);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name update_message_status
     * @description Updates a message status
     * @param $sid
     * @param $status
     */
    public function update_message_status($sid, $status)
    {
        $db_fieldsArr = array(
            'status' => $status,
            'last_update' => time(),
        );

        $this->db->update($this->tb_log, $db_fieldsArr, 'sid', $sid);
    }
}