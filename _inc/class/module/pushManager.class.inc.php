<?php
/**
 * Created by PhpStorm.
 * User: ?????
 * Date: 16/06/14
 * Time: 11:22
 */

class pushManager
{
    public $androidSenderApiKey = ''; // api key for android devices
    //public $pruduction_certificate = ''; // certificate file for iphone devices
    public $pruduction_certificate = ''; // certificate file for iphone devices
    public $entrust_2048_ca = ''; // certificate file for iphone devices
    public $passphrase = ''; // password for certificate file
    public $tb_device_tokenArr = array(
        "iphone"=>'tb_iphone_devices',
        "android"=>'tb_android_devices',
    );

    public $iphone_socket_url_production = 'ssl://gateway.push.apple.com:2195';
    public $iphone_socket_url_development = 'ssl://gateway.sandbox.push.apple.com:2195';

    private $firebase_url = 'https://fcm.googleapis.com/fcm/send';
    private $firebase_key = '';
    private $firebase_title = 'Salat';
    private $force_firebase = true;


    /*----------------------------------------------------------------------------------*/

    function __construct($project='main')	{
        switch($project){

            case "delivery":
                $this->androidSenderApiKey = '';
                //$this->pruduction_certificate = '';
                $this->passphrase = '';
                break;

            case "main":
            default:
                $this->androidSenderApiKey ='';
                $this->pruduction_certificate = '';
                $this->passphrase = '';
                break;

        }
        $this->ts = time();
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
     * send one notification to device(iphone or android)
     * expect_values:
     *
     *      type    Param name    Required    example
     *        (String)  $device_token    Yes    438359f139e1a5b944027b9323c64dc3fbb8c632ee0585202f04819e5cad3793
     *
     *        (String)  $text Yes pushMsg
     *        (String)  $platform Yes iphone
     *
     * @param $device_token
     * @param $text
     * @param $platform
     * @param array $other_params
     * @param string $source
     * @return bool|int :    true / false
     */
    function send_push($device_token,$text,$platform,$other_params=array(), $source = '') {
        if(!$text){
            return true;
        }

        $send_via = $source == 'firebase' || $this->force_firebase ? 'firebase' : $platform;

        switch(strtolower($send_via)){
            case "android":
                if(!is_array($device_token)) {
                    $device_token = array($device_token);
                }
                return $this->send_to_android_device($device_token,$text,$other_params);
                break;
            case "iphone":
                $answer= $this->send_to_iphone_device($device_token,$text,$other_params);
                return $answer;
                break;
            case "firebase":
            default:
                $answer= $this->send_with_firebase(array($device_token),$text, $platform, $other_params);
                return $answer;
                break;
        }
    }


    /*
     * Future function
        function insert_new_push($device_token,$udid,$platform,$text,$custom_params=array(),$type=1) {

            $customParams = $custom_params ? base64_encode(json_encode($custom_params)) : '';

            $db_fields = array(
                'device_token' => $device_token,
                'udid' => $udid,
                'platform' => $platform,
                'message_text' => $text,
                'custom_params' => $customParams,
                'type' => $type,
            );
            $queryString = "INSERT INTO tb_push_msg (`".implode('`,`',array_keys($db_fields))."`) VALUES ('".implode("','",$db_fields)."')";
            $insertQuery = mysql_unbuffered_query($queryString);
    }
*/

    /*----------------------------------------------------------------------------------*/
    /**
     * use google api to send notification to android device
     * expect_values:
     *
     *	  type 	Param name	Required	example
     *		(String)  $device_token	Yes
     *
     *		(String)  $text Yes pushMsg
     *
     * @return :	true / false
     *
     */
    private function send_to_android_device($device_token,$message,$other_params=array()){

        // prep the bundle
        $msg = array
        (
            'message' 		=> $message,
            /*		'title'			=> 'This is a title. title',
                    'subtitle'		=> 'This is a subtitle. subtitle',
                    'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
                    'vibrate'	=> 1,
                    'sound'		=> 1*/
        );
        //$msg += $other_params;
        if(isset($other_params)){
            foreach($other_params AS $key=>$value){
                $msg[$key]=$value;
            }
        }
        $fields = array
        (
            'registration_ids' 	=> $device_token,
            'data'				=> $msg,
            'priority'          => 'high',
            'time_to_live'     => 60 * 60 * 6
        );

        $headers = array
        (
            'Authorization: key=' . $this->androidSenderApiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        /*if(in_array($_SERVER['REMOTE_ADDR'],array('62.219.212.139','81.218.173.175'))) {
            die('<hr /><pre>' . print_r(
                    array(
                        $result,
                        $headers
                    )
                    , true) . '</pre><hr />');
        }*/
        curl_close( $ch );

        if($result) {
            return true;
        } else {
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * use push.apple.com to send notification to iphone device
     * expect_values:
     *
     *	  type 	Param name	Required	example
     *		(String)  $device_token	Yes
     *
     *		(String)  $text Yes pushMsg
     *
     * @return :	true / false
     *
     */

    private function send_to_iphone_device($device_token,$message,$other_params=array()) {
        /*if(in_array($_SERVER['REMOTE_ADDR'],array('62.219.212.139','81.218.173.175'))) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }*/

        // ============================FOR DEBUGING ===============================
        if(isset($_REQUEST["dev_mode"]) && $_REQUEST["dev_mode"]==1 && isset($_REQUEST["push"]) && $_REQUEST["push"]==1&& isset($_REQUEST["source_id"]) && $_REQUEST["source_id"]==1){
            $this->pruduction_certificate = '';
            $this->entrust_2048_ca = '';
        }
        // ========================================================================
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].$this->pruduction_certificate);
        //stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'].$this->$entrust_2048_ca);
        stream_context_set_option($ctx, 'ssl', 'cafile',  $_SERVER['DOCUMENT_ROOT'].$this->entrust_2048_ca);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        //stream_context_set_option($ctx, 'ssl', 'cafile', $_SERVER['DOCUMENT_ROOT'].'/apns/entrust_2048_ca.cer'); for local environment

        $fp = stream_socket_client($this->iphone_socket_url_production,
            $err,
            $errstr,
            60,
            STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,
            $ctx);
        if (!$fp) {
            //echo "$err ($errstr)<br />\n";
        }


        // Create the payload body
        $body['aps'] = array(
            'badge' => +1,
            'alert' => $message,
            'sound' => 'default'
        );

        //$body['aps'] += $other_params;
        if(isset($other_params)){
            foreach($other_params AS $key=>$value){
                $body['aps'][$key]=$value;
            }
        }

        /*if(isset($other_params)){
            foreach($other_params AS $key=>$value){
                $body['aps']=$value;
            }
        }*/

        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        //die('<hr /><pre>' . print_r(array($result), true) . '</pre><hr />');
        if(!$result) {
            $result = false;
        } else {
            $result = true;
        }
        // Close the connection to the server
        fclose($fp);

        /*if(in_array($_SERVER['REMOTE_ADDR'],array('149.78.232.182','79.180.139.173','62.219.212.139','81.218.173.175','20.0.0.6','20.0.0.33','20.0.0.11','20.0.0.10'))){
            die('<hr /><pre>' . print_r(' Resuklt is => '.$result.' token is => '.$device_token, true) . '</pre><hr />');
        }*/
        return $result;
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * use Firebase cloud to send notifications to ios and android in the same request
     * expect_values:
     *
     *      type    Param name    Required    example
     *        (Array)  $device_tokensArr    Yes    array('438359f139e1a5b944027b9323c64dc3fbb8c632ee0585202f04819e5cad3793')
     *
     *        (String)  $text Yes pushMsg
     *
     * @param $device_tokensArr
     * @param $message
     * @param $platform
     * @param array $other_params
     * @return bool :    true / false
     */

    public function send_with_firebase($device_tokensArr,$message,$platform,$other_params=array()) {

        $data = [
            'registration_ids' => $device_tokensArr,
            'priority' => 'high',
            'time_to_live' => 21600, // 6 hours (60 * 60 * 6)
        ];

        switch (strtolower($platform)) {
            case 'android':
                $paramsArr = [
                    'title' => $this->firebase_title,
                    'message' => $message,
                ];

                if ($other_params) {
                    $paramsArr += $other_params;
                }

                $data['data'] = $paramsArr;

                break;

            case 'iphone':
            default:
                $data['notification'] = [
                    'title' => $this->firebase_title,
                    'body' => $message,
                ];

                if ($other_params) {
                    $data['data'] = $other_params;
                }
        }

        $json_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->firebase_url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_PORT,443);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:key='.$this->firebase_key
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);

        $head = curl_exec($ch);
        $error = curl_error( $ch );	// errors in array

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($head) {
            return true;
        } else {
            return false;
        }
    }
}

?>