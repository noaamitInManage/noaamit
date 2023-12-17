<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 19/06/2017
 * Time: 13:32
 */
class SmsAuthManager extends BaseManager
{
    public static $tb_name = 'tb_sms_auth_token';
    public static $token_valid_time = 60 * 60 * 24; // In minutes - 24 hours

    /*----------------------------------------------------------------------------------*/
    /**
     * SmsAuthManager constructor.
     */
    function __construct()
    {
        parent::__construct();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_valid_query_filter
     * @description returns the query filter for an active token
     * @return string
     */
    private static function get_valid_query_filter()
    {
        // Get current time
        $ts = time();

        // Creates ts is after now minus valid minutes
        return "`used_ts` = 0 AND `cancel_ts` = 0 AND `created_ts` > (" . ($ts - (self::$token_valid_time * 60)) . ")";
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name generate_unique_sms_token
     * @description generates a unique sms token
     * @return int
     */
    private static function generate_unique_token()
    {
        // Get database instance
        $Db = Database::getInstance();

        // Generate a 4 digits token
        $token = rand(1000, 9999);

        // Check if the token already exists and active
        $sql = "SELECT `id` FROM `" . self::$tb_name . "` WHERE `token` = " . $token . " AND `used_ts` > 0 AND " . self::get_valid_query_filter();
        $result = $Db->query($sql);

        // If token already exists and active, generate a new one
        if ($result->num_rows) {
            return self::generate_unique_token();
        }

        // Code is unique, return it
        return $token;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name cancel_all_user_active_tokens
     * @description Cancels all active tokens for the given cellphone
     * @param $cellphone
     * @return bool|int
     */
    private static function cancel_all_user_active_tokens($cellphone)
    {
        // Get database instance
        $Db = Database::getInstance();

        // Auto-Internationalize number
        $cellphone = PhoneNumberManager::internationalize_number($cellphone, null);

        // Cancel all unused tokens for the cellphone
        $ts = time();
        $db_fieldsArr = array(
            'cancel_ts' => $ts,
        );
        $Db->update(self::$tb_name, $db_fieldsArr, [
            ['cellphone', $cellphone],
            ['used_ts', 0],
            ['cancel_ts', 0],
            ['created_ts', '>', $ts - (self::$token_valid_time * 60)],
        ]);

        // Return the number of canceled tokens
        return $Db->get_affected_rows();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name start_auth_process
     * @description starts an sms authentication process
     * @param $cellphone
     * @param $meeting_rooms
     * @return int|bool
     */
    public static function start_auth_process($cellphone, $meeting_rooms = 0)
    {
        // Get database instance
        $Db = Database::getInstance();

        // Auto-Internationalize number
        try {
            $cellphone = PhoneNumberManager::internationalize_number($cellphone, null);
        } catch (Exception $e) {
            return 20; // Invalid phone number
        }

        // Check if a user exists with this cellphone
        $userArr = User::get_user_by_cellphone($cellphone);
        if ($userArr === false) {
            // Write failed login attempt log
            self::log_unknown_cellphone($cellphone);

            return 180; // User doesn't exist
        }

        // If trying to log in to meeting rooms system - check permissions
        if ($meeting_rooms && !$userArr['meeting_rooms']) {
            return 250; // You are not authorized to use the meeting rooms booking system
        }

        // Cancel all user active tokens
        self::cancel_all_user_active_tokens($cellphone);

        // Generate a unique token
        $token = self::generate_unique_token();

        // Send token by sms
        if (self::send_token_by_sms($cellphone, $token) === false) {
            self::cancel_all_user_active_tokens($cellphone);

            return 220; // Error sending SMS
        }

        // Save the token to the database
        $db_fieldsArr = array(
            'user_id' => $userArr['id'],
            'meeting_rooms' => $meeting_rooms,
            'token' => $token,
            'cellphone' => $cellphone,
            'used_ts' => 0,
            'created_ts' => time(),
        );
        $Db->insert(self::$tb_name, $db_fieldsArr);

        // Return the token
        return true;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name send_token_by_sms
     * @description Sends the token to the given cellphone
     * @param $cellphone
     * @param $token
     * @return bool
     */
    private static function send_token_by_sms($cellphone, $token)
    {
        // Get Twilio instance
        $TwilioManager = new TwilioManager();

        // Set the text
        $text = str_replace('[code]', $token, lang('sms_login_message'));

        // Send the message
        $status = $TwilioManager->send($cellphone, $text, 'Auth');

        // Return the status
        return $status;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name check_token
     * @description Validates an active token for the given cellphone
     * @param $token
     * @param $cellphone
     * @param $meeting_rooms
     * @return array|int
     */
    public static function check_token($token, $cellphone, $meeting_rooms = 0)
    {
        // Get database instance
        $Db = Database::getInstance();

        // Auto-Internationalize number
        $cellphone = PhoneNumberManager::internationalize_number($cellphone, null);

        // Query the db for a valid token for the given cellphone
        $sql = "
            SELECT `id`, `user_id` FROM `" . self::$tb_name . "`
                WHERE `meeting_rooms` = " . $meeting_rooms . " AND `token` = " . $token . " AND `cellphone` = '" . $cellphone . "' AND " . self::get_valid_query_filter() . "
            ORDER BY `id` DESC
            LIMIT 1
        ";
        $result = $Db->query($sql);

        // If found, mark as used and return true. Otherwise - return false
        if ($result->num_rows) {
            $rowArr = $Db->get_stream($result);
            $db_fieldsArr = array(
                'used_ts' => time(),
            );
            $Db->update(self::$tb_name, $db_fieldsArr, 'id', $rowArr['id']);

            return array(
                'status' => 1,
                'user_id' => $rowArr['user_id'],
            );
        }

        return array(
            'status' => 0,
            'error' => 173, // Invalid login code
        );
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_active_token
     * @description returns an active token for the given cellphone
     * @param $cellphone
     * @param $meeting_rooms
     * @return int
     */
    public static function get_active_token($cellphone, $meeting_rooms = 0)
    {
        // Get database instance
        $Db = Database::getInstance();

        // Auto-Internationalize number
        $cellphone = PhoneNumberManager::internationalize_number($cellphone, null);

        // Query the db for a valid token for the given cellphone
        $sql = "
            SELECT `token` FROM `" . self::$tb_name . "`
                WHERE `meeting_rooms` = " . $meeting_rooms . " AND `cellphone` = '" . $cellphone . "' AND " . self::get_valid_query_filter() . "
            ORDER BY `id` DESC
            LIMIT 1
        ";
        $result = $Db->query($sql);

        // If found, mark as used and return true. Otherwise - return false
        if ($result->num_rows) {
            $rowArr = $Db->get_stream($result);
            return $rowArr['token'];
        }
        return 0;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name log_unknown_cellphone
     * @description Writes a log about a user that tried to login with an unrecognized cellphone
     * @param $cellphone
     * @return bool
     */
    public static function log_unknown_cellphone($cellphone)
    {
        // Get database instance
        $Db = Database::getInstance();

        $db_fieldsArr = array(
            'cellphone' => $cellphone,
            'last_update' => time(),
        );
        $Db->insert('tb_unknown_cellphone_login_attempts', $db_fieldsArr);

        return true;
    }
}